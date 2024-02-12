<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type"content="text/html; charset=utf-8"/>
		<meta name="viewport"content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
		<title>@yield('title')</title>
		<link href="{{ asset('css/bootstrap.min.css') }}"rel="stylesheet"type="text/css"/>
		<link href="{{ asset('css/bootstrap-theme.min.css') }}"rel="stylesheet"type="text/css"/>
		<link rel="stylesheet"href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
		<link href="{{ asset('css/animation.css') }}"rel="stylesheet"type="text/css">
		<link href="{{ asset('css/custom.css') }}"rel="stylesheet"type="text/css"/>
		<link href="{{ asset('css/style.css') }}"rel="stylesheet"type="text/css"/>
		<link href="{{ asset('css/responsive.css') }}"rel="stylesheet"type="text/css"/>
		<!-- CSS -->

		<!-- forecast -->
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
		<link href="font/stylesheet.css"rel="stylesheet"type="text/css"/>
		<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css'rel='stylesheet'>

		
		@yield('css')

	</head>
	<body>
        @yield('content')
		<!-- JS -->
		<script src="{{ asset('js/jquery.js') }}"type="text/javascript"></script>
		<script src="{{ asset('js/bootstrap.min.js') }}"type="text/javascript"></script>
		<script src="{{ asset('js/custom.js') }}"type="text/javascript"></script>
		<script src="{{ asset('js/animation.js') }}"type="text/javascript"></script>
		<script src="{{ asset('js/datepicker.js') }}"type="text/javascript"></script>
		@yield('script')
	</body>
</html>