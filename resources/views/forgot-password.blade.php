@extends('layouts.app')
@section('title','Forgot Password')

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
							<form action="{{ url('forgot_password') }}" method="POST">
								@csrf
								<h1>Olvidáste tu contraseña</h1>
								<label for="" class="login-lbl">
									<img src="{{ asset('images/login-mail.png') }}" alt="">
									<input type="text" name="email" value="{{ old('email') }}" class="login-txt" placeholder="Ingresa tu Email de registro">
								</label>
								@if($errors->has('email'))
									<span class="error">{{ $errors->first('email') == 'The email must be a valid email address.' ? "Please enter valid email" : $errors->first('email') }}</span>
								@endif
								@if($errors->has('error'))
									<span class="error">{{ $errors->first('error') }}</span>
								@endif
								<input type="submit" class="login-btn" value="Enviar Verificación">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection