<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Inventory Management Service
 * 
 * Handles:
 * - Stock validation
 * - Stock updates
 * - Low stock alerts
 * - Prevents race conditions with database locks
 */
class InventoryService
{
    /**
     * Check if product has sufficient stock.
     * Uses pessimistic locking to prevent race conditions.
     * 
     * @param int $productId
     * @param int $quantity
     * @throws InsufficientStockException
     * @return Product
     */
    public static function checkStock(int $productId, int $quantity): Product
    {
        // Pessimistic lock: lock row until transaction completes
        $product = Product::lockForUpdate()->findOrFail($productId);

        if ($product->stock < $quantity) {
            throw new InsufficientStockException($product->stock, $quantity);
        }

        return $product;
    }

    /**
     * Reduce stock after order (within transaction).
     * 
     * @param int $productId
     * @param int $quantity
     * @throws InsufficientStockException
     * @return bool
     */
    public static function reduceStock(int $productId, int $quantity): bool
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $product = self::checkStock($productId, $quantity);

            $product->decrement('stock', $quantity);

            // Check if stock is low
            if ($product->stock <= $product->low_stock_threshold ?? 10) {
                self::notifyLowStock($product);
            }

            \Log::info('Stock reduced', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'remaining' => $product->fresh()->stock,
            ]);

            return true;
        });
    }

    /**
     * Increase stock (e.g., when order is cancelled).
     * 
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public static function increaseStock(int $productId, int $quantity): bool
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            $product->increment('stock', $quantity);

            \Log::info('Stock increased', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'new_total' => $product->fresh()->stock,
            ]);

            return true;
        });
    }

    /**
     * Notify admin of low stock.
     * 
     * @param Product $product
     * @return void
     */
    protected static function notifyLowStock(Product $product): void
    {
        $adminEmail = config('mail.admin_email', config('mail.from.address'));

        \Log::warning('Low stock alert', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'stock' => $product->stock,
            'threshold' => $product->low_stock_threshold,
        ]);

        // TODO: Send email notification to admin
        // \Mail::to($adminEmail)->send(new LowStockAlertMail($product));
    }

    /**
     * Get products with low stock.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLowStockProducts()
    {
        return Product::whereColumn('stock', '<=', 'low_stock_threshold')
            ->orWhere('stock', '<', 10)
            ->get();
    }

    /**
     * Get stock status for product.
     * 
     * @param int $productId
     * @return array
     */
    public static function getStockStatus(int $productId): array
    {
        $product = Product::findOrFail($productId);

        return [
            'product_id' => $product->id,
            'stock' => $product->stock,
            'in_stock' => $product->stock > 0,
            'low_stock' => $product->stock <= ($product->low_stock_threshold ?? 10),
            'out_of_stock' => $product->stock <= 0,
            'threshold' => $product->low_stock_threshold,
        ];
    }

    /**
     * Reserve stock for pending order (optional - for advanced systems).
     * 
     * @param int $productId
     * @param int $quantity
     * @param string $orderId
     * @return bool
     */
    public static function reserveStock(int $productId, int $quantity, string $orderId): bool
    {
        return DB::transaction(function () use ($productId, $quantity, $orderId) {
            $product = self::checkStock($productId, $quantity);

            // Create reservation record (optional - requires stock_reservations table)
            // StockReservation::create([
            //     'product_id' => $productId,
            //     'order_id' => $orderId,
            //     'quantity' => $quantity,
            //     'expires_at' => now()->addHours(2),
            // ]);

            return true;
        });
    }
}
