<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ShippingService
{
    /**
     * Available carriers
     */
    const CARRIERS = [
        'standard' => 'Standard Shipping',
        'express' => 'Express Shipping',
        'overnight' => 'Overnight Shipping',
        'pickup' => 'Local Pickup',
    ];

    /**
     * Shipping rates (in cents, per kg)
     */
    const RATES = [
        'standard' => 500, // $5.00
        'express' => 1200, // $12.00
        'overnight' => 2500, // $25.00
        'pickup' => 0, // Free
    ];

    /**
     * Estimated delivery times (in days)
     */
    const DELIVERY_TIMES = [
        'standard' => 3,
        'express' => 1,
        'overnight' => 0,
        'pickup' => 0,
    ];

    /**
     * Calculate shipping cost for order
     */
    public function calculateShippingCost(Order $order, string $carrier = 'standard'): int
    {
        try {
            $weight = $this->calculateOrderWeight($order);
            $baseCost = self::RATES[$carrier] ?? self::RATES['standard'];
            
            // Cost per kg
            $cost = (int)($baseCost * ($weight / 1000)); // Convert to kg
            
            // Minimum shipping cost
            if ($cost === 0 && $carrier !== 'pickup') {
                $cost = self::RATES['standard'];
            }
            
            Log::info('Shipping cost calculated', [
                'order_id' => $order->id,
                'carrier' => $carrier,
                'weight' => $weight,
                'cost' => $cost,
            ]);
            
            return $cost;
        } catch (\Exception $e) {
            Log::error('Failed to calculate shipping cost', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return self::RATES['standard']; // Default to standard shipping
        }
    }

    /**
     * Calculate estimated delivery date
     */
    public function getEstimatedDeliveryDate(string $carrier = 'standard'): \Carbon\Carbon
    {
        $days = self::DELIVERY_TIMES[$carrier] ?? self::DELIVERY_TIMES['standard'];
        return now()->addDays($days);
    }

    /**
     * Get all available carriers
     */
    public function getAvailableCarriers(): Collection
    {
        return collect(self::CARRIERS)->map(function ($name, $key) {
            return [
                'id' => $key,
                'name' => $name,
                'cost' => self::RATES[$key] / 100, // Convert to dollars
                'delivery_days' => self::DELIVERY_TIMES[$key],
            ];
        });
    }

    /**
     * Calculate weight of order items
     */
    private function calculateOrderWeight(Order $order): float
    {
        // Assuming each product has estimated weight (in grams)
        // For simplicity, we're using a default weight per item
        $weight = 0;
        
        foreach ($order->items as $item) {
            // This should ideally come from product->weight
            $itemWeight = $item->product->weight ?? 500; // Default 500g per item
            $weight += $itemWeight * $item->quantity;
        }
        
        return $weight;
    }

    /**
     * Generate tracking number
     */
    public function generateTrackingNumber(Order $order, string $carrier = 'standard'): string
    {
        // Format: SHP-[CARRIER_CODE]-[TIMESTAMP]-[ORDER_ID]
        $carrierCode = strtoupper(substr($carrier, 0, 3));
        $timestamp = now()->format('YmdHis');
        $orderId = str_pad($order->id, 6, '0', STR_PAD_LEFT);
        
        return "SHP-{$carrierCode}-{$timestamp}-{$orderId}";
    }

    /**
     * Create shipment for order
     */
    public function createShipment(Order $order, string $carrier = 'standard'): array
    {
        try {
            $trackingNumber = $this->generateTrackingNumber($order, $carrier);
            $estimatedDelivery = $this->getEstimatedDeliveryDate($carrier);
            
            // Here you would integrate with real carrier APIs (FedEx, DHL, etc.)
            // For now, we're simulating the shipment
            
            $shipment = [
                'order_id' => $order->id,
                'carrier' => $carrier,
                'tracking_number' => $trackingNumber,
                'status' => 'picked_up',
                'created_at' => now(),
                'estimated_delivery' => $estimatedDelivery,
                'last_update' => now(),
                'last_location' => 'Colombo Distribution Center',
            ];
            
            Log::info('Shipment created', [
                'order_id' => $order->id,
                'tracking_number' => $trackingNumber,
                'carrier' => $carrier,
            ]);
            
            return $shipment;
        } catch (\Exception $e) {
            Log::error('Failed to create shipment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Track shipment status
     */
    public function trackShipment(string $trackingNumber): array
    {
        try {
            // In production, call carrier API to get real tracking info
            // For demo, return simulated status
            
            $statuses = [
                'picked_up' => 'Package picked up from warehouse',
                'in_transit' => 'Package is in transit',
                'out_for_delivery' => 'Out for delivery today',
                'delivered' => 'Successfully delivered',
                'failed' => 'Delivery attempt failed',
            ];
            
            Log::info('Track shipment', [
                'tracking_number' => $trackingNumber,
            ]);
            
            return [
                'tracking_number' => $trackingNumber,
                'status' => 'in_transit',
                'status_description' => $statuses['in_transit'],
                'last_update' => now()->subHours(2),
                'last_location' => 'Kandy Distribution Hub',
                'estimated_delivery' => now()->addDay()->format('Y-m-d'),
                'events' => [
                    [
                        'status' => 'picked_up',
                        'timestamp' => now()->subHours(24),
                        'location' => 'Colombo DC',
                    ],
                    [
                        'status' => 'in_transit',
                        'timestamp' => now()->subHours(12),
                        'location' => 'Kandy Hub',
                    ],
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to track shipment', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate shipping address
     */
    public function validateShippingAddress(Order $order): bool
    {
        $address = $order->address;
        $postalCode = $order->postal_code;
        $country = $order->country;
        
        // Validate address has minimum length
        if (strlen($address) < 10) {
            return false;
        }
        
        // Validate postal code format (5-10 digits)
        if (!preg_match('/^\d{5,10}$/', $postalCode)) {
            return false;
        }
        
        // Validate country is supported
        if ($country !== 'lk') {
            return false;
        }
        
        return true;
    }

    /**
     * Check if shipping is available for country
     */
    public function isShippingAvailable(string $country): bool
    {
        $availableCountries = ['lk', 'in', 'bd', 'np'];
        return in_array(strtolower($country), $availableCountries);
    }
}
