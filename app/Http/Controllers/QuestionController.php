<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Image;

use App\Models\Examination;
use App\Models\Question;

class QuestionController extends Controller
{
    function addQuestion(Request $request)
    {
        $validation = [
            'examination_id' => 'required',
            'question' => 'required',
            'type' => 'required',
            'answer' => 'required',
        ];

        $request->validate($validation);

        $question = new Question;
        $question->examination_id = $request->examination_id;
        $question->question = $request->question;
        $question->type = $request->type;
        $question->choice_1 = $request->choice_a;
        $question->choice_2 = $request->choice_b;
        $question->choice_3 = $request->choice_c;
$question->choice_4 = $request->choice_d;
        $question->answer = $request->answer;
        $question->professor_id = session('administrator');

        $isSaved = $question->save();

        if (!$isSaved)
            return redirect()->back()->withErrors([
                'message' => 'Question request failed! Try again later!',
            ]);
        else
            return redirect()->back()->with('success', 'Question has been added to request list!');

    }

   

    function approveQuestion(Request $request)
    {
        $question = Question::where('id', $request->id)->first();
        $question->status = 1;

        $isSaved = $question->save();
        if (!$isSaved)
            return redirect()->back()->withErrors([
                'message' => 'Question approve failed! Try again later!',
            ]);
        else
            return redirect()->back()->with('success', 'Question has been approved!');
    }

    function rejectQuestion(Request $request)
    {
        $question = Question::where('id', $request->id)->first();
        $question->status = 2;

        $isSaved = $question->save();
        if (!$isSaved)
            return redirect()->back()->withErrors([
                'message' => 'Question reject failed! Try again later!',
            ]);
        else
            return redirect()->back()->with('success', 'Question has been rejected!');
    }
}