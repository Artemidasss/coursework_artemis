<?php
// api/subscriptions.php  — purchase / my
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/UserModel.php';
require_once __DIR__ . '/SubscriptionModel.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'purchase':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['error' => 'Метод не разрешён']);
        if (empty($_SESSION['user_id']))           respond(401, ['error' => 'Необходима авторизация']);

        $in   = getInput();
        $type = htmlspecialchars(strip_tags(trim($in['type'] ?? '')), ENT_QUOTES, 'UTF-8');

        if (!isset(SUBSCRIPTION_TYPES[$type])) respond(400, ['error' => 'Неверный тип абонемента']);

        $user = UserModel::findById($_SESSION['user_id']);
        if (!$user) respond(401, ['error' => 'Пользователь не найден. Войдите снова.']);

        try {
            $sub      = SubscriptionModel::create($_SESSION['user_id'], $type);
            $typeName = SUBSCRIPTION_TYPES[$type]['name'];
            respond(200, [
                'success'      => true,
                'subscription' => $sub,
                'message'      => "Вы успешно забронировали абонемент «{$typeName}»! В течение 24 часов с вами свяжется наш менеджер на почту {$user['email']}.",
            ]);
        } catch (Exception $e) {
            respond(400, ['error' => $e->getMessage()]);
        }

    case 'my':
        if (empty($_SESSION['user_id'])) respond(401, ['error' => 'Необходима авторизация']);
        respond(200, ['subscriptions' => SubscriptionModel::byUser($_SESSION['user_id'])]);

    default:
        respond(404, ['error' => 'Неизвестный action']);
}
