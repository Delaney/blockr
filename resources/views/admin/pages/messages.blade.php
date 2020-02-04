<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Messages</title>
</head>
<body>
	<script src="{{ asset('backend/js/jquery-3.2.1.min.js') }}"></script>
	<script>
		$.get('/botcheck').done(data => {
			console.log(data);
		});
	</script>
</body>
</html>