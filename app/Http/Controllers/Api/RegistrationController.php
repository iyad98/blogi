<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Traits\GenetalTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RegistrationController extends Controller
{
    use GenetalTraits;

    //
    public function register(Request $request){

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
        $user->attachRole(Role::whereName('user')->first()->id);

        if ($user){
            return $this->returnSuccessMessage('the user is register successfully' , '200');
        }else{
            return $this->returnError('404' , 'error');
        }

    }

}
