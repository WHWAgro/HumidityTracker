@extends('layouts.app')
@section('title','Login')

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
							<form action="{{ url('login') }}" method="POST">
								@csrf
								<h1>Acceso</h1>
								<label for="" class="login-lbl">
									<img src="{{ asset('images/login-mail.png') }}" alt="">
									<input type="text" value="{{ old('email') }}" name="email" id="email" class="login-txt" placeholder="Email">
								</label>
								@if($errors->has('email'))
									<span class="error">{{ $errors->first('email') }}</span>
								@endif
								<label class="login-lbl">
									<img src="{{ asset('images/login-pass.png') }}" alt="">
									<input type="password" maxlength="15" class="login-txt" name="password" id="password" placeholder="Contraseña">
									<div class="password-eye"><i class="fas fa-eye-slash" id="eye"></i></div>
								</label>
								@if($errors->has('password'))
									<span class="error">{{ $errors->first('password') }}</span>
								@endif
								<div class="login-labl">
									<input type="checkbox" class="login-rmb" name="privacy_policy_and_terms_&_conditions">
									<p>Aceptas la <a href="{{ url('privacy-policy') }}"> Política de privacidad</a> y <a href="{{ url('terms-conditions') }}">Terminos y condiciones.</a></p>
								</div>
								@if($errors->has('privacy_policy_and_terms_&_conditions'))
									<span class="error">{{ $errors->first('privacy_policy_and_terms_&_conditions') }}</span>
								@endif
								@if($errors->has('error'))
									<span class="error">{{ $errors->first('error') }}</span>
								@endif
								<input type="submit" class="login-btn" value="Login">
								<div class="login-labl mt-3 mb-0">
									<p><a href="{{ url('forgot_password') }}" style="color: #1C1C1C; text-decoration: none !important; font-weight: 500;">Olvidaste la contraseña?</a></p>
								</div>
							</form>
						</div>
						<div class="login-info">
							<p>Tienes una invitación?<a href="{{ url('invitation') }}"> Aceptar ahora</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection