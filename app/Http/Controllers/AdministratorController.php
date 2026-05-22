<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Image;

use App\Models\User;

class AdministratorController extends Controller
{
    function fetchAdministrators()
    {
        //$administrators = Administrator::all();

        $administrators = User::select(
            'users.id',
            'users.username',
            DB::raw('(CASE
                WHEN users.role = 0 THEN "Super Administrator"
                WHEN users.role = 1 THEN "Administrator"
                WHEN users.role = 2 THEN "Professor"
                WHEN users.role = 3 THEN "Student"
                ELSE "Unknown"
             END) as role'),
            'users.firstname',
            'users.surname',
            'users.middlename',
            'users.photo',
            'users.email',
            'users.contact',
            'users.status',
            'users.created_at',
            'users.updated_at',
        )->whereIn('users.role', [0, 1])
            ->get();

        return view('admin.administrators', ['administrators' => $administrators]);
    }

    function addAdministrator(Request $request)
    {
        $validation = [
            'surname' => 'required|min:2|max:255',
            'firstname' => 'required|min:2|max:255',
            'middlename' => 'min:0|max:255',
            'email' => 'required|email:rfc,dns',
            'contact' => 'required|min:0|max:11',
            'photo' => 'required|image|max:2048',
        ];

        $request->validate($validation);

        $administrator = User::where('email', $request->email)->first();

        if ($administrator)
            return redirect()->back()->withErrors([
                'message' => 'Administrator add failed! Email address already exists!',
            ]);


        $photo = $request->file('photo');
        $photo_name = uniqid() . '.' . $photo->getClientOriginalExtension();
        $thumbnail = Image::make($photo)->save('storage/images/administrators/' . $photo_name);

        $username = explode('@', $request->email);
        $username = $username[0];

        $password = Hash::make('default' . $request->firstname);

        $administrator = new User;
        $administrator->username = $username;
        $administrator->surname = $request->surname;
        $administrator->firstname = $request->firstname;
        $administrator->middlename = $request->middlename;
        $administrator->role = 1;
        $administrator->email = $request->email;
        $administrator->contact = $request->contact;
        $administrator->photo = $photo_name;
        $administrator->password = $password;
        $administrator->save();

        return redirect()->back()->with('success', 'Administrator has been added successfully!');

    }

    function editAdministrator(Request $request)
    {

        //SELECT `id`, `role`, `username`, `firstname`, `middlename`, `surname`, `photo`, `email`, `contact`, 
        //`password`, `status`, `created_at`, `updated_at`
        $validation = [
            'id' => 'required',
            'surname' => 'required|min:2|max:255',
            'middlename' => 'max:255',
            'firstname' => 'required|min:2|max:255',
            'contact' => 'required',
            'email' => 'required',
            'status' => 'required',
        ];

        $isValidated = $request->validate($validation);
        $photo_name = "";
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photo = $request->file('photo');
            $photo_name = uniqid() . '.' . $photo->getClientOriginalExtension();
            Image::make($photo)->save('storage/images/administrators/' . $photo_name);
        }

        if (!$isValidated) {
            return redirect('administrators');
        } else {
            $user = User::find($request->id);
            $user->surname = $request->surname;
            $user->firstname = $request->firstname;
            $user->middlename = $request->middlename;
            $user->contact = $request->contact;
            $user->email = $request->email;

            if ($photo_name !== "") {
                $user->photo = $photo_name;
            }

            $user->status = $request->status;

            $isSaved = $user->save();

            if (!$isSaved) {
                return redirect()->back()->withErrors([
                    'message' => 'Administrator update failed! Try again later!',
                ]);
            } else {
                return redirect()->back()->with('success', 'Administrator has been updated successfully!');
            }
        }

    }

    function removeAdministrator(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
        ]);

        if (!$validated) {
            return redirect('administrators');
        }

        $administrator = User::find($request->id);
        $administrator->delete();

        return redirect()->back()->with('success', 'Administrator has been deleted successfully!');
    }
}