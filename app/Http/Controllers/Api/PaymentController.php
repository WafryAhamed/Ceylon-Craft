<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PaymentFailedException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MockPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Payment Controller
 * 
 * Handles all payment operations (using mock service for now):
 * - Creating payment intents
 * - Confirming payments
 * - Retrieving payment status
 */
class PaymentController extends Controller
{
    protected MockPaymentService $paymentService;

    public function __construct(MockPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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
            $user = $request->user();
            $order = Order::findOrFail($request->input('order_id'));

            // Verify order belongs to user
            if ($order->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Create mock payment intent
            $mockIntent = $this->paymentService->createPaymentIntent(
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
                'stripe_payment_intent_id' => $mockIntent['id'],
                'amount' => $order->total,
                'currency' => 'usd',
                'status' => 'pending',
                'payment_method_type' => 'mock',
                'metadata' => [
                    'client_secret' => $mockIntent['client_secret'],
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment intent created successfully',
                'data' => [
                    'payment_id' => $payment->id,
                    'client_secret' => $mockIntent['client_secret'],
                    'public_key' => 'mock_pk_' . fake()->numerify('####################'),
                    'amount' => $order->total,
                    'currency' => 'usd',
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Payment intent creation error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id,
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

            $user = $request->user();
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

            // Confirm mock payment
            $result = $this->paymentService->confirmPaymentIntent(
                $request->input('stripe_payment_intent_id')
            );

            $isSuccess = $result['status'] === 'succeeded';

            if ($isSuccess) {
                // Mark payment as succeeded
                $chargeId = $result['charges']['data'][0]['id'] ?? 'ch_' . fake()->numerify('####################');
                $payment->update([
                    'status' => 'succeeded',
                    'stripe_charge_id' => $chargeId,
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
                'user_id' => $request->user()?->id,
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
    public function show(Payment $payment, Request $request): JsonResponse
    {
        $user = $request->user();
        // Verify user owns this payment
        if ($payment->user_id !== $user->id && !$user->isAdmin()) {
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
}
