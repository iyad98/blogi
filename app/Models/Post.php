<?php

namespace App\Models;


use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Post extends Model
{
    use Sluggable;
    use SearchableTrait;
    protected $guarded = [];
    protected $searchable = [
        'columns' => [
            'posts.title'       => 10,
            'posts.description' => 10,
        ]
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ]
        ];

    }


    public function user(){
        return $this->belongsTo('App\Models\User'  , 'user_id' , 'id');
    }

    public function category(){
        return $this->belongsTo(Category::class , 'category_id' , 'id');
    }
    public function comments(){
        return $this->hasMany(Comment::class , 'post_id' , 'id');
    }
    public function comments_approve(){
        return $this->hasMany(Comment::class , 'post_id' , 'id')->whereStatus(1);
    }
    public function media(){
        return $this->hasMany(PostMedia::class , 'post_id' , 'id');
    }

    public function status(){
        return $this->status == 1 ? 'Active' : 'DisActive';
    }
}
