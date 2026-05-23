<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Image;

use App\Models\Examination;
use App\Models\Question;
use App\Models\Reviewer;

class ExaminationController extends Controller
{
    function fetchExaminations()
    {

        // $examinations = Examination::select(
        //     'examinations.id',
        //     'examinations.title',
        //     'examinations.duration',
        //     'examinations.limit',
        //     'examinations.description',
        //     'examinations.status AS statusInt',
        //     DB::raw('(CASE
        //         WHEN examinations.status = 0 THEN "Pending"
        //         WHEN examinations.status = 1 THEN "Active"
        //         WHEN examinations.status = 2 THEN "Finished"
        //         ELSE "Unknown" END) as status'),
        //     'examinations.examination_at',
        //     'examinations.created_at',
        //     'examinations.updated_at',
        //     'examinations.administrator_id',
        //     DB::raw('COUNT(questions.id) as question_count') // Count of questions
        // )
        // ->leftJoin('questions', 'examinations.id', '=', 'questions.examination_id')
        // ->groupBy('examinations.id', 'examinations.title', 'examinations.duration', 'examinations.limit', 'examinations.description', 'examinations.status', 'examinations.examination_at', 'examinations.created_at', 'examinations.updated_at', 'examinations.administrator_id')
        // ->paginate(25);

        $examinations = Examination::select(
            'examinations.id',
            'examinations.title',
            'examinations.duration',
            'examinations.limit',
            'examinations.description',
            'examinations.status AS statusInt',
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
        ->groupBy('examinations.id', 'examinations.title', 'examinations.duration', 'examinations.limit', 'examinations.description', 'examinations.status', 'examinations.examination_at', 'examinations.created_at', 'examinations.updated_at', 'examinations.administrator_id')
        ->paginate(25);

        return view('admin.examinations', ['examinations' => $examinations]);
    }

    function manageExamination($examination_id)
    {
        //dd($request>examination_id);
        $examination = Examination::select('examinations.title AS title')
            ->where('examinations.id', '=', $examination_id)
            ->first(); // Use first() instead of get()

        //$request->session()->put('examination_id', $request->examination_id);

        $questions = Question::select(
            'questions.id',
            'questions.question',
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
            ->paginate(25);

        return view('admin.questions', ['questions' => $questions, 'examination_id' => $examination_id, 'examination' => $examination]);

    }

    function manageReviewers($examination_id)
    {
        //dd($request>examination_id);
        $examination = Examination::select('examinations.title AS title')
        ->where('examinations.id', '=', $examination_id)
        ->first(); // Use first() instead of get()
        //$request->session()->put('examination_id', $request->examination_id);

        $reviewers = Reviewer::select(
            'reviewers.id',
            'reviewers.file',
            DB::raw('(CASE
            WHEN reviewers.status = 0 THEN "Pending"
            WHEN reviewers.status = 1 THEN "Approved"
            WHEN reviewers.status = 2 THEN "Rejected"
            ELSE "Unknown" END) as status'),
            DB::raw('CONCAT(users.surname, ", ", users.firstname) as professor'),
            'reviewers.created_at',
            'reviewers.updated_at',
        )
            ->join('users', 'users.id', '=', 'reviewers.professor_id')
            ->where('reviewers.examination_id', '=', $examination_id)
            ->paginate(25);

        return view('admin.reviewers', ['reviewers' => $reviewers, 'examination_id' => $examination_id, 'examination' => $examination]);

    }

    function addExamination(Request $request)
    {
        $validation = [
            'title' => 'required',
            'duration' => 'required',
            'limit' => 'required',
            'description' => 'required',
            'datetime' => 'required',

        ];

        $validated = $request->validate($validation);

        if (!$validated) {
            return redirect('examinations');
        } else {

            $examination = new Examination;
            $examination->title = $request->title;
            $examination->duration = $request->duration;
            $examination->limit = $request->limit;
            $examination->description = $request->description;
            $examination->examination_at = $request->datetime . ':00';
            $examination->administrator_id = session('administrator');
            $examination->save();


            return redirect()->back()->with('success', 'Examination has been added successfully!');
        }
    }

    function editExamination(Request $request)
    {

        $validation = [
            'id' => 'required',
            'title' => 'required',
            'duration' => 'required',
            'limit' => 'required',
            'description' => 'required',
            'datetime' => 'required',
            'status' => 'required',
        ];

        $validated = $request->validate($validation);
        $examination = Examination::find($request->id);
        $examination->title = $request->title;
        $examination->duration = $request->duration;
        $examination->limit = $request->limit;
        $examination->description = $request->description;
        $examination->examination_at = $request->datetime;
        $examination->status = $request->status;
        $examination->administrator_id = session('administrator');
        $examination->save();


        return redirect()->back()->with('success', 'Examination has been updated successfully!');
    }

    function removeExamination(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
        ]);

        if (!$validated) {
            return redirect('examinations');
        }

        $examination = Examination::find($request->id);
        $examination->delete();

        return redirect()->back()->with('success', 'Examination has been deleted successfully!');
    }
}