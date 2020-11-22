@extends('layouts.app')
@section('content')
    <!-- Start Blog Area -->
    <div class="page-blog bg--white section-padding--lg blog-sidebar right-sidebar">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                <th>Name</th>
                                <th>Post</th>
                                <th>Status</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($comments as $comment)
                                <tr>
                                    <td>{{$comment->name}}</td>
                                    <td>{{$comment->post->title}}</td>
                                    <td>{{$comment->status}}</td>
                                    <td>
                                        <a href="{{route('users.comments.edit' , $comment->id)}}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                        <a href="javascript:void(0); " onclick="if (confirm('Are you sure to delete this post !!')){
                                            document.getElementById('post-delete-{{$comment->id}}').submit();}else {return false}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                        <form action="{{route('users.comment.destroy' , $comment->id)}}" method="post" id="post-delete-{{$comment->id}}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No Comments found</td>
                                </tr>
                            @endforelse

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4">{!! $comments->appends(request()->input())->links() !!}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-lg-3 col-12 md-mt-40 sm-mt-40">
                    @include('frontend.includes.users.sidebar')
                </div>
            </div>
        </div>
    </div>
    <!-- End Blog Area -->@endsection
