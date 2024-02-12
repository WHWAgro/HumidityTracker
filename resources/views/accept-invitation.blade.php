@extends('layouts.app')
@section('title',"Accept Invitation")

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
							<form action="{{ url('invitation') }}" method="POST">
								@csrf
								<h1>ACeptar invitación</h1>
								<label for="" class="login-lbl">
									<img src="{{ asset('images/login-mail.png') }}" alt="">
									<input type="text" name="email" class="login-txt" value="{{ old('email') }}" placeholder="Email">
								</label>
								@if($errors->has('email'))
									<span class="error">{{ $errors->first('email') }}</span>
								@endif
								@if($errors->has('error'))
									<span class="error">{{ $errors->first('error') }}</span>
								@endif
								<label class="login-lbl">
									<img src="{{ asset('images/login-pass.png') }}" alt="">
									<input type="password" name="password" class="login-txt" id="password" placeholder="Contraseña Temporal">
									<div class="password-eye"><i class="fas fa-eye-slash" id="eye"></i></div>
								</label>
								@if($errors->has('password'))
									<span class="error">{{ $errors->first('password') }}</span>
								@endif
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
@endsection