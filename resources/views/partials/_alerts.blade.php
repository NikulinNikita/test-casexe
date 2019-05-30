<div class="container">
	<div class="row">
		@if(session('error'))
			<div class="alert alert-danger" role="alert">
				{{ session('error') }}
			</div>
		@endif
		@if(session('success'))
			<div class="alert alert-success" role="alert">
				{{ session('success') }}
			</div>
		@endif
	</div>
</div>