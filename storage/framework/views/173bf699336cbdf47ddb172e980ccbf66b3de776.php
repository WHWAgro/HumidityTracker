
<?php $__env->startSection('title','Forgot Password'); ?>

<?php $__env->startSection('content'); ?>
	<div class="login-wrap">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-6 col-md-10">
					<div class="login-in">
						<div class="login-logo">
							<div class="logo">
								<img src="<?php echo e(asset('images/login-logo.png')); ?>">
							</div>
						</div>
						<div class="login-form">
							<form action="<?php echo e(url('forgot_password')); ?>" method="POST">
								<?php echo csrf_field(); ?>
								<h1>Olvidáste tu contraseña</h1>
								<label for="" class="login-lbl">
									<img src="<?php echo e(asset('images/login-mail.png')); ?>" alt="">
									<input type="text" name="email" value="<?php echo e(old('email')); ?>" class="login-txt" placeholder="Ingresa tu Email de registro">
								</label>
								<?php if($errors->has('email')): ?>
									<span class="error"><?php echo e($errors->first('email') == 'The email must be a valid email address.' ? "Please enter valid email" : $errors->first('email')); ?></span>
								<?php endif; ?>
								<?php if($errors->has('error')): ?>
									<span class="error"><?php echo e($errors->first('error')); ?></span>
								<?php endif; ?>
								<input type="submit" class="login-btn" value="Enviar Verificación">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/forgot-password.blade.php ENDPATH**/ ?>