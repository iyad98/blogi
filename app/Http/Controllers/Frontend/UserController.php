<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Stevebauman\Purify\Facades\Purify;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth' , 'verified']);
    }

    public function index(){
        $posts = auth()->user()->posts()->with('user','category','media')->withCount('comments')->orderBy('id' , 'desc')->paginate(10);
        return view('frontend.users.dashboard' , compact('posts'));
    }
    public function create_post()
    {
        $category = Category::whereStatus(1)->pluck('name' , 'id');
        return view('frontend.users.create_post' , compact('category'));

    }

    public function store_post(Request $request){

        $validator = Validator::make($request->all() , [
            'title' => 'required',
            'description' => 'required|min:50',
            'status' => 'required',
            'comment_able' => 'required',
            'category_id' => 'required'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user_id = auth()->user()->id;
        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
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

    public function edit_post($post_id){
        $post = Post::find($post_id);
        if ($post){
            $category = Category::whereStatus(1)->pluck('name' , 'id');
            return view('frontend.users.edit_post' , compact('category' , 'post'));
        }
        return redirect()->route('frontend.index');

    }
    public function update_post(Request $request , $post_id){

        $validator = Validator::make($request->all() , [
            'title' => 'required',
            'description' => 'required|min:50',
            'status' => 'required',
            'comment_able' => 'required',
            'category_id' => 'required'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $post = Post::with('media')->find($post_id);
        if ($post){
            $user_id = auth()->user()->id;
            $post->update([
                'title' => $request->title,
                'slug' => null,
                'description' => $request->description,
                'status' => $request->status,
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
    public function destroy_post($post_id){
        $post = Post::find($post_id);
        if ($post){
            if ($post->media->count() > 0){
                foreach ($post->media as $media){
                    if (File::exists('public/assets/posts/'.$media->file_name)){
                        unlink('public/assets/posts/'.$media->file_name);
                    }
                }
            }
            $post->delete();
            return redirect()->back()->with([
                'message' => 'Post deleted Successfully',
                'alert-type' => 'success'
            ]);

        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);

    }




    public function destroy_post_media($media_id){

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


    public function show_comments(Request $request){
        $comments = Comment::query();
        if (isset($request->post) && $request->post != ''){
            $comments = $comments->wherePostId($request->post);
        }else{
            $post_id = auth()->user()->posts()->pluck('id')->toArray();
            $comments = $comments->whereIn('post_id' , $post_id);

        }
        $comments = $comments->orderBy('id' , 'desc');
        $comments = $comments->paginate(10);
        return view('frontend.users.comments' , compact('comments'));
    }
    public function edit_comment($comment_id){
        $comment = Comment::whereId($comment_id)->whereHas('post' , function ($q){
            $q->where('posts.user_id' , auth()->id());
        })->first();
        if ($comment){
            return view('frontend.users.edit_comment' , compact('comment'));

        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);
    }

    public function update_comment(Request $request , $comment_id){
        $validator = Validator::make($request->all() , [
            'name' => 'required',
            'email' => 'required|email',
            'status' => 'required',
            'comment' => 'required',
            'url' => 'nullable|url'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $comment = Comment::whereId($comment_id)->whereHas('post' , function ($q){
            $q->where('posts.user_id' , auth()->id());
        })->first();

        if ($comment){
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['status'] = $request->status;
            $data['url'] = $request->url;
            $data['comment'] = Purify::clean($request->comment);

            $comment->update($data);
            if ($request->status == 1){
                Cache::forget('recent_comment');
            }
            return redirect()->back()->with([
                'message' => 'Comment updated Successfully',
                'alert-type' => 'success'
            ]);


        }else{
            return redirect()->back()->with([
                'message' => 'Something was wrong',
                'alert-type' => 'danger'
            ]);
        }

    }
    public function destroy_comment($comment_id){
        $comment = Comment::whereId($comment_id)->whereHas('post' , function ($q){
            $q->where('posts.user_id' , auth()->id());
        })->first();
        if ($comment){
            $comment->delete();
            Cache::forget('recent_comment');
            return redirect()->back()->with([
                'message' => 'Comment deleted Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);

    }

    public function edit_info(){
        return view('frontend.users.edit_info');
    }
    public function update_info(Request $request){
        $validator = Validator::make($request->all() , [
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric',
            'receive_email' => 'required',
            'bio' => 'nullable|min:10',
            'user_image' => 'nullable|max:2000|mimes:jpg,jpeg,png'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['mobile'] = $request->mobile;
        $data['receive_email'] = $request->receive_email;
        $data['bio'] = $request->bio;
        if ($image = $request->file('user_image')){
            if (auth()->user()->user_image != ''){
                if (File::exists('public/assets/users/'.auth()->user()->user_image)){
                    unlink('public/assets/users/'.auth()->user()->user_image);
                }
            }
            $filename = Str::slug(auth()->user()->username).'.'.$image->getClientOriginalExtension();
            $path = public_path('assets/users/' . $filename);
            Image::make($image->getRealPath())->resize(300 , 300 , function ($constraint){
                $constraint->aspectRatio();

            })->save($path,100);
            $data['user_image'] = $filename;
        }
        $update_user = auth()->user()->update($data);
        if ($update_user){
            return redirect()->back()->with([
                'message' => 'User Updated Successfully',
                'alert-type' => 'success'
            ]);
        }else{
            return redirect()->back()->with([
                'message' => 'Something was wrong',
                'alert-type' => 'danger'
            ]);
        }
    }
    public function update_password(Request $request){
        $validator = Validator::make($request->all() , [
            'current_password' => 'required',
            'password' => 'required|confirmed'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = auth()->user();
        if (Hash::check($request->current_password , $user->password)){
            $update = $user->update([
                'password' => bcrypt($request->password)
            ]);
            if ($update){
                return redirect()->back()->with([
                    'message' => 'User Password Updated Successfully',
                    'alert-type' => 'success'
                ]);
            }else{
                return redirect()->back()->with([
                    'message' => 'Something was wrong',
                    'alert-type' => 'danger'
                ]);
            }
        }else{
            return redirect()->back()->with([
                'message' => 'Something was wrong',
                'alert-type' => 'danger'
            ]);
        }
    }
}
