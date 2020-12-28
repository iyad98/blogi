<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SupervisorsController extends Controller
{
    public function __construct()
    {
        if (\auth()->check()){
            $this->middleware('auth');
        } else {
            return view('backend.auth.login');
        }
    }

    public function index()
    {
        //
        if (!\auth()->user()->ability('admin', 'manage_supervisors,show_supervisors')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }

        $keyword = (isset(\request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $status = (isset(\request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(\request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(\request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(\request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'editor');
        });
        if ($keyword != null) {
            $users = $users->search($keyword);
        }
        if ($status != null) {
            $users = $users->whereStatus($status);
        }
        $users = $users->orderBy($sort_by, $order_by);
        $users = $users->paginate($limit_by);

        return view('backend.supervisors.index', compact('users'));
    }


    public function create()
    {
        //
        if (!\auth()->user()->ability('admin', 'create_supervisors')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $permissions = Permission::pluck('display_name' , 'id');
        return view('backend.supervisors.create' , compact('permissions'));
    }


    public function store(Request $request)
    {
        //
        if (!\auth()->user()->ability('admin', 'create_supervisors')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validationData = $request->validate([
            'name'          => 'required',
            'username'      => 'required|max:20|unique:users',
            'email'         => 'required|email|max:255|unique:users',
            'mobile'        => 'required|numeric|unique:users',
            'status'        => 'required',
            'password'      => 'required|min:8',
            'permissions.*' => 'required'
        ]);

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'mobile'        => $request->mobile,
            'status'        => $request->status,
            'password'      => bcrypt($request->password),
            'email_verified_at' => Carbon::now(),
            'bio' => $request->bio,
            'receive_email' => $request->receive_email
        ]);
        if ($user_image = $request->file('user_image')){
            $filename = Str::slug($request->username).'.'.$user_image->getClientOriginalExtension();
            $path = public_path('assets/users/' . $filename);
            Image::make($user_image->getRealPath())->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path, 100);
            $user->update([
                'user_image' => $filename
            ]);

        }
        $user->attachRole(Role::whereName('editor')->first()->id);
        if (isset($request->permissions) && count($request->permissions) > 0){
            $user->permissions()->sync($request->permissions);
        }
        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'Users created successfully',
            'alert-type' => 'success',
        ]);
    }


    public function show($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'display_supervisors')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }

        $user = User::withCount('posts')->whereId($id)->first();
        if ($user) {
            return view('backend.supervisors.show', compact('user'));
        }
        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger',
        ]);
    }


    public function edit($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_supervisors')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $user = User::find($id);
        if ($user) {
            $permissions = Permission::pluck('display_name', 'id');
            $userPermissions = UserPermission::whereUserId($id)->pluck('permission_id');
            return view('backend.supervisors.edit', compact('user', 'permissions', 'userPermissions'));
        }
        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger',
        ]);
    }


    public function update(Request $request, $id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_supervisors')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validationData = $request->validate([
            'name'          => 'required',
            'username'      => 'required|max:20|unique:users,username,'.$id,
            'email'         => 'required|email|max:255|unique:users,email,'.$id,
            'mobile'        => 'required|numeric|unique:users,mobile,'.$id,
            'status'        => 'required',
            'password'      => 'nullable|min:8',
        ]);
        $user = User::find($id);
        if ($user){
            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'status' => $request->status,
                'bio' => $request->bio,
                'receive_email' => $request->receive_email,
                'email_verified_at' => Carbon::now(),
            ]);
            if ($request->password != ''){
                $user->update([
                    'password' => bcrypt($request->password)
                ]);
            }

            if ($user_image = $request->file('user_image')){
                $filename = Str::slug($request->username).'.'.$user_image->getClientOriginalExtension();
                $path = public_path('assets/users/' . $filename);
                Image::make($user_image->getRealPath())->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path, 100);
                $user->update([
                    'user_image' => $filename
                ]);

            }
            if (isset($request->permissions) && count($request->permissions) > 0){
                $user->permissions()->sync($request->permissions);
            }
            return redirect()->route('admin.supervisors.index')->with([
                'message' => 'Users created successfully',
                'alert-type' => 'success',
            ]);
        }
        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'Something was wrong',
            'alert-type' => 'danger',
        ]);


    }

    public function destroy($id)
    {
        //
        if (!auth()->user()->ability('admin' , 'delete_supervisors')){
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $user = User::find($id);
        if ($user){
            if ($user->user_image != ''){
                if (File::exists('assets/users/'.$user->user_image)){
                    unlink('assets/users/'.$user->user_image);
                }
            }
            $user->delete();
            return redirect()->route('admin.supervisors.index')->with([
                'message' => 'The User Was Deleted Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'Something Was Wrong',
            'alert-type' => 'danger'
        ]);
    }
    public function remove_image(Request  $request){

        if (!\auth()->user()->ability('admin', 'delete_supervisors')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $user = User::find($request->user_id);
        if ($user){
            if (File::exists('public/assets/users/' . $user->user_image)) {
                unlink('public/assets/users/' . $user->user_image);
            }
            $user->user_image = null;
            $user->save();
            return 'true';
        }
        return 'false';

    }
}
