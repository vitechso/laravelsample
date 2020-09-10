<!DOCTYPE html>
<html lang="en">
<head>
	<title>Phone Pact</title>
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
					<div class="logns">
						<img src="{{ URL::asset('assets/user-front/img/logo_withshadow.png') }}">
						<h2>{{ucfirst(trans($users->signee_name))}},</h2>
						<h3>{{ucfirst(trans($users->name))}} SENT YOU A <span class="text_01">PHONE</span><span class="text_02">PACT</span></h3>
					</div>

					<div class="heddingss">
						<h1>{{ucfirst(str_replace(',',', ',str_replace('/','/ ',trans($pact_temp_detail->title))))}}</h1>
					</div>
					<div class="decs">
						<p>{{strtoupper(trans($pact_temp_detail->delivery_msg))}}</p>
					</div>
					{{Form::open(['route'=>['signees-pact-sections',base64_encode($users->id)],'method' => 'any'])}}
					<input type="hidden" name="signee_id" value="{{$users->user_id}}">
					<input type="hidden" name="pact_id" value="{{$users->pact_id}}">
					<input type="hidden" name="admin_id" value="{{$users->admin_id}}">
					<div class="button">
						<button type="submit" class="btn btn-primary cunsd">PROCEED <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
					</div>
					{{Form::close()}}
					<div class="bootm_text">
						<p>Not {{ucfirst(trans($users->signee_name))}}?</p>
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