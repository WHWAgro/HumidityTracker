<!-- SIDEBAR -->
<section id="sidebar">
    <a href="javascript:void(0);"class="brand">
        <!-- <i class='bx bxs-smile'></i> -->
        <img src="<?php echo e(asset('images/logo.png')); ?>" alt="">
        <span class="text"><img src="<?php echo e(asset('images/logo-txt.png')); ?>" alt=""></span>
    </a>
    <ul class="side-menu">
        <h1>Módulos</h1>
        <li class="<?php echo e(Request::segment(1)== 'forecast' ? 'active' : ''); ?>">
            <a href="<?php echo e(url('forecast')); ?>">
                <img src="<?php echo e(asset('images/menu-icons/forecast-data.svg')); ?>"alt="">
                <span class="text">Proyección Humedad</span>
            </a>
        </li>
        <li class="<?php echo e(Request::segment(1)== 'forecast_data' ? 'active' : ''); ?>">
            <a href="<?php echo e(url('forecast_data')); ?>">
                <img src="<?php echo e(asset('images/menu-icons/check-data.svg')); ?>"alt="">
                <span class="text">Revisar Mediciones</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(url('logout')); ?>" onclick="event.preventDefault(); document.getElementById('form-logout').submit();">
                <img src="<?php echo e(asset('images/menu-icons/logout.svg')); ?>"alt="">
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>
<!-- SIDEBAR -->
<form id="form-logout" action="<?php echo e(url('logout')); ?>" method="POST" style="display: none;">
    <?php echo e(csrf_field()); ?>

</form><?php /**PATH /Users/cristobal/Repos/HumidityTracker/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>