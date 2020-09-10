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
  <style type="text/css">
  	.card-heading > a{
  		padding-left: 13%; 
  	}
  	.card-heading > a:before{
  		left: 12px;
  	}
  	.card-heading > a:after{
  		left: 11px;
  	}
  	.creative-chech-label{
  		left: -4px;
  	}
  </style>
</head>
<body>

	<section class="main-body">
		<div class="tab-content">
			
			@for($ch=0;$ch<$totalsection;$ch++)
			<div id="English{{$ch}}" class="tab-pane {{ ($ch==0) ? 'active' : ''}}">
				
				<div class="row">
					<div class="col-12">
						<div class="management-block">
							<img src="{{ URL::asset('assets/user-front/img/logo_withshadow.png') }}" class="ms-logo">
							<h5>PART {{$ch+1}} OF {{$totalsection}}</h5>
							@php
							$sectiontitle = explode(',',$pact_temp_detail->section_title);
							
							@endphp
							<h4>{{$sectiontitle[$ch]}}</h4>
							
							<p>{{strtoupper(trans($pact_temp_detail->delivery_msg))}}</p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<input type="hidden" name="total_clauses_per_section{{$ch+1}}" value="{{count($clause_heading[$ch])}}">
						<div class="main-contain">
							<div id="accordion">
							@for($cb=0;$cb<count($clause_heading[$ch]);$cb++)
							@if($clause_heading[$ch][$cb]!='')
								<div class="card collapse-main-block colapsediv{{$ch}}{{$cb}}">
							    	<div class="card-header content-adjust " id="heading-1" style="padding: 15px; ">
							      		
							      		<div class="card-heading mb-0">
							      			<input type="checkbox" name="clause_heading_checkbox{{$ch+1}}[]" id="_checkbox{{$ch}}{{$cb}}" class="_checkbox">
											<label for="_checkbox{{$ch}}{{$cb}}" class="creative-chech-label">
												<div id="tick_mark"></div>
												<div class="check-details">
													
												</div>
											</label>
							        		<a class="collapsed collapd" data-value="{{$ch}}{{$cb}}" role="button" data-toggle="collapse" href="#collapse-{{$ch}}{{$cb}}" aria-expanded="false" aria-controls="collapse-1">
							          		{{$clause_heading[$ch][$cb]}}
							        		</a>
							      		</div>
							    	</div>

							    	<div id="collapse-{{$ch}}{{$cb}}" class="collapse" data-parent="#accordion" aria-labelledby="heading-1">
							      		<div class="card-body card-body2">
							        		<p>{{$clause_body[$ch][$cb]}}</p>

							        		<div class="signed-block">
							        			<div class="signed-text">
							        				<!-- <h4>First Signed 10/13/18</h4>
							        				<h5>Last Signed 9/15/19</h5> -->
							        			</div>

							        			<div class="signed-btn">
							        				<a href="#" class="agree1" data-value="{{$ch}}{{$cb}}">Agree</a>
							        			</div>
							        		</div>
							      		</div>
							    	</div>
							  	</div>
							@endif
						  	@endfor  
						  	</div>
					      	
						</div>
						
						<div class="button mt-4 no-pad">
							@if(($ch+1)==$totalsection)
							{{ Form::open(['route'=>['signees-pact-signature',base64_encode($assign_section_detail->id)],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left','style'=>'width:100%;']) }}
							<input type="hidden" name="assign_tbid" value="{{$assign_section_detail->id}}">
							<input type="hidden" name="signees_id" value="{{$assign_section_detail->user_id}}">
							<button type="submit" class="btn btn-primary cunsd" data-value="{{$ch+1}}">CONTINUE <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
							{{Form::close()}}
							@else
							<button type="button" class="btn btn-primary cunsd" data-value="{{$ch+1}}">CONTINUE <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
							@endif
						</div>
						
					</div>
				</div>
			</div>
			@endfor
			
			<ul class="nav nav-tabs safey-nav" style="display: none;">
			    @for($ch=0;$ch<count($clause_heading);$ch++)
			    <li class="nav-item {{$ch}}">
			      <a class="nav-link {{ ($ch==0) ? 'active' : ''}}" data-toggle="tab" href="#English{{$ch}}">Continue{{$ch+1}}</a>
			    </li>
			    @endfor
		  	</ul>
		</div>
<div class="modal fade" id="add_validate">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
        			<p style="color:red;">Select all checkbox</p>
		        </div>
	      	</div>
	    </div>
	</div>
	</section>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  	<script type="text/javascript">
  		$(document).ready(function(){
  			var currenttabid = $('.tab-pane.active').attr('id');
  			var li_ids = $('#'+currenttabid).find('.cunsd').data('value');
  			var total_clauses_per_section1 = $('[name="total_clauses_per_section'+li_ids+'"]').val();
			var clause_heading_checkbox1 = $('[name="clause_heading_checkbox'+li_ids+'[]"]:checked').length;
			if(total_clauses_per_section1==clause_heading_checkbox1){
				$('label').show();
				$('body').append('<style>.card-heading a::before{visibility : hidden !important;}</style>');
				
				
			
			}

  			$('.cunsd').click(function(){
  				$(this).find('#error-valid').remove();
  				var liids = $(this).data('value');
  				var total_clauses_per_section = $('[name="total_clauses_per_section'+liids+'"]').val();
  				var clause_heading_checkbox = $('[name="clause_heading_checkbox'+liids+'[]"]:checked').length;
  				console.log(total_clauses_per_section+clause_heading_checkbox);
  				if(total_clauses_per_section==clause_heading_checkbox){
	  				$('.safey-nav li.'+liids+' a').trigger('click');
  				}else{
  					// $('#add_validate').show()
  					$('#add_validate').modal('show');
  					// $(this).parent().parent().find('.main-contain').append('<span id="error-valid" style="color:red;">Select all checkbox</span>');
  					//alert('Select all checkbox');
  					return false;
  				}
  				// alert($('total_clauses_per_section'+liids).val());
  				// alert('.safey-nav li.'+liids+' a');
  			});

  			$('body').on('click','.collapsed',function(){
  				var collapsed = $(this).data('value');

  				
  				$('.colapsediv'+collapsed).find('label').show();
  				$('.colapsediv'+collapsed).find('label').css('pointer-events','none');
  				$('.colapsediv'+collapsed+' a:before').css('visibility','hidden !important');
  				$('.colapsediv'+collapsed+' a:after').css('visibility','hidden');
  				// $(this).parent().parent().find('.check-block-hide').show();
  				// $(this).parent().hide();
  				// alert('asd');
  				// $(this).parent().find('input').show();
  				// $(this).parent().find('label').show();
  				// $(this).parent().find('.check-details').text($(this).text());
  				// $(this).remove();
  				// .content-adjust padding:15px;
  			})
  			$('body').on('click','.agree1',function(){
  				var colp = $(this).data('value');
  				// $(this).text('disagree');
  				// $('.collapsed'+colp)
  				// alert($('#collapse-'+colp).parent('.colapsediv'+colp));
  				$('#collapse-'+colp).removeClass('show');
  				// $('#collapse-'+colp).parent().find('label').show();
  				$('#collapse-'+colp).parent('.colapsediv'+colp).find('#tick_mark').trigger('click');
  				var this_index=$(this).index('.agree1');
  				var nth_child=parseInt(this_index)+1;
  				if($(this).closest(".card").find("._checkbox").prop('checked')==true)
  				{
  					//alert("it is checked");
  					// $("body").append('<style id="opacity">.card:nth-child('+nth_child+') .card-heading a:before{opacity:0;}');
  					$("body").append('<style id="opacity">.card:nth-child('+nth_child+') .card-heading a:after{opacity:0;}');
  				}else{
  					//alert("it is not checked");
  					$("body").append('<style id="opacity">.card:nth-child('+nth_child+') .card-heading a:before{opacity:1;}');
  					$("body").append('<style id="opacity">.card:nth-child('+nth_child+') .card-heading a:after{opacity:1;}');
  				}
  			});

  		});
  	</script>
</body>
</html>