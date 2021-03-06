<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    //
    public function index(){
        return view('frontend.contact');
    }
    public function do_contact(Request $request){

        $validation = Validator::make( $request->all(),[
           'name' => 'required',
           'email' => 'required|email',
           'mobile' => 'nullable|numeric',
           'title' => 'required|min:5',
           'message' => 'required|min:10',
        ]);
        if ($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput();
        }
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['mobile'] = $request->mobile;
        $data['title'] = $request->title;
        $data['message'] = $request->message;

        Contact::create($data);
        return redirect()->back()->with([
            'success' => 'Message sent Successfully'
        ]);
    }
}
