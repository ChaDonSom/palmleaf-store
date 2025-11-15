<?php

namespace App\PaymentTypes;

use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Models\Contracts\Transaction as TransactionContract;
use Lunar\Stripe\StripePaymentType;
use Lunar\Stripe\Facades\Stripe;
use Lunar\Stripe\Actions\UpdateOrderFromIntent;
use Stripe\Exception\InvalidRequestException;

class StripePayment extends StripePaymentType
{
    /**
     * Capture a payment for a transaction.
     *
     * @param \Lunar\Models\Contracts\Transaction $transaction
     * @param integer $amount
     * @return \Lunar\Base\DataTransferObjects\PaymentCapture
     */
    public function capture(TransactionContract $transaction, $amount = 0): PaymentCapture
    {
        /** @var Transaction $transaction */
        $payload = [];

        if ($amount > 0) {
            $payload['amount_to_capture'] = $amount;
        }

        $charge = Stripe::getCharge($transaction->reference);

        $paymentIntent = Stripe::fetchIntent($charge->payment_intent);

        try {
            $response = $this->stripe->paymentIntents->capture(
                $paymentIntent->id,
                $payload
            );
        } catch (InvalidRequestException $e) {
            return new PaymentCapture(
                success: false,
                message: $e->getMessage()
            );
        }

        // Fetch the updated payment intent to get the latest status
        $updatedIntent = Stripe::fetchIntent($paymentIntent->id);

        UpdateOrderFromIntent::execute($transaction->order, $updatedIntent);

        // Additional status update to ensure it's set correctly
        if ($updatedIntent->status === 'succeeded') {
            $transaction->order->update([
                'status' => 'paid',
                'placed_at' => $transaction->order->placed_at ?? now(),
            ]);
        }

        return new PaymentCapture(success: true);
    }
}
