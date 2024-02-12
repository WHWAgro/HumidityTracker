
<?php $__env->startSection('title','Login'); ?>

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
							<form action="<?php echo e(url('login')); ?>" method="POST">
								<?php echo csrf_field(); ?>
								<h1>Acceso</h1>
								<label for="" class="login-lbl">
									<img src="<?php echo e(asset('images/login-mail.png')); ?>" alt="">
									<input type="text" value="<?php echo e(old('email')); ?>" name="email" id="email" class="login-txt" placeholder="Email">
								</label>
								<?php if($errors->has('email')): ?>
									<span class="error"><?php echo e($errors->first('email')); ?></span>
								<?php endif; ?>
								<label class="login-lbl">
									<img src="<?php echo e(asset('images/login-pass.png')); ?>" alt="">
									<input type="password" maxlength="15" class="login-txt" name="password" id="password" placeholder="Contraseña">
									<div class="password-eye"><i class="fas fa-eye-slash" id="eye"></i></div>
								</label>
								<?php if($errors->has('password')): ?>
									<span class="error"><?php echo e($errors->first('password')); ?></span>
								<?php endif; ?>
								<div class="login-labl">
									<input type="checkbox" class="login-rmb" name="privacy_policy_and_terms_&_conditions">
									<p>Aceptas la <a href="<?php echo e(url('privacy-policy')); ?>"> Política de privacidad</a> y <a href="<?php echo e(url('terms-conditions')); ?>">Terminos y condiciones.</a></p>
								</div>
								<?php if($errors->has('privacy_policy_and_terms_&_conditions')): ?>
									<span class="error"><?php echo e($errors->first('privacy_policy_and_terms_&_conditions')); ?></span>
								<?php endif; ?>
								<?php if($errors->has('error')): ?>
									<span class="error"><?php echo e($errors->first('error')); ?></span>
								<?php endif; ?>
								<input type="submit" class="login-btn" value="Login">
								<div class="login-labl mt-3 mb-0">
									<p><a href="<?php echo e(url('forgot_password')); ?>" style="color: #1C1C1C; text-decoration: none !important; font-weight: 500;">Olvidaste la contraseña?</a></p>
								</div>
							</form>
						</div>
						<div class="login-info">
							<p>Tienes una invitación?<a href="<?php echo e(url('invitation')); ?>"> Aceptar ahora</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/login.blade.php ENDPATH**/ ?>