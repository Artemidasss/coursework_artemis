<?php
// api/SubscriptionModel.php

class SubscriptionModel {

    private static function all(): array {
        return json_decode(file_get_contents(SUBSCRIPTIONS_FILE), true) ?? [];
    }

    private static function save(array $rows): void {
        file_put_contents(SUBSCRIPTIONS_FILE,
            json_encode(array_values($rows), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX);
    }

    public static function byUser(string $userId): array {
        return array_values(array_filter(self::all(), fn($s) => $s['user_id'] === $userId));
    }

    public static function create(string $userId, string $type): array {
        $types = SUBSCRIPTION_TYPES;
        if (!isset($types[$type])) {
            throw new InvalidArgumentException('Неверный тип абонемента');
        }
        $t   = $types[$type];
        $sub = [
            'id'         => uniqid('s_', true),
            'user_id'    => $userId,
            'type'       => $type,
            'name'       => $t['name'],
            'price'      => $t['price'],
            'duration'   => $t['duration'],
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $rows   = self::all();
        $rows[] = $sub;
        self::save($rows);
        return $sub;
    }
}
