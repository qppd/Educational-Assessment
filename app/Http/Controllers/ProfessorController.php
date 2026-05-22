<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\FromArray;

class ProfessorController extends Controller
{

    function fetchProfessors()
    {

        //$professors = Professor::all();
        //SELECT `id`, `role`, `username`, `firstname`, `middlename`, `surname`, 
        //`photo`, `email`, `contact`, `password`, `status`, `created_at`, `updated_at` FROM `users` WHERE 1
        $professors = User::select(
            'users.id',
            'users.username AS employee_no',
            'users.firstname',
            'users.middlename',
            'users.surname',
            'users.contact',
            'users.email',
            'users.status AS status',
            DB::raw('(CASE
            WHEN users.status = 0 THEN "Disabled"
            WHEN users.status = 1 THEN "Enabled"
            ELSE "Unknown" END) AS status_text'),
        )
            ->where('users.role', '=', 2)
            ->get();

        return view('admin.professors', ['professors' => $professors]);
    }
    function addprofessor(Request $request)
    {
        $validation = [
            'employee_no' => 'required|min:6|max:255',
            'surname' => 'required|min:2|max:255',
            'firstname' => 'required|min:2|max:255',
            'email' => 'required|email:rfc,dns',
            'contact' => 'required|min:11|max:11',
            'photo' => 'required|image|max:2048',
        ];

        $validated = $request->validate($validation);

        if (!$validated) {
            return redirect('professors');
        } else {

            $photo = $request->file('photo');
            $photo_name = uniqid() . '.' . $photo->getClientOriginalExtension();
            $thumbnail = Image::make($photo)->save('storage/images/professors/' . $photo_name);

            $password = Hash::make('default' . $request->employee_no);

            // $professor = new Professor;
            // $professor->employee_no = $request->employee_no;
            // $professor->surname = $request->surname;
            // $professor->firstname = $request->firstname;
            // $professor->designation = $request->designation;
            // $professor->email = $request->email;
            // $professor->contact = $request->contact;
            // $professor->photo = $photo_name;
            // $professor->password = $password;
            // $professor->save();

            $user = new User;
            $user->role = 2;
            $user->username = $request->employee_no;
            $user->surname = $request->surname;
            $user->firstname = $request->firstname;
            $user->middlename = $request->middlename;
            $user->photo = $photo_name;
            $user->email = $request->email;
            $user->contact = $request->contact;
            $user->password = $password;
            $user->save();

            return redirect()->back()->with('success', 'Professor has been added successfully!');
        }
    }

    function editProfessor(Request $request)
    {
        $validation = [
            'id' => 'required',
            'employee_no' => 'required',
            'surname' => 'required',
            'firstname' => 'required',
            'contact' => 'required',
            'email' => 'required',
            'status' => 'required',
        ];

        $isValidated = $request->validate($validation);

        if (!$isValidated) {
            return redirect('professors');
        } else {

            $user = User::find($request->id);
            $user->username = $request->employee_no;
            $user->surname = $request->surname;
            $user->firstname = $request->firstname;
            $user->contact = $request->contact;
            $user->email = $request->email;
            $user->status = $request->status;

            $isSaved = $user->save();

            if (!$isSaved) {
                return redirect()->back()->withErrors([
                    'message' => 'Professor update failed! Try again later!',
                ]);
            } else {
                return redirect()->back()->with('success', 'Proffesor information has been updated successfully!');
            }

        }

    }

    // DELETE PROFESSOR
    function removeProfessor(Request $req)
    {

        $validated = $req->validate([
            'id' => 'required',
        ]);

        if (!$validated) {
            return redirect('professors');
        }

        $professor = User::find($req->id);
        $professor->delete();

        return redirect()->back()->with('success', 'Professor has been deleted successfully!');
    }
    // DELETE PROFESSOR


    function uploadProfessors(Request $request)
    {
        $validated = $request->validate([
            'excel' => 'required|mimes:pdf,ppt,doc,docx,xls,xlsx',
        ]);

        //Excel::import(new DonorsImport($request), request()->file('file'));
        $import = new ProfessorsImport($request);
        Excel::import($import, $request->file('excel'));

        return redirect()->back()->with('success', 'Professors has been uploaded successfully!');
    }

    public function exportTemplate()
    {
        return Excel::download(new ProfessorsExport, 'Professor-Template.xlsx');
    }
}

class ProfessorsImport implements ToModel, WithHeadingRow
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function model(array $row)
    {
        if (
            isset($row['employee_no']) && !empty($row['employee_no']) && 
            isset($row['surname']) && !empty($row['surname']) && 
            isset($row['firstname']) && !empty($row['firstname'])
        ) {
            // Check if employee_no contains hyphen
            if (strpos($row['employee_no'], '-') !== false) {
                // Check if it contains both letters and numbers
                if (preg_match('/[a-zA-Z]/', $row['employee_no']) && preg_match('/[0-9]/', $row['employee_no'])) {
                    $password = isset($row['password']) ? Hash::make($row['password']) : Hash::make('defaultPassword');

                    return new User([
                        'username' => $row['employee_no'],
                        'role' => 2,
                        'firstname' => $row['firstname'],
                        'middlename' => $row['middlename'],
                        'surname' => $row['surname'],
                        'contact' => $row['contact'],
                        'email' => $row['email'],
                        'photo' => "professor.jpg",
                        'password' => $password,
                    ]);
                } else {
                    // Log or handle the case where employee_no contains hyphen but doesn't have letters and numbers
                    // Example: Log::error('Invalid employee_no format for row: ' . json_encode($row));
                    // return null; // or any other handling logic
                }
            } else {
                // Check if employee_no is numeric
                if (preg_match('/[0-9]/', $row['employee_no'])) {
                    $password = isset($row['password']) ? Hash::make($row['password']) : Hash::make('defaultPassword');

                    return new User([
                        'username' => $row['employee_no'],
                        'role' => 2,
                        'firstname' => $row['firstname'],
                        'middlename' => $row['middlename'],
                        'surname' => $row['surname'],
                        'contact' => $row['contact'],
                        'email' => $row['email'],
                        'photo' => "professor.jpg",
                        'password' => $password,
                    ]);
                } else {
                    // Log or handle the case where employee_no doesn't contain hyphen and is not numeric
                    // Example: Log::error('Invalid employee_no format for row: ' . json_encode($row));
                    // return null; // or any other handling logic
                }
            }
        }
    }
}

class ProfessorsExport implements FromArray
{
    public function array(): array
    {
        return [
            ['EMPLOYEE NO.', 'FIRSTNAME', 'MIDDLENAME', 'SURNAME', 'CONTACT', 'EMAIL']
        ];
    }
}

