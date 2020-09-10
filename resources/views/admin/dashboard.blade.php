@include('admin.include.header')
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
							<h5>Company Pacts</h5>
						</div>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="page-Description">
							<img src="{{ URL::asset('assets/front/svg/desh-i-logo.svg') }}">
							<!-- <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
								xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 							viewBox="-287 378 37 37" style="enable-background:new -287 378 37 37;" xml:space="preserve">
								<style type="text/css">
									.st0{fill:#C6CCD2;}
									.st1{fill:#3E404F;}
								</style>
								<circle class="st0" cx="-268.5" cy="396.5" r="18.5"/>
								<path class="st1" d="M-266.1,406.3c-0.7,0.9-1.6,1.3-2.7,1.3c-1.1,0-1.9-0.3-2.6-0.8c-0.6-0.6-0.9-1.3-0.9-2.2
									c0-0.3,0.2-1.6,0.6-3.9l1.2-6.4h-1.5l0.1-0.6h1.5c1.8,0,3.1-0.1,3.9-0.2l0.5-0.1l-2.5,13.6c0.7-0.1,1.3-0.4,1.8-1L-266.1,406.3z
									 M-269.2,389.8c-0.5-0.5-0.8-1.1-0.8-1.8s0.3-1.3,0.8-1.8c0.5-0.5,1.1-0.8,1.8-0.8s1.3,0.3,1.8,0.8c0.5,0.5,0.8,1.1,0.8,1.8
									s-0.3,1.3-0.8,1.8c-0.5,0.5-1.1,0.8-1.8,0.8S-268.7,390.3-269.2,389.8z"/>
							</svg> -->


							<p>Here is your List of Pacts.  If you wish to add additional Pacts, please click on the icon below. The action icons on the right side of the Pact allow you to preview, edit, and delete the Pact.</p>
						</div>
					</div>
				</div>

				<div class="row mt-2 mb-2">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="file-nav-block">
							<div class="file-block">
								<img src="{{ URL::asset('assets/front/svg/file-text2.svg') }}">
								<!-- <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
									xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 								viewBox="-286 377 39 39" style="enable-background:new -286 377 39 39;" xml:space="preserve">
									<style type="text/css">
										.st0{fill:#333645;}
										.st1{fill:none;stroke:#FFFFFF;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
										.st2{fill:none;stroke:#FFFDFC;stroke-linecap:round;stroke-linejoin:round;}
									</style>
									<circle class="st0" cx="-266.5" cy="396.5" r="19.5"/>
									<g>
										<path class="st1" d="M-265.8,387.8h-7c-0.5,0-0.9,0.2-1.2,0.5s-0.5,0.8-0.5,1.2v14c0,0.5,0.2,0.9,0.5,1.2c0.3,0.3,0.8,0.5,1.2,0.5
											h10.5c0.5,0,0.9-0.2,1.2-0.5c0.3-0.3,0.5-0.8,0.5-1.2V393L-265.8,387.8z"/>
										<path class="st1" d="M-265.8,387.8v5.2h5.3"/>
										<path class="st1" d="M-264,397.4h-7"/>
										<path class="st1" d="M-264,400.9h-7"/>
										<path class="st1" d="M-269.3,393.9h-0.9h-0.9"/>
									</g>
									<g>
										<path class="st2" d="M-260.5,386.5h5"/>
										<path class="st2" d="M-258,384v5"/>
									</g>
								</svg>
 -->
							</div>

							<div class="filter-btn">
								<ul>
									<li><a href="#">All</a></li>
									<li class="active"><a href="#">Active</a></li>
									<li><a href="#">Inactive</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-12">
						<table class="table my-table-striped table-responsive inline-table custom-table">
						    <thead>
						      	<tr>
							        <th>Pact Name</th>
							        <th class="text-center">Sections</th>
							        <th class="text-center">Clauses</th>
							        <th class="text-center">Edited On</th>
							        <th class="text-center">Last Sent</th>
							        <th class="text-center">Status</th>
							        <th class="text-center">Action</th>
						      	</tr>
						    </thead>

						    <tbody>
						      	<tr>
							        <td>
							        	<div class="custom-control custom-checkbox">
    										<input type="checkbox" class="custom-control-input" id="customCheck" name="example1">
    										<label class="custom-control-label" for="customCheck"><span class="check-adjust">Manager Safety</span></label>
  										</div>
  									</td>

							        <td class="text-center">7</td>
							        <td class="text-center">25</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center status">Active<i class="fas fa-chevron-down"></i></td>
							        <td class="text-center action">
							        	<a href="manager_safetypact.html"><i class="far fa-eye"></i></a>
							        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
							        	<a href="#"><i class="far fa-window-close"></i></a>
							        </td>
						      	</tr>

						      	<tr>
							        <td>
							        	<div class="custom-control custom-checkbox">
    										<input type="checkbox" class="custom-control-input" id="customCheck2" name="example1">
    										<label class="custom-control-label" for="customCheck2"><span class="check-adjust">Team Member Safety</span></label>
  										</div>
  									</td>

							        <td class="text-center">5</td>
							        <td class="text-center">12</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center status">Active<i class="fas fa-chevron-down"></i></td>
							        <td class="text-center action">
							        	<a href="#"><i class="far fa-eye"></i></a>
							        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
							        	<a href="#"><i class="far fa-window-close"></i></a>
							        </td>
						      	</tr>

						      	<tr>
							        <td>
							        	<div class="custom-control custom-checkbox">
    										<input type="checkbox" class="custom-control-input" id="customCheck3" name="example1">
    										<label class="custom-control-label" for="customCheck3"><span class="check-adjust">Ethics Policy</span></label>
  										</div>
							        </td>
							        <td class="text-center">5</td>
							        <td class="text-center">8</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center status">Active<i class="fas fa-chevron-down"></i></td>
							        <td class="text-center action">
							        	<a href="#"><i class="far fa-eye"></i></a>
							        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
							        	<a href="#"><i class="far fa-window-close"></i></a>
							        </td>
						      	</tr>

						      	<tr>
							        <td>
							        	<div class="custom-control custom-checkbox">
    										<input type="checkbox" class="custom-control-input" id="customCheck4" name="example1">
    										<label class="custom-control-label" for="customCheck4"><span class="check-adjust">Security & Non Disclosure</span></label>
  										</div>
							        </td>
							        <td class="text-center">7</td>
							        <td class="text-center">8</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center">11/23/19</td>
							        <td class="text-center status">Active<i class="fas fa-chevron-down"></i></td>
							        <td class="text-center action">
							        	<a href="#"><i class="far fa-eye"></i></a>
							        	<a href="#"><i class="fas fa-pencil-alt"></i></a>
							        	<a href="#"><i class="far fa-window-close"></i></a>
							        </td>
						      	</tr>
						    </tbody>
					  	</table>
					</div>
				</div>
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
</body>
</html>
