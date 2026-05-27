<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransService
{
    public function isConfigured(): bool
    {
        return filled(config('services.midtrans.server_key'))
            && filled(config('services.midtrans.client_key'));
    }

    public function getSnapJsUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public function getClientKey(): ?string
    {
        return config('services.midtrans.client_key');
    }

    public function createSnapTransaction(Order $order): array
    {
        $order->loadMissing(['user', 'orderItems.ticket.event']);

        $itemDetails = $order->orderItems->map(function ($item) {
            $eventTitle = $item->ticket?->event?->title;

            return [
                'id' => (string) $item->ticket_id,
                'price' => (int) round((float) $item->price),
                'quantity' => (int) $item->quantity,
                'name' => Str::limit(trim(($eventTitle ? $eventTitle . ' - ' : '') . $item->ticket?->name), 50, ''),
            ];
        })->values()->all();

        $response = $this->snapRequest()->post('/snap/v1/transactions', [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) round((float) $order->total_amount),
            ],
            'item_details'     => $itemDetails,
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
            ],
            'callbacks' => [
                'finish'   => route('attendee.dashboard'),
                'unfinish' => route('attendee.dashboard'),
                'error'    => route('attendee.dashboard'),
            ],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to create Midtrans Snap transaction: ' . $response->body());
        }

        return $response->json();
    }

    public function getTransactionStatus(string $orderNumber): array
    {
        $response = $this->coreRequest()->get('/v2/' . $orderNumber . '/status');

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch Midtrans transaction status: ' . $response->body());
        }

        return $response->json();
    }

    public function verifyNotificationSignature(array $payload): bool
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $serverKey = (string) config('services.midtrans.server_key');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '' || $serverKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signatureKey);
    }

    public function mapTransactionStatus(array $payload): string
    {
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'challenge' ? 'pending' : 'paid',
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny', 'failure' => 'failed',
            'cancel', 'expire' => 'cancelled',
            'refund', 'partial_refund', 'chargeback', 'partial_chargeback' => 'refunded',
            default => 'pending',
        };
    }

    protected function snapRequest()
    {
        $serverKey = config('services.midtrans.server_key');

        if (blank($serverKey)) {
            throw new RuntimeException('Midtrans server key is not configured.');
        }

        return Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->baseUrl($this->getSnapApiBaseUrl());
    }

    protected function coreRequest()
    {
        $serverKey = config('services.midtrans.server_key');

        if (blank($serverKey)) {
            throw new RuntimeException('Midtrans server key is not configured.');
        }

        return Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->baseUrl($this->getCoreApiBaseUrl());
    }

    protected function getSnapApiBaseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    protected function getCoreApiBaseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }
}
