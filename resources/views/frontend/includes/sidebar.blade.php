<div class="wn__sidebar">
    <!-- Start Single Widget -->
    <aside class="widget search_widget">
        <h3 class="widget-title">Search</h3>

        {!! Form::open(['route' => 'frontend.search' , 'method' => 'get']) !!}
            <div class="form-input">
                {!! Form::text('keyword' , old('keyword') , ['placeholder' =>'Search...' ]) !!}
                {!! Form::button('<i class="fa fa-search"></i>' , ['type' => 'submit']) !!}
            </div>
        {!! Form::close() !!}

    </aside>
    <!-- End Single Widget -->
    <!-- Start Single Widget -->
    <aside class="widget recent_widget">
        <h3 class="widget-title">Recent</h3>
        <div class="recent-posts">
            <ul>
                @foreach($recent_post as $post)
                    <li>
                        <div class="post-wrapper d-flex">
                            <div class="thumb">


                                <a href="{{route('post.show' , $post->slug)}}">
                                    @if($post->media->count() > 0)
                                    <img src="{{url('/')}}/public/frontend/images/blog/sm-img/1.jpg" alt="{{$post->title}}">
                                @else
                                    <img src="{{url('/')}}/public/frontend/images/blog/sm-img/1.jpg" alt="{{$post->title}}">
                                    @endif

                                </a>
                            </div>
                            <div class="content">
                                <h4><a href="{{route('post.show' , $post->slug)}}">{{ \Illuminate\Support\Str::limit($post->title , 15 , '...')}}</a></h4>
                                <p>{{$post->created_at->format('M d Y')}}</p>
                            </div>
                        </div>
                    </li>

                @endforeach

            </ul>
        </div>
    </aside>
    <!-- End Single Widget -->
    <!-- Start Single Widget -->
    <aside class="widget comment_widget">
        <h3 class="widget-title">Comments</h3>
        <ul>
            @foreach($recent_comment as $comment)
            <li>
                <div class="post-wrapper">
                    <div class="thumb">
                            <img src="{{get_gravatar($comment->email , 47)}}" alt="{{$comment->name}}">
                    </div>
                    <div class="content">
                        <p>{{$comment->name}} says:</p>
                        <a href="{{route('post.show' , $comment->post->slug)}}">{{\Illuminate\Support\Str::limit($comment->comment , 25 , '...')}}</a>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </aside>
    <!-- End Single Widget -->
    <!-- Start Single Widget -->
    <aside class="widget category_widget">
        <h3 class="widget-title">Categories</h3>
        <ul>
            @foreach($recent_category as $category)
            <li><a href="{{route('frontend.category.posts' , $category->slug)}}">{{$category->name}}</a></li>
            @endforeach

        </ul>
    </aside>
    <!-- End Single Widget -->
    <!-- Start Single Widget -->
    <aside class="widget archives_widget">
        <h3 class="widget-title">Archives</h3>
        <ul>
            @foreach($global_archives as $key => $val)
            <li><a href="{{route('frontend.archive.posts' , $key.'-'.$val)}}">{{date('F' , mktime(0,0,0,$key , 1)) . ' ' . $val}}</a></li>
            @endforeach
        </ul>
    </aside>
    <!-- End Single Widget -->
</div>
