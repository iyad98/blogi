<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostCommentsController extends Controller
{


    public function __construct()
    {
        if (\auth()->check()){
            $this->middleware('auth');
        } else {
            return view('backend.auth.login');
        }
    }
    public function index()
    {
        //
        if (!\auth()->user()->ability('admin', 'manage_post_comments,show_post_comments')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $keyword = (isset(request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $status = (isset(request()->status) && \request()->status != '') ? \request()->status : null;
        $post_id = (isset(request()->post_id) && \request()->post_id != '') ? \request()->post_id : null;
        $sort_by = (isset(request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';

        $comments = Comment::with(['user' , 'post']);
        if ($keyword != null){
            $comments = $comments->search($keyword);
        }
        if ($status != null){
            $comments = $comments->whereStatus($status);
        }
        if ($post_id != null){
            $posts = $comments->where('post_id',$post_id);
        }

        $comments = $comments->orderBy($sort_by , $order_by)->paginate($limit_by);
        $posts = Post::orderBy('id' , 'desc')->pluck('title' , 'id');

        return view('backend.post_comments.index' , compact('comments' , 'posts'));
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        //
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_post_comments')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $comment = Comment::with(['user' , 'post'])->find($id);
        return view('backend.post_comments.edit' , compact('comment'));
    }
    public function update(Request $request, $id)
    {
        if (!\auth()->user()->ability('admin', 'update_post_comments')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        //
        $validatedData = $request->validate([
            'name' =>'required',
            'email' =>'required|email',
            'url' =>'required|url',
            'ip_address' =>'required',
            'status' =>'required',
            'comment' =>'required|min:50',
        ]);
        $comment = Comment::with(['user' , 'post'])->find($id);
        if ($comment){
            $comment->update([
                'name' =>$request->name,
                'email' =>$request->email,
                'url' =>$request->url,
                'ip_address' =>$request->ip_address,
                'status' =>$request->status,
                'comment' =>Purify::clean($request->comment)
            ]);

            Cache::forget('recent_comment');
            return redirect()->back()->with([
                'message' => 'Comment Updated Successfully',
                'alert-type' => 'success'
            ]);

        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);
    }
    public function destroy($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'delete_post_comments')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }

        $comment = Comment::find($id);
        $comment->delete();

        return redirect()->route('admin.post_comments.index')->with([
            'message' => 'Comment deleted successfully',
            'alert-type' => 'success',
        ]);
    }

}
