
<?php $__env->startSection('title','Account Created Successfully'); ?>

<?php $__env->startSection('content'); ?>
	<div class="login-succ-wrap">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-4 col-md-10 wow bounceInDown" data-wow-duration="1000ms" data-wow-delay="100ms">
					<div class="login-succ">
						<img src="<?php echo e(asset('images/create-success.png')); ?>" alt="">
						<h1>Éxito!</h1>
						<p>Tu cuenta <br> ha sido creada exitósamente.</p>
						<!-- <a href="forecast-data.html">X</a> -->
						<a href="<?php echo e(url('track_humidity')); ?>" onclick="event.preventDefault(); document.getElementById('track_humidity').submit();">X</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form action="<?php echo e(url('track_humidity')); ?>" method="post" id="track_humidity" style="display: none;">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="email" value="<?php echo e(Session::get('email') ?? old('email')); ?>">
	</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/success.blade.php ENDPATH**/ ?>