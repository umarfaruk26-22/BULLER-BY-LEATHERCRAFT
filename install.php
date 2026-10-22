<?php
/**
 * BULLER LEATHER — AUTOMATED 1-CLICK WEB INSTALLER FOR HOSTINGER
 * Access via: https://bullerleather.com/install.php
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

$configFile = __DIR__ . '/app/config/config.php';
$schemaFile = __DIR__ . '/database/schema.sql';
$seedsFile = __DIR__ . '/database/seeds.sql';

$installed = false;
$error = null;
$logs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = trim($_POST['db_pass'] ?? '');

    if (empty($dbName) || empty($dbUser)) {
        $error = "Database Name aur Database User zaroori hain!";
    } else {
        try {
            // 1. Test PDO Connection
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            $logs[] = "✅ Database connection successful!";

            // 2. Run Schema SQL
            if (file_exists($schemaFile)) {
                $schemaSql = file_get_contents($schemaFile);
                $pdo->exec($schemaSql);
                $logs[] = "✅ All database tables created successfully!";
            } else {
                throw new Exception("schema.sql file nahi mili!");
            }

            // 3. Run Seeds SQL
            if (file_exists($seedsFile)) {
                $seedsSql = file_get_contents($seedsFile);
                $pdo->exec($seedsSql);
                $logs[] = "✅ Default products, categories, currencies & admin accounts created!";
            } else {
                throw new Exception("seeds.sql file nahi mili!");
            }

            // 4. Update config.php
            if (file_exists($configFile)) {
                $configContent = file_get_contents($configFile);
                $configContent = preg_replace("/define\('DB_HOST',\s*'.*?'\);/", "define('DB_HOST', " . var_export($dbHost, true) . ");", $configContent);
                $configContent = preg_replace("/define\('DB_PORT',\s*'.*?'\);/", "define('DB_PORT', " . var_export($dbPort, true) . ");", $configContent);
                $configContent = preg_replace("/define\('DB_NAME',\s*'.*?'\);/", "define('DB_NAME', " . var_export($dbName, true) . ");", $configContent);
                $configContent = preg_replace("/define\('DB_USER',\s*'.*?'\);/", "define('DB_USER', " . var_export($dbUser, true) . ");", $configContent);
                $configContent = preg_replace("/define\('DB_PASS',\s*'.*?'\);/", "define('DB_PASS', " . var_export($dbPass, true) . ");", $configContent);
                file_put_contents($configFile, $configContent);
                $logs[] = "✅ app/config/config.php updated with live database credentials!";
            }

            $installed = true;
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buller Leather — 1-Click Hostinger Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #110E0B;
            color: #E8E1D9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .install-card {
            background: #1E1813;
            border: 1px solid rgba(197, 168, 128, 0.3);
            border-radius: 14px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
            width: 100%;
            max-width: 580px;
            padding: 35px;
        }
        .brand-title {
            color: #C5A880;
            font-family: Georgia, serif;
            letter-spacing: 2px;
            font-size: 1.6rem;
            text-align: center;
            margin-bottom: 5px;
        }
        .form-control {
            background: #15110D;
            border: 1px solid rgba(197, 168, 128, 0.25);
            color: #FAF6F0;
        }
        .form-control:focus {
            background: #15110D;
            border-color: #C5A880;
            color: #FAF6F0;
            box-shadow: 0 0 0 0.25rem rgba(197, 168, 128, 0.15);
        }
        .btn-gold {
            background: #C5A880;
            color: #110E0B;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
        }
        .btn-gold:hover {
            background: #D9BE9B;
            color: #110E0B;
        }
        .log-box {
            background: #0D0A08;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 15px;
            font-family: monospace;
            font-size: 0.85rem;
            color: #A3E635;
        }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="brand-title">BULLER LEATHER</div>
        <p class="text-center text-muted small mb-4">Hostinger 1-Click Automated Setup for <code>bullerleather.com</code></p>

        <?php if ($installed): ?>
            <div class="alert alert-success bg-opacity-10 border border-success text-success p-3 rounded mb-3">
                <i class="bi bi-check-circle-fill me-2"></i> <strong>Installation Complete!</strong>
            </div>

            <div class="log-box mb-4">
                <?php foreach ($logs as $log): ?>
                    <div><?= htmlspecialchars($log) ?></div>
                <?php endforeach; ?>
            </div>

            <div class="card bg-dark border-secondary p-3 mb-3">
                <div class="fw-bold text-warning mb-2"><i class="bi bi-shield-lock-fill"></i> Default Super Admin Credentials:</div>
                <div class="small"><strong>Email:</strong> <code>superadmin@bullerleather.com</code></div>
                <div class="small"><strong>Password:</strong> <code>BullerAdmin@2026!</code></div>
            </div>

            <div class="d-grid gap-2">
                <a href="./" class="btn btn-gold"><i class="bi bi-shop me-2"></i> Visit Live Storefront</a>
                <a href="./login" class="btn btn-outline-warning"><i class="bi bi-person-circle me-2"></i> Log In to Admin Panel</a>
            </div>
            <p class="text-muted small text-center mt-3">Security Tip: You can now delete <code>install.php</code> from your File Manager.</p>

        <?php else: ?>

            <?php if ($error): ?>
                <div class="alert alert-danger bg-opacity-10 border border-danger text-danger p-3 rounded mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small text-warning">Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="localhost" required>
                    <small class="text-muted" style="font-size: 0.75rem;">Hostinger me 'localhost' hi rehta hai.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-warning">Hostinger Database Name</label>
                    <input type="text" name="db_name" class="form-control" placeholder="e.g. u123456789_buller" required>
                    <small class="text-muted" style="font-size: 0.75rem;">Hostinger MySQL Databases page se poora naam dalein (prefix ke saath).</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-warning">Hostinger Database Username</label>
                    <input type="text" name="db_user" class="form-control" placeholder="e.g. u123456789_buller_user" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small text-warning">Hostinger Database Password</label>
                    <input type="password" name="db_pass" class="form-control" placeholder="Database password" required>
                </div>

                <button type="submit" class="btn btn-gold">
                    <i class="bi bi-rocket-takeoff me-2"></i> Connect & Install Everything (1-Click)
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
