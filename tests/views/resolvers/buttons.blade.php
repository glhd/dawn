@extends('layout')

@section('content')
	<div x-data="{ clicked: 'N/A' }">
		
		<div class="border rounded block" id="clicked" x-text="clicked"></div>
		
		<div class="flex space-x-2">
			<button id="button-with-id" @click="clicked = 'button with id'">
				Button With ID
			</button>
			
			<button class="button-with-class" @click="clicked = 'button with class'">
				Button With Class
			</button>
			
			<input
				type="submit"
				name="submit-input"
				value="Submit Input"
				@click.prevent="clicked = 'submit input'"
			/>
			
			<input
				type="button"
				value="button-input"
				@click="clicked = 'button input'"
			/>
			
			<button name="button-with-name" @click="clicked = 'button with name'">
				Button With Name
			</button>
			
			<input
				type="submit"
				value="submit button by value 1"
				@click.prevent="clicked = 'submit button by value 1'"
			/>
			
			<input
				type="submit"
				value="submit button by value 2"
				@click.prevent="clicked = 'submit button by value 2'"
			/>
			
			<button @click="clicked = 'button with text'">
				Button With Text
			</button>
		</div>
	</div>
@endsection
