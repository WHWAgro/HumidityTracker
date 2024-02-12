@extends('layouts.app')
@section('title', "Create Password")

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
							<form action="{{ url('create_password') }}" method="POST">
								@csrf
								<h1>Crear Contraseña</h1>
								<label class="login-lbl">
									<img src="{{ asset('images/login-pass.png') }}" alt="">
									<input type="password" maxlength="15" name="password" class="login-txt" placeholder="Contraseña">
								</label>
								@if($errors->has('password'))
									<span class="error">{{ $errors->first('password') }}</span>
								@endif
					
								<label class="login-lbl">
									<img src="{{ asset('images/login-pass.png') }}" alt="">
									<input type="password"  maxlength="15" name="confirm_password" class="login-txt" placeholder="Confirmar Contraseña">
								</label>
								@if($errors->has('confirm_password'))
									<span class="error">{{ $errors->first('confirm_password') }}</span>
								@endif
								<input type="hidden" name="email" value="{{ Session::get('email') ?? old('email') }}">
								<input type="hidden" name="type" value="{{ Session::get('type') ?? old('type') }}">
								<input type="submit" class="login-btn" value="Enviar">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection