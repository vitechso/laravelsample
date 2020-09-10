<!DOCTYPE html>
<html lang="en">
<head>
  <title>Phone Pact Signature</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <!-- <link rel="stylesheet" href="css/style.css"> -->
  <link rel="stylesheet" href="{{ URL::asset('assets/user-front/css/style.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/user-front/css/signature-pad.css') }}">
  	<style type="text/css">
  	
    #warning-message { display: none; }
    @media only screen and (orientation:portrait){
        #signature-pad { display:none; }
        #warning-message { display:block; }
    }
    @media only screen and (orientation:landscape){
        #warning-message { display:none; }
    }
    
    body{
    	padding: 10px 15px !important;
    }
	</style>
</head>

<body onselectstart="return false"> 
	
	    <!-- your html for your website -->
	
	  	<div id="signature-pad" class="signature-pad">
		  	<p class="hedis">Please Sign BEFORE SUBMITTING </p>
		    <div class="signature-pad--body">
		      <canvas></canvas>
		    </div>

		    <div class="signature-pad--footer">
		      <div class="description">
		        <div><p>By submitting your signature you agree to adhere to the previously selected terms defined by CRH Coporation.</p></div>
		        <div><button type="button" class="button clear cunsd" data-action="clear">Clear</button></div>
		        {{Form::open(['route'=>['thankyou'],'method' => 'any','id'=>'signature-form'])}}
		        <button type="button" class="btn btn-primary cunsd sumbit" data-action="save-png">CONTINUE <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
		        {{Form::close()}}
		      </div>

		     
		    </div>
	  	</div>

		<!-- <div class="signature-pad--actions">
		    <div>
		      <button type="button" class="button clear" data-action="clear">Clear</button>
		      <button type="button" class="button" data-action="change-color">Change color</button>
		      <button type="button" class="button" data-action="undo">Undo</button>
		    </div>

		    <div>
		      <button type="button" class="button save" data-action="save-png">Save as PNG</button>
		      <button type="button" class="button save" data-action="save-jpg">Save as JPG</button>
		      <button type="button" class="button save" data-action="save-svg">Save as SVG</button>
		    </div>
	  	</div> -->
  	
  	
	<div id="warning-message">
	    this app screen is only viewable in landscape mode
	</div>



  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	<script src="{{ URL::asset('assets/user-front/js/signature_pad.umd.js') }}"></script>
	<script src="{{ URL::asset('assets/user-front/js/app.js') }}"></script>
	<script type="text/javascript">
    $(document).ready(function(){
      $('.cunsd.sumbit').click(function(){
        var dataURL = $('body > a').attr('href');
        $.ajax({
          type: "post",
          url: "{{ url('/') }}/saveimage",
          data:{coloumn:"to",_token:"{{ csrf_token() }}",imgBase64: dataURL},
          async: true,
        }).done(function(o) {
          console.log(o);
          // window.location.href = '{{ url("/") }}/thankyou'; 
        });
      });


     // if (window.DeviceOrientationEvent) {
		    window.addEventListener('orientationchange', function() { 
		    	location.reload(); 
		    	console.log('asdasd');
		    }, false);
		// }


    })
  	</script>
</body>
</html>