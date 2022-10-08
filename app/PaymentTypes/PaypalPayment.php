<?php

namespace App\PaymentTypes;

use App\Managers\PaypalManager;
use Exception;
use GetCandy\PaymentTypes\AbstractPayment;
use GetCandy\Base\DataTransferObjects\PaymentAuthorize;
use GetCandy\Base\DataTransferObjects\PaymentCapture;
use GetCandy\Base\DataTransferObjects\PaymentRefund;
use GetCandy\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalPayment extends AbstractPayment
{
    /**
     * The PayPal instance.
     *
     * @var PayPalClient
     */
    protected $paypal;

    /**
     * The policy when capturing payments.
     *
     * @var string
     */
    protected $policy;

    protected $paypalPayment;

    /**
     * Initialise the payment type.
     */
    public function __construct()
    {
        $this->paypal = (new PaypalManager())->getClient();

        $this->policy = config('getcandy.paypal.policy', 'automatic');
    }

    /**
     * Authorize the payment for processing.
     *
     * @return \GetCandy\Base\DataTransferObjects\PaymentAuthorize
     */
    public function authorize(): PaymentAuthorize
    {
        if (!$this->order) {
            if (!$this->order = $this->cart->order) {
                $this->order = $this->cart->getManager()->createOrder();
            }
        }

        if ($this->order->placed_at) {
            // Somethings gone wrong!
            return new PaymentAuthorize(
                success: false,
                message: 'This order has already been placed',
            );
        }

        try {
            $this->paypalOrderId = $this->data['paypal_order_id'];
            $this->paypalOrder = (object) $this->paypal->showOrderDetails($this->paypalOrderId);

            // if (
            //     (
            //         $this->paypalOrder->intent == 'AUTHORIZE'
            //             && $this->paypalOrder->status == 'COMPLETED'
            //     )
            //         && $this->policy == 'automatic'
            // ) {
            //     $this->paypalPayment = $this->paypal->capturePaymentOrder($this->paypalOrderId);
            // }

            if ($this->cart) {
                if (!$this->cart->meta) {
                    $this->cart->update([
                        'meta' => [
                            'paypal_order_id' => $this->paypalOrderId,
                            'paypal_payment_id' => $this->paypalPayment?->id,
                        ],
                    ]);
                } else {
                    $meta = $this->cart->meta;
                    $meta->paypal_order_id = $this->paypalOrderId;
                    $meta->paypal_payment_id = $this->paypalPayment?->id;
                    $this->cart->meta = $meta;
                    $this->cart->save();
                }
            }
        } catch (Exception $e) {
            return new PaymentAuthorize(
                success: false,
                message: $e->getMessage(),
            );
        }

        return $this->releaseSuccess();
    }

    /**
     * Capture a payment for a transaction.
     *
     * @param \GetCandy\Models\Transaction $transaction
     * @param integer $amount
     * @return \GetCandy\Base\DataTransferObjects\PaymentCapture
     */
    public function capture(Transaction $transaction, $amount = 0): PaymentCapture
    {
        $payload = [];

        if ($amount > 0) {
            $payload['amount_to_capture'] = $amount;
        }

        try {
            $response = $this->paypal->paymentIntents->capture(
                $transaction->reference,
                $payload
            );
        } catch (Exception $e) {
            return new PaymentCapture(
                success: false,
                message: $e->getMessage()
            );
        }

        $charges = $response->charges->data;

        $transactions = [];

        foreach ($charges as $charge) {
            $card = $charge->payment_method_details->card;
            $transactions[] = [
                'parent_transaction_id' => $transaction->id,
                'success' => $charge->status != 'failed',
                'type' => 'capture',
                'driver' => 'stripe',
                'amount' => $charge->amount_captured,
                'reference' => $response->id,
                'status' => $charge->status,
                'notes' => $charge->failure_message,
                'card_type' => $card->brand,
                'last_four' => $card->last4,
                'captured_at' => $charge->amount_captured ? now() : null,
            ];
        }

        $transaction->order->transactions()->createMany($transactions);

        return new PaymentCapture(success: true);
    }

    /**
     * Refund a captured transaction
     *
     * @param \GetCandy\Models\Transaction $transaction
     * @param integer $amount
     * @param string|null $notes
     * @return \GetCandy\Base\DataTransferObjects\PaymentRefund
     */
    public function refund(Transaction $transaction, int $amount = 0, $notes = null): PaymentRefund
    {
        try {
            $refund = $this->paypal->refunds->create(
                ['payment_intent' => $transaction->reference, 'amount' => $amount]
            );
        } catch (Exception $e) {
            return new PaymentRefund(
                success: false,
                message: $e->getMessage()
            );
        }

        $transaction->order->transactions()->create([
            'success' => $refund->status != 'failed',
            'type' => 'refund',
            'driver' => 'stripe',
            'amount' => $refund->amount,
            'reference' => $refund->payment_intent,
            'status' => $refund->status,
            'notes' => $notes,
            'card_type' => $transaction->card_type,
            'last_four' => $transaction->last_four,
        ]);

        return new PaymentRefund(
            success: true
        );
    }

    /**
     * Return a successfully released payment.
     *
     * @return void
     */
    private function releaseSuccess()
    {
        clock()->info('PaypalPayment releaseSuccess paypalOrder:');
        clock()->info($this->paypalOrder);
        clock()->info('PaypalPayment releaseSuccess paypalPayment:');
        clock()->info($this->paypalPayment);
        DB::transaction(function () {
            // Convert it to (object) for easy ?-> access
            $paypalOrder = json_decode(json_encode($this->paypalOrder));
            $purchaseUnits = $paypalOrder->purchase_units;
            
            $this->order->update([
                'status' => $this->config['released'] ?? 'paid',
                'placed_at' => now()->parse($paypalOrder->create_time),
            ]);

            $transactions = [];

            $type = 'capture';
            if ($this->policy == 'manual') {
                $type = 'intent';
            }

            foreach ($purchaseUnits as $purchaseUnit) {
                $transactions[] = [
                    /**
                     * All statuses seem to mean 'success'. The only two I could see not meaning success were
                     * 'VOIDED' and 'PAYER_ACTION_REQUIRED'. VOIDED: all payments are voided. PAYER_ACTION_REQUIRED:
                     * there's another step the payer has to do. Send them to the "rel": "payer-action" link.
                     */
                    'success' => in_array($paypalOrder->status, [
                        'CREATED', 'SAVED', 'APPROVED', 'COMPLETED',
                    ]),
                    'type' => $type,
                    'driver' => 'paypal',
                    'amount' => $purchaseUnit?->amount?->value * 100,
                    'reference' => $purchaseUnit?->payments?->authorizations[0]?->id,
                    'status' => $purchaseUnit?->payments?->authorizations[0]?->status,
                    // 'notes' => $purchaseUnit->failure_message,
                    'card_type' => 'paypal',
                    // 'last_four' => substr($paypalOrder->payment_source?->paypal?->account_id, -4),
                    // 'captured_at' => $purchaseUnit->amount_captured ? now() : null,
                    'meta' => [
                        'paypal_authorization_id' => $purchaseUnit?->payments?->authorizations[0]?->id,
                        // 'address_line1_check' => $card->checks->address_line1_check,
                        // 'address_postal_code_check' => $card->checks->address_postal_code_check,
                        // 'cvc_check' => $card->checks->cvc_check,
                    ],
                ];
            }
            $this->order->transactions()->createMany($transactions);
        });

        return new PaymentAuthorize(success: true);
    }
}
