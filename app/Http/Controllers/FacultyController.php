     1|<?php
     2|
     3|namespace App\Http\Controllers;
     4|
     5|use Illuminate\Http\Request;
     6|use Illuminate\Support\Facades\Auth;
     7|use Illuminate\Support\Facades\Hash;
     8|use Illuminate\Support\Facades\Storage;
     9|use Illuminate\Support\Facades\DB;
    10|use Image;
    11|
    12|use App\Models\User;
    13|
    14|use App\Models\Professor;
    15|use App\Models\Student;
    16|use App\Models\Examination;
    17|use App\Models\Question;
    18|
    19|class FacultyController extends Controller
    20|{
    21|    function faculties()
    22|    {
    23|
    24|
    25|        return view('faculty.login');
    26|    }
    27|
    28|    function facultyLogin(Request $request)
    29|    {
    30|
    31|        $credentials = $request->only('username', 'password');
    32|
    33|        $isdefaultPassword = false;
    34|
    35|        if (strpos($request->password, 'default') !== false) {
    36|            $isdefaultPassword = true;
    37|        } else {
    38|            $isdefaultPassword = false;
    39|        }
    40|
    41|        if (Auth::attempt($credentials)) {
    42|
    43|            $user = Auth::user();
    44|
    45|            $userId = Auth::id();
    46|            $userEmployeeNo = $user->username;
    47|            $userRole = $user->role;
    48|            $userName = $user->surname . ', ' . $user->firstname;
    49|            $userPhoto = 'storage/images/professors/' . $user->photo;
    50|            $userStatus = $user->status;
    51|
    52|            if ($userStatus == 0) {
    53|                return redirect()->back()->withErrors([
    54|                    'message' => 'Account locked!',
    55|                ]);
    56|            }
    57|
    58|            $request->session()->regenerate();
    59|            $request->session()->put('professor', $userId);
    60|            $request->session()->put('employee_no', $userEmployeeNo);
    61|            $request->session()->put('role', $userRole);
    62|            $request->session()->put('name', $userName);
    63|            $request->session()->put('photo', $userPhoto);
    64|            $request->session()->put('default', $isdefaultPassword);
    65|            // Authentication passed...
    66|
    67|            return redirect('/faculty/dash');
    68|        } else {
    69|            return redirect()->back()->withErrors([
    70|                'message' => 'Username or password invalid!',
    71|            ]);
    72|        }
    73|    }
    74|
    75|    function facultyupdatePassword(Request $request)
    76|    {
    77|        $validation = [
    78|            'user_id' => 'required',
    79|            'password' => 'required',
    80|            'curr_password' => 'required',
    81|        ];
    82|
    83|        $request->validate($validation);
    84|
    85|        $user = User::find($request->user_id);
    86|
    87|
    88|        if (Hash::check($request->curr_password, $user->password)) {
    89|            $user->password = Hash::make($request->password);
    90|
    91|
    92|            $ispasswordUpdated = $user->save();
    93|
    94|            if ($ispasswordUpdated) {
    95|                if (session()->has('professor')) {
    96|                    session()->pull('professor');
    97|                }
    98|
    99|                return redirect('/faculty/login')->with('success', 'Password has been updated  successfully!');
   100|            } else {
   101|                return redirect()->back()->withErrors([
   102|                    'message' => 'Current password invalid!',
   103|                ]);
   104|            }
   105|
   106|        } else {
   107|            return redirect()->back()->withErrors([
   108|                'message' => 'Current password invalid!',
   109|            ]);
   110|        }
   111|
   112|    }
   113|
   114|    function fetchDash()
   115|    {
   116|        $students = Student::whereIn('status', [0, 1])
   117|            ->select(DB::raw('COUNT(*) as students_count'))
   118|            ->get();
   119|
   120|        $examinations = Examination::whereIn('status', [0, 1])
   121|            ->select(DB::raw('COUNT(*) as examinations_count'))
   122|            ->get();
   123|
   124|        $questions = Question::whereIn('status', [0, 1])
   125|            ->select(DB::raw('COUNT(*) as questions_count'))
   126|            ->where('questions.professor_id', session('professor'))
   127|            ->get();
   128|
   129|        $limit = 10;
   130|
   131|        // Get the ranked students based on average score
   132|        $rankedStudents = User::select('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo', DB::raw('AVG(results.score) as average_score'))
   133|            ->join('results', 'users.id', '=', 'results.user_id')
   134|            ->groupBy('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo') // Explicitly include all columns from 'users'
   135|            ->orderByDesc('average_score')
   136|            ->limit($limit)
   137|            ->get();
   138|
   139|        // $examinations = User::where('role', 3)
   140|        //     ->select(DB::raw('COUNT(*) as examinations_count'))
   141|        //     ->get();
   142|
   143|        //$examinations = [];
   144|
   145|        return view('faculty.dashboard', ['students' => $students, 'examinations' => $examinations, 'questions' => $questions, 'rankedStudents' => $rankedStudents]);
   146|    }
   147|
   148|    function fetchExaminations()
   149|    {
   150|
   151|        // $examinations = Examination::select(
   152|        //     'examinations.id',
   153|        //     'examinations.title',
   154|        //     'examinations.duration',
   155|        //     'examinations.limit',
   156|        //     'examinations.description',
   157|        //     DB::raw('(CASE
   158|        //     WHEN examinations.status = 0 THEN "Pending"
   159|        //     WHEN examinations.status = 1 THEN "Active"
   160|        //     WHEN examinations.status = 2 THEN "Finished"
   161|        //     ELSE "Unknown" END) as status'),
   162|        //     'examinations.examination_at',
   163|        //     'examinations.created_at',
   164|        //     'examinations.updated_at',
   165|        //     'examinations.administrator_id',
   166|        //     DB::raw('COUNT(questions.id) as question_count')
   167|        // )
   168|        //     ->leftJoin('questions', 'examinations.id', '=', 'questions.examination_id')
   169|        //     ->groupBy('examinations.id', 'examinations.title', 'examinations.duration', 'examinations.limit', 'examinations.description', 'examinations.status', 'examinations.examination_at', 'examinations.created_at', 'examinations.updated_at', 'examinations.administrator_id')
   170|
   171|        //     ->where('examinations.status', '=', 0)
   172|        //     ->get();
   173|
   174|        $examinations = Examination::select(
   175|            'examinations.id',
   176|            'examinations.title',
   177|            'examinations.duration',
   178|            'examinations.limit',
   179|            'examinations.description',
   180|            DB::raw('(CASE
   181|                WHEN examinations.status = 0 THEN "Pending"
   182|                WHEN examinations.status = 1 THEN "Active"
   183|                WHEN examinations.status = 2 THEN "Finished"
   184|                ELSE "Unknown" END) as status'),
   185|            'examinations.examination_at',
   186|            'examinations.created_at',
   187|            'examinations.updated_at',
   188|            'examinations.administrator_id',
   189|            DB::raw('COUNT(CASE WHEN questions.status = 1 THEN questions.id END) as question_count') // Count of questions with status = 1
   190|        )
   191|        ->leftJoin('questions', function ($join) {
   192|            $join->on('examinations.id', '=', 'questions.examination_id')
   193|                ->where('questions.status', '=', 1);
   194|        })
   195|        ->where('examinations.status', '=', 0) // Additional condition for examinations.status = 0
   196|        ->groupBy('examinations.id', 'examinations.title', 'examinations.duration', 'examinations.limit', 'examinations.description', 'examinations.status', 'examinations.examination_at', 'examinations.created_at', 'examinations.updated_at', 'examinations.administrator_id')
   197|        ->get();
   198|        
   199|
   200|        return view('faculty.examinations', ['examinations' => $examinations]);
   201|    }
   202|
   203|    function fetchQuestions($examination_id)
   204|    {
   205|
   206|
   207|        $examination = Examination::select('examinations.title AS title')
   208|    ->where('examinations.id', '=', $examination_id)
   209|    ->withCount(['questions' => function ($query) {
   210|        $query->where('status', 1);
   211|    }])
   212|    ->first();
   213|        
   214|
   215|        $questions = Question::select(
   216|            'questions.id',
   217|            'questions.question',
   218|            'questions.type AS type_int',
   219|            DB::raw('(CASE
   220|            WHEN questions.type = 0 THEN "Multiple Choice"
   221|            WHEN questions.type = 1 THEN "Enumeration"
   222|            WHEN questions.type = 2 THEN "Fill in the blank"
   223|            ELSE "Unknown" END) as type'),
   224|            'questions.choice_1',
   225|            'questions.choice_2',
   226|            'questions.choice_3',
   227|            'questions.choice_4',
   228|            'questions.answer',
   229|            DB::raw('(CASE
   230|            WHEN questions.status = 0 THEN "Pending"
   231|            WHEN questions.status = 1 THEN "Approved"
   232|            WHEN questions.status = 2 THEN "Rejected"
   233|            ELSE "Unknown" END) as status'),
   234|            DB::raw('CONCAT(users.surname, ", ", users.firstname) as professor'),
   235|            'questions.created_at',
   236|            'questions.updated_at',
   237|        )
   238|            ->join('users', 'users.id', '=', 'questions.professor_id')
   239|            ->where('questions.examination_id', '=', $examination_id)
   240|            ->where('users.id', '=', session('professor'))
   241|            ->get();
   242|
   243|        return view('faculty.questions', ['questions' => $questions, 'examination_id' => $examination_id, 'examination' => $examination]);
   244|    }
   245|
   246|    function requestQuestion(Request $request)
   247|    {
   248|        $validation = [
   249|            'examination_id' => 'required',
   250|            'question' => 'required',
   251|            'type' => 'required',
   252|            'answer' => 'required',
   253|        ];
   254|
   255|
   256|        $isValidated = $request->validate($validation);
   257|        if (!$isValidated)
   258|            return redirect()->back()->withErrors([
   259|                'message' => 'Question request failed! Invalid question details.',
   260|            ]);
   261|
   262|        $question = new Question;
   263|        $question->examination_id = $request->examination_id;
   264|        $question->question = $request->question;
   265|        $question->type = $request->type;
   266|        $question->choice_1 = $request->choice_a;
   267|        $question->choice_2 = $request->choice_b;
   268|        $question->choice_3 = $request->choice_c;
   269|        $question->choice_4 = $request->choice_d;
   270|        $question->answer = $request->answer;
   271|        $question->professor_id = session('professor');
   272|
   273|        $isSaved = $question->save();
   274|
   275|        if (!$isSaved)
   276|            return redirect()->back()->withErrors([
   277|                'message' => 'Question request failed! Try again later!',
   278|            ]);
   279|        else
   280|            return redirect()->back()->with('success', 'Question has been added to request list!');
   281|
   282|    }
   283|
   284|    function editQuestion(Request $request)
   285|    {
   286|
   287|        $validation = [
   288|            'id' => 'required',
   289|            'examination_id' => 'examination_id',
   290|            'question' => 'required',
   291|            'type' => 'required',
   292|            'answer' => 'required',
   293|        ];
   294|
   295|        //$request->validate($validation);
   296|        $question = Question::find($request->id);
   297|        $question->examination_id = $request->examination_id;
   298|        $question->question = $request->question;
   299|        $question->type = $request->type;
   300|        $question->choice_1 = $request->choice_a;
   301|        $question->choice_2 = $request->choice_b;
   302|        $question->choice_3 = $request->choice_c;
   303|        $question->choice_4 = $request->choice_d;
   304|        $question->answer = $request->answer;
   305|        $question->professor_id = session('professor');
   306|
   307|        $isSaved = $question->save();
   308|
   309|        if (!$isSaved)
   310|            return redirect()->back()->withErrors([
   311|                'message' => 'Question edit failed! Try again later!',
   312|            ]);
   313|        else
   314|            return redirect()->back()->with('success', 'Question has been updated and added to request list!');
   315|    }
   316|
   317|
   318|}