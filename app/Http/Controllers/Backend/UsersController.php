<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UsersController extends Controller
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
        if (!\auth()->user()->ability('admin', 'manage_users,show_users')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $users = User::whereHas('roles' , function ($q){
           $q->where('name' , 'user');
        })->paginate(10);
        return view('backend.users.index' , compact('users'));
    }


    public function create()
    {
        //
        if (!\auth()->user()->ability('admin', 'create_users')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        return view('backend.users.create');
    }


    public function store(Request $request)
    {
        //
        if (!\auth()->user()->ability('admin', 'create_users')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validatedData = $request->validate([
            'name'          => 'required',
            'username'      => 'required|max:20|unique:users',
            'email'         => 'required|email|max:255|unique:users',
            'mobile'        => 'required|numeric|unique:users',
            'status'        => 'required',
            'password'      => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status,
            'bio' => $request->bio,
            'password' => bcrypt($request->password),
            'receive_email' => $request->receive_email,
            'email_verified_at' => Carbon::now(),
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
        $user->attachRole(Role::whereName('user')->first()->id);
        return redirect()->route('admin.users.index')->with([
            'message' => 'Users created successfully',
            'alert-type' => 'success',
        ]);
    }


    public function show($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'display_users')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $user = User::withCount('posts')->find($id);
        if ($user){
            return view('backend.users.show' , compact('user'));
        }
    }


    public function edit($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_users')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $user = User::find($id);
        if ($user){
            return view('backend.users.edit' , compact('user'));
        }else{
            return redirect()->back()->with([
                'message' => 'Something Was Wrong',
                'alert-type' => 'error'
            ]);
        }
    }


    public function update(Request $request, $id)
    {
        //
        if (!\auth()->user()->ability('admin', 'update_users')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $validatedData = $request->validate([
            'name'          => 'required',
            'username'      => 'required|max:20|unique:users,username,'.$id,
            'email'         => 'required|email|max:255|unique:users,email,'.$id,
            'mobile'        => 'required|numeric|unique:users,mobile,'.$id,
            'status'        => 'required',
            'password'      => 'nullable|min:8',
        ]);
        $user = User::find($id);
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
            if ($user->user_image != '') {
                if (File::exists('public/assets/users/' . $user->user_image)) {
                    unlink('public/assets/users/' . $user->user_image);
                }
            }
            $filename = Str::slug($request->username).'.'.$user_image->getClientOriginalExtension();
            $path = public_path('assets/users/' . $filename);
            Image::make($user_image->getRealPath())->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path, 100);
            $user->update([
                'user_image' => $filename
            ]);

        }
        return redirect()->route('admin.users.index')->with([
            'message' => 'User updated successfully',
            'alert-type' => 'success',
        ]);

    }


    public function destroy($id)
    {
        //
        if (!\auth()->user()->ability('admin', 'delete_users')) {
            return redirect('admin/index')->with([
                'message' => 'You do not have access to this page',
                'alert-type' => 'danger'
            ]);
        }
        $user = User::find($id);
        if ($user){
            $user->delete();
            return redirect()->route('admin.users.index')->with([
                'message' => 'User Deleted Successfully',
                'alert-type' => 'success'
            ]);
        }
        return redirect()->route('admin.users.index')->with([
            'message' => 'Something Was Wrong',
            'alert-type' => 'error'
        ]);
    }

    public function remove_image(Request  $request){

        if (!\auth()->user()->ability('admin', 'delete_users')) {
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
