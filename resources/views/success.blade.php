@extends('layouts.app')
@section('title','Account Created Successfully')

@section('content')
	<div class="login-succ-wrap">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-4 col-md-10 wow bounceInDown" data-wow-duration="1000ms" data-wow-delay="100ms">
					<div class="login-succ">
						<img src="{{ asset('images/create-success.png') }}" alt="">
						<h1>Éxito!</h1>
						<p>Tu cuenta <br> ha sido creada exitósamente.</p>
						<!-- <a href="forecast-data.html">X</a> -->
						<a href="{{ url('track_humidity') }}" onclick="event.preventDefault(); document.getElementById('track_humidity').submit();">X</a>
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