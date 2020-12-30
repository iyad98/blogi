<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentForPostOwnerNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function foo\func;

class IndexController extends Controller
{
    //
    public function index(){
        $posts = Post::with(['media' , 'user'])->whereHas('category', function ($q){
           $q->whereStatus(1);
        })->whereHas('user' , function ($q){
            $q->whereStatus(1);
        })->wherePostType('post')->whereStatus(1)->orderBy('id' , 'desc')->paginate(5);


        return view('frontend.index' , compact('posts'));
    }

    public function post_show($post){

        $post = Post::with(['category' , 'media' , 'user' ,
            'comments_approve' => function($q){
            $q->orderBy('id' , 'desc');
        }])->whereHas('category' , function ($q){
            $q->whereStatus(1);
        })->whereHas('user' , function ($q){
            $q->whereStatus(1);
        })->wherePostType('post')->whereSlug($post)->first();
        if ($post){
            return view('frontend.post-details' , compact('post'));
        }else{
            return view('frontend.index');
        }

    }
    public function page_show($page_slug){

        $page = Post::with([ 'media'])->wherePostType('page')->whereSlug($page_slug)->first();
        if ($page){
            return view('frontend.page-details' , compact('page'));
        }else{
            return view('frontend.index');
        }

    }

    public function comment_add(Request  $request , $post_slug){

        $validation = Validator::make($request->all() , [
            'name' => 'required',
            'email' => 'required|email',
            'url' => 'nullable|url',
            'comment' => 'required|min:10',
        ]);
        if ($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput();
        }
        $post = Post::whereSlug($post_slug)->first();
        if ($post){
            $user_id = Auth::check() ? Auth::id():null;
            $comment = Comment::create([
                'name' => $request->name,
                'email' => $request->email,
                'url' => $request->url,
                'ip_address' => $request->ip(),
                'comment' => $request->comment,
                'post_id' => $post->id,
                'user_id' => $user_id
            ]);
            if (auth()->guest() || auth()->id() != $post->user_id){
                $post->user->notify(new NewCommentForPostOwnerNotify($comment));
            }
            return redirect()->back()->with([
                'success' => 'added successfully'
            ]);

        }
        return redirect()->back()->with([
            'errors' => 'added fails'
        ]);


    }

    public function search(Request $request){
        $keyword = isset($request->keyword) && $request->keyword != '' ? $request->keyword : null;
        $posts = Post::with(['category' , 'media' , 'user'])
            ->whereHas('category' , function ($q){
            $q->whereStatus(1);
        })->whereHas('user' , function ($q){
            $q->whereStatus(1);
        });

        if ($keyword != null) {
            $post = $posts->search($keyword, null, true);
        }
        $posts = $posts->whereStatus(1)->wherePostType('post')->orderBy('id' , 'desc')->paginate(5);
        return view('frontend.index' , compact('posts'));
    }

    public function category($category_slug){
        $category = Category::whereSlug($category_slug)->whereStatus(1)->orWhere('id' , $category_slug)->first()->id;
        if ($category){
            $posts = Post::with(['media' , 'user' , 'category'])->withCount('comments_approve')
                ->whereCategoryId($category)
                ->wherePostType('post')
                ->whereStatus(1)
                ->orderBy('id' , 'desc')
                ->paginate(5);
            return view('frontend.index' , compact('posts'));
        }
    }

    public function archive($date){
        $exploded_date = explode('-' , $date);
        $month = $exploded_date[0];
        $year = $exploded_date[1];

        $posts = Post::with(['media' , 'user' , 'category'])
            ->withCount('comments_approve')
            ->whereMonth('created_at' , $month)
            ->whereYear('created_at' , $year)
            ->whereStatus(1)
            ->wherePostType('post')
            ->orderBy('id' , 'desc')
            ->paginate(5);
        return view('frontend.index' , compact('posts'));
    }
    public function author($username){
        $user = User::whereUserName($username)->whereStatus(1)->first()->id;
        if ($user){
            $posts = Post::with(['media' , 'user' , 'category'])
                ->withCount('comments_approve')
                ->whereStatus(1)
                ->whereUserId($user)
                ->wherePostType('post')
                ->orderBy('id' , 'desc')
                ->paginate(5);
            return view('frontend.index' , compact('posts'));        }

    }
}
