<div class="sitenav" id="mysitenav">
	<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
	<div class="sitebar-logo">
		<img src="{{ URL::asset('assets/front/svg/pp-logo.svg') }}" alt="logo">
	</div>
	<!-- sidebar navigation  -->
	<div class="sitebar-nav">

				<ul class="navigation-ul">
					@if(auth()->user()->hasRole('administrator'))
					<li class="{{ Request::path() == 'admin' ? 'active' : '' }}">

						<a href="{{route('admin.company')}}">

							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">

								<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M14 2V8H20" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 13H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 17H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M10 9H9H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

							</svg>Company<i class="fas fa-chevron-right"></i>

						</a>

					</li>
					@else
					<li class="{{ Request::path() == 'admin' ? 'active' : '' }}">

						<a href="{{route('admin.dashboard')}}">

							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">

								<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M14 2V8H20" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 13H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 17H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M10 9H9H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

							</svg>Pacts<i class="fas fa-chevron-right"></i>

						</a>

					</li>



					<li class="{{ (Request::path() == 'admin/users' || Request::path() == 'admin/users/create' || Request::path() == 'admin/users/create-admin') ? 'active' : '' }}">

						<a href="{{route('admin.users')}}">

							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">

								<path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M8.5 11C10.7091 11 12.5 9.20914 12.5 7C12.5 4.79086 10.7091 3 8.5 3C6.29086 3 4.5 4.79086 4.5 7C4.5 9.20914 6.29086 11 8.5 11Z" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M17 11L19 13L23 9" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

							</svg>Signees<i class="fas fa-chevron-right"></i>

						</a>

					</li>



					<li>

						<a href="#">

							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M20.24 12.24C21.3658 11.1142 21.9983 9.58722 21.9983 7.99504C21.9983 6.40285 21.3658 4.87588 20.24 3.75004C19.1142 2.62419 17.5872 1.9917 15.995 1.9917C14.4028 1.9917 12.8758 2.62419 11.75 3.75004L5 10.5V19H13.5L20.24 12.24Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 8L2 22" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M17 15H9" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Signed Pacts<i class="fas fa-chevron-right"></i>
						</a>
					</li>
					<li>
						<a href="#">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M15 10.3333C15 11.1924 14.3036 11.8889 13.4444 11.8889H4.11111L1 15V2.55556C1 1.69645 1.69645 1 2.55556 1H13.4444C14.3036 1 15 1.69645 15 2.55556V10.3333Z" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Options<i class="fas fa-chevron-right"></i>
						</a> 
					</li>
					@endif
				</ul>

			</div>
	<!-- sidebar navigation  -->

	<div class="logout-block mtop-5">
		<p>Logged in as:</p>
		<h5>{{ auth()->user()->name }}</h5>
		<a href="{{ route('logout')}}" class="logout-btn">Logout</a>
	</div>
</div>