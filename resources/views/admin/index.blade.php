<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title') - {{ config('app.name') }}</title>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" type="text/css" href="{{ asset('backend/css/main.css') }}" />
	<link rel="stylesheet" type="text/css" href="{{ asset('backend/css/font-awesome/4.7.0/css/font-awesome.min.css') }}"/>
</head>
<body class="app sidebar-mini rtl">
	<div id="app">
		<app></app>
	</div>
	<script src="{{ asset('backend/js/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ asset('backend/js/popper.min.js') }}"></script>
	<script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('backend/js/main.js') }}"></script>
	<script src="{{ asset('backend/js/plugins/pace.min.js') }}"></script>

	<script src="{{ mix('backend/js/app.js') }}"></script>
	<!-- <script src="{{ asset('backend/js/app.js') }}"></script> -->
</body>
</html>