@extends('layouts.app')

@section('content')
    <section class="my_account_area pt--80 pb--55 bg--white">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-md-3">
                    <div class="my__account__wrapper">
                        <h3 class="account__title">Login</h3>
                        {!! Form::open(['route' => 'frontend.login' , 'method' => 'post']) !!}
                        <div class="account__form">
                            <div class="input__box">
                                {!! Form::label('username' , 'UserName *') !!}
                                {!! Form::text('username' , old('username') , ['placeholder' => 'Enter Your UserName']) !!}
                                @error('username')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="input__box">
                                {!! Form::label('password' , 'Password *') !!}
                                {!! Form::password('password' , old('password') , ['placeholder' => 'Enter Your Password']) !!}
                                @error('password')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form__btn">
                                {!! Form::button('Login' , ['type' => 'submit']) !!}
                                <label class="label-for-checkbox">
                                    <input id="remember" class="input-checkbox" name="remember" value="forever" type="checkbox" {{old('remember'?'checked':'')}}>
                                    <span>Remember me</span>
                                </label>
                            </div>
                            <a class="forget_pass" href="{{route('password.request')}}">Lost your password?</a>
                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
