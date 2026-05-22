<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Image;


use App\Models\User;
use App\Models\Student;
use App\Models\Examination;
use App\Models\Question;
use App\Models\Result;
use App\Models\Answer;

class PortalController extends Controller
{
    function portals()
    {

        $students = User::select(
            'users.username',
        )
            ->where('users.status', '=', 1)
            ->where('users.role', '=', 3)
            ->get();

        //dd($students);    
        $studentUsernames = $students->pluck('username')->toArray();
        return view('portal', ['studentUsernames' => $studentUsernames]);
    }

    function forgotPassword()
    {

        $students = User::select(
            'users.username',
        )
            ->where('users.status', '=', 1)
            ->where('users.role', '=', 3)
            ->get();

        //dd($students);    
        $studentUsernames = $students->pluck('username')->toArray();
        return view('forgot-password', ['studentUsernames' => $studentUsernames]);
    }

    function registerPage()
    {

        return view('register');
    }

    function dashboardPage()
    {
        // Assuming you want to fetch the top 10 students
        $limit = 10;

        $rankedStudents = User::select('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo', DB::raw('(SUM(results.score) / (COUNT(questions.id) * MAX(examinations.limit))) * 100 AS percentage_score'))
            ->join('results', 'users.id', '=', 'results.user_id')
            ->join('examinations', 'results.examination_id', '=', 'examinations.id')
            ->join('questions', 'examinations.id', '=', 'questions.examination_id')
            ->groupBy('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo')
            ->orderByDesc('percentage_score')
            ->limit($limit)
            ->get();

        // Now $rankedStudents contains the result set

        return view('dashboard', ['rankedStudents' => $rankedStudents]);
    }

    function examinationsAvailablePage()
    {
        // $examinations = Examination::select(
        //     'examinations.id',
        //     'examinations.title',
        //     'examinations.duration',
        //     'examinations.limit',
        //     'examinations.description',
        //     DB::raw('(CASE
        //         WHEN examinations.status = 0 THEN "Pending"
        //         WHEN examinations.status = 1 THEN "Active"
        //         WHEN examinations.status = 2 THEN "Finished"
        //         ELSE "Unknown" END) as status'),
        //     'examinations.examination_at',
        //     'examinations.created_at',
        //     'examinations.updated_at',
        //     'examinations.administrator_id'
        // )
        //     ->leftJoin('results', 'examinations.id', '=', 'results.examination_id')
        //     ->whereNull('results.user_id') // Check for NULL values in the results table
        //     ->where('examinations.status', '=', 1)
        //     ->where('results.user_id', '=', session('student'))
        //     ->get();

        $studentId = session('student');

        $examinations = Examination::select(
            'examinations.id',
            'examinations.title',
            'examinations.duration',
            'examinations.limit',
            'examinations.description',
            DB::raw('(CASE
        WHEN examinations.status = 0 THEN "Pending"
        WHEN examinations.status = 1 THEN "Active"
        WHEN examinations.status = 2 THEN "Finished"
        ELSE "Unknown" END) as status'),
            'examinations.examination_at',
            'examinations.created_at',
            'examinations.updated_at',
            'examinations.administrator_id'
        )
            ->where('examinations.status', '=', 1)
            ->whereDoesntHave('results', function ($query) use ($studentId) {
                $query->where('user_id', '=', $studentId);
            })
            ->orderBy('examinations.examination_at', 'DESC')
            ->get();

        return view('examinations_available', ['examinations' => $examinations]);
    }

    function examinationsTakenPage()
    {
        $examinations = Examination::select(
            'examinations.id',
            'examinations.title',
            'examinations.duration',
            'examinations.limit',
            'examinations.description',
            DB::raw('(CASE
                WHEN examinations.status = 0 THEN "Pending"
                WHEN examinations.status = 1 THEN "Active"
                WHEN examinations.status = 2 THEN "Finished"
                ELSE "Unknown" END) as status'),
            'examinations.examination_at',
            'examinations.created_at',
            'examinations.updated_at',
            'examinations.administrator_id'
        )
            ->join('results', 'examinations.id', '=', 'results.examination_id')
            ->where('examinations.status', '=', 1)
            ->where('results.user_id', '=', session('student'))
            ->get();

        return view('examinations_taken', ['examinations' => $examinations]);
    }

