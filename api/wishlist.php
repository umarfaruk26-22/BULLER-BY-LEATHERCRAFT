<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Helpers.php';

use App\Core\Wishlist;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$productId = (int)($input['product_id'] ?? 0);

if ($productId <= 0) {
    json_response(['success' => false, 'message' => 'Invalid product identifier.'], 400);
}

$result = Wishlist::toggle($productId);
json_response($result);
