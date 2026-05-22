     1|     1|<?php
     2|     2|
     3|     3|namespace App\Http\Controllers;
     4|     4|
     5|     5|use Illuminate\Http\Request;
     6|     6|use Illuminate\Support\Facades\Hash;
     7|     7|use Illuminate\Support\Facades\Auth;
     8|     8|use Illuminate\Support\Facades\DB;
     9|     9|
    10|    10|use Image;
    11|    11|
    12|    12|
    13|    13|use App\Models\User;
    14|    14|use App\Models\Student;
    15|    15|use App\Models\Examination;
    16|    16|use App\Models\Question;
    17|    17|use App\Models\Result;
    18|    18|use App\Models\Answer;
    19|    19|
    20|    20|class PortalController extends Controller
    21|    21|{
    22|    22|    function portals()
    23|    23|    {
    24|    24|
    25|    25|        $students = User::select(
    26|    26|            'users.username',
    27|    27|        )
    28|    28|            ->where('users.status', '=', 1)
    29|    29|            ->where('users.role', '=', 3)
    30|    30|            ->get();
    31|    31|
    32|    32|        //dd($students);    
    33|    33|        $studentUsernames = $students->pluck('username')->toArray();
    34|    34|        return view('auth.login', ['studentUsernames' => $studentUsernames]);
    35|    35|    }
    36|    36|
    37|    37|    function forgotPassword()
    38|    38|    {
    39|    39|
    40|    40|        $students = User::select(
    41|    41|            'users.username',
    42|    42|        )
    43|    43|            ->where('users.status', '=', 1)
    44|    44|            ->where('users.role', '=', 3)
    45|    45|            ->get();
    46|    46|
    47|    47|        //dd($students);    
    48|    48|        $studentUsernames = $students->pluck('username')->toArray();
    49|    49|        return view('auth.forgot-password', ['studentUsernames' => $studentUsernames]);
    50|    50|    }
    51|    51|
    52|    52|    function registerPage()
    53|    53|    {
    54|    54|
    55|    55|        return view('auth.register');
    56|    56|    }
    57|    57|
    58|    58|    function dashboardPage()
    59|    59|    {
    60|    60|        // Assuming you want to fetch the top 10 students
    61|    61|        $limit = 10;
    62|    62|
    63|    63|        $rankedStudents = User::select('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo', DB::raw('(SUM(results.score) / (COUNT(questions.id) * MAX(examinations.limit))) * 100 AS percentage_score'))
    64|    64|            ->join('results', 'users.id', '=', 'results.user_id')
    65|    65|            ->join('examinations', 'results.examination_id', '=', 'examinations.id')
    66|    66|            ->join('questions', 'examinations.id', '=', 'questions.examination_id')
    67|    67|            ->groupBy('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo')
    68|    68|            ->orderByDesc('percentage_score')
    69|    69|            ->limit($limit)
    70|    70|            ->get();
    71|    71|
    72|    72|        // Now $rankedStudents contains the result set
    73|    73|
    74|    74|        return view('portal.dashboard', ['rankedStudents' => $rankedStudents]);
    75|    75|    }
    76|    76|
    77|    77|    function examinationsAvailablePage()
    78|    78|    {
    79|    79|        // $examinations = Examination::select(
    80|    80|        //     'examinations.id',
    81|    81|        //     'examinations.title',
    82|    82|        //     'examinations.duration',
    83|    83|        //     'examinations.limit',
    84|    84|        //     'examinations.description',
    85|    85|        //     DB::raw('(CASE
    86|    86|        //         WHEN examinations.status = 0 THEN "Pending"
    87|    87|        //         WHEN examinations.status = 1 THEN "Active"
    88|    88|        //         WHEN examinations.status = 2 THEN "Finished"
    89|    89|        //         ELSE "Unknown" END) as status'),
    90|    90|        //     'examinations.examination_at',
    91|    91|        //     'examinations.created_at',
    92|    92|        //     'examinations.updated_at',
    93|    93|        //     'examinations.administrator_id'
    94|    94|        // )
    95|    95|        //     ->leftJoin('results', 'examinations.id', '=', 'results.examination_id')
    96|    96|        //     ->whereNull('results.user_id') // Check for NULL values in the results table
    97|    97|        //     ->where('examinations.status', '=', 1)
    98|    98|        //     ->where('results.user_id', '=', session('student'))
    99|    99|        //     ->get();
   100|   100|
   101|   101|        $studentId = session('student');
   102|   102|
   103|   103|        $examinations = Examination::select(
   104|   104|            'examinations.id',
   105|   105|            'examinations.title',
   106|   106|            'examinations.duration',
   107|   107|            'examinations.limit',
   108|   108|            'examinations.description',
   109|   109|            DB::raw('(CASE
   110|   110|        WHEN examinations.status = 0 THEN "Pending"
   111|   111|        WHEN examinations.status = 1 THEN "Active"
   112|   112|        WHEN examinations.status = 2 THEN "Finished"
   113|   113|        ELSE "Unknown" END) as status'),
   114|   114|            'examinations.examination_at',
   115|   115|            'examinations.created_at',
   116|   116|            'examinations.updated_at',
   117|   117|            'examinations.administrator_id'
   118|   118|        )
   119|   119|            ->where('examinations.status', '=', 1)
   120|   120|            ->whereDoesntHave('results', function ($query) use ($studentId) {
   121|   121|                $query->where('user_id', '=', $studentId);
   122|   122|            })
   123|   123|            ->orderBy('examinations.examination_at', 'DESC')
   124|   124|            ->get();
   125|   125|
   126|   126|        return view('portal.exams-available', ['examinations' => $examinations]);
   127|   127|    }
   128|   128|
   129|   129|    function examinationsTakenPage()
   130|   130|    {
   131|   131|        $examinations = Examination::select(
   132|   132|            'examinations.id',
   133|   133|            'examinations.title',
   134|   134|            'examinations.duration',
   135|   135|            'examinations.limit',
   136|   136|            'examinations.description',
   137|   137|            DB::raw('(CASE
   138|   138|                WHEN examinations.status = 0 THEN "Pending"
   139|   139|                WHEN examinations.status = 1 THEN "Active"
   140|   140|                WHEN examinations.status = 2 THEN "Finished"
   141|   141|                ELSE "Unknown" END) as status'),
   142|   142|            'examinations.examination_at',
   143|   143|            'examinations.created_at',
   144|   144|            'examinations.updated_at',
   145|   145|            'examinations.administrator_id'
   146|   146|        )
   147|   147|            ->join('results', 'examinations.id', '=', 'results.examination_id')
   148|   148|            ->where('examinations.status', '=', 1)
   149|   149|            ->where('results.user_id', '=', session('student'))
   150|   150|            ->get();
   151|   151|
   152|   152|        return view('portal.exams-taken', ['examinations' => $examinations]);
   153|   153|    }
   154|   154|
   155|   155|    public function examinationAttempt(Request $request)
   156|   156|    {
   157|   157|
   158|   158|        //`id`, `user_id`, `examination_id`, `score`, `remarks`, `status`, `created_at`, `updated_at` 
   159|   159|
   160|   160|
   161|   161|        $result = new Result;
   162|   162|        $result->user_id = session('student');
   163|   163|        $result->examination_id = $request->id;
   164|   164|
   165|   165|        $result->save();
   166|   166|
   167|   167|        $students = User::select(
   168|   168|            'users.username',
   169|   169|        )
   170|   170|            ->where('users.id', '=', session('student'))
   171|   171|            ->where('users.status', '=', 1)
   172|   172|
   173|   173|            ->where('users.role', '=', 3)
   174|   174|            ->get();
   175|   175|
   176|   176|        //dd($students);    
   177|   177|        $studentUsernames = $students->pluck('username')->toArray();
   178|   178|
   179|   179|        $examination = Examination::select(
   180|   180|            'examinations.id',
   181|   181|            'examinations.title',
   182|   182|            'examinations.duration',
   183|   183|            'examinations.limit',
   184|   184|            'examinations.description',
   185|   185|            'examinations.examination_at',
   186|   186|            'examinations.created_at',
   187|   187|            'examinations.updated_at',
   188|   188|            'examinations.administrator_id'
   189|   189|        )
   190|   190|            ->where('examinations.id', '=', $request->id)
   191|   191|            ->get();
   192|   192|
   193|   193|        //`id`, `examination_id`, `question`, `type`, `choice_1`, `choice_2`, 
   194|   194|        //`choice_3`, `choice_4`, `answer`, `status`, `professor_id`, `created_at`, `updated_at`
   195|   195|
   196|   196|        $questions = Question::select(
   197|   197|            'questions.id',
   198|   198|            'questions.question',
   199|   199|            DB::raw('(CASE
   200|   200|                WHEN questions.type = 0 THEN "Multiple choice"
   201|   201|                WHEN questions.type = 1 THEN "Enumeration"
   202|   202|                WHEN questions.type = 2 THEN "Fill in the blank"
   203|   203|                ELSE "Unknown" END) AS type'),
   204|   204|            'questions.choice_1',
   205|   205|            'questions.choice_2',
   206|   206|            'questions.choice_3',
   207|   207|            'questions.choice_4',
   208|   208|        )
   209|   209|            ->where('questions.examination_id', '=', $request->id)
   210|   210|            ->where('questions.status', '=', 1)
   211|   211|            ->get();
   212|   212|
   213|   213|        return view('portal.exam.attempt', ['examination' => $examination, 'questions' => $questions, 'studentUsernames' => $studentUsernames]);
   214|   214|    }
   215|   215|
   216|   216|    function examinationSubmit(Request $request)
   217|   217|    {
   218|   218|
   219|   219|        //SELECT `id`, `student_id`, `examination_id`, `question_id`, `student_answer`, `status`, 
   220|   220|        //`created_at`, `updated_at` FROM `answers` WHERE 1
   221|   221|
   222|   222|
   223|   223|        $user_id = session('student'); // Assuming you are using authentication
   224|   224|
   225|   225|        // Extract common data from the request
   226|   226|        $examination_id = $request->input('examination_id');
   227|   227|
   228|   228|        // Process and save answers
   229|   229|        // foreach ($request->input('question_ids', []) as $index => $question_id) {
   230|   230|        //     $answer = new Answer();
   231|   231|
   232|   232|        //     $answer->examination_id = $examination_id;
   233|   233|        //     $answer->student_id = $user_id;
   234|   234|        //     $answer->question_id = $question_id;
   235|   235|        //     $answer->status = 1;
   236|   236|
   237|   237|        //     if ($request->has("answers.$index")) {
   238|   238|        //         $answer->student_answer = $request->input("answers.$index");
   239|   239|        //     } elseif ($request->has("fill_in_the_blank_answers.$index")) {
   240|   240|        //         $answer->student_answer = $request->input("fill_in_the_blank_answers.$index");
   241|   241|        //     } elseif ($request->has("enumeration_answers.$index")) {
   242|   242|        //         $answer->student_answer = $request->input("enumeration_answers.$index");
   243|   243|        //     }
   244|   244|
   245|   245|        //     $answer->save();
   246|   246|        // }
   247|   247|
   248|   248|        foreach ($request->input('question_ids', []) as $index => $question_id) {
   249|   249|            $answer = new Answer();
   250|   250|
   251|   251|            $answer->examination_id = $examination_id;
   252|   252|            $answer->student_id = $user_id;
   253|   253|            $answer->question_id = $question_id;
   254|   254|            $answer->status = 1;
   255|   255|
   256|   256|            $sanswer = "";
   257|   257|            if ($request->has("answers.$index")) {
   258|   258|                $answer->student_answer = $request->input("answers.$index");
   259|   259|                $sanswer = $request->input("answers.$index");
   260|   260|            } elseif ($request->has("fill_in_the_blank_answers.$index")) {
   261|   261|                $answer->student_answer = $request->input("fill_in_the_blank_answers.$index");
   262|   262|                $sanswer = $request->input("fill_in_the_blank_answers.$index");
   263|   263|            } elseif ($request->has("enumeration_answers.$index")) {
   264|   264|                $answer->student_answer = $request->input("enumeration_answers.$index");
   265|   265|                $sanswer = $request->input("enumeration_answers.$index");
   266|   266|            }
   267|   267|
   268|   268|
   269|   269|
   270|   270|            // Check if the answer is correct and update the score
   271|   271|            $question = Question::find($question_id);
   272|   272|            if ($question && $answer->student_answer === $question->answer) {
   273|   273|                $result = Result::where('user_id', $user_id)
   274|   274|                    ->where('examination_id', $examination_id)
   275|   275|                    ->first();
   276|   276|
   277|   277|                if ($result) {
   278|   278|                    // Increment the score if the result already exists
   279|   279|                    $result->score += 1;
   280|   280|                    $result->save();
   281|   281|                } else {
   282|   282|                    // Create a new result record if it doesn't exist
   283|   283|                    Result::create([
   284|   284|                        'user_id' => $user_id,
   285|   285|                        'examination_id' => $examination_id,
   286|   286|                        'score' => 1, // Initial score for the correct answer
   287|   287|                        'status' => 1, // You may adjust this status accordingly
   288|   288|                    ]);
   289|   289|                }
   290|   290|            }
   291|   291|
   292|   292|            $answer->save();
   293|   293|        }
   294|   294|
   295|   295|
   296|   296|        // Optionally, you can redirect the user to a thank-you page or any other page
   297|   297|        return redirect('/portal/dashboard')
   298|   298|            ->with('success', 'Answer submitted successfully!');
   299|   299|
   300|   300|
   301|   301|    }
   302|   302|
   303|   303|    function examinationResult($examination_id)
   304|   304|    {
   305|   305|
   306|   306|        $examinations = Examination::select('examinations.title', 'examinations.limit')
   307|   307|            ->where('examinations.id', '=', $examination_id)
   308|   308|            ->get();
   309|   309|
   310|   310|
   311|   311|        // Count correct and wrong answers
   312|   312|        $results = DB::table('answers')
   313|   313|            ->join('questions', 'answers.question_id', '=', 'questions.id')
   314|   314|            ->where('answers.examination_id', $examination_id)
   315|   315|            ->where('answers.student_id', session('student'))
   316|   316|            ->select(
   317|   317|                'questions.answer as correct_answer',
   318|   318|                'answers.student_answer',
   319|   319|                DB::raw('CASE WHEN LOWER(questions.answer) = LOWER(answers.student_answer) THEN "correct" ELSE "wrong" END as result')
   320|   320|            )
   321|   321|            ->get();
   322|   322|
   323|   323|        $correctCount = $results->where('result', 'correct')->count();
   324|   324|        $wrongCount = $results->where('result', 'wrong')->count();
   325|   325|
   326|   326|        // Optionally, you can do something with the counts (e.g., store in the database, display to the user, etc.)
   327|   327|// For now, let's just print them
   328|   328|
   329|   329|
   330|   330|        return view('portal.exam.result', ['results' => $results, 'examinations' => $examinations, 'correctCount' => $correctCount, 'wrongCount' => $wrongCount]);
   331|   331|    }
   332|   332|
   333|   333|    function studentRegister(Request $request)
   334|   334|    {
   335|   335|        $validation = [
   336|   336|            'student_no' => 'required',
   337|   337|            'email' => 'required',
   338|   338|            'contact' => 'required',
   339|   339|            'photos.*' => 'image|mimes:jpeg,png,gif|max:2048',
   340|   340|            'password' => 'required',
   341|   341|            'confirm_password' => 'required',
   342|   342|        ];
   343|   343|
   344|   344|        if (!$request->hasFile('photos')) {
   345|   345|            return redirect()->back()->withErrors([
   346|   346|                'message' => 'Registration failed! Your face photos is required for face recognition.',
   347|   347|            ]);
   348|   348|        }
   349|   349|
   350|   350|        $request->validate($validation);
   351|   351|
   352|   352|        if ($request->password != $request->confirm_password) {
   353|   353|            return redirect()->back()->withErrors([
   354|   354|                'message' => 'Registration failed! Passwords do not match.',
   355|   355|            ]);
   356|   356|        }
   357|   357|
   358|   358|
   359|   359|        // Use the Eloquent model to find a student by student_no
   360|   360|        $student = Student::where('student_no', $request->student_no)->first();
   361|   361|
   362|   362|        if (!$student) {
   363|   363|            return redirect()->back()->withErrors([
   364|   364|                'message' => 'Registration failed! Student ID is invalid.',
   365|   365|            ]);
   366|   366|        } else {
   367|   367|            $user = User::where('username', $student->student_no)->first();
   368|   368|            if ($user) {
   369|   369|                return redirect()->back()->withErrors([
   370|   370|                    'message' => 'Registration failed! Student ID is already registered.',
   371|   371|                ]);
   372|   372|            }
   373|   373|        }
   374|   374|
   375|   375|        $photo_counter = 0;
   376|   376|
   377|   377|        if ($request->hasFile('photos')) {
   378|   378|            $studentNo = $request->student_no;
   379|   379|            $targetDirectory = 'storage/images/students/' . $studentNo;
   380|   380|
   381|   381|            // Check if the target directory exists, and create it if it doesn't
   382|   382|            if (!is_dir($targetDirectory)) {
   383|   383|                if (!mkdir($targetDirectory, 0755, true)) {
   384|   384|                    // Directory creation failed, handle the error
   385|   385|                    return response()->json(['error' => 'Failed to create the directory.']);
   386|   386|                }
   387|   387|            }
   388|   388|
   389|   389|            foreach ($request->file('photos') as $photo) {
   390|   390|                $photo_counter++;
   391|   391|
   392|   392|                $photo_name = $photo_counter . '.' . $photo->getClientOriginalExtension();
   393|   393|
   394|   394|                //$photo_name = encrypt($photo_name);
   395|   395|
   396|   396|                // Specify the full path to save the image
   397|   397|                $pathToSave = $targetDirectory . '/' . $photo_name;
   398|   398|
   399|   399|                // Check for errors during image save
   400|   400|                $saveResult = Image::make($photo)->save($pathToSave);
   401|   401|
   402|   402|                if (!$saveResult) {
   403|   403|                    // Handle the error, e.g., log it or return a response
   404|   404|                    return response()->json(['error' => 'Failed to save the image.']);
   405|   405|                }
   406|   406|            }
   407|   407|        }
   408|   408|
   409|   409|
   410|   410|        $user = new User;
   411|   411|        $user->username = $request->student_no;
   412|   412|        $user->firstname = $student->firstname;
   413|   413|        $user->surname = $student->lastname;
   414|   414|        $user->role = 3;
   415|   415|        $user->email = $request->email;
   416|   416|        $user->contact = $request->contact;
   417|   417|        $user->photo = 'student.jpg';
   418|   418|        $user->password = Hash::make($request->password);
   419|   419|
   420|   420|        $isRegistered = $user->save();
   421|   421|
   422|   422|        if ($isRegistered) {
   423|   423|            return redirect('/portal')
   424|   424|                ->with('success', 'Registration successful. You can now login using face recognition.');
   425|   425|        } else {
   426|   426|            return redirect()->back()->withErrors([
   427|   427|                'message' => 'Registration failed! Try again later!',
   428|   428|            ]);
   429|   429|        }
   430|   430|
   431|   431|    }
   432|   432|
   433|   433|    public function studentLogin(Request $request)
   434|   434|    {
   435|   435|        $credentials = $request->only('username', 'password');
   436|   436|
   437|   437|        if (Auth::attempt($credentials)) {
   438|   438|
   439|   439|            $user = Auth::user();
   440|   440|
   441|   441|            $userId = Auth::id();
   442|   442|            $userStudentNo = $user->username;
   443|   443|            $userRole = $user->role;
   444|   444|            $userName = $user->surname . ', ' . $user->firstname . ', ' . $user->middlename;
   445|   445|            $userPhoto = 'storage/images/students/' . $user->photo;
   446|   446|            $userStatus = $user->status;
   447|   447|
   448|   448|            if ($userStatus == 0) {
   449|   449|                return redirect()->back()->withErrors([
   450|   450|                    'message' => 'Account locked!',
   451|   451|                ]);
   452|   452|            }
   453|   453|
   454|   454|            $request->session()->put('student', $userId);
   455|   455|            $request->session()->put('student_no', $userStudentNo);
   456|   456|            $request->session()->put('role', $userRole);
   457|   457|            $request->session()->put('name', $userName);
   458|   458|            $request->session()->put('photo', $userPhoto);
   459|   459|            //$req->session()->put('default', $isdefaultPassword);
   460|   460|            // Authentication passed...
   461|   461|            return redirect('/portal/dashboard');
   462|   462|        } else {
   463|   463|            return redirect()->back()->withErrors([
   464|   464|                'message' => 'Username or password invalid!',
   465|   465|            ]);
   466|   466|        }
   467|   467|
   468|   468|    }
   469|   469|
   470|   470|    public function findUserByLabel($label)
   471|   471|    {
   472|   472|        $user = User::where('username', $label)->first();
   473|   473|
   474|   474|        if ($user) {
   475|   475|            return response()->json($user);
   476|   476|        } else {
   477|   477|
   478|   478|            return response()->json(['message' => $label], 404);
   479|   479|        }
   480|   480|    }
   481|   481|
   482|   482|
   483|   483|    function saveNewPassword(Request $request)
   484|   484|    {
   485|   485|        $validation = [
   486|   486|            'username' => 'required',
   487|   487|            'ppassword' => 'required',
   488|   488|        ];
   489|   489|
   490|   490|        $request->validate($validation);
   491|   491|
   492|   492|        // Use the Eloquent model to find a student by student_no
   493|   493|        $student = User::where('username', $request->username)->first();
   494|   494|
   495|   495|        $student->password = Hash::make($request->ppassword);
   496|   496|        $isSaved = $student->save();
   497|   497|        if (!$isSaved) {
   498|   498|            return redirect()->back()->withErrors([
   499|   499|                'message' => 'Password update failed! Please try again!',
   500|   500|            ]);
   501|