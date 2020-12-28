<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Page extends Model
{
    //
    use Sluggable;
    use SearchableTrait;

    protected $table = 'posts';
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

    public function status(){
        return $this->status == 1 ? 'Active' : 'DisActive';
    }
    public function user(){
        return $this->belongsTo('App\Models\User'  , 'user_id' , 'id');
    }
    public function category(){
        return $this->belongsTo(Category::class , 'category_id' , 'id');
    }
    public function media(){
        return $this->hasMany(PostMedia::class , 'post_id' , 'id');
    }

}
