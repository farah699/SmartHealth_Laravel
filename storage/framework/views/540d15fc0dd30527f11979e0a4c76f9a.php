

<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $__env->make('partials.title-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

     <!-- Token CSRF pour les requêtes AJAX -->
    <!--- Le token CSRF est requis pour les requêtes AJAX que nous utilisons pour les likes/dislikes des commentaires. Sans ce token, Laravel bloquera les requêtes POST par sécurité. --->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php echo $__env->yieldContent('css'); ?>
</head>
<body>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.horizontal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="app-wrapper d-flex flex-column min-vh-100">
        <div class="container-fluid flex-grow-1">
            <?php echo $__env->make('partials.page-title', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->yieldContent('content'); ?>
            <?php echo $__env->make('partials.switcher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('partials.scroll-to-top', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </main>

    <?php echo $__env->make('partials.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldContent('js'); ?>
</body>
</html>
<?php /**PATH /var/www/resources/views/partials/layouts/master.blade.php ENDPATH**/ ?>