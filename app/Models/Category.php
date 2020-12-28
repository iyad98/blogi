<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Category extends Model
{
    //
    use Sluggable;
    use SearchableTrait;
    protected $guarded = [];
    protected $searchable = [
        'columns' => [
            'categories.name'       => 10,
            'categories.slug'       => 10,
        ]
    ];

    public function sluggable():array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function posts(){
        return $this->hasMany(Post::class , 'category_id' , 'id');
    }
    public function status(){
        return $this->status == 1 ? 'Active' : 'Inactive';
    }
}
