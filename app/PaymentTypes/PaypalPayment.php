<?php

namespace App\PaymentTypes;

use App\Managers\PaypalManager;
use Exception;
use Lunar\PaymentTypes\AbstractPayment;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Base\DataTransferObjects\PaymentRefund;
use Lunar\Models\Transaction;
use Lunar\Models\Contracts;
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

        $this->policy = config('lunar.paypal.policy', 'automatic');
    }

    /**
     * Authorize the payment for processing.
     *
     * @return \Lunar\Base\DataTransferObjects\PaymentAuthorize
     */
    public function authorize(): PaymentAuthorize
    {
        if (!$this->order) {
            if (!$this->order = $this->cart->order) {
                $this->order = $this->cart->createOrder();
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
     * @param \Lunar\Models\Contracts\Transaction $transaction
     * @param integer $amount
     * @return \Lunar\Base\DataTransferObjects\PaymentCapture
     */
    public function capture(Contracts\Transaction $transaction, $amount = 0): PaymentCapture
    {
        try {
            $response = $this->paypal->captureAuthorizedPayment(
                $transaction->reference,
                $transaction->order->reference, // Invoice id
                $amount / 100,
                'Payment accepted for order ' . $transaction->order->reference,
            );
        } catch (Exception $e) {
            return new PaymentCapture(
                success: false,
                message: $e->getMessage()
            );
        }

        $response = json_decode(json_encode($response));

        $transactions = [];

        $transactions[] = [
            'parent_transaction_id' => $transaction->id,
            'success' => $response->status == 'COMPLETED',
            'type' => 'capture',
            'driver' => 'paypal',
            'amount' => $amount,
            'reference' => $response->id,
            'status' => $response->status,
            'notes' => '',
            'card_type' => 'paypal',
            'last_four' => null,
            'captured_at' => $response->status == 'COMPLETED' ? now() : null,
        ];

        $transaction->order->transactions()->createMany($transactions);

        return new PaymentCapture(success: true);
    }

    /**
     * Refund a captured transaction
     *
     * @param \Lunar\Models\Contracts\Transaction $transaction
     * @param integer $amount
     * @param string|null $notes
     * @return \Lunar\Base\DataTransferObjects\PaymentRefund
     */
    public function refund(Contracts\Transaction $transaction, int $amount, $notes = null): PaymentRefund
    {
        try {
            $refund = $this->paypal->refundCapturedPayment(
                $transaction->reference,
                $transaction->order->reference,
                $amount / 100,
                $notes,
            );
        } catch (Exception $e) {
            return new PaymentRefund(
                success: false,
                message: $e->getMessage()
            );
        }

        $refund = json_decode(json_encode($refund));

        if ($refund?->error ?? false) {
            return new PaymentRefund(
                success: false,
                message: $refund?->error?->details[0]?->description ?? $refund?->error?->message
            );
        }

        $transaction->order->transactions()->create([
            'success' => $refund->status == 'COMPLETED',
            'type' => 'refund',
            'driver' => 'paypal',
            'amount' => $amount,
            'reference' => $refund->id,
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
        DB::transaction(function () {
            // Convert it to (object) for easy ?-> access
            $paypalOrder = json_decode(json_encode($this->paypalOrder));
            $purchaseUnits = $paypalOrder->purchase_units;

            // For manual capture, set status to requires-capture and leave placed_at null
            // For automatic capture, set status to paid and set placed_at
            $orderUpdate = [
                'status' => $this->policy == 'manual' ? 'requires-capture' : ($this->config['released'] ?? 'paid'),
            ];

            if ($this->policy !== 'manual') {
                $orderUpdate['placed_at'] = now()->parse($paypalOrder->create_time);
            }

            $this->order->update($orderUpdate);

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
                        'CREATED',
                        'SAVED',
                        'APPROVED',
                        'COMPLETED',
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

        return new PaymentAuthorize(
            success: true,
            orderId: $this->order->id
        );
    }
}
