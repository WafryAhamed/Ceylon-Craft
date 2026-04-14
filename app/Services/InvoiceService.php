<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate invoice number in format: INV-2026-000001
     */
    public static function generateInvoiceNumber(Order $order): string
    {
        $year = $order->created_at->year;
        $paddedId = str_pad($order->id, 6, '0', STR_PAD_LEFT);
        return "INV-{$year}-{$paddedId}";
    }

    /**
     * Generate invoice HTML
     */
    public function generateInvoiceHtml(Order $order): string
    {
        $invoiceNumber = self::generateInvoiceNumber($order);
        $currency = 'USD'; // Or get from order.payment_currency
        $currencySymbol = '$';

        $itemsHtml = '';
        $subtotal = 0;

        foreach ($order->items as $item) {
            $lineTotal = $item->price * $item->quantity;
            $subtotal += $lineTotal;
            
            $itemsHtml .= sprintf(
                '<tr>
                    <td>%s</td>
                    <td class="qty">%d</td>
                    <td class="price">%s%.2f</td>
                    <td class="total">%s%.2f</td>
                </tr>',
                htmlspecialchars($item->product->name),
                $item->quantity,
                $currencySymbol,
                $item->price,
                $currencySymbol,
                $lineTotal
            );
        }

        // Calculate tax (assumed 10%)
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        return sprintf('
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice %s</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .invoice-container { max-width: 900px; margin: 0 auto; padding: 40px; }
        .header { border-bottom: 3px solid #4a5568; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 28px; font-weight: bold; color: #4a5568; }
        .invoice-details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .detail-section { flex: 1; }
        .detail-section h3 { font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 8px; }
        .detail-section p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background-color: #f0f0f0; padding: 12px; text-align: left; font-weight: bold; border-bottom: 2px solid #ddd; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        .qty { text-align: center; }
        .price, .total { text-align: right; }
        .totals { width: 35%%; float: right; }
        .totals table { width: 100%%; }
        .totals td { padding: 8px 0; }
        .totals .total-amount { font-size: 18px; font-weight: bold; border-top: 2px solid #4a5568; padding-top: 12px; }
        .footer { clear: both; border-top: 1px solid #eee; padding-top: 20px; margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
        .print-only { display: none; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-name">Ceylon Craft</div>
        </div>

        <div class="invoice-details">
            <div class="detail-section">
                <h3>Invoice Details</h3>
                <p>Invoice #: <strong>%s</strong></p>
                <p>Order #: <strong>#%s</strong></p>
                <p>Date: <strong>%s</strong></p>
                <p>Due Date: <strong>%s</strong></p>
            </div>

            <div class="detail-section">
                <h3>Customer</h3>
                <p>Name: <strong>%s</strong></p>
                <p>Email: <strong>%s</strong></p>
                <p>Address: <strong>%s</strong></p>
                <p>Postal: <strong>%s</strong></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="qty">Qty</th>
                    <th class="price">Unit Price</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                %s
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="price">%s%.2f</td>
                </tr>
                <tr>
                    <td>Tax (10%%):</td>
                    <td class="price">%s%.2f</td>
                </tr>
                <tr class="total-amount">
                    <td>Total:</td>
                    <td class="price">%s%.2f</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Ceylon Craft | contact@ceyloncraft.com | +94 (0) 771 234 567</p>
            <p style="font-size: 10px; color: #999; margin-top: 20px;">This invoice was automatically generated on %s</p>
        </div>
    </div>
</body>
</html>',
            htmlspecialchars($invoiceNumber),
            htmlspecialchars($invoiceNumber),
            str_pad($order->id, 6, '0', STR_PAD_LEFT),
            $order->created_at->format('M d, Y'),
            $order->created_at->addDays(30)->format('M d, Y'),
            htmlspecialchars($order->user->name),
            htmlspecialchars($order->user->email),
            htmlspecialchars($order->address),
            htmlspecialchars($order->postal_code),
            $itemsHtml,
            $currencySymbol,
            $subtotal,
            $currencySymbol,
            $tax,
            $currencySymbol,
            $total,
            now()->format('M d, Y H:i A')
        );
    }

    /**
     * Store invoice as HTML file
     */
    public function storeInvoice(Order $order): string
    {
        try {
            $invoiceNumber = self::generateInvoiceNumber($order);
            $html = $this->generateInvoiceHtml($order);
            
            $filename = "invoices/{$invoiceNumber}.html";
            Storage::put($filename, $html);
            
            Log::info('Invoice generated', [
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'filename' => $filename,
            ]);
            
            return $filename;
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get invoice download URL
     */
    public static function getInvoiceUrl(Order $order): string
    {
        $invoiceNumber = self::generateInvoiceNumber($order);
        return route('invoices.download', ['invoiceNumber' => $invoiceNumber]);
    }

    /**
     * Get invoice by order
     */
    public static function getInvoiceByOrder(Order $order): ?string
    {
        $invoiceNumber = self::generateInvoiceNumber($order);
        $path = "invoices/{$invoiceNumber}.html";
        
        if (Storage::exists($path)) {
            return Storage::get($path);
        }
        
        return null;
    }
}
