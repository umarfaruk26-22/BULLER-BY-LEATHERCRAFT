<?php
use App\Core\Auth;
use App\Core\MarketContext;

$pageTitle = 'My Account';
require __DIR__ . '/../layouts/header.php';
?>

<div class="bg-white border-bottom py-3">
    <div class="container small text-muted">
        <a href="<?= url('') ?>" class="text-muted text-decoration-none">Home</a>
        <span class="mx-2">&bull;</span>
        <span class="text-dark fw-semibold">Customer Account</span>
    </div>
</div>

<section class="py-5" style="background-color: var(--color-ivory);">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="p-4 bg-white border shadow-soft" style="border-color: var(--color-border);">
                    <div class="text-center pb-3 mb-3 border-bottom">
                        <div class="rounded-circle bg-dark text-gold d-inline-flex align-items-center justify-content-center mb-2 fs-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-person"></i>
                        </div>
                        <h6 class="font-serif mb-0" style="color: var(--color-espresso-deep);"><?= e($user['full_name']) ?></h6>
                        <span class="text-muted small d-block mb-2"><?= e($user['email']) ?></span>
                        <span class="badge text-uppercase d-inline-flex align-items-center gap-1" style="background-color: var(--color-espresso-deep); color: var(--color-gold); font-size: 0.68rem; letter-spacing: 0.05em; padding: 0.35rem 0.6rem;">
                            <?= country_flag($activeCountry['code'], $activeCountry['name'], 14) ?>
                            <?= e($activeCountry['name']) ?> Market
                            <i class="bi bi-shield-lock-fill ms-1 text-warning" title="Market locked to profile"></i>
                        </span>
                    </div>

                    <ul class="nav flex-column gap-2 small">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold text-dark bg-light px-3 py-2 rounded-1" href="<?= url('customer/dashboard') ?>">
                                <i class="bi bi-speedometer2 me-2"></i> Account Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted px-3 py-2" href="<?= url('customer/orders') ?>">
                                <i class="bi bi-bag-check me-2"></i> Order History & Invoices
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted px-3 py-2" href="<?= url('customer/addresses') ?>">
                                <i class="bi bi-geo-alt me-2"></i> Saved Addresses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted px-3 py-2" href="<?= url('wishlist') ?>">
                                <i class="bi bi-heart me-2"></i> Saved Wishlist
                            </a>
                        </li>
                        <li class="nav-item border-top pt-2">
                            <a class="nav-link text-danger px-3 py-2" href="<?= url('logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Account Content -->
            <div class="col-lg-9">
                <!-- Welcome Banner -->
                <div class="p-4 bg-white border shadow-soft mb-4" style="border-color: var(--color-border); border-left: 4px solid var(--color-gold) !important;">
                    <h3 class="font-serif mb-1" style="color: var(--color-espresso-deep);">Welcome, <?= e($user['full_name']) ?></h3>
                    <p class="text-muted small mb-0">From your account dashboard, you can view your previous orders, download invoices, track shipments, and manage your delivery addresses.</p>
                </div>

                <!-- Recent Orders -->
                <div class="p-4 bg-white border shadow-soft" style="border-color: var(--color-border);">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                        <h5 class="font-serif mb-0" style="color: var(--color-espresso-deep);">Recent Orders</h5>
                        <a href="<?= url('customer/orders') ?>" class="small text-muted">View All</a>
                    </div>

                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bag-dash fs-2 mb-2 d-block"></i>
                            <span>You haven't placed an order yet.</span>
                            <div class="mt-3">
                                <a href="<?= url('shop') ?>" class="btn btn-buller-primary btn-sm">Explore All Products</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th>Order Ref</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $ord): ?>
                                        <tr>
                                            <td><strong>#<?= e($ord['order_number']) ?></strong></td>
                                            <td class="small text-muted"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                                            <td class="small"><?= (int)$ord['item_count'] ?> Pieces</td>
                                            <td class="fw-bold"><?= e($ord['currency_symbol']) . number_format($ord['grand_total'], 2) ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($ord['order_status'] === 'delivered') ? 'success' : 'warning text-dark' ?> text-uppercase small">
                                                    <?= e($ord['order_status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= url('order/confirmation?id=' . $ord['id']) ?>" class="btn btn-outline-dark btn-sm">
                                                    Invoice
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
