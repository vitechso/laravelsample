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
							<h5>Pacts</h5>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="pacts-info">

							<!-- Nav tabs -->
							<ul class="nav nav-tabs custom-nav-tabs ">
							    <li class="nav-item">
							      <a class="nav-link " href="{{route('company.pactall')}}">Pacts</a>
							    </li>
							    <li class="nav-item">
							      <a class="nav-link active" href="#menu1">Templates</a>
							    </li>
						  	</ul>

						  	<!-- Tab panes -->
						  	<div class="tab-content bg-lite">
							    <div id="home" class="container-fluid tab-pane active">
							    	<div class="row">
							    		<div class="col-lg-12 col-md-12 col-12 pad-0">
							    			<div class="file-nav-block">
							    				<!-- <a  href="{{route('company.pact')}}" id="plusadmin" class="file-block plusmodel">
													<img src="{{ URL::asset('assets/front/svg/user-2.svg') }}">
												</a> -->
							    				

												<!-- <a  href="{{ route('admin.users.add-admin')}}" class="file-block">
													<img src="{{ URL::asset('assets/front/svg/user-2.svg') }}">
												</a> -->
											</div>
							    		</div>
							    	</div>
									<div class="row">
										<div class="col-lg-12 col-md-12 col-12 pad-0">
											<table class="table my-table-striped table-responsive inline-table custom-table">
											    <thead>
											      	<tr>
												        <th class="w300">{{ __('views.admin.pact.template.title') }}</th>
												        <th class="text-center">{{ __('views.admin.pact.template.type') }}</th>
												        <th class="text-center">{{ __('views.admin.pact.template.sections') }}</th>
												        <th class="text-center">{{ __('views.admin.pact.template.clauses') }}</th>
												        <!-- <th class="text-center">Lost Activity</th> -->
												        <th class="text-center">{{ __('views.admin.pact.template.status') }}</th>
												        <th class="text-center">{{ __('views.admin.pact.template.action') }}</th>
											      	</tr>
											    </thead>

											    <tbody>
											    	@if(isset($pacttemplate) && count($pacttemplate)>0)
											    	@php $k = 0; @endphp
											    	@foreach($pacttemplate as $pact_temp_val)
											    	@php $k+=1; @endphp
											      	<tr>
												        <td>{{$pact_temp_val->title}}
												        	<!-- <div class="custom-control custom-checkbox">
					    										<input type="checkbox" class="custom-control-input" id="customCheck5" name="example1">
					    										<label class="custom-control-label" for="customCheck5"><span class="check-adjust"></span></label>
					  										</div> -->
					  									</td>

												        <td class="text-center">{{$pact_temp_val->type}} </td>
												        <td class="text-center">{{count(explode(",",$pact_temp_val->section_title))}}
												        </td>
												        <td class="text-center">{{count(json_decode($pact_temp_val->clause_body,true))}}</td>
												        <!-- <td class="text-center view-detail"><a href="#" data-toggle="modal" data-target="#pact_assignments">View</a></td> -->
												        <td class="text-center status">@if($pact_temp_val->active==1) Active @else Inactive @endif</td>
												        <td class="text-center action">
												        	<a  href="{{route('company.pact_templateview',[$pact_temp_val->id])}}" title="view template"><i class="fas fa-eye"></i></a>
												        	<a href="{{route('company.pactexport',[$pact_temp_val->id])}}" title="export template"><i class="fas fa-file-export"></i></a>
												        </td>
											      	</tr>
											      
											      	@endforeach
											      	@if($k==0)
											      	<tr><th colspan="10" class="text-center">No record found</th></tr>
											      	@endif
											      	@else
											      	<tr><th colspan="10" class="text-center">No record found</th></tr>
											      	@endif
											    </tbody>
										  	</table>
										</div>
									</div>
							    </div>

							    
						  	</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- Add admin Modal -->
	<div class="modal fade" id="add_template">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i>Add <span>Template</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
        			{{ Form::open(['route'=>['admin.pact-template.add'],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left','style'=>'width:100%;']) }}
		        	<div class="row">
        				<input type="hidden" name="role" value="4">
		        		<div class="col-lg-6 col-md-6 col-12">
		        			<div class="add-admin-block">
								<div class="form-group lock">
									<!-- <i class="fas fa-user"></i> -->
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('title')) parsley-error @endif"" placeholder="{{ __('views.admin.pact.template.title') }}" id="title" name="title" required>
								    @if($errors->has('title'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('title') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>

								<div class="form-group lock">
									<i class="fas fa-envelope"></i>
								    <div class="select form-group">

										<select name="type" id="type" class="form-control custom-color hight-adjust" required>
									    	<option selected disabled value="">{{ __('views.admin.pact.template.type')}}</option>
											<option value="hr">Hr</option>
											<option value="legal">Legal</option>
											<option value="safety">Safety</option>
											<option value="security">Security</option>
											<option value="personal">Personal</option>
									  </select>
									</div>
								    @if($errors->has('type'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('type') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
								
								
		        			</div>
		        		</div>

		        		<div class="col-lg-6 col-md-6 col-12">
		        			<div class="add-admin-block">
		        				<div class="form-group lock">
									<!-- <i class="fas fa-phone-square"></i> -->
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('sections')) parsley-error @endif" placeholder="{{ __('views.admin.pact.template.sections') }}" id="sections" name="sections"  required>
			                        @if($errors->has('sections'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('sections') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
								<div class="form-group lock">
									<i class="fas fa-envelope"></i>
								    <div class="select form-group">
										<select name="status" id="status" class="form-control custom-color hight-adjust" required>
									    	<option selected disabled value="">Status</option>
											<option value="1">Active</option>
											<option value="0">Inactive</option>
									  </select>
									</div>
								    
								</div>
		        			</div>
		        		</div>

		        		<div class="col-lg-12 col-md-12 col-12">
		        			<div class="button-block">
								<button type="button" class="btn custom-btn"><span class="check"><i class="fas fa-check"></i></span>CANCEL</button>
			  					<button type="submit" class="btn custom-btn check-main"><span class="check"><i class="fas fa-check"></i></span>SAVE</button>
							</div>
		        		</div>
		        	</div>
					{{Form::close()}}
		        </div>
	      	</div>
	    </div>
	</div>
	<!-- Add admin Modal -->

	<!-- Add admin Modal -->
	
	<!-- Add admin Modal -->
	<!-- Edit admin Modal -->
	<div class="modal fade" id="edit_admin123">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i>Edit <span>Template</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
	        		
    				{{ Form::open(['route'=>['admin.users.update'],'method' => 'put','class'=>'user-add-form form-horizontal form-label-left']) }}
		        	<div class="row">
        				<input type="hidden" name="role" value="2">
        				<input type="hidden" name="user_editid" value="">
		        		<div class="col-lg-12 col-md-12 col-12">
		        			<div class="add-admin-block">
								<div class="form-group lock">
									<i class="fas fa-user"></i>
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('title')) parsley-error @endif"" placeholder="{{ __('views.admin.users.show.table_header_1') }}" id="title" name="title" required>
								    @if($errors->has('title'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('title') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>

								<div class="form-group lock">
									<i class="fas fa-envelope"></i>
								    <input type="email" class="form-control custom-color place-adjuct-center @if($errors->has('email')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.email') }}" id="email" name="email" required>
								    @if($errors->has('email'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('email') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>

								<div class="form-group lock">
									<i class="fas fa-phone-square"></i>
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('phone_number')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.phone') }}" id="phone" name="phone_number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
			                        @if($errors->has('phone_number'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('phone_number') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
								<div class="select form-group">
									<select name="status" id="editslct" class="form-control custom-color hight-adjust" required>
								    	<option selected disabled value="">Status</option>
								    	<option value="1">Active</option>
								    	<option value="0">Inactive</option>
								  </select>
								</div>
		        			</div>
		        		</div>

		        		<div class="col-lg-6 col-md-6 col-12" style="display: none;">
		        			<div class="add-admin-block">

								<div class="form-group lock">
								    <i class="fas fa-unlock-alt"></i>
								    <input type="password" class="form-control custom-color place-adjuct-center @if($errors->has('password')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.password') }}" id="password" name="password">
								    @if($errors->has('password'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('password') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>

								<div class="form-group lock">
									<i class="fas fa-unlock-alt"></i>
								    <input type="password" class="form-control custom-color place-adjuct-center @if($errors->has('confirmed')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.confirmed') }}" id="password" name="confirmed">
								    @if($errors->has('confirmed'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('confirmed') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
		        			</div>
		        		</div>

		        		<div class="col-lg-12 col-md-12 col-12">
		        			<div class="button-block">
								<button type="button" class="btn custom-btn"><span class="check"><i class="fas fa-check"></i></span>CANCEL</button>
			  					<button type="submit" class="btn custom-btn check-main"><span class="check"><i class="fas fa-check"></i></span>SAVE</button>
							</div>
		        		</div>
		        	</div>
					{{ Form::close() }}
		        </div>
	      	</div>
	    </div>
	</div>
	<!-- edit admin Modal -->


	<!-- The Modal -->
  	<div class="modal fade" id="pact_assignments">
    	<div class="modal-dialog modal-lg">
      		<div class="modal-content">
      
	        	<!-- Modal Header -->
	        	<div class="modal-header pact_assignments_header">
	          		<div class="pact_assignments_heading">
						<h4>Pact Assignments</h4>
						<h5>Ian Barkley</h5>
					</div>
	          		<button type="button" class="close close-btn" data-dismiss="modal">×</button>
	        	</div>
        
	        	<!-- Modal body -->
	        	<div class="modal-body">
	          		<div class="row">
	          			<div class="col-lg-12 col-md-12 col-12">
	          				<table class="table my-table-striped table-responsive inline-table custom-table">
							    <thead>
							      	<tr>
								        <th>Pact Name</th>
								        <th class="text-center">Assigned On</th>
								        <th class="text-center">Status</th>
								        <th class="text-center">Last Activity</th>
								        <th class="text-center">Action</th>
								        <th class="text-center"></th>
							      	</tr>
							    </thead>

							    <tbody>
							      	<tr>
								        <td>
								        	<div class="custom-control custom-checkbox">
	    										<input type="checkbox" class="custom-control-input" id="modelCheck" name="example1">
	    										<label class="custom-control-label" for="modelCheck"><span class="check-adjust">Manager Safety</span></label>
	  										</div>
	  									</td>

								        <td class="text-center">11/23/19</td>
								        <td class="text-center">Unsigned</td>
								        <td class="text-center">12/25/19</td>
								        <td class="text-center status">Re-send<i class="fas fa-chevron-down"></i></td>
								        <td class="text-center action-btn">
								        	<button type="submit">Update</button>
								        </td>
							      	</tr>

							      	<tr>
								        <td>
								        	<div class="custom-control custom-checkbox">
	    										<input type="checkbox" class="custom-control-input" id="modelCheck2" name="example1">
	    										<label class="custom-control-label" for="modelCheck2"><span class="check-adjust">Manager Safety</span></label>
	  										</div>
	  									</td>

								        <td class="text-center">11/23/19</td>
								        <td class="text-center">Unsigned</td>
								        <td class="text-center">12/25/19</td>
								        <td class="text-center status">Re-send<i class="fas fa-chevron-down"></i></td>
								        <td class="text-center action-btn">
								        	<button type="submit">Update</button>
								        </td>
							      	</tr>

							      	<tr>
								        <td>
								        	<div class="custom-control custom-checkbox">
	    										<input type="checkbox" class="custom-control-input" id="modelCheck3" name="example1">
	    										<label class="custom-control-label" for="modelCheck3"><span class="check-adjust">Manager Safety</span></label>
	  										</div>
	  									</td>

								        <td class="text-center">11/23/19</td>
								        <td class="text-center">Unsigned</td>
								        <td class="text-center">12/25/19</td>
								        <td class="text-center status">Re-send<i class="fas fa-chevron-down"></i></td>
								        <td class="text-center action-btn">
								        	<button type="submit">Update</button>
								        </td>
							      	</tr>

							      	<tr>
								        <td>
								        	<div class="custom-control custom-checkbox">
	    										<input type="checkbox" class="custom-control-input" id="modelCheck4" name="example1">
	    										<label class="custom-control-label" for="modelCheck4"><span class="check-adjust">Manager Safety</span></label>
	  										</div>
	  									</td>

								        <td class="text-center">11/23/19</td>
								        <td class="text-center">Unsigned</td>
								        <td class="text-center">12/25/19</td>
								        <td class="text-center status">Re-send<i class="fas fa-chevron-down"></i></td>
								        <td class="text-center action-btn">
								        	<button type="submit">Update</button>
								        </td>
							      	</tr>
							    </tbody>
						  	</table>
	          			</div>
	          		</div>
	        	</div>
        
	        	<!-- Modal footer -->
	        	<!-- <div class="modal-footer">
	          		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	        	</div> -->
      		</div>
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

</body>
</html>
