@extends('layout')

@section('content')
	<div x-data="{ status: 'N/A', clicks: 0 }">
		<div id="status" x-text="status"></div>
		<button id="click-and-hold" @mousedown="status = 'Mouse Down'" @mouseup="status = 'Mouse Up'">Click and Hold</button>
		<button id="double-click" @click="status = ++clicks;">Double Click</button>
		<button id="right-click" @contextmenu.prevent="status = 'Right Click'">Right Click</button>
		<div>
			<div 
				id="drag-object"
				class="w-12 h-12 bg-gray-400"
				draggable="true"
				@dragstart="status = 'Dragging Started'"
			></div>
			<div 
				id="drag-target"
				class="w-24 h-24 border-2 border-dashed"
				@dragover.prevent="status = 'Dragging Over'"
				@drop.prevent="status = 'Dropped'"
			></div>
		</div>
	</div>
@endsection
