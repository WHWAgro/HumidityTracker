
<?php $__env->startSection('title','Verification Code'); ?>

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
							<form action="<?php echo e(url('verify_otp')); ?>" method="POST">
								<?php echo csrf_field(); ?>
								<h1>Código de veficación</h1>
								<p class="login-form-p">Ingresa el código de verificación enviado a tu Email </p>
								<div class="ver-code">
									<input type="number" name="otp[]" class="donor-register-txt inputs" maxlength="1" onkeypress="if(this.value.length==1) return false;">
									<input type="number" name="otp[]" class="donor-register-txt inputs" maxlength="1" onkeypress="if(this.value.length==1) return false;">
									<input type="number" name="otp[]" class="donor-register-txt inputs" maxlength="1" onkeypress="if(this.value.length==1) return false;">
									<input type="number" name="otp[]" class="donor-register-txt inputs" maxlength="1" onkeypress="if(this.value.length==1) return false;">
									<input type="number" name="otp[]" class="donor-register-txt inputs" maxlength="1" onkeypress="if(this.value.length==1) return false;">
									<input type="number" name="otp[]" class="donor-register-txt inputs" maxlength="1" onkeypress="if(this.value.length==1) return false;">
								</div>
									<?php if($errors->has('error')): ?>
										<span class="error"><?php echo e($errors->first('error')); ?></span>
									<?php endif; ?>	
									<?php if(session('success')): ?>
										<span class="verifcation-code-sent"><?php echo e(session('success')); ?></span>
									<?php endif; ?>	
									<input type="hidden" name="email" value="<?php echo e(Session::get('email') ?? old('email')); ?>">
									<input type="hidden" name="type" value="<?php echo e(Session::get('type') ?? old('type')); ?>">
									<input type="submit" class="login-btn" value="Verificar y Proceder">
								<div class="login-labl mt-3 mb-0">
									<p class="text-white">
										No has recibido el código? 
										
										<a href="<?php echo e(url('resend_code')); ?>" style="text-decoration: none !important; color: #1C1C1C; font-weight: 500;" onclick="event.preventDefault(); document.getElementById('resend_code').submit();">Reenviar</a>
									</p>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form action="<?php echo e(url('resend_code')); ?>" method="post" id="resend_code" style="display: none;">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="email" value="<?php echo e(Session::get('email')); ?>">
		<input type="hidden" name="type" value="<?php echo e(Session::get('type')); ?>">
	</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/verification-code.blade.php ENDPATH**/ ?>