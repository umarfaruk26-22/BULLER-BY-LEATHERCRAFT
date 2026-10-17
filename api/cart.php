<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Helpers.php';

use App\Core\Cart;
use App\Core\MarketContext;

header('Content-Type: application/json; charset=utf-8');

MarketContext::init();

$action = $_GET['action'] ?? 'get';
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'add':
        $productId = (int)($input['product_id'] ?? 0);
        $qty = (int)($input['quantity'] ?? 1);
        if ($productId <= 0) {
            json_response(['success' => false, 'message' => 'Invalid product identifier.'], 400);
        }
        $result = Cart::add($productId, $qty);
        json_response($result, $result['success'] ? 200 : 400);
        break;

    case 'update':
        $productId = (int)($input['product_id'] ?? 0);
        $qty = (int)($input['quantity'] ?? 0);
        $result = Cart::update($productId, $qty);
        json_response($result);
        break;

    case 'remove':
        $productId = (int)($input['product_id'] ?? 0);
        Cart::remove($productId);
        json_response(['success' => true, 'cart_count' => Cart::getCount()]);
        break;

    case 'clear':
        Cart::clear();
        json_response(['success' => true, 'cart_count' => 0]);
        break;

    case 'get':
    default:
        $summary = Cart::getSummary();
        json_response($summary);
        break;
}
