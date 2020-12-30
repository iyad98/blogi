<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\GenetalTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use GenetalTraits;

    //
    public function login(Request  $request){
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $token = $user->createToken('login token')->accessToken;
            return response()->json([
                'status' => 'success login',
                'token' => $token
            ]);
        }else {
            return $this->returnError('404' , 'invalid authentication');
        }
    }
}
