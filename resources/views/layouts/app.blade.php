<!DOCTYPE html>
<html lang="en">
@include('partials._head')
<body id="app-layout">
	@include('partials._header')
	@include('partials._alerts')
	@yield('content')
	@include('partials._footer')
</body>
</html>