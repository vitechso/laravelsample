<!DOCTYPE html>
<html lang="en">
<head>
  <title>Phone Pact Agree</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ URL::asset('assets/user-front/css/style.css') }}">
</head>
<body>
	<section class="main-body">
		<div class="row">
			<div class="col-12">
				<div class="management-block">
					<img src="{{ URL::asset('assets/user-front/img/logo_withshadow.png') }}" class="ms-logo">
					<h5>PART 1 OF {{$totalsection}}</h5>
					<h4>{{ucfirst(str_replace(',',', ',str_replace('/','/ ',trans($pact_temp_detail->section_title))))}}</h4>
					<p>{{strtoupper(trans($pact_temp_detail->delivery_msg))}}</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				@for($ch=0;$ch<count($clause_heading[0]);$ch++)
				<div class="agree-details-block">
					<div class="check-block">
						<input type="checkbox" value="{{$clause_heading[0][$ch]}}" id="_checkbox0{{$ch}}" class="_checkbox">
						<label for="_checkbox0{{$ch}}" class="creative-chech-label">
							 <div id="tick_mark"></div>
							<div class="check-details">
								<h5>{{$clause_heading[0][$ch]}}</h5>
								<p>{{$clause_body[0][$ch]}}</p>
							</div>
						</label>
					</div>
				</div>
				@endfor;
				<!--
				<div class="agree-details-block">
					<div class="check-block">
						<input type="checkbox" id="_checkbox02" class="_checkbox">
						<label for="_checkbox02" class="creative-chech-label">
							 <div id="tick_mark"></div>
							<div class="check-details">
								<h5>Provide each team member with the proper safety equipment.</h5>
								<p>I agree to keep enough safety equipment inventory on hand to ensure no team member is forced to work without their necessary gear.  This includes, Hardhats, Gloves, Eye Protection, Steel Toed Boots (or covers).</p>
							</div>
						</label>
					</div>
				</div>

				<div class="agree-details-block">
					<div class="check-block">
						<input type="checkbox" id="_checkbox03" class="_checkbox">
						<label for="_checkbox03" class="creative-chech-label">
							 <div id="tick_mark"></div>
							<div class="check-details">
								<h5>Eliminate job site hazards by providing proper safeguards.</h5>
								<p>I agree to properly address or report potential safety concerns prior to the upcoming work day.  This requires me to walk the site in advance of my team’s shift.</p>
							</div>
						</label>
					</div>
				</div>
				-->
				<div class="button mt-4 no-pad">
					<button type="submit" class="btn btn-primary cunsd">CONTINUE <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
				</div>
			</div>
		</div>
	</section>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>