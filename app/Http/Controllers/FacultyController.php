<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Image;

use App\Models\User;

use App\Models\Professor;
use App\Models\Student;
use App\Models\Examination;
use App\Models\Question;

class FacultyController extends Controller
{
    function faculties()
    {


        return view('faculty');
    }

    function facultyLogin(Request $request)
    {

        $credentials = $request->only('username', 'password');

        $isdefaultPassword = false;

        if (strpos($request->password, 'default') !== false) {
            $isdefaultPassword = true;
        } else {
            $isdefaultPassword = false;
        }

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            $userId = Auth::id();
            $userEmployeeNo = $user->username;
            $userRole = $user->role;
            $userName = $user->surname . ', ' . $user->firstname;
            $userPhoto = 'storage/images/professors/' . $user->photo;
            $userStatus = $user->status;

            if ($userStatus == 0) {
                return redirect()->back()->withErrors([
                    'message' => 'Account locked!',
                ]);
            }

            $request->session()->regenerate();
            $request->session()->put('professor', $userId);
            $request->session()->put('employee_no', $userEmployeeNo);
            $request->session()->put('role', $userRole);
            $request->session()->put('name', $userName);
            $request->session()->put('photo', $userPhoto);
            $request->session()->put('default', $isdefaultPassword);
            // Authentication passed...

            return redirect('/faculty/dash');
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Username or password invalid!',
            ]);
        }
    }

    function facultyupdatePassword(Request $request)
    {
        $validation = [
            'user_id' => 'required',
            'password' => 'required',
            'curr_password' => 'required',
        ];

        $request->validate($validation);

        $user = User::find($request->user_id);


        if (Hash::check($request->curr_password, $user->password)) {
            $user->password = Hash::make($request->password);


            $ispasswordUpdated = $user->save();

            if ($ispasswordUpdated) {
                if (session()->has('professor')) {
                    session()->pull('professor');
                }

                return redirect('/faculty/login')->with('success', 'Password has been updated  successfully!');
            } else {
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

    function fetchDash()
    {
        $students = Student::whereIn('status', [0, 1])
            ->select(DB::raw('COUNT(*) as students_count'))
            ->get();

        $examinations = Examination::whereIn('status', [0, 1])
            ->select(DB::raw('COUNT(*) as examinations_count'))
            ->get();

        $questions = Question::whereIn('status', [0, 1])
            ->select(DB::raw('COUNT(*) as questions_count'))
            ->where('questions.professor_id', session('professor'))
            ->get();

        $limit = 10;

        // Get the ranked students based on average score
        $rankedStudents = User::select('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo', DB::raw('AVG(results.score) as average_score'))
            ->join('results', 'users.id', '=', 'results.user_id')
            ->groupBy('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo') // Explicitly include all columns from 'users'
            ->orderByDesc('average_score')
            ->limit($limit)
            ->get();

        // $examinations = User::where('role', 3)
        //     ->select(DB::raw('COUNT(*) as examinations_count'))
        //     ->get();

        //$examinations = [];

        return view('faculty.dashboard', ['students' => $students, 'examinations' => $examinations, 'questions' => $questions, 'rankedStudents' => $rankedStudents]);
    }

    function fetchExaminations()
    {

        // $examinations = Examination::select(
        //     'examinations.id',
        //     'examinations.title',
        //     'examinations.duration',
        //     'examinations.limit',
        //     'examinations.description',
        //     DB::raw('(CASE
        //     WHEN examinations.status = 0 THEN "Pending"
        //     WHEN examinations.status = 1 THEN "Active"
        //     WHEN examinations.status = 2 THEN "Finished"
        //     ELSE "Unknown" END) as status'),
        //     'examinations.examination_at',
        //     'examinations.created_at',
        //     'examinations.updated_at',
        //     'examinations.administrator_id',
        //     DB::raw('COUNT(questions.id) as question_count')
        // )
        //     ->leftJoin('questions', 'examinations.id', '=', 'questions.examination_id')
        //     ->groupBy('examinations.id', 'examinations.title', 'examinations.duration', 'examinations.limit', 'examinations.description', 'examinations.status', 'examinations.examination_at', 'examinations.created_at', 'examinations.updated_at', 'examinations.administrator_id')

        //     ->where('examinations.status', '=', 0)
        //     ->get();

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
            'examinations.administrator_id',
            DB::raw('COUNT(CASE WHEN questions.status = 1 THEN questions.id END) as question_count') // Count of questions with status = 1
        )
        ->leftJoin('questions', function ($join) {
            $join->on('examinations.id', '=', 'questions.examination_id')
                ->where('questions.status', '=', 1);
        })
        ->where('examinations.status', '=', 0) // Additional condition for examinations.status = 0
        ->groupBy('examinations.id', 'examinations.title', 'examinations.duration', 'examinations.limit', 'examinations.description', 'examinations.status', 'examinations.examination_at', 'examinations.created_at', 'examinations.updated_at', 'examinations.administrator_id')
        ->get();
        

        return view('faculty.examinations', ['examinations' => $examinations]);
    }

    function fetchQuestions($examination_id)
    {


        $examination = Examination::select('examinations.title AS title')
    ->where('examinations.id', '=', $examination_id)
    ->withCount(['questions' => function ($query) {
        $query->where('status', 1);
    }])
    ->first();
        

        $questions = Question::select(
            'questions.id',
            'questions.question',
            'questions.type AS type_int',
            DB::raw('(CASE
            WHEN questions.type = 0 THEN "Multiple Choice"
            WHEN questions.type = 1 THEN "Enumeration"
            WHEN questions.type = 2 THEN "Fill in the blank"
            ELSE "Unknown" END) as type'),
            'questions.choice_1',
            'questions.choice_2',
            'questions.choice_3',
            'questions.choice_4',
            'questions.answer',
            DB::raw('(CASE
            WHEN questions.status = 0 THEN "Pending"
            WHEN questions.status = 1 THEN "Approved"
            WHEN questions.status = 2 THEN "Rejected"
            ELSE "Unknown" END) as status'),
            DB::raw('CONCAT(users.surname, ", ", users.firstname) as professor'),
            'questions.created_at',
            'questions.updated_at',
        )
            ->join('users', 'users.id', '=', 'questions.professor_id')
            ->where('questions.examination_id', '=', $examination_id)
            ->where('users.id', '=', session('professor'))
            ->get();

        return view('faculty.questions', ['questions' => $questions, 'examination_id' => $examination_id, 'examination' => $examination]);
    }

    function requestQuestion(Request $request)
    {
        $validation = [
            'examination_id' => 'required',
            'question' => 'required',
            'type' => 'required',
            'answer' => 'required',
        ];


        $isValidated = $request->validate($validation);
        if (!$isValidated)
            return redirect()->back()->withErrors([
                'message' => 'Question request failed! Invalid question details.',
            ]);

        $question = new Question;
        $question->examination_id = $request->examination_id;
        $question->question = $request->question;
        $question->type = $request->type;
        $question->choice_1 = $request->choice_a;
        $question->choice_2 = $request->choice_b;
        $question->choice_3 = $request->choice_c;
        $question->choice_4 = $request->choice_d;
        $question->answer = $request->answer;
        $question->professor_id = session('professor');

        $isSaved = $question->save();

        if (!$isSaved)
            return redirect()->back()->withErrors([
                'message' => 'Question request failed! Try again later!',
            ]);
        else
            return redirect()->back()->with('success', 'Question has been added to request list!');

    }

    function editQuestion(Request $request)
    {

        $validation = [
            'id' => 'required',
            'examination_id' => 'examination_id',
            'question' => 'required',
            'type' => 'required',
            'answer' => 'required',
        ];

        //$request->validate($validation);
        $question = Question::find($request->id);
        $question->examination_id = $request->examination_id;
        $question->question = $request->question;
        $question->type = $request->type;
        $question->choice_1 = $request->choice_a;
        $question->choice_2 = $request->choice_b;
        $question->choice_3 = $request->choice_c;
        $question->choice_4 = $request->choice_d;
        $question->answer = $request->answer;
        $question->professor_id = session('professor');

        $isSaved = $question->save();

        if (!$isSaved)
            return redirect()->back()->withErrors([
                'message' => 'Question edit failed! Try again later!',
            ]);
        else
            return redirect()->back()->with('success', 'Question has been updated and added to request list!');
    }


}