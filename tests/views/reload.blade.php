@extends('layout')

@section('content')
	<div id="random">{{ Str::random() }}</div>
	<button id="reload" onclick="setTimeout(() => window.location.reload(), 100)">Reload</button>
@endsection
