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
							<h5>Users</h5>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="pacts-info">

							<!-- Nav tabs -->
							<ul class="nav nav-tabs custom-nav-tabs ">
							    <li class="nav-item">
							      <a class="nav-link active" tab_name="Users" data-toggle="tab" href="#home">Admin Users</a>
							    </li>
							    <li class="nav-item">
							      <a class="nav-link" tab_name="Signees"  data-toggle="tab" href="#menu1">SIGNEES</a>
							    </li>


						  	</ul>

						  	<!-- Tab panes -->
						  	<div class="tab-content bg-lite">
							    <div id="home" class="container-fluid tab-pane active">
							    	<div class="row">
							    		<div class="col-lg-12 col-md-12 col-12 pad-0">
							    			<div class="file-nav-block">

							    				<a  href="#add_admin" id="plusadmin" data-toggle="modal" class="file-block plusmodel">
													<img src="{{ URL::asset('assets/front/svg/user-2.svg') }}">
												</a>

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
												        <th class="text-center">Last Activity</th>
												        <th class="text-center">Status</th>
												        <th class="text-center">Action</th>
											      	</tr>
											    </thead>

											    <tbody>
											    	@if(isset($users) && count($users))
											    	@foreach($users as $user)
											    	
											    	@if($user->roles[0]->id==2)
											      	<tr>
												        <td>
												        	<div class="custom-control custom-checkbox">
					    										<input type="checkbox" class="custom-control-input" id="customCheck{{$user->id}}" name="example1">
					    										<label class="custom-control-label" for="customCheck{{$user->id}}"><span class="check-adjust">{{$user->name}}</span></label>
					  										</div>
					  									</td>

												        <td class="text-center">{{$user->phone_number}} </td>
												        <td class="text-center">{{$user->email}}</td>
												        <td class="text-center">{{date("m-d-Y H:i:s",strtotime($user->created_at))}}

												        </td>
												        <td class="text-center view-detail">
												        	
												        	{{date("m-d-Y H:i:s",strtotime($user->last_login))}}
												        	<!-- <a href="#" data-toggle="modal" data-target="#pact_assignments1">View</a> -->
												        </td>
												        <td class="text-center status">{{ ($user->active==1) ? 'Active' : 'Inactive'}}<!-- <i class="fas fa-chevron-down"></i> --></td>
												        <td class="text-center action">
												        	<a  href="#edit_admin" data-toggle="modal" class="file-block" onclick="editusers(`{{$user->name}}`,`{{$user->phone_number}}`,`{{$user->email}}`,`{{$user->id}}`,`{{$user->active}}`)">
												        	<i class="fas fa-pencil-alt"></i></a>
												        	<a href="{{ route('company.destroy', [$user->id]) }}"><i class="far fa-window-close"></i></a>
												        </td>
											      	</tr>
											      	@endif
											      	@endforeach
											      	@else
											      	<tr><th colspan="10" style="text-align: center;">No Record Found!</th></tr>
											      	@endif
											      	
											    </tbody>
										  	</table>
										  	
										</div>
									</div>
							    </div>

							    <div id="menu1" class="container-fluid tab-pane fade">
							    	<div class="row">
							    		<div class="col-lg-12 col-md-12 col-12 pad-0">
							    			<div class="file-nav-block">
												<a  href="#add_signees" data-toggle="modal" class="file-block plusmodel">
													<img src="{{ URL::asset('assets/front/svg/user-2.svg') }}">
												</a>
												<a  href="#assign_signees" data-toggle="modal" class="btn custom-btn check-main assign_signees">
													<span class="check"><i class="fas fa-check"></i></span>
													Assign
												</a>
											</div>
							    		</div>
							    	</div>

									<div class="row">
										<div class="col-lg-12 col-md-12 col-12 pad-0">
											<table class="table my-table-striped table-responsive inline-table custom-table">
											    <thead>
											      	<tr>
												        <th>Users</th>
												        <th class="text-center">Phone</th>
												        <th class="text-center">Email</th>
												        <th class="text-center">Date Added</th>
												        <th class="text-center">Pact Assignments</th>
												        <th class="text-center">Status</th>
												        <th class="text-center">Action</th>
											      	</tr>
											    </thead>

											    <tbody>
											    	@if(isset($users) && count($users))
											    	@foreach($users as $user)
											    	@if($user->roles[0]->id==3)
											      	<tr>
												        <td>
												        	<div class="custom-control custom-checkbox">
					    										<input type="hidden" name="admin_id" class="custom-control-input" value="{{auth()->user()->id}}">
					    										<input type="checkbox" name="signees_id" class="custom-control-input signees" id="customCheck{{$user->id}}" value="{{$user->id}}">
					    										<label class="custom-control-label" for="customCheck{{$user->id}}"><span class="check-adjust">{{$user->name}}</span></label>
					  										</div>
					  									</td>

												        <td class="text-center">{{$user->phone_number}}</td>
												        <td class="text-center">{{$user->email}}</td>
												        <td class="text-center">
                                                        {{ date("m-d-Y H:i:s",strtotime($user->created_at)) }}
												        </td>
												        <td class="text-center view-detail"><a href="#" data-toggle="modal" class="pact_assignments" data-target="#pact_assignments">View</a></td>
												        <td class="text-center status">
												        	{{ ($user->active==1) ? 'Active' : 'Inactive'}}<!-- <i class="fas fa-chevron-down"></i> --></td>
												        <td class="text-center action">
												        	<a  href="#edit_signees" data-toggle="modal" class="file-block" onclick="editusers(`{{$user->name}}`,`{{$user->phone_number}}`,`{{$user->email}}`,`{{$user->id}}`,`{{$user->active}}`,`{{$user->pin}}`)">
												        	<i class="fas fa-pencil-alt"></i></a>
												        	<a href="{{ route('company.destroy', [$user->id]) }}"><i class="far fa-window-close"></i></a>
												        </td>
											      	</tr>
											      	@endif
											      	@endforeach
											      	@else
											      	<tr><th colspan="10" style="text-align: center;">No Record Found!</th></tr>
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
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i><span class="textadd">Add</span> <span>Admin</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
	        		{{$errors->has('role')}}
    				{{ Form::open(['route'=>['company.add'],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left']) }}
		        	<div class="row">
        				<input type="hidden" name="role" value="2">
		        		<div class="col-lg-6 col-md-6 col-12">
		        			<div class="add-admin-block">
								<div class="form-group lock">
									<i class="fas fa-user"></i>
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('name')) parsley-error @endif"" placeholder="{{ __('views.admin.users.show.table_header_1') }}" id="name" name="name" value="{{old('name')}}" required>
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
								    <input type="email" class="form-control custom-color place-adjuct-center @if($errors->has('email')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.email') }}" id="email" name="email" value="{{old('email')}}" required>
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
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('phone_number')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.phone') }}" id="phone" name="phone_number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="{{old('phone_number')}}" required>
			                        @if($errors->has('phone_number'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('phone_number') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
		        			</div>
		        		</div>

		        		<div class="col-lg-6 col-md-6 col-12">
		        			<div class="add-admin-block">
								<div class="select form-group">
									<select name="status" id="slct" class="form-control custom-color hight-adjust popup-select-block" required>
								    	<option selected disabled value="">Status</option>
								    	<option value="1">Active</option>
								    	<option value="0">Inactive</option>
								  </select>
								</div>

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
	<!-- Add admin Modal -->

	<!-- Edit admin Modal -->
	<div class="modal fade" id="edit_admin">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i>Edit <span>Admin</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
	        		
    				{{ Form::open(['route'=>['company.edit'],'method' => 'put','class'=>'user-add-form form-horizontal form-label-left']) }}
		        	<div class="row">
        				<input type="hidden" name="role" value="2">
        				<input type="hidden" name="user_editid" value="">
		        		<div class="col-lg-12 col-md-12 col-12">
		        			<div class="add-admin-block">
								<div class="form-group lock">
									<i class="fas fa-user"></i>
								    <input type="text" class="form-control custom-color place-adjuct-center pop-adjst @if($errors->has('name')) parsley-error @endif"" placeholder="{{ __('views.admin.users.show.table_header_1') }}" id="name" name="name" required>
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
								    <input type="email" class="form-control custom-color place-adjuct-center pop-adjst @if($errors->has('email')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.email') }}" id="email" name="email" required>
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
								    <input type="text" class="form-control custom-color place-adjuct-center pop-adjst @if($errors->has('phone_number')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.phone') }}" id="phone" name="phone_number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
			                        @if($errors->has('phone_number'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('phone_number') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
								<div class="select form-group">
									<!-- <label class="text-white">Status</label> -->
									<select name="status" id="slct-edit" class="form-control custom-color hight-adjust popup-select-block" required>
								    	<option value=""></option>
								    	<option value="1">Active</option>
								    	<option value="0">Inactive</option>
								  </select>
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

	<!-- Add admin Modal -->
	<div class="modal fade" id="add_signees">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i><span class="textadd">Add</span> <span>Signees</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
        			{{ Form::open(['route'=>['company.add'],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left']) }}
		        	<div class="row">
        				<input type="hidden" name="role" value="3">
		        		<div class="col-lg-6 col-md-6 col-12 other-field-section">
		        			<div class="add-admin-block">
								<div class="form-group lock">
									<i class="fas fa-user"></i>
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('name')) parsley-error @endif"" placeholder="{{ __('views.admin.users.show.table_header_1') }}" id="name" name="name" value="{{ old('name') }}" required>
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
								    <input type="email" class="form-control custom-color place-adjuct-center @if($errors->has('email')) parsley-error @endif" placeholder="signees Email" id="email" name="email" value="{{ old('email') }}" required>
								    @if($errors->has('email'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('email') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>

								<div class="form-group lock phone_numbervali">
									<i class="fas fa-phone-square"></i>
								    <input type="text" class="form-control custom-color place-adjuct-center @if($errors->has('phone_number')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.phone') }}" id="phone" name="phone_number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="{{ old('phone_number') }}" required>
			                        @if($errors->has('phone_number'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('phone_number') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
		        			</div>
		        		</div>

		        		<div class="col-lg-6 col-md-6 col-12 password-section">
		        			<div class="add-admin-block">
		        				
								<div class="select form-group">
									<select name="status" id="slct" class="form-control custom-color hight-adjust popup-select-block" required>
								    	<option selected disabled value="">Status</option>
								    	<option value="1">Active</option>
								    	<option value="0">Inactive</option>
								  </select>
								</div>

								<div class="form-group lock">
								    <i class="fas fa-unlock-alt"></i>
								    <input type="text" maxlength="6" class="form-control custom-color place-adjuct-center" placeholder="Pin No." value="{{ old('pin') }}" id="pin" name="pin">
								    
								</div>

								
		        			</div>
		        		</div>

		        		<div class="col-lg-12 col-md-12 col-12">
		        			<div class="button-block">
								<button type="button" class="btn custom-btn"><span class="check"><i class="fas fa-check"></i></span>CANCEL</button>
			  					<button type="submit" id="add_signees_btn" class="btn custom-btn check-main"><span class="check"><i class="fas fa-check" ></i></span>SAVE</button>
							</div>
		        		</div>
		        	</div>
					{{ Form::close() }}
		        </div>
	      	</div>
	    </div>
	</div>
	<!-- Add admin Modal -->

	<!-- Add admin Modal -->
	<div class="modal fade" id="assign_signees">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i><span class="textadd">Assign</span> <span>Pact</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        	
		        <!-- Modal body -->
		        <div class="modal-body">
		        	<p class="text-white">You have select  signees for your PACT</p>
        			{{ Form::open(['route'=>['company.assign_userpact'],'method' => 'post','class'=>'user-add-form form-horizontal form-label-left']) }}

		        	<div class="row justify-content-center align-items-center">
        				<input type="hidden" name="user_id" id="assign_userid" value="">
		        		<div class="col-lg-8 col-md-8 other-field-section">
		        			<div class="add-admin-block">
								<div class="form-group lock">
									<label class="text-white">PACT</label>
								    <select class="form-control" name="pact_id">
								    	@foreach($pacttemplate as $pact_value)
								    	<option value="{{$pact_value->id}}">{{$pact_value->title}}</option>
								    	@endforeach
								    </select>
								</div>

								<div class="form-group lock">
									<label class="text-white">Schedule Delivery</label>
								    <select class="form-control" name="schedule_time">
								    	<option value="deliver now">Deliver Now</option>
								    </select>
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
	<!-- Add admin Modal -->

	<!-- Edit admin Modal -->
	<div class="modal fade" id="edit_signees">
	    <div class="modal-dialog popup-center">
	    	<div class="modal-content bg-theme">
	      
		        <!-- Modal Header -->
		        <div class="modal-header add-admin-heading-block">
		        	<h3 class="modal-title"><i class="fas fa-user-plus"></i>Edit <span>Signees</span></h3>
		          	<button type="button" class="close close-btn" data-dismiss="modal">×</button>
		        </div>
	        
		        <!-- Modal body -->
		        <div class="modal-body">
        			{{ Form::open(['route'=>['company.edit'],'method' => 'put','class'=>'user-add-form form-horizontal form-label-left']) }}
		        	<div class="row">
		        		<input type="hidden" name="user_editid" value="">
        				<input type="hidden" name="role" value="3">
		        		<div class="col-lg-12 col-md-12 col-12">
		        			<div class="add-admin-block">
								<div class="form-group lock">
									<i class="fas fa-user"></i>
								    <input type="text" class="form-control custom-color place-adjuct-center pop-adjst @if($errors->has('name')) parsley-error @endif"" placeholder="{{ __('views.admin.users.show.table_header_1') }}" id="name" name="name" required>
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
								    <input type="email" class="form-control custom-color place-adjuct-center pop-adjst @if($errors->has('email')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.email') }}" id="email" name="email" required>
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
								    <input type="text" class="form-control custom-color place-adjuct-center pop-adjst @if($errors->has('phone_number')) parsley-error @endif" placeholder="{{ __('views.admin.users.edit.phone') }}" id="phone" name="phone_number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
			                        @if($errors->has('phone_number'))
			                            <ul class="parsley-errors-list filled">
			                                @foreach($errors->get('phone_number') as $error)
			                                    <li class="parsley-required">{{ $error }}</li>
			                                @endforeach
			                            </ul>
			                        @endif
								</div>
								<div class="select form-group">
									<!-- <label class="text-white">Status</label> -->
									<select name="status" id="slct-edit1" class="form-control custom-color hight-adjust popup-select-block" required>
								    	<option value=""></option>
								    	<option value="1">Active</option>
								    	<option value="0">Inactive</option>
								  </select>
								</div>
								<div class="form-group lock">
								    <i class="fas fa-unlock-alt"></i>
								    <input type="text" maxlength="6" class="form-control custom-color place-adjuct-center" placeholder="Pin No." value="{{ old('pin') }}" id="pin" name="pin">
								    
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
						<h5></h5>
					</div>
	          		<button type="button" class="close close-btn" data-dismiss="modal">×</button>
	        	</div>
        
	        	<!-- Modal body -->
	        	<div class="modal-body">
	          		<div class="row">
	          			<div class="col-lg-12 col-md-12 col-12">
	          				<table class="table pact_assignments-table my-table-striped table-responsive inline-table custom-table">
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
  	{{$errors->has('role')}}
  	@if($errors->has('email') || $errors->has('phone_number') || $errors->has('password') || $errors->has('confirmed'))
  	@if(session('message')==2)
	<script>
		$(window).on('load',function(){
        $('#add_admin').modal('show');
    });
	</script>
	@else
	<script>
		$(window).on('load',function(){
        $('#add_signees').modal('show');
    });
	</script>
	@endif
	@endif
	<script type="text/javascript">
		function editusers(name,phone_number,email,id,status,pin) {
			// console.log(name+email+phone_number+id+status);
			// alert(status);
			$('input[name="name"]').val(name);
			$('input[name="email"]').val(email);
			$('input[name="phone_number"]').val(phone_number);
			$('input[name="user_editid"]').val(id);
			$('input[name="pin"]').val(pin);
			$('#slct-edit1').val(status);
			$('#slct-edit').val(status);
		}
		$(document).ready(function(){
			//var returnval = false;
			/*$('#add_signees_btn').click(function(){
				var phone_number = $('#add_signees #phone').val();
				console.log(phone_number);
				$('.phone_numbervali').find('.error-phone').remove();
				if(phone_number!=''){
					$.ajax({
			            type: "post",
			            url: "{{ route('company.checkphonenumber') }}",
			            data:{coloumn:"to",_token:"{{ csrf_token() }}",'phone_number':phone_number},
			            async: true,
			          }).done(function(resp) {
			          	if(resp>1){
			          		$('.phone_numbervali').append('<span class="text-danger error-phone">Phone number already in use.</span>');
			          		return false;
				// return returnval;
			          	}else{
			          		return true;

			          	}
			   //        	else{
						// return true;

			   //        	}
			          	console.log(resp);
			          });

				}
				//return returnval;
			})*/
			$('.pact_assignments').click(function(){
	            $('.pact_assignments-table').find('tbody').html('');
	            $('#pact_assignments .pact_assignments_heading').find('h5').text('');
				// console.log("{{ route('company.pactshowlist') }}");
				var user_id = $(this).parent().parent().find('input[type="checkbox"]').val();
				var adminid = $(this).parent().parent().find('input[name="admin_id"]').val();
				// console.log(user_id+adminid);
				$.ajax({
		            type: "post",
		            url: "{{ route('company.pactshowlist') }}",
		            data:{coloumn:"to",_token:"{{ csrf_token() }}",'admin_id':adminid,'user_id':user_id},
		            async: true,
		          }).done(function(resp) {
						    console.log(resp);
		            //console.log(resp.length);
		            if(resp.length>0){
			            var table_data = '';
			            $('#pact_assignments .pact_assignments_heading').find('h5').text(resp[0].signee_name);
			            $.each(resp, function(i, item) {
						    //var sigstatus = '<a href="{{ URL::asset("assets/images/") }}/'+resp[i].sig_name+'">signed</a>';
						    var sigstatus = 'unsigned';
						    var sigbtn = 'button';
						    if(resp[i].sig_name!='' && resp[i].sig_name!=null){
								sigstatus = '<a href="{{ URL::asset("storage/signature_image/") }}/'+resp[i].sig_name+'" target="_blank">signed</a>';
								sigbtn = 'submit';
							}
						    $('.pact_assignments-table').find('tbody').append('<tr><td>'+resp[i].title+'</td><td class="text-center">'+resp[i].created_at+'</td><td class="text-center">'+sigstatus+'</td><td class="text-center">Last Activity</td><td class="text-center status">Re-send<i class="fas fa-chevron-down"></i></td><td class="text-center action-btn"><form action="{{route("company.assign_userpact")}}" method="post"><input type="hidden" name="_token" value="{{ csrf_token()}}"/><input type="hidden" name="user_id" value="'+resp[i].user_id+'"/><input type="hidden" name="pact_id" value="'+resp[i].pact_id+'"/><button type="'+sigbtn+'">Update</button></form></td></tr>');
						});
			        }else{

			        	$('.pact_assignments-table').find('tbody').append('<tr><th colspan="10">No record found</th></tr>');
			        }
					// console.log(table_data);
					// $('#pact_assignments').find('table').append();
		          });	
				// admin_id
			});

			$('.plusmodel').click(function(){
				var modelid = ($(this).attr('href'));
				$(modelid).find('input#name').val('');
				$(modelid).find('input#email').val('');
				$(modelid).find('input#phone').val('');
				$(modelid).find('input#pin').val('');
			});

			var chkvalue = [];
			$('.assign_signees').click(function(){
				var n = $( "input.signees:checked" ).length;
				if(n==0){
					alert('No signees selected');
					return false;
				}else{
				// alert(n);
				for(var i=0;i<n;i++){
					chkvalue.push($($("input.signees:checked")[i]).val());
					// chkvalue.push($('.signees').val());
			    	// console.log('4. checked');

				}
				$('#assign_userid').val(chkvalue);
			    
				console.log(chkvalue);
				}
			});

			$('.nav-link').click(function(){
		         var tab_name=$(this).attr("tab_name");
		         
				$(".page-heading h5").html(tab_name);
				
			});

		});
		
	</script>
</body>
</html>
