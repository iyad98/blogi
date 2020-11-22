@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{url('/')}}/public/frontend/js/summernote/summernote.min.css">
    <link href="{{url('/')}}/public/frontend/js/bootstrap-fileinput/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

@endsection
@section('content')

    <div class="page-blog bg--white section-padding--lg blog-sidebar right-sidebar">
        <div class="container">
            <div class="row">

                <div class="col-lg-9 col-12">
                    <h3>Edit Comment On : {{$comment->post->title}}</h3>
                    <br>
                    {!! Form::model($comment , ['route' => ['users.comment.update' , $comment->id] , 'method' => 'put' , 'files' => true]) !!}
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                {!! Form::label('name' , 'Name') !!}
                                {!! Form::text('name' , old('name' , $comment->name) , ['class' => 'form-control'])!!}
                                @error('name')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {!! Form::label('email' , 'Email') !!}
                                {!! Form::text('email' , old('email' , $comment->email) , ['class' => 'form-control'])!!}
                                @error('email')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {!! Form::label('url' , 'Website') !!}
                                {!! Form::text('url' , old('url' , $comment->url) , ['class' => 'form-control'])!!}
                                @error('url')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {!! Form::label('status' , 'status') !!}
                                {!! Form::select('status' , ['1' => 'Active' , '0'=>'InActive'],old('status' , $comment->status) , ['class' => 'form-control'])!!}
                                @error('status')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('comment' , 'Comment') !!}
                        {!! Form::textarea('comment' , old('comment' , $comment->comment) , ['class' => 'form-control summernote'])!!}
                        @error('comment')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="form-group pt-4">
                        {!! Form::submit('Submit' , ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-3 col-12 md-mt-40 sm-mt-40">
                    @include('frontend.includes.users.sidebar')
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{url('/')}}/public/frontend/js/summernote/summernote.min.js"></script>
    <script>
        $('.summernote').summernote({
            placeholder: 'Hello stand alone ui',
            tabsize: 2,
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        </script>

        @endsection

