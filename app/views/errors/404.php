<?php
$pageTitle = '404 - Page Not Located';
require __DIR__ . '/../layouts/header.php';
?>

<section class="py-5 text-center" style="background-color: var(--color-ivory); min-height: 65vh; display: flex; align-items: center;">
    <div class="container">
        <span class="text-gold font-serif fs-1 fw-bold d-block mb-2">404</span>
        <h1 class="font-serif mb-3" style="color: var(--color-espresso-deep); font-size: 2.2rem;">The Requested Curation Cannot Be Located</h1>
        <p class="text-muted small mb-4 mx-auto" style="max-width: 500px;">
            The archive or product catalogue you are attempting to inspect may have been acquired or relocated.
        </p>
        <a href="<?= url('') ?>" class="btn btn-buller-primary btn-sm me-2">Return To Storefront</a>
        <a href="<?= url('shop') ?>" class="btn btn-buller-outline btn-sm">Explore Master Curations</a>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
