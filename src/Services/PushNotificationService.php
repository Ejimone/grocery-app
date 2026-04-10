<?php

declare(strict_types=1);

namespace App\Services;

use App\Database\Connection;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class PushNotificationService
{
    public static function sendNewOrder(array $order): void
    {
        $publicKey = trim((string) ($_ENV['VAPID_PUBLIC_KEY'] ?? ''));
        $privateKey = trim((string) ($_ENV['VAPID_PRIVATE_KEY'] ?? ''));
        $subject = trim((string) ($_ENV['VAPID_SUBJECT'] ?? 'mailto:admin@example.com'));

        if ($publicKey === '' || $privateKey === '' || !class_exists(WebPush::class)) {
            return;
        }

        $subscriptions = Connection::collection('push_subscriptions')->find()->toArray();
        if (!$subscriptions) {
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);

        $payload = json_encode([
            'title' => 'New customer order',
            'body' => sprintf('%s placed an order of %s', (string) ($order['customer_name'] ?? 'A customer'), format_price((int) ($order['total'] ?? 0))),
            'url' => '/admin',
        ]);

        if ($payload === false) {
            return;
        }

        foreach ($subscriptions as $row) {
            $item = (array) $row;
            $endpoint = (string) ($item['endpoint'] ?? '');
            $keys = (array) ($item['keys'] ?? []);

            if ($endpoint === '' || empty($keys['p256dh']) || empty($keys['auth'])) {
                continue;
            }

            try {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $endpoint,
                        'publicKey' => (string) $keys['p256dh'],
                        'authToken' => (string) $keys['auth'],
                    ]),
                    $payload
                );
            } catch (Throwable) {
                // Ignore invalid subscription payloads.
            }
        }

        try {
            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    continue;
                }

                $subscription = $report->getRequest()->getSubscription();
                if ($subscription !== null) {
                    Connection::collection('push_subscriptions')->deleteOne([
                        'endpoint' => $subscription->getEndpoint(),
                    ]);
                }
            }
        } catch (Throwable) {
            // Ignore transport errors to avoid impacting checkout.
        }
    }
}
