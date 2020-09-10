<div class="sitenav" id="mysitenav">
	<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
	<div class="sitebar-logo">
		<img src="{{ URL::asset('assets/front/svg/pp-logo.svg') }}" alt="logo">
	</div>
	<!-- sidebar navigation  -->
	<div class="sitebar-nav">

				<ul class="navigation-ul">
					@if(auth()->user()->hasRole('administrator'))
					<li class="{{ Request::path() == 'admin/users' ? 'active' : '' }}">

						<a href="{{route('admin.users')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M8.5 11C10.7091 11 12.5 9.20914 12.5 7C12.5 4.79086 10.7091 3 8.5 3C6.29086 3 4.5 4.79086 4.5 7C4.5 9.20914 6.29086 11 8.5 11Z" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M17 11L19 13L23 9" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							

							</svg>Accounts<i class="fas fa-chevron-right"></i>

						</a>

					</li>
					
					<li class="{{ Request::path() == 'admin/pact-template' ? 'active' : '' }}">

						<a href="{{route('admin.pact-template')}}">

							<!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">

								<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M14 2V8H20" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 13H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 17H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M10 9H9H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

							</svg> -->

							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M20.24 12.24C21.3658 11.1142 21.9983 9.58722 21.9983 7.99504C21.9983 6.40285 21.3658 4.87588 20.24 3.75004C19.1142 2.62419 17.5872 1.9917 15.995 1.9917C14.4028 1.9917 12.8758 2.62419 11.75 3.75004L5 10.5V19H13.5L20.24 12.24Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 8L2 22" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M17 15H9" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>

							Pacts<i class="fas fa-chevron-right"></i>

						</a>

					</li>

					<li class="{{ Request::path() == 'admin/reports' ? 'active' : '' }}">

						<a href="{{route('admin.reports')}}">

							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">

								<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M14 2V8H20" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 13H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 17H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M10 9H9H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

							</svg>Reports<i class="fas fa-chevron-right"></i>

						</a>

					</li>
					@elseif(auth()->user()->hasRole('admin'))
					<li class="{{ (Request::path() == 'companyadmin/signees' ) ? 'active' : '' }}">
						<a href="{{route('companyadmin.signees')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M8.5 11C10.7091 11 12.5 9.20914 12.5 7C12.5 4.79086 10.7091 3 8.5 3C6.29086 3 4.5 4.79086 4.5 7C4.5 9.20914 6.29086 11 8.5 11Z" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M17 11L19 13L23 9" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>signees<i class="fas fa-chevron-right"></i>
						</a>
					</li>
					@else
					
					<li class="{{ (Request::path() == 'company/users' ) ? 'active' : '' }}">
						<a href="{{route('company.users')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M8.5 11C10.7091 11 12.5 9.20914 12.5 7C12.5 4.79086 10.7091 3 8.5 3C6.29086 3 4.5 4.79086 4.5 7C4.5 9.20914 6.29086 11 8.5 11Z" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M17 11L19 13L23 9" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Users<i class="fas fa-chevron-right"></i>
						</a>
					</li>
					
					
					<li class="{{ Request::path() == 'company/pactall' ? 'active' : '' }}">
						<!-- <a href="{{route('company.pactall')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M14 2V8H20" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16 13H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16 17H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M10 9H9H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Pacts<i class="fas fa-chevron-right"></i>
						</a> -->

						<a href="{{route('company.pactall')}}">

							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M20.24 12.24C21.3658 11.1142 21.9983 9.58722 21.9983 7.99504C21.9983 6.40285 21.3658 4.87588 20.24 3.75004C19.1142 2.62419 17.5872 1.9917 15.995 1.9917C14.4028 1.9917 12.8758 2.62419 11.75 3.75004L5 10.5V19H13.5L20.24 12.24Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 8L2 22" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M17 15H9" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Pacts<i class="fas fa-chevron-right"></i>
						</a>
					</li>
					<!-- <li class="{{ Request::path() == 'company/pact-template' ? 'active' : '' }}">
						<a href="{{route('company.pact-template')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M14 2V8H20" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16 13H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16 17H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M10 9H9H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Pacts Templates<i class="fas fa-chevron-right"></i>
						</a>
					</li> the old link change at 30 jan 2020 -->
					<li>
						<a href="#">

							<!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M20.24 12.24C21.3658 11.1142 21.9983 9.58722 21.9983 7.99504C21.9983 6.40285 21.3658 4.87588 20.24 3.75004C19.1142 2.62419 17.5872 1.9917 15.995 1.9917C14.4028 1.9917 12.8758 2.62419 11.75 3.75004L5 10.5V19H13.5L20.24 12.24Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M16 8L2 22" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

								<path d="M17 15H9" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg> -->
							<svg width="26" height="20" viewBox="0 0 26 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0)">
								<path d="M21.2625 15.643C22.5958 16.364 23.3577 17.6493 23.2624 18.9345C23.1989 19.7182 23.1672 19.7496 22.1513 19.875C21.5164 19.969 17.5165 20.0004 13.3895 20.0004C8.72293 20.0004 3.8341 19.9063 3.4849 19.8123C2.08809 19.4675 2.84998 16.9596 4.69123 15.7684C6.11978 14.8593 9.04038 13.48 9.83402 13.3233C10.9134 13.1038 11.0404 12.4455 9.83402 10.3139C9.54831 9.84363 9.23086 8.40163 9.19911 6.89692C9.16737 4.45178 9.64355 2.79034 11.8023 1.9753C12.2467 1.81856 12.6911 1.75586 13.1038 1.75586C14.5324 1.75586 15.8657 2.53956 16.4054 3.69943C17.199 5.23548 16.8816 9.34207 16.0562 10.8154C15.1038 12.4769 15.199 13.0098 16.2466 13.2919C16.945 13.48 19.1038 14.4831 21.2625 15.643ZM3.99283 14.7026C3.4849 15.0474 3.00871 15.4863 2.62776 15.9251C1.42143 15.9251 0.500804 15.8938 0.405567 15.8624C-0.388074 15.6744 0.0563653 14.2323 1.10397 13.6054C1.89761 13.1038 3.51664 12.3201 3.96108 12.2261C4.5325 12.1007 4.65948 11.7559 3.96108 10.5646C3.80235 10.3139 3.61188 9.49881 3.61188 8.65241C3.58013 7.2731 3.86584 6.33266 5.07218 5.92514C6.08804 5.54896 7.23088 6.01918 7.64357 6.86558C8.05627 7.74332 7.83405 10.0004 7.38961 10.8154C6.88168 11.7872 6.97692 12.038 7.54834 12.1947C7.67532 12.2261 7.92929 12.3515 8.27849 12.5082C6.88168 13.0725 4.97694 14.107 3.99283 14.7026ZM24.8815 13.5113C25.6434 13.9189 26.0561 14.6085 25.9926 15.3295C25.9608 15.7684 25.9608 15.7997 25.3894 15.8624C25.2307 15.8938 24.5005 15.9251 23.5481 15.9251C23.1355 15.3922 22.564 14.8907 21.8656 14.5458C20.5641 13.7935 18.9768 12.9784 17.8022 12.4769C18.1196 12.3515 18.4054 12.2574 18.5323 12.2261C19.1355 12.1007 19.2307 11.7559 18.5323 10.5646C18.4054 10.3139 18.1831 9.49881 18.1831 8.65241C18.1514 7.2731 18.4054 6.33266 19.6117 5.92514C20.6593 5.54896 21.8021 6.01918 22.1831 6.86558C22.5958 7.74332 22.437 10.0004 21.9926 10.8154C21.4847 11.7872 21.5482 12.038 22.0878 12.1947C22.4688 12.3201 23.6751 12.8844 24.8815 13.5113Z" fill="#91929B" class="group-svg"/>
								</g>
								<defs>
								<clipPath id="clip0">
								<rect width="26" height="20" fill="white"/>
								</clipPath>
								</defs>
								</svg>

							Groups<i class="fas fa-chevron-right"></i>
						</a>
					</li>
					<!-- <li class="{{ Request::path() == 'company/pact' ? 'active' : '' }}">
						<a href="{{route('company.pact')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#99A1A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M14 2V8H20" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16 13H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16 17H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M10 9H9H8" stroke="#99A1A9" class="svg-file" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Pact Add<i class="fas fa-chevron-right"></i>
						</a>
					</li> -->
					<!--
					<li>
						<a href="#">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M15 10.3333C15 11.1924 14.3036 11.8889 13.4444 11.8889H4.11111L1 15V2.55556C1 1.69645 1.69645 1 2.55556 1H13.4444C14.3036 1 15 1.69645 15 2.55556V10.3333Z" stroke="#91929B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>Options<i class="fas fa-chevron-right"></i>
						</a> 
					</li> -->
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