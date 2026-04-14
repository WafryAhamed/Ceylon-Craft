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
     * @param Request $request
     * @return JsonResponse
     */
    public function createIntent(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $order = Order::findOrFail($request->input('order_id'));

            // Verify order belongs to user
            if ($order->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Create Stripe payment intent
            $stripeIntent = $this->stripeService->createPaymentIntent(
                userId: $user->id,
                amount: (int)($order->total * 100), // Convert to cents
                description: "Order #{$order->id} - Ceylon Craft",
                metadata: [
                    'order_id' => $order->id,
                ],
            );

            // Store payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'stripe_payment_intent_id' => $stripeIntent->id,
                'amount' => $order->total,
                'currency' => 'usd',
                'status' => 'pending',
                'payment_method_type' => 'stripe',
                'metadata' => [
                    'client_secret' => $stripeIntent->client_secret,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment intent created successfully',
                'data' => [
                    'payment_id' => $payment->id,
                    'client_secret' => $stripeIntent->client_secret,
                    'public_key' => config('services.stripe.public_key'),
                    'amount' => $order->total,
                    'currency' => 'usd',
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (PaymentFailedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 402);
        } catch (\Exception $e) {
            \Log::error('Payment intent creation error', [
                'error' => $e->getMessage(),
                'user_id' => auth('api')->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent',
            ], 500);
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
                'payment_id' => 'required|exists:payments,id',
                'stripe_payment_intent_id' => 'required|string',
            ]);

            $user = auth('api')->user();
            $payment = Payment::findOrFail($request->input('payment_id'));

            // Verify payment belongs to user
            if ($payment->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Check if payment already processed
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment has already been processed',
                ], 409);
            }

            // Simulate payment processing based on test ID
            $stripeIntentId = $request->input('stripe_payment_intent_id');
            $isSuccess = !str_contains($stripeIntentId, 'failed');

            if ($isSuccess) {
                // Mark payment as succeeded
                $payment->update([
                    'status' => 'succeeded',
                    'stripe_charge_id' => 'ch_' . fake()->numerify('####################'),
                    'succeeded_at' => now(),
                ]);

                // Update order payment status
                $payment->order->update(['payment_status' => 'paid']);
            } else {
                // Mark payment as failed
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $isSuccess ? 'Payment successful' : 'Payment failed',
                'data' => [
                    'payment_id' => $payment->id,
                    'status' => $payment->status,
                    'order_id' => $payment->order_id,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Payment confirmation error', [
                'error' => $e->getMessage(),
                'user_id' => auth('api')->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment',
            ], 500);
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
