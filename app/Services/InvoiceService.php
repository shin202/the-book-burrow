<?php

namespace App\Services;

use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class InvoiceService
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function generate(string $orderNumber)
    {
        $order = $this->orderService->findByOrderNumber($orderNumber);
        $order->load('items');

        $customerAddress = "$order->billing_address, $order->billing_city, $order->billing_state $order->billing_zip, $order->billing_country";

        $customer = new Party([
            'name' => $order->billing_name,
            'address' => $customerAddress,
            'custom_fields' => [
                'phone' => $order->billing_phone,
                'order number' => "#$order->order_number",
            ],
        ]);

        $items = $order->items->map(function ($item) {
            return InvoiceItem::make($item->title)
                ->pricePerUnit($item->pivot->price)
                ->quantity($item->pivot->quantity)
                ->discount($item->pivot->discount);
        });

        $invoice = Invoice::make("Invoice no. $order->id")
            ->sequence($order->id)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->buyer($customer)
            ->date($order->created_at)
            ->dateFormat('m/d/Y')
            ->payUntilDays(7)
            ->currencySymbol('$')
            ->currencyCode('USD')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->addItems($items)
            ->notes($order->notes ?? '')
            ->logo(public_path('images/the-book-burrow.png'));

        return $invoice->stream();
    }
}
