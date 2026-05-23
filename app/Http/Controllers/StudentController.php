<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\FromArray;

class StudentController extends Controller
{
    function fetchStudents(Request $request)
    {
        $search = $request->get('search', '');

        $query = Student::select(
            'students.id',
            'students.student_no',
            'students.lastname',
            'students.firstname',
            'students.middlename',
            'students.photo',
            'users.contact',
            'users.email',
            'students.status AS student_status',
            'users.status AS user_status',
            DB::raw('CASE WHEN users.username IS NOT NULL THEN "with account" ELSE "without account" END as account_status'),
        )
            ->leftJoin('users', 'students.student_no', '=', 'users.username');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('students.student_no', 'like', "%{$search}%")
                  ->orWhere('students.lastname', 'like', "%{$search}%")
                  ->orWhere('students.firstname', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(25);

        return view('admin.students', ['students' => $students]);
    }

    public function getStudentImages($studentNo)
    {
        $imagePath = '/storage/images/students/' . $studentNo;

        if (Storage::exists($imagePath)) {
            $files = Storage::files($imagePath);

            // Check if there are any files in the directory
            if (count($files) > 0) {
                // Convert file paths to URLs
                $images = array_map(function ($file) {
                    return Storage::url($file);
                }, $files);

                return response()->json(['images' => $images]);
            }
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Storage!',
            ]);
        }

        return response()->json(['message' => 'No images found for the student.'], Response::HTTP_NOT_FOUND);
    }


    function addStudent(Request $request)
    {
        $validation = [
            'student_no' => 'required|min:7|max:255',
            'lastname' => 'required|min:2|max:255',
            'firstname' => 'required|min:2|max:255',
            'middlename' => 'min:0|max:255',
        ];

        $request->validate($validation);

        $password = Hash::make('default' . $request->password);

        $student = new Student;
        $student->student_no = $request->student_no;
        $student->lastname = $request->lastname;
        $student->firstname = $request->firstname;
        $student->middlename = $request->middlename;
        $student->save();

        return redirect()->back()->with('success', 'Student has been added successfully!');
    }

    function editStudent(Request $request)
    {
        $validation = [
            'id' => 'required',
            'student_no' => 'required|min:7|max:255',
            'lastname' => 'required|min:2|max:255',
            'firstname' => 'required|min:2|max:255',
            'middlename' => 'min:0|max:255',
            'sstatus' => 'required',
        ];

        $request->validate($validation);

        //$password = Hash::make('default' . $request->password);

        $student = Student::find($request->id);
        $student->student_no = $request->student_no;
        $student->lastname = $request->lastname;
        $student->firstname = $request->firstname;
        $student->middlename = $request->middlename;
        $student->status = $request->sstatus;
        $student->save();

        $studentNo = $request->student_no;

        // Find the user by student_no
        $user = User::where('username', $studentNo)->first();

        if ($user) {
            // Update the status
            $user->status = $request->sstatus;
            $user->save();

            // Success message or further actions
            return redirect()->back()->with('success', 'Student has been updated successfully!');
        } else {
            // User not found
            return response()->json(['error' => 'User not found'], 404);
        }




    }

    function editStudent(Request $request)
    {
        $validation = [
            'id' => 'required',
            'student_no' => 'required|min:7|max:255',
            'lastname' => 'required|min:2|max:255',
            'firstname' => 'required|min:2|max:255',
            'middlename' => 'min:0|max:255',
            'sstatus' => 'required',
        ];

        $request->validate($validation);

        //$password = Hash::make('default' . $request->password);

        $student = Student::find($request->id);
        $student->student_no = $request->student_no;
        $student->lastname = $request->lastname;
        $student->firstname = $request->firstname;
        $student->middlename = $request->middlename;
        $student->status = $request->sstatus;
        $student->save();

        // Success message or further actions
        return redirect()->back()->with('success', 'Student has been updated successfully!');
    }

    function removeStudent(Request $req)
    {

        $validated = $req->validate([
            'id' => 'required',
        ]);

        if (!$validated) {
            return redirect('students');
        }

        $student = Student::find($req->id);
        $student->delete();

        return redirect()->back()->with('success', 'Student has been deleted successfully!');
    }



    function uploadStudents(Request $request)
    {
        $validated = $request->validate([
            'excel' => 'required|mimes:xls,xlsx',
        ]);

        //Excel::import(new DonorsImport($req), request()->file('excel'));
        $import = new StudentsImport($request);
        Excel::import($import, $request->file('excel'));

        return redirect()->back()->with('success', 'Student has been uploaded successfully!');
    }

    public function exportTemplate()
    {
        return Excel::download(new StudentsExport, 'Student-Template.xlsx');
    }
}

class StudentsImport implements ToModel, WithHeadingRow
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function model(array $row)
    {

        // Check if 'student_no' is set and not empty
        if (
            isset($row['student_no']) && !empty($row['student_no']) &&
            isset($row['lastname']) && !empty($row['lastname']) &&
            isset($row['firstname']) && !empty($row['firstname'])
        ) {
            if (preg_match('/[0-9]/', $row['student_no'])) {
                $password = isset($row['password']) ? Hash::make($row['password']) : Hash::make('defaultPassword');

                return new Student([
                    'student_no' => $row['student_no'],
                    'firstname' => $row['firstname'],
                    'middlename' => $row['middlename'],
                    'lastname' => $row['lastname'],
                ]);
            }
        }
    }
}

class StudentsExport implements FromArray
{
    public function array(): array
    {
        return [
            ['STUDENT NO.', 'FIRSTNAME', 'MIDDLENAME', 'LASTNAME']
        ];
    }
}
