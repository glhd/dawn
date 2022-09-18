@extends('layout')

@section('content')
    <div x-data="{ name: '' }">
	    <input x-model="name" class="name" />
	    <button class="hello" @click="alert(`${window.greeting} ${name}!`)">
		    Hello
	    </button>
    </div>
@endsection