    public function examinationAttempt(Request $request)
    {

        //`id`, `user_id`, `examination_id`, `score`, `remarks`, `status`, `created_at`, `updated_at` 


        $result = new Result;
        $result->user_id = session('student');
        $result->examination_id = $request->id;

        $result->save();

        $students = User::select(
            'users.username',
        )
            ->where('users.id', '=', session('student'))
            ->where('users.status', '=', 1)

            ->where('users.role', '=', 3)
            ->get();

        //dd($students);    
        $studentUsernames = $students->pluck('username')->toArray();

        $examination = Examination::select(
            'examinations.id',
            'examinations.title',
            'examinations.duration',
            'examinations.limit',
            'examinations.description',
            'examinations.examination_at',
            'examinations.created_at',
            'examinations.updated_at',
            'examinations.administrator_id'
        )
            ->where('examinations.id', '=', $request->id)
            ->get();

        //`id`, `examination_id`, `question`, `type`, `choice_1`, `choice_2`, 
        //`choice_3`, `choice_4`, `answer`, `status`, `professor_id`, `created_at`, `updated_at`

        $questions = Question::select(
            'questions.id',
            'questions.question',
            DB::raw('(CASE
                WHEN questions.type = 0 THEN "Multiple choice"
                WHEN questions.type = 1 THEN "Enumeration"
                WHEN questions.type = 2 THEN "Fill in the blank"
                ELSE "Unknown" END) AS type'),
            'questions.choice_1',
            'questions.choice_2',
            'questions.choice_3',
            'questions.choice_4',
        )
            ->where('questions.examination_id', '=', $request->id)
            ->where('questions.status', '=', 1)
            ->get();

        return view('examination_attempt', ['examination' => $examination, 'questions' => $questions, 'studentUsernames' => $studentUsernames]);
    }

    function examinationSubmit(Request $request)
    {

        //SELECT `id`, `student_id`, `examination_id`, `question_id`, `student_answer`, `status`, 
        //`created_at`, `updated_at` FROM `answers` WHERE 1


        $user_id = session('student'); // Assuming you are using authentication

        // Extract common data from the request
        $examination_id = $request->input('examination_id');

        // Process and save answers
        // foreach ($request->input('question_ids', []) as $index => $question_id) {
        //     $answer = new Answer();

        //     $answer->examination_id = $examination_id;
        //     $answer->student_id = $user_id;
        //     $answer->question_id = $question_id;
        //     $answer->status = 1;

        //     if ($request->has("answers.$index")) {
        //         $answer->student_answer = $request->input("answers.$index");
        //     } elseif ($request->has("fill_in_the_blank_answers.$index")) {
        //         $answer->student_answer = $request->input("fill_in_the_blank_answers.$index");
        //     } elseif ($request->has("enumeration_answers.$index")) {
        //         $answer->student_answer = $request->input("enumeration_answers.$index");
        //     }

        //     $answer->save();
        // }

        foreach ($request->input('question_ids', []) as $index => $question_id) {
            $answer = new Answer();

            $answer->examination_id = $examination_id;
            $answer->student_id = $user_id;
            $answer->question_id = $question_id;
            $answer->status = 1;

            $sanswer = "";
            if ($request->has("answers.$index")) {
                $answer->student_answer = $request->input("answers.$index");
                $sanswer = $request->input("answers.$index");
            } elseif ($request->has("fill_in_the_blank_answers.$index")) {
                $answer->student_answer = $request->input("fill_in_the_blank_answers.$index");
                $sanswer = $request->input("fill_in_the_blank_answers.$index");
            } elseif ($request->has("enumeration_answers.$index")) {
                $answer->student_answer = $request->input("enumeration_answers.$index");
                $sanswer = $request->input("enumeration_answers.$index");
            }



            // Check if the answer is correct and update the score
            $question = Question::find($question_id);
            if ($question && $answer->student_answer === $question->answer) {
                $result = Result::where('user_id', $user_id)
                    ->where('examination_id', $examination_id)
                    ->first();

                if ($result) {
                    // Increment the score if the result already exists
                    $result->score += 1;
                    $result->save();
                } else {
                    // Create a new result record if it doesn't exist
                    Result::create([
                        'user_id' => $user_id,
                        'examination_id' => $examination_id,
                        'score' => 1, // Initial score for the correct answer
                        'status' => 1, // You may adjust this status accordingly
                    ]);
                }
            }

            $answer->save();
        }


        // Optionally, you can redirect the user to a thank-you page or any other page
        return redirect('/portal/dashboard')
            ->with('success', 'Answer submitted successfully!');


    }

    function examinationResult($examination_id)
    {

        $examinations = Examination::select('examinations.title', 'examinations.limit')
            ->where('examinations.id', '=', $examination_id)
            ->get();


        // Count correct and wrong answers
        $results = DB::table('answers')
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->where('answers.examination_id', $examination_id)
            ->where('answers.student_id', session('student'))
            ->select(
                'questions.answer as correct_answer',
                'answers.student_answer',
                DB::raw('CASE WHEN LOWER(questions.answer) = LOWER(answers.student_answer) THEN "correct" ELSE "wrong" END as result')
            )
            ->get();

        $correctCount = $results->where('result', 'correct')->count();
        $wrongCount = $results->where('result', 'wrong')->count();

        // Optionally, you can do something with the counts (e.g., store in the database, display to the user, etc.)
// For now, let's just print them


        return view('examination_student_result', ['results' => $results, 'examinations' => $examinations, 'correctCount' => $correctCount, 'wrongCount' => $wrongCount]);
    }

