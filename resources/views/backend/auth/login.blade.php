@extends('layouts.admin-auth')

@section('content')
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                </div>

                                {!! Form::open(['route' => 'admin.show_login_form' , 'method' => 'post' , 'class' => 'user' ]) !!}
                                <div class="form-group">
                                    {!! Form::text('username' , old('username') , ['class' => 'form-control form-control-user' , 'placeholder' => 'Enter Your User Name...']) !!}
                                    @error('username')
                                    <sapn class="text-danger">{{$message}}</sapn>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    {!! Form::password('password' , ['class' => 'form-control form-control-user' , 'placeholder' => 'Enter Your User Password...']) !!}
                                    @error('password')
                                    <sapn class="text-danger">{{$message}}</sapn>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember" {{old('remember') ? 'checked':''}}>
                                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                                    </div>
                                </div>

                                {!! Form::button('Login' , ['type' => 'submit' ,'class' => 'btn btn-primary btn-user btn-block']) !!}
                                {!! Form::close() !!}

                                <hr>
                                <div class="text-center">
                                    <a class="small" href="{{route('password.request')}}">Forgot Password?</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection
