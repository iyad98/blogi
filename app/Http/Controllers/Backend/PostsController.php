<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Models\Permission;

class PostsController extends Controller
{

    public function __construct()
    {
        if (auth()->check()){
            $this->middleware('auth');
        }else{
            return view('backend.auth.login');
        }
    }


    public function index()
    {
        //
        if (!\auth()->user()->ability('admin', 'manage_posts,show_posts')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $keyword = (isset(request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $categoryId = (isset(request()->category_id) && \request()->category_id != '') ? \request()->category_id : null;
        $status = (isset(request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';



        $posts = Post::with(['user', 'category', 'comments'])->wherePostType('post');
        if ($keyword != null) {
            $posts = $posts->search($keyword);
        }
        if ($categoryId != null) {
            $posts = $posts->whereCategoryId($categoryId);
        }
        if ($status != null) {
            $posts = $posts->where('status',$status);
        }

        $posts = $posts->orderBy($sort_by, $order_by);
        $posts = $posts->paginate($limit_by);
        $categories = Category::orderBy('id' , 'desc')->pluck('name' , 'id');

        return view('backend.posts.index' , compact('posts' , 'categories'));
    }


    public function create()
    {
        //
        if (!auth()->user()->ability('admin' , 'create_posts')){
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $categories = Category::orderBy('id' , 'desc')->where('status' , 1)->pluck('name' , 'id');
        return view('backend.posts.create' , compact('categories'));
    }


    public function store(Request $request)
    {
        //
        if (!auth()->user()->ability('admin' , 'create_posts')){
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validator = Validator::make($request->all() , [
            'title' => 'required',
            'description' => 'required|min:50',
            'status' => 'required',
            'comment_able' => 'required',
            'category_id' => 'required',
            'images.*' => 'nullable|mimes:jpg,jpeg,png,gif'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user_id = auth()->user()->id;
        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'post_type' => 'post',
            'comment_able' => $request->comment_able,
            'category_id' => $request->category_id,
            'user_id' => $user_id
        ]);

        if ($request->images && count($request->images) > 0){
            $i = 1;
            foreach ($request->images as $file){
                $filename = $post->slug.'-'.time().'-'.$i.'.'.$file->getClientOriginalExtension();
                $file_size = $file->getSize();
                $file_type = $file->getMimeType();
                $path = public_path('assets/posts/' . $filename);
                Image::make($file->getRealPath())->resize(800 , null , function ($constraint){
                    $constraint->aspectRatio();

                })->save($path,100);
                $post->media()->create([
                    'file_name' => $filename,
                    'file_type' => $file_type,
                    'file_size' => $file_size
                ]);
                $i++;
            }
        }
        if ($post->status == 1){
            Cache::forget('recent_post');
        }
        return redirect()->back()->with([
            'message' => 'Post created Successfully',
            'alert-type' => 'success'
        ]);
    }


    public function show($id)
    {
        //
        if (!auth()->user()->ability('admin' , 'display_posts')){
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }

        $post = Post::with(['media' , 'category'])->find($id);
        return view('backend.posts.show' , compact('post'));
    }


    public function edit($id)
    {
        //
        if (!auth()->user()->ability('admin' , 'update_posts')){
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $categories = Category::orderBy('id' , 'desc')->get()->pluck('name' , 'id');
        $post = Post::with(['media'])->find($id);
        return view('backend.posts.edit' , compact('categories' , 'post'));

    }


    public function update(Request $request, $id)
    {
        //
        if (!auth()->user()->ability('admin' , 'update_posts')){
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validatedData = $request->validate([
            'title' =>'required',
            'description' =>'required|min:50',
            'category_id' =>'required',
            'comment_able' =>'required',
            'status' =>'required',
        ] , [
            'title.required' => 'يجب ادخال العنوان'
        ]);

        $post = Post::with(['media' , 'user' , 'category'])->find($id);

        if ($post){
            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'comment_able' => $request->comment_able,
                'category_id' => $request->category_id,
            ]);
            if ($request->images && count($request->images) > 0){
                $i = 1;
                foreach ($request->images as $file){
                    $filename = $post->slug.'-'.time().'-'.$i.'.'.$file->getClientOriginalExtension();
                    $file_size = $file->getSize();
                    $file_type = $file->getMimeType();
                    $path = public_path('assets/posts/' . $filename);
                    Image::make($file->getRealPath())->resize(800 , null , function ($constraint){
                        $constraint->aspectRatio();

                    })->save($path,100);
                    $post->media()->create([
                        'file_name' => $filename,
                        'file_type' => $file_type,
                        'file_size' => $file_size
                    ]);
                    $i++;
                }
            }

            return redirect()->back()->with([
                'message' => 'Post Updated Successfully',
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
        if (!\auth()->user()->ability('admin', 'delete_posts')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $post = Post::with(['media' , 'category' , 'user'])->find($id);
        if ($post){
            $post->delete();
            return redirect()->back()->with([
                'message' => 'Post Updated Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);
    }


    public function remove_media($media_id){
        if (!\auth()->user()->ability('admin', 'delete_posts')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }

        $media = PostMedia::find($media_id);
        if ($media){
            if (File::exists('public/assets/posts/'.$media->file_name)){
                unlink('public/assets/posts/'.$media->file_name);
            }
            $media->delete();
            return true;
        }
        return false;

    }
}
