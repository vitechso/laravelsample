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
							<h4>CR Builders</h4>
							<h5>Company</h5>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="pacts-info">

							<!-- Nav tabs -->
							<ul class="nav nav-tabs custom-nav-tabs ">
							    <li class="nav-item">
							      <a class="nav-link active" data-toggle="tab" href="#home">Company</a>
							    </li>
							    <!-- <li class="nav-item">
							      <a class="nav-link" data-toggle="tab" href="#menu1">SIGNEES</a>
							    </li> -->
						  	</ul>

						  	<!-- Tab panes -->
						  	<div class="tab-content bg-lite">
							    <div id="home" class="container-fluid tab-pane active">
							    	<div class="row">
							    		<div class="col-lg-12 col-md-12 col-12 pad-0">
							    			<div class="file-nav-block">

							    				<a  href="#add_admin" data-toggle="modal" class="file-block">
													<img src="{{ URL::asset('assets/front/svg/user-2.svg') }}">
												</a>

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
												        <th>User</th>
												        <th class="text-center">Phone</th>
												        <th class="text-center">Email</th>
												        <th class="text-center">Date Added</th>
												        <th class="text-center">Lost Activity</th>
												        <th class="text-center">Status</th>
												        <th class="text-center">Action</th>
											      	</tr>
											    </thead>

											    <tbody>
											    	@if(isset($users) && count($users)>0)
											    	@php $k = 0; @endphp
											    	@foreach($users as $user)
											    	@if($user->hasRole('company'))
											    	@php $k+=1; @endphp
											      	<tr>
												        <td>
												        	<div class="custom-control custom-checkbox">
					    										<input type="checkbox" class="custom-control-input" id="customCheck5" name="example1">
					    										<label class="custom-control-label" for="customCheck5"><span class="check-adjust">{{$user->name}}</span></label>
					  										</div>
					  									</td>

												        <td class="text-center">{{$user->phone_number}} </td>
												        <td class="text-center">{{$user->email}}</td>
												        <td class="text-center">{{$user->created_at}}</td>
												        <td class="text-center view-detail"><a href="#" data-toggle="modal" data-target="#pact_assignments">View</a></td>
												        <td class="text-center status">Active<i class="fas fa-chevron-down"></i></td>
												        <td class="text-center action">
												        	<a href="{{ route('admin.users.edit', [$user->id]) }}"><i class="fas fa-pencil-alt"></i></a>
												        	<a href="{{ route('admin.users.destroy', [$user->id]) }}"><i class="far fa-window-close"></i></a>
												        </td>
											      	</tr>
											      	@endif
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
	<div class="modal fade" id="add_admin">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i>Add <span>Admin</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
		        	<div class="row">
        				{{ Form::open(['route'=>['admin.company'],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left','style'=>'width:100%;']) }}
        				<input type="hidden" name="role" value="4">
		        		<div class="col-lg-6 col-md-6 col-12">
		        			<div class="add-admin-block">
									<div class="form-group lock">
										<i class="fas fa-user"></i>
									    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('name')) parsley-error @endif"" placeholder="{{ __('views.admin.users.show.table_header_1') }}" id="name" name="name" required>
									    @if($errors->has('name'))
				                            <ul class="parsley-errors-list filled">
				                                @foreach($errors->get('name') as $error)
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
								<!-- </form> -->
		        			</div>
		        		</div>

		        		<div class="col-lg-6 col-md-6 col-12">
		        			<div class="add-admin-block">
		        				<!-- <form action=""> -->
									<!-- <div class="select form-group">
										<select name="slct" id="slct" class="form-control custom-color hight-adjust">
									    	<option selected disabled>Admin Type</option>
									    	<option value="1">1</option>
									    	<option value="2">2</option>
									    	<option value="3">3</option>
									  </select>
									</div> -->

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
						{{ Form::close() }}
		        	</div>
		        </div>
	      	</div>
	    </div>
	</div>
	<!-- Add admin Modal -->


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
		        $('#add_admin').modal('show');
		    });
  		</script>
  		@endif
</body>
</html>
