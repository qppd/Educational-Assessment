<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    function administratorLogin(Request $req)
    {

        $credentials = $req->only('username', 'password');

        $isdefaultPassword = false;

        if (strpos($req->password, 'default') !== false) {
            $isdefaultPassword = true;
        } else {
            $isdefaultPassword = false;
        }

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            $userId = Auth::id();
            $userRole = $user->role;
            $userName  = $user->surname . ', ' . $user->firstname;
            $userPhoto = 'storage/images/administrators/' . $user->photo;
            $userStatus = $user->status;

            if($userStatus == 0){
                return redirect()->back()->withErrors([
                    'message' => 'Account locked!',
                ]);
            }

            $req->session()->regenerate();
            $req->session()->put('administrator', $userId);
            $req->session()->put('role', $userRole);
            $req->session()->put('name', $userName);
            $req->session()->put('photo', $userPhoto);
            $req->session()->put('default', $isdefaultPassword);
            // Authentication passed...
            return redirect('/admin/home');
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Username or password invalid!',
            ]);
        }
    }

    function administratorupdatePassword(Request $request){
        $validation = [
            'user_id' => 'required',
            'password' => 'required',
            'curr_password' => 'required',
        ];

        $isValidated = $request->validate($validation);

        if (!$isValidated) {
            return redirect()->back()->withErrors([
                'message' => 'Password or Confirm password invalid!',
            ]);
        } else {

            $user = User::find($request->user_id);

            
            if (Hash::check($request->curr_password, $user->password)) {
                $user->password = Hash::make($request->password);

                
                $ispasswordUpdated = $user->save();

                if ($ispasswordUpdated) {
                    if (session()->has('administrator')) {
                        session()->pull('administrator');
                    }

                    return redirect('/admin/login')->with('success', 'Password has been updated successfully!');
                }else {
                    return redirect()->back()->withErrors([
                        'message' => 'Current password invalid!',
                    ]);
                }

            } else {
                return redirect()->back()->withErrors([
                    'message' => 'Current password invalid!',
                ]);
            }

        }
    }
}
