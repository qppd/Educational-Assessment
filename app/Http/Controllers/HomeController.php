     1|<?php
     2|
     3|namespace App\Http\Controllers;
     4|
     5|use Illuminate\Support\Facades\DB;
     6|use App\Models\User;
     7|use App\Models\Professor;
     8|use App\Models\Student;
     9|use App\Models\Examination;
    10|
    11|class HomeController extends Controller
    12|{
    13|
    14|    function fetchHome()
    15|    {
    16|        $administrators = User::whereIn('role', [0, 1])
    17|            ->select(DB::raw('COUNT(*) as administrator_count'))
    18|            ->get();
    19|
    20|        $professors = User::where('role', '=', 2)
    21|            ->select(DB::raw('COUNT(*) as professors_count'))
    22|            ->get();
    23|
    24|        $students = User::where('role', '=', 3)
    25|            ->select(DB::raw('COUNT(*) as students_count'))
    26|            ->get();
    27|
    28|        $examinations = Examination::whereIn('status', [0, 1])
    29|            ->select(DB::raw('COUNT(*) as examinations_count'))
    30|            ->get();
    31|
    32|            $limit= 10;
    33|            $rankedStudents = User::select('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo', DB::raw('AVG(results.score) as average_score'))
    34|            ->join('results', 'users.id', '=', 'results.user_id')
    35|            ->groupBy('users.id', 'users.role', 'users.firstname', 'users.surname', 'users.photo') // Explicitly include all columns from 'users'
    36|            ->orderByDesc('average_score')
    37|            ->limit($limit)
    38|            ->get();
    39|
    40|        // $examinations = User::where('role', 3)
    41|        //     ->select(DB::raw('COUNT(*) as examinations_count'))
    42|        //     ->get();
    43|
    44|        //$examinations = [];
    45|
    46|        return view('admin.home', ['administrators' => $administrators, 'professors' => $professors, 'students' => $students, 'examinations' => $examinations, 'rankedStudents' => $rankedStudents]);
    47|    }
    48|}