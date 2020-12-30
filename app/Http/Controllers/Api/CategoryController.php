<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\GenetalTraits;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    use GenetalTraits;


    public function index()
    {
        //
        $categories = Category::get();
        return $this->returnData('categories' , $categories , 'success');
    }



    public function store(Request $request)
    {
        //
        $category = Category::create([
            'name' => $request->name,
            'status' => $request->status
        ]);
        if ($category){
            return $this->returnSuccessMessage('category created successfully' , '200');
        }else{
            return $this->returnError('404' , 'Error');
        }
    }

    public function show($id)
    {
        //
    }




    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
