<?php
// api/config.php — единственная точка конфигурации

define('DATA_PATH',          __DIR__ . '/../data/');
define('USERS_FILE',         DATA_PATH . 'users.json');
define('SUBSCRIPTIONS_FILE', DATA_PATH . 'subscriptions.json');

define('SUBSCRIPTION_TYPES', [
    'month' => [
        'name'          => 'Месяц',
        'price'         => 10000,
        'duration'      => '1 месяц',
        'duration_days' => 30,
    ],
    'halfyear' => [
        'name'          => 'Полгода',
        'price'         => 60000,
        'duration'      => '6 месяцев',
        'duration_days' => 180,
    ],
    'year' => [
        'name'          => 'Год',
        'price'         => 80000,
        'duration'      => '12 месяцев',
        'duration_days' => 365,
    ],
]);

// --- Сессия ---------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime',  (string)(3600 * 24 * 7));
    ini_set('session.cookie_lifetime', (string)(3600 * 24 * 7));
    session_start();
}

// --- Заголовки ответа -----------------------------------------------
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// --- Инициализация файлов данных ------------------------------------
if (!is_dir(DATA_PATH)) {
    mkdir(DATA_PATH, 0755, true);
}
foreach ([USERS_FILE, SUBSCRIPTIONS_FILE] as $f) {
    if (!file_exists($f)) {
        file_put_contents($f, '[]', LOCK_EX);
    }
}

// --- Вспомогательная функция ответа --------------------------------
function respond(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// --- Читаем JSON-тело запроса --------------------------------------
function getInput(): array {
    static $parsed = null;
    if ($parsed === null) {
        $raw    = file_get_contents('php://input');
        $parsed = json_decode($raw ?: '{}', true) ?? [];
    }
    return $parsed;
}