    function studentRegister(Request $request)
    {
        $validation = [
            'student_no' => 'required',
            'email' => 'required',
            'contact' => 'required',
            'photos.*' => 'image|mimes:jpeg,png,gif|max:2048',
            'password' => 'required',
            'confirm_password' => 'required',
        ];

        if (!$request->hasFile('photos')) {
            return redirect()->back()->withErrors([
                'message' => 'Registration failed! Your face photos is required for face recognition.',
            ]);
        }

        $request->validate($validation);

        if ($request->password != $request->confirm_password) {
            return redirect()->back()->withErrors([
                'message' => 'Registration failed! Passwords do not match.',
            ]);
        }


        // Use the Eloquent model to find a student by student_no
        $student = Student::where('student_no', $request->student_no)->first();

        if (!$student) {
            return redirect()->back()->withErrors([
                'message' => 'Registration failed! Student ID is invalid.',
            ]);
        } else {
            $user = User::where('username', $student->student_no)->first();
            if ($user) {
                return redirect()->back()->withErrors([
                    'message' => 'Registration failed! Student ID is already registered.',
                ]);
            }
        }

        $photo_counter = 0;

        if ($request->hasFile('photos')) {
            $studentNo = $request->student_no;
            $targetDirectory = 'storage/images/students/' . $studentNo;

            // Check if the target directory exists, and create it if it doesn't
            if (!is_dir($targetDirectory)) {
                if (!mkdir($targetDirectory, 0755, true)) {
                    // Directory creation failed, handle the error
                    return response()->json(['error' => 'Failed to create the directory.']);
                }
            }

            foreach ($request->file('photos') as $photo) {
                $photo_counter++;

                $photo_name = $photo_counter . '.' . $photo->getClientOriginalExtension();

                //$photo_name = encrypt($photo_name);

                // Specify the full path to save the image
                $pathToSave = $targetDirectory . '/' . $photo_name;

                // Check for errors during image save
                $saveResult = Image::make($photo)->save($pathToSave);

                if (!$saveResult) {
                    // Handle the error, e.g., log it or return a response
                    return response()->json(['error' => 'Failed to save the image.']);
                }
            }
        }


        $user = new User;
        $user->username = $request->student_no;
        $user->firstname = $student->firstname;
        $user->surname = $student->lastname;
        $user->role = 3;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->photo = 'student.jpg';
        $user->password = Hash::make($request->password);

        $isRegistered = $user->save();

        if ($isRegistered) {
            return redirect('/portal')
                ->with('success', 'Registration successful. You can now login using face recognition.');
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Registration failed! Try again later!',
            ]);
        }

    }

    public function studentLogin(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            $userId = Auth::id();
            $userStudentNo = $user->username;
            $userRole = $user->role;
            $userName = $user->surname . ', ' . $user->firstname . ', ' . $user->middlename;
            $userPhoto = 'storage/images/students/' . $user->photo;
            $userStatus = $user->status;

            if ($userStatus == 0) {
                return redirect()->back()->withErrors([
                    'message' => 'Account locked!',
                ]);
            }

            $request->session()->put('student', $userId);
            $request->session()->put('student_no', $userStudentNo);
            $request->session()->put('role', $userRole);
            $request->session()->put('name', $userName);
            $request->session()->put('photo', $userPhoto);
            //$req->session()->put('default', $isdefaultPassword);
            // Authentication passed...
            return redirect('/portal/dashboard');
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Username or password invalid!',
            ]);
        }

    }

    public function findUserByLabel($label)
    {
        $user = User::where('username', $label)->first();

        if ($user) {
            return response()->json($user);
        } else {

            return response()->json(['message' => $label], 404);
        }
    }


    function saveNewPassword(Request $request)
    {
        $validation = [
            'username' => 'required',
            'ppassword' => 'required',
        ];

        $request->validate($validation);

        // Use the Eloquent model to find a student by student_no
        $student = User::where('username', $request->username)->first();

        $student->password = Hash::make($request->ppassword);
        $isSaved = $student->save();
        if (!$isSaved) {
            return redirect()->back()->withErrors([
                'message' => 'Password update failed! Please try again!',
            ]);
        } else {
            return redirect('/portal')
                ->with('success', 'Password update successful. You can now login using face recognition.');
        }

    }

}