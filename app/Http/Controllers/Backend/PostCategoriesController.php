<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostCategoriesController extends Controller
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
        if (!auth()->user()->ability('admin' , 'manage_post_categories,show_post_categories')){
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }

        $keyword = (isset(request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $status = (isset(request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';

        $categories = Category::query();
        if ($keyword != null){
            $categories = $categories->search($keyword);
        }
        if ($status != null){
            $categories = $categories->whereStatus($status);
        }

        $categories = $categories->orderBy($sort_by, $order_by);
        $categories = $categories->paginate($limit_by);


        return view('backend.post_categories.index' , compact('categories'));
    }


    public function create()
    {
        //
        if (!\auth()->user()->ability('admin', 'create_post_categories')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        return view('backend.post_categories.create');
    }


    public function store(Request $request)
    {
        //
        if (!\auth()->user()->ability('admin', 'create_post_categories')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validatedData = $request->validate([
            'name' =>'required',
            'status' =>'required',
        ]);
        $category = Category::create([
            'name' => $request->name,
            'status' => $request->status
        ]);

        Cache::forget('recent_category');
        if ($category){
            return redirect()->back()->with([
                'message' => 'Category Created Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);
    }


    public function show($id)
    {
        //

    }


    public function edit($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_post_categories')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $category = Category::withCount(['posts'])->find($id);
        return view('backend.post_categories.edit' , compact('category'));
    }


    public function update(Request $request, $id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_post_categories')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validatedData = $request->validate([
            'name' =>'required',
            'status' =>'required',
        ]);
        $category = Category::withCount(['posts'])->find($id);
        if ($category){
            $category->update([
               'name' => $request->name,
               'status' => $request->status
            ]);
            Cache::forget('category');
            return redirect()->back()->with([
                'message' => 'Category Updated Successfully',
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
        if (!\auth()->user()->ability('admin', 'delete_post_categories')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $category = Category::withCount(['posts'])->find($id);
        if ($category){
            $category->delete();
            Cache::forget('category');
            return redirect()->back()->with([
                'message' => 'Category Deleted Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);
    }
}
