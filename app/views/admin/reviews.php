<?php
use App\Core\Auth;
$isSuper = Auth::isSuperAdmin();
$pageTitle = 'Patron Product Reviews & Moderation';
$currentStatusFilter = $_GET['status'] ?? 'all';
require __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container-fluid p-0">
    <!-- Super Admin Scope Indicator if in regional context -->
    <?php if ($isSuper && isset($selectedCountryId)): ?>
        <div class="alert alert-dark border border-warning d-flex justify-content-between align-items-center py-2 px-3 mb-4 shadow-sm" style="background: #1a120b;">
            <div class="text-white small">
                <i class="bi bi-shield-check text-warning me-2 fs-5 align-middle"></i>
                <strong class="text-warning">Global Super Admin Mode:</strong> Moderating Client Reviews & Testimonials
            </div>
            <a href="<?= url('superadmin/dashboard') ?>" class="btn btn-outline-warning btn-sm py-1">
                <i class="bi bi-arrow-left me-1"></i> Back to Global Dashboard
            </a>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 font-serif mb-1 d-flex align-items-center gap-2" style="color: var(--color-espresso-deep);">
                <i class="bi bi-star-fill text-warning"></i> Patron Reviews & Testimonial Moderation
            </h1>
            <p class="text-muted small mb-0">Review, approve, or reject customer feedback and satisfaction ratings across regional boutiques.</p>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <?php if ($isSuper && !empty($allCountries)): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel me-1"></i> Market: <?= !empty($filterCountryName) ? e($filterCountryName) : 'All Markets' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 small">
                        <li><a class="dropdown-item <?= empty($_GET['country_id']) ? 'active fw-bold' : '' ?>" href="<?= url('admin/reviews?status=' . urlencode($currentStatusFilter)) ?>">All Markets</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($allCountries as $c): ?>
                            <li>
                                <a class="dropdown-item <?= (isset($_GET['country_id']) && $_GET['country_id'] == $c['id']) ? 'active fw-bold' : '' ?>" href="<?= url('admin/reviews?status=' . urlencode($currentStatusFilter) . '&country_id=' . $c['id']) ?>">
                                    <?= country_flag($c['code'], $c['name'], 20) ?> <?= e($c['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Status KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Pending Approval</div>
                <div class="kpi-value text-warning"><?= (int)($statusCounts['pending'] ?? 0) ?></div>
                <small class="text-muted">Awaiting Moderator Action</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Approved Live</div>
                <div class="kpi-value text-success"><?= (int)($statusCounts['approved'] ?? 0) ?></div>
                <small class="text-muted">Visible on Storefront</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Rejected</div>
                <div class="kpi-value text-danger"><?= (int)($statusCounts['rejected'] ?? 0) ?></div>
                <small class="text-muted">Filtered Out</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card p-3 bg-white border">
                <div class="kpi-label">Total Testimonials</div>
                <div class="kpi-value" style="color: var(--color-espresso-deep);"><?= (int)($statusCounts['total'] ?? 0) ?></div>
                <small class="text-muted">Average: ★ <?= number_format($statusCounts['avg_rating'] ?? 5.0, 1) ?></small>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="d-flex gap-2 mb-3 border-bottom pb-2">
        <?php 
            $cParam = !empty($_GET['country_id']) ? '&country_id=' . (int)$_GET['country_id'] : ''; 
        ?>
        <a href="<?= url('admin/reviews?status=all' . $cParam) ?>" class="btn btn-sm <?= ($currentStatusFilter === 'all') ? 'btn-dark' : 'btn-outline-secondary' ?>">
            All (<?= (int)($statusCounts['total'] ?? 0) ?>)
        </a>
        <a href="<?= url('admin/reviews?status=pending' . $cParam) ?>" class="btn btn-sm <?= ($currentStatusFilter === 'pending') ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' ?>">
            <i class="bi bi-clock-history me-1"></i> Pending (<?= (int)($statusCounts['pending'] ?? 0) ?>)
        </a>
        <a href="<?= url('admin/reviews?status=approved' . $cParam) ?>" class="btn btn-sm <?= ($currentStatusFilter === 'approved') ? 'btn-success text-white fw-bold' : 'btn-outline-secondary' ?>">
            <i class="bi bi-check-circle me-1"></i> Approved (<?= (int)($statusCounts['approved'] ?? 0) ?>)
        </a>
        <a href="<?= url('admin/reviews?status=rejected' . $cParam) ?>" class="btn btn-sm <?= ($currentStatusFilter === 'rejected') ? 'btn-danger text-white fw-bold' : 'btn-outline-secondary' ?>">
            <i class="bi bi-x-circle me-1"></i> Rejected (<?= (int)($statusCounts['rejected'] ?? 0) ?>)
        </a>
    </div>

    <!-- Reviews Table Card -->
    <div class="bg-white border p-4 shadow-soft" style="border-color: var(--admin-card-border);">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-square-quote fs-1 mb-2 d-block text-warning"></i>
                <h5>No Reviews Under This Criteria</h5>
                <p class="small">There are no customer testimonials matching the selected filter.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th style="width: 25%;">Product</th>
                            <th style="width: 15%;">Patron</th>
                            <th style="width: 12%;">Rating</th>
                            <th style="width: 28%;">Testimonial Content</th>
                            <th style="width: 10%;">Status</th>
                            <th class="text-end" style="width: 10%;">Moderation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $rev): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($rev['product_image'])): ?>
                                            <img src="<?= asset($rev['product_image']) ?>" alt="" style="width: 44px; height: 48px; object-fit: cover;" class="border rounded-1">
                                        <?php endif; ?>
                                        <div>
                                            <strong class="d-block small text-dark"><?= e($rev['product_title']) ?></strong>
                                            <small class="text-muted">SKU: <?= e($rev['sku'] ?? 'N/A') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><strong><?= e($rev['user_name'] ?? 'Patron') ?></strong></div>
                                    <div class="small text-muted d-flex align-items-center gap-1 mt-1">
                                        <?= country_flag($rev['country_code'] ?? '', $rev['country_name'] ?? '', 16) ?>
                                        <span><?= e($rev['country_name'] ?? 'Global') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-warning small text-nowrap">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= ($i <= $rev['rating']) ? '-fill' : '' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted d-block mt-1"><?= date('M d, Y', strtotime($rev['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong class="d-block small text-dark mb-1">&ldquo;<?= e($rev['review_title']) ?>&rdquo;</strong>
                                    <p class="small text-muted mb-0" style="line-height: 1.4;"><?= nl2br(e($rev['review_body'])) ?></p>
                                </td>
                                <td>
                                    <?php if ($rev['status'] === 'approved'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success">Approved</span>
                                    <?php elseif ($rev['status'] === 'pending'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <?php if ($rev['status'] !== 'approved'): ?>
                                            <!-- Approve Form -->
                                            <form action="<?= url('admin/reviews/update-status') ?>" method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                                <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-outline-success btn-sm py-1 px-2" title="Approve Testimonial">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($rev['status'] !== 'rejected'): ?>
                                            <!-- Reject Form -->
                                            <form action="<?= url('admin/reviews/update-status') ?>" method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                                <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-outline-warning btn-sm py-1 px-2" title="Reject Testimonial">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Delete Form -->
                                        <form action="<?= url('admin/reviews/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this testimonial?');">
                                            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                            <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Permanently Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
