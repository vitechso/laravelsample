@include('admin.include.header')
<style type="text/css">
    .user-add-form{
        box-shadow: 0px 0px 10px inset #ccc;
        padding: 50px 0px 20px;
    }
</style>

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
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <!-- Nav tabs -->
                            <ul class="nav custom-nav-tabs ">
                                <li class="nav-item">
                                  <a class="nav-link active" href="#home">Edit</a>
                                </li>
                                
                            </ul>

                    {{ Form::open(['route'=>['admin.users.update', $user->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                    <!-- @if(Request::path()=='admin/users/create-admin') 
                    <input type="hidden" name="role" value="2"> 
                    @else 
                    <input type="hidden" name="role" value="3"> 
                    @endif -->
                    <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                        {{ __('views.admin.users.show.table_header_1') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif" name="name" value="{{ $user->name }}" required>
                        @if($errors->has('name'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('name') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">
                        {{ __('views.admin.users.edit.email') }}
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="email" type="email" class="form-control col-md-7 col-xs-12 @if($errors->has('email')) parsley-error @endif" name="email" value="{{ $user->email }}" required>
                        @if($errors->has('email'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('email') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">
                        {{ __('views.admin.users.edit.phone') }}<span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="password" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('phone_number')) parsley-error @endif" name="phone_number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="{{ $user->phone_number }}" required>
                        @if($errors->has('phone_number'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('phone_number') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                
                <!-- <div class="form-group">


                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password_confirmation">


                        {{ __('views.admin.users.edit.confirm_password') }}


                    </label>


                    <div class="col-md-6 col-sm-6 col-xs-12">


                        <input id="password_confirmation" type="password" class="form-control col-md-7 col-xs-12 @if($errors->has('password_confirmation')) parsley-error @endif"


                               name="password_confirmation">


                        @if($errors->has('password_confirmation'))


                            <ul class="parsley-errors-list filled">


                                @foreach($errors->get('password_confirmation') as $error)


                                    <li class="parsley-required">{{ $error }}</li>


                                @endforeach


                            </ul>


                        @endif


                    </div>


                </div> -->





                





                <div class="form-group">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                        
                        <button type="submit" class="btn custom-btn check-main"> {{ __('views.admin.users.edit.save') }}</button>
                    </div>
                </div>
            {{ Form::close() }}


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