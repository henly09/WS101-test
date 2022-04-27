<?php

namespace App\Http\Controllers;

use App\Http\Requests\changePasswordRequest;
use App\Models\auther;
use App\Models\book;
use App\Models\book_issue;
use App\Models\category;
use App\Models\publisher;
use App\Models\student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Hashing\BcryptHasher;



class dashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'authors' => auther::count(),
            'publishers' => publisher::count(),
            'categories' => category::count(),
            'books' => book::count(),
            'students' => student::count(),
            'issued_books' => book_issue::count(),
        ]);
    }

    public function change_password_view()
    {
        return view('reset_password');
    }

    public function change_password(changePasswordRequest $request)
    {
        try {
            $valid = validator($request->only('c_password', 'password', 'password_confirmation'), [
                'c_password' => 'required',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
                    ], [
                'c_password.required_with' => 'Confirm password is required.'
            ]);

            if ($valid->fails()) {
                return response()->json([
                            'errors' => $valid->errors(),
                            'message' => 'Faild to update password.',
                            'status' => false
                                ], 200);
            }
//            Hash::check("param1", "param2")
//            param1 - user password that has been entered on the form
//            param2 - old password hash stored in database
            if (Hash::check($request->get('c_password'), Auth::user()->password)) {
                $user = User::find(Auth::user()->id);
                $user->password = (new BcryptHasher)->make($request->get('password'));
                if ($user->save()) {
                    return redirect()->back()->with(['c_password_m' => "Password Changed Successfully!."]);
                }
                 } else {
                    return redirect()->back()->withError(['c_password_m' => "Password Changed Failed!."]);
            }
        } catch (Exception $e) {
            return redirect()->back()->withError(['c_password_m' => "Please Try Again Later!."]);
        }
    }
    
        /*
        $user = Auth::user();

        $this->validate($request, [
            'c_password' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:new_password'
        ]);

        $data = $request->all();

        if(!\Hash::check($data['old_password'], auth()->user()->password)){

             return back()->with('error','You have entered wrong password');

        }else{

            auth()->user()->password = bcrypt($request->password);
            return redirect()->back()->with(['message' => "Password Changed Successfully!."]);

        }

        
        $user->password = Hash::make(Input::get('new_password'));
        $user->save();

        if (Auth::check(["username" => auth()->user()->username, "password" => $request->c_password])) {
            
            auth()->user()->password = bcrypt($request->password);
            return redirect()->back()->with(['message' => "Password Changed Successfully!."]);

        } else {
            return "";
        }  */
        
    
}
