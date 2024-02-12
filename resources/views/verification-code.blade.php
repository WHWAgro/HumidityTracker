@extends('layouts.app')
@section('title','Verification Code')

@section('content')
	<div class="login-wrap">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-6 col-md-10">
					<div class="login-in">
						<div class="login-logo">
							<div class="logo">
								<img src="{{ asset('images/login-logo.png') }}">
							</div>
						</div>
						<div class="login-form">
							<form action="{{ url('verify_otp') }}" method="POST">
								@csrf
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
									@if($errors->has('error'))
										<span class="error">{{ $errors->first('error') }}</span>
									@endif	
									@if(session('success'))
										<span class="verifcation-code-sent">{{ session('success') }}</span>
									@endif	
									<input type="hidden" name="email" value="{{ Session::get('email') ?? old('email') }}">
									<input type="hidden" name="type" value="{{ Session::get('type') ?? old('type') }}">
									<input type="submit" class="login-btn" value="Verificar y Proceder">
								<div class="login-labl mt-3 mb-0">
									<p class="text-white">
										No has recibido el código? 
										
										<a href="{{ url('resend_code') }}" style="text-decoration: none !important; color: #1C1C1C; font-weight: 500;" onclick="event.preventDefault(); document.getElementById('resend_code').submit();">Reenviar</a>
									</p>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form action="{{ url('resend_code') }}" method="post" id="resend_code" style="display: none;">
		@csrf
		<input type="hidden" name="email" value="{{ Session::get('email') }}">
		<input type="hidden" name="type" value="{{ Session::get('type') }}">
	</form>
@endsection