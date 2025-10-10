<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8" />
<title><?php echo $__env->yieldContent('title', ' | FabKin Admin & Dashboards Template'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta content="Admin & Dashboards Template" name="description" />
<meta content="Pixeleyez" name="author" />

<!-- layout setup -->
<script type="module" src="assets/js/layout-setup.js"></script>

<!-- App favicon -->
<link rel="shortcut icon" href="assets/images/k_favicon_32x.png">

<?php echo $__env->yieldContent('css'); ?>
<?php echo $__env->make('partials.head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> 

<body>

<?php echo $__env->yieldContent('content'); ?>

<?php echo $__env->make('partials.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>  

<?php echo $__env->yieldContent('js'); ?>

</body>

</html><?php /**PATH /var/www/resources/views/partials/layouts/master_auth.blade.php ENDPATH**/ ?>