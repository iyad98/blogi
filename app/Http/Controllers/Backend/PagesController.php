<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class PagesController extends Controller
{

    public function __construct()
    {
        if (Auth::check()){
            $this->middleware('auth');
        }else{
            return view('backend.auth.login');
        }
    }

    public function index()
    {
        //
        if (!\auth()->user()->ability('admin', 'manage_pages,show_pages')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $keyword = (isset(request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $category_id = (isset(request()->category_id) && \request()->category_id != '') ? \request()->category_id : null;
        $status = (isset(request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';


        $pages =  Page::where('post_type' , 'page');
        $categories = Category::orderBy('id' , 'desc')->pluck('name' , 'id');

        if ($keyword != null){
            $pages = $pages->search($keyword);
        }
        if ($category_id != null){
            $pages = $pages->where('category_id' ,$category_id);
        }
        if ($status != null){
            $pages = $pages->where('status' , $status);
        }
        $pages = $pages->orderBy($sort_by , $order_by);
        $pages = $pages->paginate($limit_by);
        return view('backend.pages.index' , compact('pages' , 'categories'));
    }
    public function create()
    {
        //
        if (!\auth()->user()->ability('admin', 'create_pages')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $categories = Category::orderBy('id' , 'desc')->pluck('name' , 'id');
        return view('backend.pages.create' , compact('categories'));
    }
    public function store(Request $request)
    {
        //
        if (!\auth()->user()->ability('admin', 'create_pages')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }

        $validateData = $request->validate([
            'title' => 'required',
            'description' => 'required|min:50',
            'category_id' => 'required',
            'status' => 'required',
            'images.*'      => 'nullable|mimes:jpg,jpeg,png,gif|max:20000',

        ]);

        $userID = Auth::id();
        $page = Page::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'user_id' => $userID,
            'post_type' => 'page'
        ]);
        if ($request->images && count($request->images) > 0){
            $i = 1;
            foreach ($request->images as $file){
                $filename = $page->slug.'-'.time().'-'.$i.'.'.$file->getClientOriginalExtension();
                $file_size = $file->getSize();
                $file_type = $file->getMimeType();
                $path = public_path('assets/posts/' . $filename);
                Image::make($file->getRealPath())->resize(800 , null , function ($constraint){
                    $constraint->aspectRatio();

                })->save($path,100);
                $page->media()->create([
                    'file_name' => $filename,
                    'file_type' => $file_type,
                    'file_size' => $file_size
                ]);
                $i++;
            }
        }
        return redirect()->back()->with([
            'message' => 'Page created Successfully',
            'alert-type' => 'success'
        ]);

    }
    public function show($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'display_pages')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
    }
    public function edit($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_pages')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $page = Page::with(['media' , 'user' , 'category'])->where('post_type' , 'page')->find($id);
        $categories = Category::orderBy('id' , 'desc')->pluck('name' , 'id');
        return view('backend.pages.edit' , compact('categories' , 'page'));
    }
    public function update(Request $request, $id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_pages')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validateData = $request->validate([
            'title' => 'required',
            'description' => 'required|min:50',
            'category_id' => 'required',
            'status' => 'required',
            'images.*'      => 'nullable|mimes:jpg,jpeg,png,gif|max:20000',

        ]);

        $page = Page::find($id);
        if ($page){
            $page->update([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status' => $request->status

            ]);
            if ($request->images && count($request->images) > 0){
                $i = 1;
                foreach ($request->images as $file){
                    $filename = $page->slug.'-'.time().'-'.$i.'.'.$file->getClientOriginalExtension();
                    $file_size = $file->getSize();
                    $file_type = $file->getMimeType();
                    $path = public_path('assets/posts/' . $filename);
                    Image::make($file->getRealPath())->resize(800 , null , function ($constraint){
                        $constraint->aspectRatio();

                    })->save($path,100);
                    $page->media()->create([
                        'file_name' => $filename,
                        'file_type' => $file_type,
                        'file_size' => $file_size
                    ]);
                    $i++;
                }
            }
            return redirect()->back()->with([
                'message' => 'Page Updated Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'Something was Wrong',
            'alert-type' => 'error'
        ]);

    }
    public function destroy($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'deleted_pages')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $page = Page::with(['media' , 'category' , 'user'])->find($id);
        if ($page){
            $page->delete();
            return redirect()->back()->with([
                'message' => 'Page Deleted Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->back()->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger'
        ]);
    }
    public function remove_media($media_id){

        if (!\auth()->user()->ability('admin', 'deleted_pages')) {
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
