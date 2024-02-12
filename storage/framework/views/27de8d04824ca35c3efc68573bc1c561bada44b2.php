
<?php $__env->startSection('title',"Accept Invitation"); ?>

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
							<form action="<?php echo e(url('invitation')); ?>" method="POST">
								<?php echo csrf_field(); ?>
								<h1>ACeptar invitación</h1>
								<label for="" class="login-lbl">
									<img src="<?php echo e(asset('images/login-mail.png')); ?>" alt="">
									<input type="text" name="email" class="login-txt" value="<?php echo e(old('email')); ?>" placeholder="Email">
								</label>
								<?php if($errors->has('email')): ?>
									<span class="error"><?php echo e($errors->first('email')); ?></span>
								<?php endif; ?>
								<?php if($errors->has('error')): ?>
									<span class="error"><?php echo e($errors->first('error')); ?></span>
								<?php endif; ?>
								<label class="login-lbl">
									<img src="<?php echo e(asset('images/login-pass.png')); ?>" alt="">
									<input type="password" name="password" class="login-txt" id="password" placeholder="Contraseña Temporal">
									<div class="password-eye"><i class="fas fa-eye-slash" id="eye"></i></div>
								</label>
								<?php if($errors->has('password')): ?>
									<span class="error"><?php echo e($errors->first('password')); ?></span>
								<?php endif; ?>
								<input type="submit" class="login-btn" value="Confirmar">
							</form>
						</div>
						<div class="login-info">
							<p>Problemas? <a href="mailto:contactwhw@yopmail.com?subject=Contact%20Support" ><b> Contacto a Soporte</b></a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/accept-invitation.blade.php ENDPATH**/ ?>