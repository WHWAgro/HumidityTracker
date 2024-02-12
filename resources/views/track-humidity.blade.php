@extends('layouts.app')
@section('title','Track Humidity')

@section('content')
	<div class="login-wrap">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-5 col-md-10">
					<div class="login-in">
						<div class="login-logo">
							<div class="logo">
								<img src="images/login-logo.png">
							</div>
						</div>
						<div class="login-form">
							<form action="forecast-data.html" method="POST">
								<div class="login-form-img">
									<img src="images/humidity.png" alt="">
								</div>
								<!-- <input type="submit" class="login-btn" value="Track Humidity"> -->
								<a href="{{ url('track_humidity') }}" class="login-btn" onclick="event.preventDefault(); document.getElementById('track_humidity').submit();">Seguimiento de Humedad</a>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form action="{{ url('track_humidity') }}" method="post" id="track_humidity" style="display: none;">
		@csrf
		<input type="hidden" name="email" value="{{ Session::get('email') ?? old('email') }}">
	</form>
@endsection