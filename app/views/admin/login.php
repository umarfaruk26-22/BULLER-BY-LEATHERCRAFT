<?php
use App\Core\Auth;
use App\Core\MarketContext;

$pageTitle = 'Executive Administration | Sign In';
$activeCountry = MarketContext::getCountry();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle) ?> — Buller Leather</title>
    
    <link rel="icon" type="image/x-icon" href="<?= asset('favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Playfair+Display:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --color-espresso-deep: #0e0804;
            --color-espresso: #160d07;
            --color-espresso-card: #1c1109;
            --color-gold: #c5a880;
            --color-gold-bright: #dfc298;
            --color-gold-dark: #9e7d4e;
            --color-border: rgba(197, 168, 128, 0.22);
            --font-brand: 'Cinzel', serif;
            --font-heading: 'Playfair Display', Georgia, serif;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: radial-gradient(ellipse at 50% 20%, #20130a 0%, #0d0804 70%, #050302 100%);
            min-height: 100vh;
            color: #e5ded4;
            font-family: var(--font-sans);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(rgba(197, 168, 128, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            opacity: 0.6;
        }

        .admin-login-card {
            width: 100%;
            max-width: 440px;
            background: var(--color-espresso-card);
            border: 1px solid var(--color-border);
            border-top: 3px solid var(--color-gold);
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6), 0 0 40px rgba(197, 168, 128, 0.06);
            padding: 2.5rem 2.25rem;
            position: relative;
            z-index: 2;
        }

        .brand-logo-wrap {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .brand-logo-icon {
            height: 52px;
            width: auto;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4));
        }

        .brand-title {
            font-family: var(--font-brand);
            font-size: 1.45rem;
            letter-spacing: 0.22em;
            color: var(--color-gold-bright);
            font-weight: 700;
            margin: 0;
            line-height: 1.1;
        }

        .brand-sub {
            font-size: 0.64rem;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--color-gold);
            opacity: 0.85;
            margin-top: 2px;
        }

        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.68rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 600;
            background: rgba(197, 168, 128, 0.12);
            color: var(--color-gold-bright);
            border: 1px solid rgba(197, 168, 128, 0.3);
            margin-top: 0.75rem;
        }

        .form-label {
            font-size: 0.76rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #c5bbae;
            margin-bottom: 0.4rem;
        }

        .form-control-admin {
            background-color: rgba(10, 6, 3, 0.65);
            border: 1px solid rgba(197, 168, 128, 0.3);
            color: #ffffff;
            font-size: 0.92rem;
            padding: 0.75rem 0.95rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .form-control-admin:focus {
            background-color: rgba(15, 9, 5, 0.9);
            border-color: var(--color-gold-bright);
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(197, 168, 128, 0.2);
            outline: none;
        }

        .form-control-admin::placeholder {
            color: #6d645a;
        }

        .input-group-text-admin {
            background-color: rgba(10, 6, 3, 0.65);
            border: 1px solid rgba(197, 168, 128, 0.3);
            border-left: none;
            color: #a89f93;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
        }

        .input-group-text-admin:hover {
            color: var(--color-gold-bright);
        }

        .btn-admin-login {
            background: linear-gradient(135deg, #d8bc93 0%, #ba986a 50%, #9e7d4e 100%);
            color: #120b06;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.85rem;
            border: none;
            border-radius: 4px;
            width: 100%;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
        }

        .btn-admin-login:hover {
            background: linear-gradient(135deg, #e4cba7 0%, #c9a777 50%, #af8c5a 100%);
            color: #0b0704;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(197, 168, 128, 0.3);
        }

        .btn-admin-login:active {
            transform: translateY(0);
        }

        .security-footer {
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(197, 168, 128, 0.15);
            text-align: center;
            font-size: 0.72rem;
            color: #8f8578;
            line-height: 1.5;
        }

        .back-store-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--color-gold);
            text-decoration: none;
            font-size: 0.78rem;
            margin-top: 1rem;
            transition: color 0.2s ease;
        }

        .back-store-link:hover {
            color: var(--color-gold-bright);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="admin-login-card">
        <div class="brand-logo-wrap">
            <a href="<?= url('') ?>" title="Buller Leather Storefront" class="text-decoration-none">
                <img src="<?= asset('assets/images/buller-logo-gold.svg') ?>" alt="Buller Leather" class="brand-logo-icon">
                <h1 class="brand-title">BULLER</h1>
                <div class="brand-sub">By leathercraft</div>
            </a>
            <div>
                <span class="portal-badge">
                    <i class="bi bi-shield-lock-fill text-warning"></i> Executive Administration
                </span>
            </div>
        </div>

        <?php if (has_flash('admin_login_error')): ?>
            <div class="alert alert-danger small py-2 px-3 mb-3 d-flex align-items-center gap-2" style="background: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.4); color: #ff99a4; border-radius: 4px;">
                <i class="bi bi-exclamation-triangle-fill fs-6 flex-shrink-0"></i>
                <div style="font-size: 0.8rem;"><?= flash('admin_login_error') ?></div>
            </div>
        <?php endif; ?>

        <?php if (has_flash('admin_login_success')): ?>
            <div class="alert alert-success small py-2 px-3 mb-3 d-flex align-items-center gap-2" style="background: rgba(25, 135, 84, 0.15); border: 1px solid rgba(25, 135, 84, 0.4); color: #87e8a9; border-radius: 4px;">
                <i class="bi bi-check-circle-fill fs-6 flex-shrink-0"></i>
                <div style="font-size: 0.8rem;"><?= flash('admin_login_success') ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= url('admin/login') ?>" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

            <div class="mb-3">
                <label class="form-label">Administrator Email</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control form-control-admin" required placeholder="admin@bullerleather.com" autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Security Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="adminPasswordInput" class="form-control form-control-admin" style="border-right: none;" required placeholder="••••••••">
                    <span class="input-group-text input-group-text-admin" id="togglePasswordBtn" title="Show / Hide Password">
                        <i class="bi bi-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-admin-login">
                <i class="bi bi-shield-check me-1"></i> Authenticate & Enter Portal
            </button>
        </form>

        <div class="security-footer">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <i class="bi bi-lock-fill text-warning"></i>
                <span>256-Bit Encrypted Session &bull; RBAC Protected</span>
            </div>
            <div>All administrative actions are cryptographically audited and IP-logged for enterprise compliance.</div>
            <div>
                <a href="<?= url('') ?>" class="back-store-link">
                    <i class="bi bi-arrow-left"></i> Return to Storefront
                </a>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passInput = document.getElementById('adminPasswordInput');
        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passInput.type === 'password';
                passInput.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            });
        }
    </script>
</body>
</html>
