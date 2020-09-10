<!DOCTYPE html>
<html lang="en">
<head>
  <title>Phone Pact Thank You</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ URL::asset('assets/user-front/css/style.css') }}">
</head>
<body>
	<section class="main-body d-flex align-items-center">
		<div class="row">
			<div class="col-12">
				<div class="iner_mn">
					<div class="logns" style="padding-bottom: 15%;">
						<img src="{{ URL::asset('assets/user-front/img/logo_withshadow.png') }}">
					</div>

					<div class="tank text-center">
						<img src="{{ URL::asset('assets/user-front/img/Vector.png') }}">
					</div>

					<div class="heddingss">
						<h1>THANK YOU</h1>
					</div>
					<div class="decs">
						<p>Your PhonePact has been delivered to {{ucfirst(trans($assign_users->name))}} at {{date('H:i')}} on {{date('d/m/Y')}}.</p>
					</div>
				</div>
			</div>
		</div>
	</section>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>