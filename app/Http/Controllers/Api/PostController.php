<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\GenetalTraits;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PostController extends Controller
{

    use GenetalTraits;

    public function index()
    {
        //
        $post = Post::with(['user' , 'media' , 'category' , 'comments'])->get();
        return response()->json([
            'post' => $post
        ]);
    }


    public function store(Request $request)
    {
        //

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'post_type' => $request->post_type,
            'comment_able' => $request->comment_able,
            'category_id' => $request->category_id,
            'user_id' => $request->user_id
        ]);

        if ($request->images && count($request->images) > 0){
            $i = 1;
            foreach ($request->images as $file){

                $post->media()->create([
                    'file_name' => $post->slug.'-'.time().'-'.$i,
                    'file_type' => 'png',
                    'file_size' => '10mb'
                ]);
                $i++;
            }
        }
        if ($post){
            return $this->returnSuccessMessage('post created successfully' , '200');
        }else{
            return $this->returnError('404' , 'this post not found');
        }

    }


    public function show($id)
    {
        //
        $post = Post::with(['user' , 'media' , 'category' , 'comments'])->find($id);
        if ($post){
            return response()->json([
                'post' => $post
            ]);
        }else{
            return $this->returnError('404' , 'this post not found');
        }

    }



    public function update(Request $request, $id)
    {
        //
        $post = Post::with(['media' , 'user' , 'category'])->find($id);
        if ($post){
            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'comment_able' => $request->comment_able,
                'category_id' => $request->category_id,
            ]);

            return $this->returnSuccessMessage('post updated successfully' , '200');
        }
        return $this->returnError('404' , 'post not found');

    }

    public function destroy($id)
    {
        $post = Post::with(['media' , 'category' , 'user'])->find($id);
        if ($post){
            $post->delete();
            return $this->returnSuccessMessage('post deleted successfully' , '200');
        }
        return $this->returnError('404' , 'post not found');
    }
}
