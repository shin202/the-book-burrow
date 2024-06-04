<?php

namespace App\Jobs\StripeWebhooks;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;

class ChargeSucceededJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WebhookCall $webhookCall;

    /**
     * Create a new job instance.
     */
    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $charge = $this->webhookCall->payload['data']['object'];
        Payment::query()->create([
            'order_id' => $charge['metadata']['order_id'],
            'amount' => $charge['amount'] / 100,
            'transaction_id' => $charge['id'],
            'method' => PaymentMethodEnum::STRIPE,
            'paid_at' => now(),
        ]);

        Order::query()
            ->find($charge['metadata']['order_id'])
            ->update([
                'status' => OrderStatusEnum::COMPLETED,
            ]);
    }
}
