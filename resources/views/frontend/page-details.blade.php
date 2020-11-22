@extends('layouts.app')
@section('content')
    <div class="page-blog-details section-padding--lg bg--white">
        <div class="container">
            @if (Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{Session::get('success')}}
                </div>

            @endif
                @if (Session::has('errors'))
                    <div class="alert alert-danger" role="alert">
                        {{Session::get('errors')}}
                    </div>

                @endif
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="blog-details content">
                        <article class="blog-post-details">
                            @if($page->count() > 0)
                                <div id="carouselIndicators" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        @foreach($page->media as $media)
                                            <li data-target="#carouselIndicators" data-slide-to="{{$loop->index}}" class="{{$loop->index == 0 ? 'active':''}}"></li>
                                        @endforeach
                                    </ol>
                                    <div class="carousel-inner">
                                        @foreach($page->media as $media)
                                            <div class="carousel-item {{$loop->index == 0 ? 'active' : ''}}">
                                                <img class="d-block w-100" src="{{url('/')}}/public/posts/{{$page->file_name}}" alt="{{$page->file_name}}">
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($page->media->count() > 1)
                                    <a class="carousel-control-prev" href="#carouselIndicators" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselIndicators" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                    @endif
                                </div>
                            @endif

                            <div class="post_wrapper">
                                <div class="post_header">
                                    <h2>{{$page->title}}</h2>
                                    <div class="blog-date-categori">
                                        <ul>
                                            <li>{{$page->created_at->format('M d Y')}}</li>
                                            <li><a href="#" title="Posts by {{$page->user->name}}" rel="author">{{$page->user->name}}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="post_content">
                                    <p>
                                        {!! $page->description !!}
                                    </p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
