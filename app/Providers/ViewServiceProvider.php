<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if (!Request::is('/admin/*'))
            Paginator::defaultView('vendor.pagination.blogi');

        view()->composer('*' , function ($view){
        if (! Cache::has('recent_post')){

                $recent_post = Post::with(['user' , 'category' , 'media'])->whereHas('category' , function ($q){
                    $q->whereStatus(1);
                })->whereHas('user' , function ($q){
                    $q->whereStatus(1);
                })->wherePostType('post')->whereStatus(1)->orderBy('id' , 'desc')->limit(5)->get();

                Cache::remember('recent_post' , 3600 , function () use ($recent_post){
                    return $recent_post;
                });
        }
            $recent_post = Cache::get('recent_post');


            if (! Cache::has('recent_comment')){

                $recent_comment = Comment::with(['user' , 'post'])->whereHas('post' , function ($q){
                    $q->whereStatus(1);
                })->whereStatus(1)->orderBy('id' , 'desc')->limit(5)->get();

                Cache::remember('recent_comment' , 3600 , function () use ($recent_comment){
                    return $recent_comment;
                });
            }
            $recent_comment = Cache::get('recent_comment');

            if (! Cache::has('category')){
                $category = Category::with(['posts'])->whereHas('posts' , function ($q){
                    $q->whereStatus(1);
                })->whereStatus(1)->orderBy('id' , 'desc')->limit(5)->get();

                Cache::remember('category' , 3600 , function () use ($category){
                    return $category;
                });
            }
            $recent_category = Cache::get('category');

            if (! Cache::has('global_archives')){
                $global_archives = Post::whereStatus(1)->orderBy('created_at' , 'desc')
                    ->select(DB::raw("Year(created_at) as year") , DB::raw("Month(created_at) as month"))
                    ->pluck('year' , 'month')->toArray();

                Cache::remember('global_archives' , 3600 , function () use ($global_archives){
                    return $global_archives;
                });
            }
            $global_archives = Cache::get('global_archives');

            $view->with([
                'recent_post' => $recent_post,
                'recent_comment' => $recent_comment,
                'recent_category' => $recent_category,
                'global_archives' => $global_archives
            ]);


        });



    }
}
