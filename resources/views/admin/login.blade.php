@include('admin.include.header')
	<section class="login-section">
		<div class="container h-100">
			<div class="row h-100 align-items-center justify-content-center">
				<div class="col-lg-6 col-md-6 col-12">
					<div class="login-block">
						<div class="row align-items-center justify-content-center">
							<div class="col-lg-8">
								<div class="logo-block pb-5">
									<img src="img/logo.png" alt="logo">
								</div>

								<form class="form-block">
								  	<div class="form-group adjust-form">
								    	<input type="text" class="form-control custom-input custom-color" id="username" placeholder="Username">
								  	</div>

								  	<div class="form-group adjust-form2">
								    	<input type="password" class="form-control custom-input custom-color" id="password" placeholder="Password">
								  	</div>

								  	<div class="btn-block adjust-form2">
								  		<button type="button" class="btn custom-btn"><span class="check"><i class="fas fa-check"></i></span>Sign Up</button>
								  		<button type="button" class="btn custom-btn check-main"><span class="check"><i class="fas fa-check"></i></span>Login</button>
								  	</div>

								  	<div class="forgot-block mt-5">
								  		<a href="#">Forgot Your Password?</a>
								  	</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-12 col-md-12 col-12">
					<div class="copyright-block">
						<p>Copyright PhonePact 2020. All Rights reserved</p>
					</div>
				</div>
			</div>
		</div>
	</section>
@include('admin.include.footer')
	
