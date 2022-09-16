<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Dawn</title>
	<script src="/_dawn/alpine.js"></script>
	<link href="/_dawn/tailwind.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans antialiased">
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
	<div class="mx-auto max-w-3xl min-h-screen bg-white rounded-lg mt-12 p-8 shadow" @dawnTarget('content')>
		@yield('content')
	</div>
</div>
</body>
</html>
