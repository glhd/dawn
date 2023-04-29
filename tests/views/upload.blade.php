@extends('layout')

@section('content')
	<form method="POST" enctype="multipart/form-data">
		@csrf
		<input type="file" name="upload" id="upload-input" />
		<button type="submit">Upload</button>
	</form>
@endsection
