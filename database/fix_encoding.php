<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

$db = Database::getConnection();
$db->exec("SET NAMES utf8mb4");

$updates = [
    [
        'code' => 'IN',
        'currency_symbol' => '₹',
        'currency_name' => 'Indian Rupee',
        'flag_icon' => 'in'
    ],
    [
        'code' => 'US',
        'currency_symbol' => '$',
        'currency_name' => 'US Dollar',
        'flag_icon' => 'us'
    ],
    [
        'code' => 'GB',
        'currency_symbol' => '£',
        'currency_name' => 'British Pound',
        'flag_icon' => 'gb'
    ],
    [
        'code' => 'AE',
        'currency_symbol' => 'AED',
        'currency_name' => 'UAE Dirham',
        'flag_icon' => 'ae'
    ]
];

$stmt = $db->prepare("UPDATE countries SET currency_symbol = ?, currency_name = ?, flag_icon = ? WHERE code = ?");

foreach ($updates as $u) {
    $stmt->execute([$u['currency_symbol'], $u['currency_name'], $u['flag_icon'], $u['code']]);
    echo "Updated {$u['code']}: Symbol={$u['currency_symbol']} Flag={$u['flag_icon']}" . PHP_EOL;
}

echo "Database encoding successfully fixed!" . PHP_EOL;
