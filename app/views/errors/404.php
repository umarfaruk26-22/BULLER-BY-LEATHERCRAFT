<?php
$pageTitle = 'Page Not Found';
require __DIR__ . '/../layouts/header.php';
?>

<section class="py-5 text-center" style="background-color: var(--color-ivory); min-height: 65vh; display: flex; align-items: center;">
    <div class="container">
        <span class="text-gold font-serif fs-1 fw-bold d-block mb-2">404</span>
        <h1 class="font-serif mb-3" style="color: var(--color-espresso-deep); font-size: 2.2rem;">The Requested Page Cannot Be Found</h1>
        <p class="text-muted small mb-4 mx-auto" style="max-width: 500px;">
            The page or product you are looking for may have been moved or is no longer available.
        </p>
        <a href="<?= url('') ?>" class="btn btn-buller-primary btn-sm me-2">Return to Home</a>
        <a href="<?= url('shop') ?>" class="btn btn-buller-outline btn-sm">Explore All Products</a>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
