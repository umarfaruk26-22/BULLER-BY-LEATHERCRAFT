<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden | Access Restricted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #120b06;
            color: #fdfbf7;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .error-card {
            background: #1f140c;
            border: 1px solid rgba(197, 168, 128, 0.3);
            border-top: 4px solid #c5a880;
            max-width: 550px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 16px 45px rgba(0,0,0,0.5);
        }
        .btn-gold {
            background: #c5a880;
            color: #120b06;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-radius: 0;
            padding: 0.75rem 1.8rem;
        }
        .btn-gold:hover {
            background: #dfc298;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="bi bi-shield-x text-warning fs-1 mb-3 d-block"></i>
        <h1 class="h3 fw-bold mb-2" style="font-family: Georgia, serif; color: #c5a880;">403 — Unauthorized Regional Access</h1>
        <p class="text-secondary small mb-4">
            Security Violation: Your administrative credentials do not possess authorized clearance for this national market. This access attempt has been logged to the central audit security ledger.
        </p>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="btn btn-gold btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Return To Authorized Dashboard
        </a>
    </div>
</body>
</html>
