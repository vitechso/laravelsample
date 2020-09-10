@include('admin.include.header')

@include('admin.include.flash-messages')


	<div class="wrapper">

		<!-- sitebar -->
		@include('admin.include.sidebar')
		<!-- sitebar end-->

		<div class="main-panel">
			<div class="header">
				<div class="toggle-menu">
					<span class="menulines" onclick="openNav()">&#9776;</span>
				</div>
			</div>
			<div class="content">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="page-heading">
							<h4>{{ auth()->user()->name }}</h4>
							<h5>Pact Template</h5>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="pacts-info">

							<!-- Nav tabs -->
							<ul class="nav nav-tabs custom-nav-tabs ">
							    <li class="nav-item">
							      <a class="nav-link active" data-toggle="tab" href="#home">PACT DETAILS</a>
							    </li>
							    <li class="nav-item">
							      <a class="nav-link" data-toggle="tab" href="#menu1">CLAUSES</a>
							    </li>

							    <li class="nav-item">
							      <a class="nav-link" data-toggle="tab" href="#menu2">Non-Compliance</a>
							    </li>
						  	</ul>
							    	

						  	<!-- Tab panes -->
						  	<div class="tab-content custom-tab-content">
							    <div id="home" class="container-fluid tab-pane active">

							    	{{ Form::open(['route'=>['admin.pact-template.addsection'],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left','style'=>'width:100%;'])}}
							    	<input type="hidden" name="pacttemp_id" value="{{$pacttemplate->id}}">
							    	<div class="row">
						  				
							    		<div class="col-lg-12 col-md-12 col-12">
							    			<div class="page-Description">
									      		<div>
									      			<img src="{{ URL::asset('assets/front/svg/desh-i-logo.svg') }}">
													
									      		</div>

									      		<div>
									      			<h5>Update PACT</h5>
									      			<p>You can make any changes to the form below.  Just click ‘Save’ to update the PACT details.</p>
									      		</div>
											</div>
							    		</div>
							    	</div>

									<div class="row mt-3 mb-3">
										<div class="col-lg-6 col-md-6 col-12">
											<div class="part-1">
												<div class="row mb-3">
													<div class="col-md-12"><p><span>Title:</span></p></div>
													<div class="col-md-12 form-group">
														<input type="text" value="{{ (old('title')) ? old('title') : ucfirst(trans($pacttemplate->title))}}" name="title" placeholder="CRH has sent you the Manager Policy Pact. You have 24hrs to complete." class="form-control f-14 @if($errors->has('title')) parsley-error @endif">
														@if($errors->has('title'))
								                            <ul class="parsley-errors-list filled">
							                                @foreach($errors->get('title') as $error)
							                                    <li class="parsley-required">{{ $error }}</li>
							                                @endforeach
								                            </ul>
								                        @endif
													</div>
													<!-- <p><span>Title :</span></p> -->
												</div>

												<div class="row mb-3">
													<div class="col-md-12"><p><span>Delivery Message:</span></p></div>
													<div class="col-md-12 form-group">
														<textarea name="delivery_msg" placeholder="CRH has sent you the Manager Policy Pact. You have 24hrs to complete." class="form-control f-14 @if($errors->has('delivery_msg')) parsley-error @endif" required="">@if(old('delivery_msg')) {{ old('delivery_msg')}} @elseif(isset($pact_temp_section->delivery_msg) && $pact_temp_section->delivery_msg!='') {{$pact_temp_section->delivery_msg}} @endif</textarea>
														@if($errors->has('delivery_msg'))
								                            <ul class="parsley-errors-list filled">
							                                @foreach($errors->get('delivery_msg') as $error)
							                                    <li class="parsley-required">{{ $error }}</li>
							                                @endforeach
								                            </ul>
								                        @endif
													</div>
													<!-- <p><span>Delivery Message:</span>CRH has sent you the Manager Policy Pact. You have 24hrs to complete .</p> -->
												</div>
												
												
												
												<div class="table-block">
													<table class="table table-striped-copy table-responsive inline-table custom-table2" id="createsectionclone">
													    <thead>
													      	<tr>
														        <th>Sections:</th>
														        <th class="text-right"><a href="javascript:;" style="color: #fff;" id="addrow">Add New</a></th>
													      	</tr>
													    </thead>

													    <tbody>
													    	
													    	@if(isset($pact_temp_section->section_title) && $pact_temp_section->section_title!='') 
													    	@php
													    	$section_titlearr = explode(',',$pact_temp_section->section_title); 
													    	@endphp
													    	@for($s=0;$s<count($section_titlearr);$s++)
													      	<tr>
														        <td class="text-left">
														        	<input type="text" value="{{$section_titlearr[$s]}}" name="section_title[]" placeholder="Demo Section {{$s+1}}" class="form-control @if($errors->has('section_title')) parsley-error @endif" required>
														        	@if($errors->has('section_title'))
											                            <ul class="parsley-errors-list filled">
											                                @foreach($errors->get('section_title') as $error)
											                                    <li class="parsley-required">{{ $error }}</li>
											                                @endforeach
											                            </ul>
											                        @endif
														        </td>
														        <td class="text-right add-action">
														        	<!-- <a href="#"><i class="fas fa-pencil-alt"></i></a> -->
														        	<a href="javascript:;" class="remove"><i class="far fa-trash-alt"></i></a>
														        </td>
													      	</tr>
													      	@endfor
													    	
													      	@else
													      	<tr>
														        <td class="text-left">  
														        	<input type="text" name="section_title[]" placeholder="Demo Section 1" class="form-control @if($errors->has('section_title')) parsley-error @endif" required>
														        	@if($errors->has('section_title'))
											                            <ul class="parsley-errors-list filled">
											                                @foreach($errors->get('section_title') as $error)
											                                    <li class="parsley-required">{{ $error }}</li>
											                                @endforeach
											                            </ul>
											                        @endif
														        </td>
														        <td class="text-right add-action">
														        	<a href="javascript:;" class="remove"><i class="far fa-trash-alt"></i></a>
														        </td>
													      	</tr>
													      	@endif
													      	<!-- <tr>
														        <td class="text-left">3.&nbsp;&nbsp;Time off Policy </td>
														        <td class="text-right action">
														        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
														        	<a href="#"><i class="far fa-trash-alt"></i></a>
														        </td>
													      	</tr>

													      	<tr>
														        <td class="text-left">4.&nbsp;&nbsp;Data Security Policy</td>
														        <td class="text-right action">
														        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
														        	<a href="#"><i class="far fa-trash-alt"></i></a>
														        </td>
													      	</tr>

													      	<tr>
														        <td class="text-left">1.&nbsp;&nbsp;Safety Equipment</td>
														        <td class="text-right action">
														        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
														        	<a href="#"><i class="far fa-trash-alt"></i></a>
														        </td>
													      	</tr>

													      	<tr>
														        <td class="text-left">1.&nbsp;&nbsp;Safety Equipment</td>
														        <td class="text-right action">
														        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
														        	<a href="#"><i class="far fa-trash-alt"></i></a>
														        </td>
													      	</tr> -->
													    </tbody>
												  	</table>
												</div>
											</div> <!-- part-1 end -->
										</div>

										<div class="col-lg-6 col-md-6 col-12">
											<div class="part-2">
												<div class="row mb-3">
													<div class="col-md-12"><p><span>Type :</span></p></div>
													<div class="col-md-12 lock">
														<!-- <i class="fas fa-envelope"></i> -->
													    <div class="select form-group">
															<select name="type" id="type" class="form-control custom-color hight-adjust @if($errors->has('type')) parsley-error @endif" required>
																@php
																$typeArr = array('','hr','legal','safety','security','personal');
																@endphp
																@for($t=0;$t<count($typeArr);$t++){
																	<option value="{{$typeArr[$t]}}" @if($pacttemplate->type==$typeArr[$t]) selected="selected" @endif>{{ucfirst(trans($typeArr[$t]))}}</option>
																@endfor
															
															</select>
														</div>
													    
													</div>
													
												</div>
												<div class="row mb-3">
													<div class="col-md-12"><p><span>Completion Confirmation:</span></p></div>
													<div class="col-md-12 form-group">
														<textarea name="complete_confirm_msg" placeholder="Thank you for  agreeing to the terms and conditions of this CRH policy." class="form-control f-14 @if($errors->has('complete_confirm_msg')) parsley-error @endif" required="">@if(old('complete_confirm_msg')) {{ old('complete_confirm_msg')}} @elseif(isset($pact_temp_section->complete_confirm_msg) && $pact_temp_section->complete_confirm_msg!='') {{$pact_temp_section->complete_confirm_msg}} @endif</textarea>
														
														@if($errors->has('complete_confirm_msg'))
								                            <ul class="parsley-errors-list filled">
							                                @foreach($errors->get('complete_confirm_msg') as $error)
							                                    <li class="parsley-required">{{ $error }}</li>
							                                @endforeach
								                            </ul>
								                        @endif
													</div>
												</div>


												<div class="row">
													<div class="col-lg-6 col-md-6 col-12">
														<div class="select form-group">
															<select class="form-control hight-adjust custom-color @if($errors->has('section_title')) parsley-error @endif" name="has_campaign" id="sel1" required>
														    	<option value="">Has Campaign?</option>
														    	<option value="yes" @if(isset($pact_temp_section->has_campaign) && $pact_temp_section->has_campaign=='yes') selected="selected" @endif">Yes</option>
														    	<option value="no" @if(isset($pact_temp_section->has_campaign) && $pact_temp_section->has_campaign=='no') selected="selected" @endif">No</option>
														  	</select>
														  	@if($errors->has('delivery_msg'))
									                            <ul class="parsley-errors-list filled">
								                                @foreach($errors->get('delivery_msg') as $error)
								                                    <li class="parsley-required">{{ $error }}</li>
								                                @endforeach
									                            </ul>
									                        @endif
														</div>
													</div>

													<div class="col-lg-6 col-md-6 col-12">
														<div class="form-group">
															<input type="text" name="time_complete" class="form-control hight-adjust  @if($errors->has('time_complete')) parsley-error @endif" id="usr" value="@if(old('time_complete')) {{ old('time_complete')}} @elseif(isset($pact_temp_section->time_complete) && $pact_temp_section->time_complete!='') {{$pact_temp_section->time_complete}} @endif" placeholder="Time to Complete:" required="">
															@if($errors->has('time_complete'))
									                            <ul class="parsley-errors-list filled">
								                                @foreach($errors->get('time_complete') as $error)
								                                    <li class="parsley-required">{{ $error }}</li>
								                                @endforeach
									                            </ul>
									                        @endif
														</div>
													</div>
												</div>

												<!-- <div class="mini-form-block">
													<h5>NUDGE Campaign</h5>
													<div class="row">
														<div class="col-lg-6 col-md-6 col-12">
															<div class="form-box">
																<div class="row align-items-center mb-4">
																	<div class="col-lg-6 col-md-6 col-12">
																		<p class="form-label">Frequency</p>
																	</div>
																	<div class="col-lg-6 col-md-6 col-12">
																		<select class="form-control font-adjust" id="sel1">
																		    <option>Monthly</option>
																		    <option>2</option>
																		    <option>3</option>
																		    <option>4</option>
																	 	</select>
																	</div>
																</div>

																<div class="row align-items-center mb-4">
																	<div class="col-lg-6 col-md-6 col-12">
																		<p class="form-label">Delivery Time</p>
																	</div>
																	<div class="col-lg-6 col-md-6 col-12">
																		<input type="text" class="form-control font-adjust" id="usr" placeholder="10:00 am">
																	</div>
																</div>
															</div>
														</div>

														<div class="col-lg-6 col-md-6 col-12">
															<div class="form-box">
																<div class="row align-items-center mb-4">
																	<div class="col-lg-6 col-md-6 col-12">
																		<p class="form-label">Days Until First Nudge:</p>
																	</div>
																	<div class="col-lg-6 col-md-6 col-12">
																		<input type="text" class="form-control font-adjust" id="usr" placeholder="15">
																	</div>
																</div>

																<div class="row align-items-center mb-4">
																	<div class="col-lg-6 col-md-6 col-12">
																		<p class="form-label">Total Nudges:</p>
																	</div>
																	<div class="col-lg-6 col-md-6 col-12">
																		<input type="text" class="form-control font-adjust" id="usr" placeholder="5">
																	</div>
																</div>
															</div>
														</div>

														<div class="col-lg-12 col-md-12 col-12">
															<div class="form-group">
																<p class="form-label mb-2">Nudge Message ( Shown in SMS )</p>
																<textarea class="form-control font-adjust" rows="5" id="comment" placeholder="Notice!  You have not yet signed the Manager Policy Pact from CRH.  Please complete at your earliest convenience."></textarea>
															</div>
														</div>
													</div>
												</div> -->

												
											</div>
										</div>
									</div>
								    <div class="button-block">
										<button type="button" class="btn custom-btn"><span class="check"><i class="fas fa-check"></i></span>CANCEL</button>
					  					<button type="submit" class="btn custom-btn check-main"><span class="check"><i class="fas fa-check"></i></span>SAVE</button>
									</div>
									{{ Form::close() }}
							    </div>
							    <div id="menu1" class="container-fluid tab-pane fade">
							    	<div class="row">
							    		<div class="col-lg-12 col-md-12 col-12">
							    			<div class="page-Description">
									      		<div>
									      			<!-- <img src="svg/desh-i-logo.svg"> -->
									      			<img src="{{ URL::asset('assets/front/svg/desh-i-logo.svg') }}">
													
									      		</div>
									      		
									      		<div>
									      			<h5>Update Clauses</h5>
									      			<p>You can make any changes to the form below.  Just click ‘Save’ to update the Clause details.</p>
									      		</div>
											</div>
							    		</div>
							    	</div>
							    	{{ Form::open(['route'=>['admin.pact-template.addclauses'],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left','style'=>'width:100%;'])}}
							    	<input type="hidden" name="pacttemp_id" value="{{$pacttemplate->id}}">
							    	<div class="row">
							    		@if(isset($pact_temp_section->section_title) && $pact_temp_section->section_title!='') 
							    		@php
								    	$section_titlearr = explode(',',$pact_temp_section->section_title); 
								    	$headingarr = json_decode($pact_temp_section->clause_heading);
								    	$bodyarr = json_decode($pact_temp_section->clause_body);
								    	@endphp
								    	@for($s=0;$s<count($section_titlearr);$s++)
								    	
							    		<div class="col-lg-12 col-md-12 col-12 mt-4 mb-3 clouses-div-{{$s}}">
							    			<div class="safety-section mb-4">
							    				<div class="section-title">
							    					<h5>SECTION {{$s+1}}:&nbsp;&nbsp;{{$section_titlearr[$s]}}</h5>
							    				</div>
							    				<input type="hidden" name="sections_no" value="{{$s}}">
							    				<!-- <div class="action">
							    					<a href="#"><i class="fas fa-pencil-alt"></i></a>
													<a href="#"><i class="far fa-trash-alt"></i></a>
							    				</div> -->
							    			</div>
							    			@if(isset($headingarr[$s]))
								    		@for($h=0;$h<count($headingarr[$s]);$h++)
							    			<div class="row clauses-row">
									    		<!-- 1st sub row -->
									    		<div class="col-lg-4 col-md-4 col-12">
									    			<div class="clause-input-block">
									    				<div class="form-group">
															<input type="text" class="form-control theme-form-control" name="clause_heading[{{$s}}][]" id="usr" placeholder="Clause Heading" value="{{$headingarr[$s][$h]}}">
														</div>
									    			</div>
									    		</div>

									    		<div class="col-lg-7 col-md-7 col-12">
									    			<div class="clause-input-block">
									    				<div class="form-group">
															<input type="text" class="form-control theme-form-control" name="clause_body[{{$s}}][]" id="usr" placeholder="Clause Body" value="{{$bodyarr[$s][$h]}}">
														</div>
									    			</div>
									    		</div>
									    		<div class="col-lg-1 col-md-1 col-12">
									    			<div class="remove-btn">
														<span class="remove1">
															<i class="far fa-trash-alt"></i>
														</span>
									    			</div>
												</div>
									    		<!-- 1st sub row end-->
								    		</div>
								    		@endfor
								    		@endif
								    		
							    		</div>
							    		<div class="clone-clouses" style="margin-left:15px;">
							    			<button type="button" class="add-row-btn addrow_clouses" data-value="{{$s}}">
							    				<i class="fas fa-plus-circle"></i><span>clause</span> 
							    			</button>
							    		</div>
							    		@endfor
							    		@endif

							    		<!-- 2nd sub row --
							    		<div class="col-lg-4 col-md-4 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Heading">
												</div>
							    			</div>
							    		</div>

							    		<div class="col-lg-8 col-md-8 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Body">
												</div>
							    			</div>
							    		</div>
							    		<!-- 2nd sub row end-->

							    		<!-- 3rd sub row --
							    		<div class="col-lg-4 col-md-4 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Heading">
												</div>
							    			</div>
							    		</div>

							    		<div class="col-lg-8 col-md-8 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Body">
												</div>
							    			</div>
							    		</div>
							    		<!-- 3rd sub row end-->

							    		<!-- 4th sub row --
							    		<div class="col-lg-4 col-md-4 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Heading">
												</div>
							    			</div>
							    		</div>

							    		<div class="col-lg-8 col-md-8 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Body">
												</div>
							    			</div>
							    		</div>
							    		<!-- 4th sub row end-->

							    		<!-- 5th sub row --
							    		<div class="col-lg-4 col-md-4 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Heading">
												</div>
							    			</div>
							    		</div>

							    		<div class="col-lg-8 col-md-8 col-12">
							    			<div class="clause-input-block">
							    				<div class="form-group">
													<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Body">
												</div>
							    			</div>
							    		</div>
							    		<!-- 5th sub row end-->
							    	</div>

							    	

							    	<div class="row">
							    		<div class="col-lg-12 col-md-12 col-12">
							    			<div class="button-block">
												<button type="button" class="btn custom-btn"><span class="check"><i class="fas fa-check"></i></span>CANCEL</button>
								  				<button type="submit" class="btn custom-btn check-main"><span class="check"><i class="fas fa-check"></i></span>SAVE</button>
											</div>
							    		</div>
							    	</div>
							    {{ Form::close() }}
							    	
							    </div>
							    <div id="menu2" class="container-fluid tab-pane fade">
							    	lorem ipsum
							    </div>
						  	</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


<table class="samplerow" style="display:none">
	<tr>
        <td class="text-left"><input type="text" value="" name="section_title[]" placeholder="Demo Section" class="form-control" required></td>
        <td class="text-right action">
        	<!-- <a href="#"><i class="fas fa-pencil-alt"></i></a> -->
        	<a href="javascript:;" class="remove"><i class="far fa-trash-alt"></i></a>
        </td>
  	</tr>
</table>

<div id="createclousesclone" style="display:none">
<div class="row">
	<!-- 1st sub row -->
	<div class="col-lg-4 col-md-4 col-12">
		<div class="clause-input-block">
			<div class="form-group">
				<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Heading">
			</div>
		</div>
	</div>

	<div class="col-lg-7 col-md-7 col-12">
		<div class="clause-input-block">
			<div class="form-group">
				<input type="text" class="form-control theme-form-control" id="usr" placeholder="Clause Body">
			</div>
		</div>
	</div>
	<div class="col-lg-1 col-md-1 col-12">
		<a href="javascript:;" class="remove1"><i class="far fa-trash-alt"></i></a>
	</div>
	<!-- 1st sub row end-->
</div>
</div>
	
	


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

  	<script>
  		const openNav = () => {
  			document.getElementById('mysitenav').style.width ="260px"
  			document.getElementById('mysitenav').style.opacity ="1"
  		}

  		const closeNav = () => {
  			document.getElementById('mysitenav').style.width ="0px"
  			document.getElementById('mysitenav').style.opacity ="0"
  		}

  		// function createsectionclone() {
  		// 	alert('asd');
  		// 	alert(clone);
  		// 	$(this).parents('table').find('tbody').appendTo(clone);
  		// }
  		/*$(document).ready(function(){
  			var clone = '<tr><td class="text-left"><input type="text" name="section_title[]" placeholder="Demo Section" class="form-control" required></td><td class="text-right action"><a href="javascript:;" class="remove"><i class="far fa-trash-alt"></i></a></td></tr>';
  			$('.add_sectionclone').click(function(){
  				alert('asd');
  				$('#clonediv').appendTo('<a href="javascript:;" class="remove"><i class="far fa-trash-alt"></i></a>');
  			});
  			$('.remove').on("click", function() {
			  $(this).parents("tr").remove();
			});
  		})*/
  	</script>

  		@if($errors->has('email') || $errors->has('phone_number') || $errors->has('password') || $errors->has('confirmed'))
  		<script>
  			$(window).on('load',function(){
		        $('#add_template').modal('show');
		    });
  		</script>
  		@endif
  		<script type="text/javascript">
		function editpact_temp_vals(title,type,sections,id,clauses,status) {
			console.log(type);
			$('#edit_template input[name="title"]').val(title);
			$('#edit_template #type').val(type);
			$('#edit_template input[name="sections"]').val(sections);
			$('#edit_template input[name="template_editid"]').val(id);
			$('#edit_template input[name="clauses"]').val(clauses);
			$('#edit_template #status').val(status);
		}
	</script>
	<script type="text/javascript" src="//code.jquery.com/jquery-2.1.0.js"></script>
	<script type="text/javascript">//<![CDATA[

		$(window).load(function(){



		jQuery(document).ready(function() {
			var id = 0;
			jQuery("#addrow").click(function() {
				id++;           
				var row = jQuery('.samplerow tr').clone(true);
				row.find("input:text").val("");
				row.attr('id',id); 
				row.appendTo('#createsectionclone');        
				return false;
			});        

			$('.remove').on("click", function() {
				$(this).parents("tr").remove();
			});

			/****************************/
			var id = 0;
			jQuery(".addrow_clouses").on('click',function() {
				id++;  
				var ids = $(this).data('value'); 
				// alert($('.clouses-div-'+ids).size());
				var clonepart = '<div class="row clauses-row"><div class="col-lg-4 col-md-4 col-12"><div class="clause-input-block"><div class="form-group"><input type="text" class="form-control theme-form-control" name="clause_heading['+ids+'][]" id="usr" placeholder="Clause Heading"></div></div></div><div class="col-lg-7 col-md-7 col-12"><div class="clause-input-block"><div class="form-group"><input type="text" class="form-control theme-form-control" name="clause_body['+ids+'][]" id="usr" placeholder="Clause Body"></div></div></div><div class="col-lg-1 col-md-1 col-12"><div class="remove-btn"><span class="remove1"><i class="far fa-trash-alt"></i></span></div></div></div>';       
				//var row = jQuery('#createclousesclone .row').clone(true);
				$('.clouses-div-'+ids).append(clonepart);        
				return false;
			});        

			$('body').on("click",'.remove1', function() {
				// alert($(this).parents(".row").addClass('class'));
				$(this).parents(".clauses-row").remove();
			});
		});


		});

		//]]>
	</script>
	<script type="text/javascript">
  		$SIDEBAR_MENU = $('#sitebar-nav');
  		$SIDEBAR_MENU.find('a').on('click', function(ev) {
		  console.log('clicked - sidebar_menu');
	   //      var $li = $(this).parent();

	   //      if ($li.is('.active')) {
	   //          $li.removeClass('active active-sm');
	   //          $('ul:first', $li).slideUp(function() {
	   //              setContentHeight();
	   //          });
	   //      } else {
	   //          // prevent closing menu if we are on child menu
	   //          if (!$li.parent().is('.child_menu')) {
	   //              $SIDEBAR_MENU.find('li').removeClass('active active-sm');
	   //              $SIDEBAR_MENU.find('li ul').slideUp();
	   //          }else
	   //          {
				// 	if ( $BODY.is( ".nav-sm" ) )
				// 	{
				// 		$SIDEBAR_MENU.find( "li" ).removeClass( "active active-sm" );
				// 		$SIDEBAR_MENU.find( "li ul" ).slideUp();
				// 	}
				// }
	   //          $li.addClass('active');

	   //          $('ul:first', $li).slideDown(function() {
	   //              setContentHeight();
	   //          });
	   //      }
	    });
  	</script>
</body>
</html>
