@extends('layouts.app')
@section('content')
    <!-- Start Blog Area -->
    <div class="page-blog bg--white section-padding--lg blog-sidebar right-sidebar">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-12">
                    <div class="blog-page">
                        <!-- Start Single Post -->
                        @forelse($posts  as $post)
                        <article class="blog__post d-flex flex-wrap">
                            <div class="thumb">
                                <a href="">
                                    @if ($post->media->count() > 0)
                                        <img src="{{url('/')}}/public/assets/posts/{{$post->media->first()->file_name}}" alt="{{$post->title}}">
                                    @else
                                        <img src="{{url('/')}}/public/assets/posts/default.jpg" alt="blog images">
                                    @endif
                                </a>
                            </div>
                            <div class="content">
                                <h4><a href="#">{{$post->tilte}}</a></h4>
                                <ul class="post__meta">
                                    <li>Posts by : <a href="{{route('frontend.author.posts',$post->user->username)}}">{{$post->user->name}}</a></li>
                                    <li class="post_separator">/</li>
                                    <li>{{$post->created_at->format('M d Y')}}</li>
                                </ul>
                                <p> {!! \Illuminate\Support\Str::limit($post->description , 145 , '...') !!}</p>
                                <div class="blog__btn">
                                    <a href="{{route('post.show' , $post->slug)}}">read more</a>
                                </div>
                            </div>
                        </article>
                    @empty
                            <div class="text-center">No posts</div>
                    @endforelse
                        <!-- End Single Post -->

                    </div>
                    <ul class="wn__pagination">

                        {{$posts->appends(request()->input())->links()}}
{{--                        <li class="active"><a href="#">1</a></li>--}}
{{--                        <li><a href="#">2</a></li>--}}
{{--                        <li><a href="#">3</a></li>--}}
{{--                        <li><a href="#">4</a></li>--}}
{{--                        <li><a href="#"><i class="zmdi zmdi-chevron-right"></i></a></li>--}}
                    </ul>
                </div>
                <div class="col-lg-3 col-12 md-mt-40 sm-mt-40">
                    @include('frontend.includes.sidebar')
                </div>
            </div>
        </div>
    </div>
    <!-- End Blog Area -->@endsection
