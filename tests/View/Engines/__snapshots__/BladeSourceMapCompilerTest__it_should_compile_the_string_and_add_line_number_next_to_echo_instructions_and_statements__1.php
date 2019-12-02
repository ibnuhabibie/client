<?php return '|---LINE:3---|<?php $__env->startSection(\'content\'); ?>

    <div>
        |---LINE:6---|<?php echo $__env->make(\'header\', \\Illuminate\\Support\\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>

            <h2>|---LINE:8---|<?php echo e($title); ?></h2>

            <div>
                |---LINE:11---|<?php echo e($content); ?>

            </div>
        |---LINE:13---|<?php echo $__env->make(\'footer\', \\Illuminate\\Support\\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>
    </div>

|---LINE:16---|<?php $__env->stopSection(); ?>
<?php echo $__env->make(\'template\', \\Illuminate\\Support\\Arr::except(get_defined_vars(), [\'__data\', \'__path\']))->render(); ?>';
