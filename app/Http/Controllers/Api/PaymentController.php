<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PaymentFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentIntentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\Payment;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Payment Controller
 * 
 * Handles all payment operations:
 * - Creating payment intents
 * - Confirming payments
 * - Processing webhooks
 * - Retrieving payment status
 */
class PaymentController extends Controller
{
    protected StripePaymentService $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create a payment intent for an order.
     * 
     * POST /api/payments/intent
     * 
     * @param CreatePaymentIntentRequest $request
     * @return JsonResponse
     */
    public function createIntent(CreatePaymentIntentRequest $request): JsonResponse
    {
        try {
            $user = auth('api')->user();

            // Create Stripe payment intent
            $stripeIntent = $this->stripeService->createPaymentIntent(
                userId: $user->id,
                amount: (int)($request->input('amount') * 100), // Convert to cents
                description: $request->input('description', 'Ceylon Craft Order'),
                metadata: [
                    'order_id' => $request->input('order_id'),
                ],
            );

            // Store payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'order_id' => $request->input('order_id'),
                'stripe_payment_intent_id' => $stripeIntent->id,
                'amount' => $request->input('amount'),
                'currency' => $request->input('currency', 'usd'),
                'status' => 'pending',
                'payment_method_type' => 'stripe',
                'metadata' => [
                    'client_secret' => $stripeIntent->client_secret,
                ],
            ]);

            return ApiResponse::created([
                'payment_id' => $payment->id,
                'intent_id' => $stripeIntent->id,
                'client_secret' => $stripeIntent->client_secret,
                'amount' => $stripeIntent->amount,
                'currency' => $stripeIntent->currency,
                'status' => $stripeIntent->status,
            ], 'Payment intent created successfully');
        } catch (PaymentFailedException $e) {
            return ApiResponse::error($e->getMessage(), null, 402);
        } catch (\Exception $e) {
            \Log::error('Payment intent creation error', [
                'error' => $e->getMessage(),
                'user_id' => auth('api')->id(),
            ]);

            return ApiResponse::error('Failed to create payment intent', null, 500);
        }
    }

    /**
     * Confirm a payment intent.
     * 
     * POST /api/payments/confirm
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string|exists:payments,stripe_payment_intent_id',
                'payment_method_id' => 'required|string|regex:/^pm_/',
            ]);

            $user = auth('api')->user();

            // Get payment record
            $payment = Payment::where('stripe_payment_intent_id', $request->input('payment_intent_id'))
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($payment->status !== 'pending') {
                return ApiResponse::error('Payment has already been processed', null, 409);
            }

            // Mark as processing
            $payment->markAsProcessing();

            // Confirm payment with Stripe
            $stripeIntent = $this->stripeService->confirmPaymentIntent(
                $request->input('payment_intent_id'),
                $request->input('payment_method_id')
            );

            // Handle different payment states
            if ($stripeIntent->status === 'succeeded') {
                // Payment successful
                $payment->markAsSucceeded($stripeIntent->charges->data[0]->id ?? '');

                // Update order status
                $order = $payment->order;
                $order->update(['payment_status' => 'paid']);

                // TODO: Send order confirmation email

                return ApiResponse::success([
                    'payment_id' => $payment->id,
                    'status' => 'succeeded',
                    'order_id' => $order->id,
                ], 'Payment successful');
            } elseif ($stripeIntent->status === 'requires_action') {
                // 3D Secure or other verification required
                return ApiResponse::success([
                    'payment_id' => $payment->id,
                    'status' => 'requires_action',
                    'client_secret' => $stripeIntent->client_secret,
                ], 'Additional verification required', 202);
            } else {
                // Payment requires_payment_method or other state
                return ApiResponse::success([
                    'payment_id' => $payment->id,
                    'status' => $stripeIntent->status,
                ]);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::error('Payment not found', null, 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::unprocessable($e->errors());
        } catch (PaymentFailedException $e) {
            $payment?->markAsFailed($e->getMessage());

            return ApiResponse::error($e->getMessage(), null, 402);
        } catch (\Exception $e) {
            \Log::error('Payment confirmation error', [
                'error' => $e->getMessage(),
                'user_id' => auth('api')->id(),
            ]);

            $payment?->markAsFailed('An unexpected error occurred');

            return ApiResponse::error('Failed to confirm payment', null, 500);
        }
    }

    /**
     * Get payment status.
     * 
     * GET /api/payments/{id}
     * 
     * @param Payment $payment
     * @return JsonResponse
     */
    public function show(Payment $payment): JsonResponse
    {
        // Verify user owns this payment
        if ($payment->user_id !== auth('api')->id() && !auth('api')->user()->isAdmin()) {
            return ApiResponse::forbidden();
        }

        return ApiResponse::success([
            'id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'order_id' => $payment->order_id,
            'method_type' => $payment->payment_method_type,
            'created_at' => $payment->created_at,
            'succeeded_at' => $payment->succeeded_at,
            'failed_at' => $payment->failed_at,
        ]);
    }

    /**
     * Handle Stripe webhook.
     * 
     * POST /api/webhooks/stripe
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $event = $this->stripeService->verifyWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );

            // Handle different event types
            match ($event['type']) {
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($event['data']['object']),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event['data']['object']),
                'charge.refunded' => $this->handleChargeRefunded($event['data']['object']),
                default => \Log::info('Unhandled webhook event', ['type' => $event['type']]),
            };

            return response()->json(['received' => true]);
        } catch (PaymentFailedException $e) {
            \Log::error('Webhook verification failed', ['error' => $e->getMessage()]);

            return ApiResponse::error('Webhook verification failed', null, 401);
        } catch (\Exception $e) {
            \Log::error('Webhook processing error', ['error' => $e->getMessage()]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle payment_intent.succeeded webhook event.
     */
    protected function handlePaymentSucceeded(array $intent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $intent['id'])->firstOrFail();

        if ($payment->status !== 'succeeded') {
            $chargeId = $intent['charges']['data'][0]['id'] ?? null;
            $payment->markAsSucceeded($chargeId);

            // Update order
            $payment->order->update(['payment_status' => 'paid']);

            \Log::info('Payment succeeded via webhook', ['payment_id' => $payment->id]);
        }
    }

    /**
     * Handle payment_intent.payment_failed webhook event.
     */
    protected function handlePaymentFailed(array $intent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $intent['id'])->firstOrFail();

        if ($payment->status !== 'failed') {
            $payment->markAsFailed(
                $intent['last_payment_error']['message'] ?? 'Payment failed'
            );

            \Log::info('Payment failed via webhook', ['payment_id' => $payment->id]);
        }
    }

    /**
     * Handle charge.refunded webhook event.
     */
    protected function handleChargeRefunded(array $charge): void
    {
        $payment = Payment::where('stripe_charge_id', $charge['id'])->firstOrFail();

        if ($payment->status !== 'refunded') {
            $payment->markAsRefunded($charge['refunds']['data'][0]['id'] ?? '');

            // Update order status
            $payment->order->update(['status' => 'cancelled']);

            \Log::info('Payment refunded via webhook', ['payment_id' => $payment->id]);
        }
    }
}
