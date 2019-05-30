<head>
	<link rel="icon" href="/favicon.ico">
	<title>{{ $metatitle ?? config('admin.title') }}</title>
    <meta name="description" content="{{ $metadesc ?? null }}">
    <meta name="keywords" content="{{ $metakeyw ?? null }}">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
	@yield('assets-css')
</head>