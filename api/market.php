<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Helpers.php';

use App\Core\MarketContext;
use App\Core\Auth;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}

// Check if logged-in customer is attempting to switch market
if (Auth::isCountryLocked()) {
    $active = MarketContext::getCountry();
    json_response([
        'success' => false,
        'locked' => true,
        'message' => 'Your regional market is fixed to your registered country (' . $active['name'] . ') to maintain accurate localized pricing and tax calculation.'
    ], 403);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$countryCode = $input['country_code'] ?? null;

if (!$countryCode) {
    json_response(['success' => false, 'message' => 'Country code is required.'], 400);
}

$switched = MarketContext::switchCountry($countryCode);

if ($switched) {
    $active = MarketContext::getCountry();
    json_response([
        'success' => true,
        'country_code' => $active['code'],
        'country_name' => $active['name'],
        'currency_code' => $active['currency_code'],
        'currency_symbol' => $active['currency_symbol']
    ]);
} else {
    json_response(['success' => false, 'message' => 'Selected country is currently unavailable.'], 404);
}
