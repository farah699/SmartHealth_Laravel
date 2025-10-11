<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartHealth - <?php echo $__env->yieldContent('title', 'Authentication'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo e(asset('assets/css/app.min.css')); ?>" rel="stylesheet">
    <!-- Icons -->
    <link href="<?php echo e(asset('assets/css/icons.min.css')); ?>" rel="stylesheet">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-wrapper">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\ferie\OneDrive\Bureau\LaravelDevops\SmartHealth_Laravel-main\resources\views/partials/layouts/master_auth.blade.php ENDPATH**/ ?>