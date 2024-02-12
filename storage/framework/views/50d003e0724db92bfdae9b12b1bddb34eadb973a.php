
<?php $__env->startSection('title', "Create Password"); ?>

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
							<form action="<?php echo e(url('create_password')); ?>" method="POST">
								<?php echo csrf_field(); ?>
								<h1>Crear Contraseña</h1>
								<label class="login-lbl">
									<img src="<?php echo e(asset('images/login-pass.png')); ?>" alt="">
									<input type="password" maxlength="15" name="password" class="login-txt" placeholder="Contraseña">
								</label>
								<?php if($errors->has('password')): ?>
									<span class="error"><?php echo e($errors->first('password')); ?></span>
								<?php endif; ?>
					
								<label class="login-lbl">
									<img src="<?php echo e(asset('images/login-pass.png')); ?>" alt="">
									<input type="password"  maxlength="15" name="confirm_password" class="login-txt" placeholder="Confirmar Contraseña">
								</label>
								<?php if($errors->has('confirm_password')): ?>
									<span class="error"><?php echo e($errors->first('confirm_password')); ?></span>
								<?php endif; ?>
								<input type="hidden" name="email" value="<?php echo e(Session::get('email') ?? old('email')); ?>">
								<input type="hidden" name="type" value="<?php echo e(Session::get('type') ?? old('type')); ?>">
								<input type="submit" class="login-btn" value="Enviar">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/create-password.blade.php ENDPATH**/ ?>