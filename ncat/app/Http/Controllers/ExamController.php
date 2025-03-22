<?php
namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\Information;
use App\Models\EnrolledStudent;
use App\Models\Region;
use App\Models\ExamAttempt;
use App\Models\Trial;
use App\Models\Result;
use App\Models\Batch;

use App\Models\Rstoss;
use Illuminate\Http\Request;
use DB;
use Carbon\carbon;
class ExamController extends Controller

{

    // public function show( $student_id, $course)
    // {
    //     $questions = Question::all();
    //     $trials = Trial::all();
    //     // Fetch all questions
       

    //     return view('Exam.example', compact(['questions','student_id','course','trials']));
    // }


    public function show($student_id = null)
    { 
        $studentId = session('studentId');
      
        $name = EnrolledStudent::where('id_number', $student_id)
        ->pluck('name')
        ->first();
        $course = EnrolledStudent::where('id_number', $student_id)
        ->pluck('course')
        ->first();
       
        // Check if student_id or course is null
        if (is_null($student_id)) {
            abort(404, 'Not Found');
        }
    
      
        // Retrieve the active batch
        $activeBatch = Batch::where('status', 'active')->first();
    
        // Retrieve all questions and trials
        $questions = Question::where('isDeleted', 0)->inRandomOrder()->get();

        $trials = Trial::all();
    
        // Get the duration from the active batch (assuming it's stored in seconds)
        $duration = $activeBatch->duration;
    
        // Pass the data to the view, including the duration
        return view('Exam.example', compact(['questions', 'student_id', 'course', 'trials', 'activeBatch', 'duration','name']));
    }
    
//     public function show()
// {
   
// $student_id = 1;
// $course = 'BSBA';
//     $questions = Question::all();
//     $trials = Trial::all();

//     return view('Exam.example', compact(['questions', 'student_id', 'course', 'trials']));
// }

    public function getRegions(): JsonResponse
    {
        $regions = Region::with('provinces.cities')->get();
        return response()->json($regions);
    }


    public function index()
    {
        
        $questions = DB::table('questions')->orderBy('id', 'ASC')->limit(36)->get();

        // Return view with questions data
        return view('Exam.example', compact('questions'));
    }


    public function results()
  
    {
        $studentId = session('studentId');
        // $studentId = 10000961;
       
        if (is_null($studentId)) {
            abort(404, 'Not Found');
        }
       
        $results = Result::with([
            'enrolledStudent.information.region',
            'enrolledStudent.information.city',
            'enrolledStudent.information.province',
            'enrolledStudent.information.school',
            'batch'
        ])
        ->whereHas('enrolledStudent', function ($query) use ($studentId) {
            $query->where('id', $studentId)->orWhere('id_number',   $studentId);
        })
        ->select('id', 'enrolled_student_id', 'batch_id', 'test_ip')
        ->get();

        
        $formattedResults = $results->map(function ($result) {

            // Get the student's ID
            $studentId = $result->enrolled_student_id;
    
            // Get all answers for the student
            $studentAnswers = StudentAnswer::where('student_id', $studentId)->get();
    
            // Calculate total correct answers
            $correctAnswersCount = $studentAnswers->where('is_correct', true)->count();
           
    
        
            if ($correctAnswersCount === 0) {
                $correctAnswersCount = 1;
            }
    
            // Calculate verbal and non-verbal scores 
            $verbalScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'Verbal' && $answer->is_correct;
            })->count();
            // $verbalScore = 5;
    
            $nonVerbalScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'NonVerbal' && $answer->is_correct;
            })->count();
    
            // Specific score types
            $VerbalReasoningScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'Verbal' &&
                       $answer->question->ctype === 'Verbal Reasoning' && $answer->is_correct;
            })->count();
    
            $VerbalComprehensionScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'Verbal' &&
                       $answer->question->ctype === 'Verbal Comprehension' && $answer->is_correct;
            })->count();
    
            $QuantitativeReasoningScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'NonVerbal' &&
                       $answer->question->ctype === 'Quantitative Reasoning' && $answer->is_correct;
            })->count();
    
            $FiguralReasoningScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'NonVerbal' &&
                       $answer->question->ctype === 'Figural Reasoning' && $answer->is_correct;
            })->count();
    
            // Get the student's region
            $re = $result->enrolledStudent->information->region->id ?? null;
    
            $group_abc = $result->enrolledStudent->information->group_abc ?? null;
    
    
           
            // scaled score 
            $scaledScore = Rstoss::where('raw_score_t', $correctAnswersCount)
                ->get();
    
    
            // scaled non verbal
    
    
            // address
            $address = implode(", ", array_filter([
                $result->enrolledStudent->information->city->name ?? null,
                $result->enrolledStudent->information->province->name ?? null,
            ]));
    
    
    
    //get bday
    $birth_date = $result->enrolledStudent->information->birth_date;
    //get create_at
    $create_at =  $result->enrolledStudent->information->created_at;
    
      
    
    //validate bday and create_at
    if ($birth_date && $create_at) {
        $birthDateCarbon = Carbon::parse($birth_date);
        $createAtCarbon = Carbon::parse($create_at);
    
        // calculate the difference between the birth date and creation date
        $diff = $birthDateCarbon->diff($createAtCarbon);
    
        // Extract years, months, and days from the difference
        $age_year = $diff->y;
        // $age_year = 20;
        $age_month = $diff->m;
        // $age_month = 8;
        $age_day = $diff->d;
    } else {
        // set not available
        $age_year = 'N/A';
        $age_month = 'N/A';
        $age_day = 'N/A';
    }
    
    //age table for sstosai_v
    if ($age_year >= 18) {
        $sstosai = "tbl_sstosai_18t";
    } elseif ( $age_month == 17) {
        $sstosai = "tbl_sstosai_17t";
    } elseif ( $age_month <= 16) {
        $sstosai = "tbl_sstosai_16t";
    }
    // Determine the month column and SAI based on the age
    if ($age_month <= 2) {
        $sstosai_m = "month_a";
        $sstosai_sai = "sai_a";
    } elseif ($age_month <= 5) {
        $sstosai_m = "month_b";
        $sstosai_sai = "sai_b"; 
    } elseif ($age_month <= 8) {
        $sstosai_m = "month_c";
        $sstosai_sai = "sai_c";
    } elseif ($age_month <= 11) {
        $sstosai_m = "month_d";
        $sstosai_sai = "sai_d";
    }
    
    
    
    //total Calculation
    $sai_t = DB::table($sstosai)
        ->where($sstosai_m, $scaledScore[0]->scaled_score_t)
        ->pluck($sstosai_sai)
        ->first() ?? 50;
    
    
     
    
        $sai_t = DB::table($sstosai)
        ->where($sstosai_m, $scaledScore[0]->scaled_score_t)
        ->pluck($sstosai_sai)
        ->first();
    
    if ($sai_t == '') {
        $sai_t = $scaledScore[0]->scaled_score_t > 700 ? '150' : '50';
    }
    
    
        $pbg = DB::table('tbl_pbg_prnst')
            ->where('scaled_score',  $scaledScore[0]->scaled_score_t ,)
            ->get();
        
        $p_r = $pbg->isNotEmpty() ? $pbg[0]->percentile_ranks : null;
        $sta9 = $pbg->isNotEmpty() ? $pbg[0]->stanines : null;
      
        if($p_r==''&&$sta9==''){
                if($scaledScore[0]->scaled_score_t >700){
                    $p_r='99';
                    $sta9[0]->stanines='9';
                    }else if($scaledScore[0]->scaled_score_t <700){
                    $p_r ='1';
                    $sta9='1';}
                }
                
        $pba = DB::table('tbl_pba_prns')
                ->where('sai',$sai_t)
                ->get();
                
                
            
    //verbal 
    if($verbalScore=='0'){
        $verbalScore=1;
    }
    
            // scaled verbal
            $verbalscaledScore = Rstoss::where('raw_score_v', $verbalScore)
             ->get();
             if ($age_year >= 18) {
                $sstosai = "tbl_sstosai_18v";
            } elseif ( $age_month == 17) {
                $sstosai = "tbl_sstosai_17v";
            } elseif ( $age_month <= 16) {
                $sstosai = "tbl_sstosai_16v";
            }
    
            $sai_v = DB::table($sstosai)
            ->where($sstosai_m,   $verbalscaledScore[0]->scaled_score_v )
           ->pluck($sstosai_sai); 
    
        if($sstosai_sai==''){
            if(  $verbalscaledScore[0]->scaled_score_v ->scaled_score_v>700){
                $sstosai_sai='150';
                }else
                $sstosai_sai='50';
            }
    
            $pbgv = DB::table('tbl_pbg_prnsv')
            ->where('scaled_score', $verbalscaledScore[0]->scaled_score_v)
            ->get();
                
            $p_rv = $pbgv->isNotEmpty() ? $pbgv[0]->percentile_ranks : null;
            $sta9v = $pbgv->isNotEmpty() ? $pbgv[0]->stanines : null;
    
            
            if($p_rv==''&&$sta9v==''){
                if($verbalscaledScore[0]->scaled_score_v >700){
                    $p_rv='99';
                    $sta9v[0]->stanines='9';
                    }else if($verbalscaledScore[0]->scaled_score_v <700){
                    $p_rv ='1';
                    $sta9v='1';}
                }
    //non verbal calculation
    
                if( $nonVerbalScore=='0'){
                    $nonVerbalScore=1;
                }
                
            $nonverbalscaledScore = Rstoss::where('raw_score_nv',  $nonVerbalScore)
                ->get();
    
                if ($age_year >= 18) {
                    $sstosai = "tbl_sstosai_18nv";
                } elseif ( $age_month == 17) {
                    $sstosai = "tbl_sstosai_17nv";
                } elseif ( $age_month <= 16) {
                    $sstosai = "tbl_sstosai_16nv";
                }
    
                $sai_nv = DB::table($sstosai)
                ->where($sstosai_m,   $nonverbalscaledScore[0]->scaled_score_nv )
               ->pluck($sstosai_sai); 
    
    
               if($sstosai_sai==''){
                if(  $nonverbalscaledScore[0]->scaled_score_nv ->scaled_score_nv>700){
                    $sstosai_sai='150';
                    }else
                    $sstosai_sai='50';
                }
    
                $pbgnv = DB::table('tbl_pbg_prnsnv')
                ->where('scaled_score', $nonverbalscaledScore[0]->scaled_score_nv)
                ->get();
    
    
                $p_rnv = $pbgnv->isNotEmpty() ? $pbgnv[0]->percentile_ranks : null;
            $sta9nv = $pbgnv->isNotEmpty() ? $pbgnv[0]->stanines : null;
          
            if($p_rnv==''&&$sta9nv==''){
                if($nonverbalscaledScore[0]->scaled_score_nv>700){
                    $p_rv='99';
                    $sta9v[0]->stanines='9';
                    }else if($nonverbalscaledScore[0]->scaled_score_nv<700){
                    $p_rv ='1';
                    $sta9v='1';}
                }
    
    $rsc2pc_t = $correctAnswersCount <= 35 ? "Below Average" : ($VerbalComprehensionScore <= 57 ? "Average" : "Above Average");
    $rsc2pc_v = $verbalScore <= 16 ? "Below Average" : ($verbalScore <= 28 ? "Average" : "Above Average");
    $rsc2pc_vc = $VerbalComprehensionScore <= 5 ? "Below Average" : ($VerbalComprehensionScore <= 9 ? "Average" : "Above Average");
    $rsc2pc_vr = $VerbalReasoningScore <= 10 ? "Below Average" : ($VerbalReasoningScore <= 18 ? "Average" : "Above Average");
    $rsc2pc_nv = $nonVerbalScore <= 18 ? "Below Average" : ($nonVerbalScore <= 30 ? "Average" : "Above Average");
    $rsc2pc_fr = $FiguralReasoningScore <= 8 ? "Below Average" : ($FiguralReasoningScore <= 14 ? "Average" : "Above Average");
    $rsc2pc_qr = $QuantitativeReasoningScore <= 9 ? "Below Average" : ($QuantitativeReasoningScore <= 15 ? "Above Average" : "Above Average");
    
    
    
    
    
    
    
    
    
    
        
    // die($sstosai_m . " - " . $sstosai_sai);
    
    return [
         // Check if not empty
    
        'sai_t' => $sai_t,
        // 'pba' => $pba,
        // // 'percentile_ranks' => $pbg[0]->percentile_ranks,
        // 'pbg' => $pbg,
        'name'                     => $result->enrolledStudent->name ?? 'N/A',
        'gender'                   => $result->enrolledStudent->gender ?? 'N/A',
        'bday'                     => $birth_date,
        'age_year'                 => $age_year,    
        'age_month'                => $age_month, 
        'age_day'                  => $age_day,     
        'id_number'                => $result->enrolledStudent->id_number ?? 'N/A',
        'course'                   => $result->enrolledStudent->course ?? 'N/A',
        'batch'                    => $result->batch->name ?? 'N/A',
        'group_abc'                  => $result->enrolledStudent->information->group_abc ?? null,
        'raw_score_t'              => $correctAnswersCount,
        'verbal_score'             => $verbalScore,
        'verbalReasoning_score'     => $VerbalReasoningScore,
        'verbalComprehension_score' => $VerbalComprehensionScore,
        'non_verbal_score'         => $nonVerbalScore,
        'quantitativeReasoning_score' => $QuantitativeReasoningScore,
        'figuralReasoning_score'    => $FiguralReasoningScore,
        'scaled_score'             => $scaledScore[0]->scaled_score_t,
        'verbalscaledScore'        => $verbalscaledScore[0]->scaled_score_v,
        'nonverbalscaledScore'     => $nonverbalscaledScore[0]->scaled_score_nv,
        'region'                   => $re,
        'address'                  => $address,
        'test_ip'                  => $result->test_ip,
        'percentile_ranks_pba'     => $pba->isNotEmpty() ? $pba[0]->percentile_ranks : 1,
        'stanine_pba'              => $pba->isNotEmpty() ? $pba[0]->stanines : 1,
        'percentile_ranks_pbg'     => $pbg->isNotEmpty() ? $pbg[0]->percentile_ranks : 1,
        'stanine_pbg'              => $pbg->isNotEmpty() ? $pbg[0]->stanines : 1,
        'sai_v'                    => $sai_v,
        'percentile_ranks_pbgv'    => $pbgv->isNotEmpty() ? $pbgv[0]->percentile_ranks : 1,
        'stanine_pbgv'             => $pbgv->isNotEmpty() ? $pbgv[0]->stanines : 1,
        'sai_nv'                   => $sai_nv,
        'percentile_ranks_pbgnv'   => $pbgnv->isNotEmpty() ? $pbgnv[0]->percentile_ranks : 1,
        'stanine_pbgnv'            => $pbgnv->isNotEmpty() ? $pbgnv[0]->stanines : 1,
        'rsc2pc_v' => $rsc2pc_v,
        'rsc2pc_vc' => $rsc2pc_vc,
        'rsc2pc_vr' => $rsc2pc_vr,
        'rsc2pc_nv' => $rsc2pc_nv,
        'rsc2pc_fr' => $rsc2pc_fr,
        'rsc2pc_qr' => $rsc2pc_qr, 
    ];
    
    
    
        });
    
      
   
       
        return view('Exam.results', compact('formattedResults'));
    }


    public function showInformationForm(Request $request)
    {
        // Get the studentId passed from the previous step (OTP verification)
        $studentId = $request->session()->get('studentId');
    
        // Ensure the student ID exists before proceeding
        if (!$studentId) {
            return redirect()->back()->withErrors('No student found.');
        }
    
        // Retrieve student details from the database using the studentId
        $student = EnrolledStudent::find($studentId);  // Assuming you have a Student model
    
        // Ensure the student exists in the database
        if (!$student) {
            return redirect()->back()->withErrors('No student found.');
        }

        ExamAttempt::create([
            'student_id' => $studentId,
            'start_time' => now(),
        ]);
    
        // Pass both studentId and studentName to the view
        return view('layouts.information', [
            'studentId' => $studentId,
            'studentName' => $student->name,  // Assuming the 'name' column exists in the students table
        ]);
    }
    

    public function storeInformation(Request $request)
    {
        // Validate the form input
        $request->validate([
            'address' => 'required|string|max:255',
            // You can add more fields here if needed
        ]);

        // Retrieve the student ID from the form submission or session
        $studentId = $request->input('student_id');

        // Save the information into the `information` table
        Information::create([
            'student_id' => $studentId,  // Link to enrolledstudent via student_id
            'address' => $request->input('address'),  // The address from the form
            
        ]);

        // After saving, redirect the student to the dashboard or next step
        return redirect('Exam')->with('success', 'Information saved successfully.');
    }




    public function json(){

        $student_id = 21100871;
        $results = Result::with([
            'enrolledStudent.information.region',
            'enrolledStudent.information.city',
            'enrolledStudent.information.province',
            'enrolledStudent.information.school',
            'batch'
        ])
        ->whereHas('enrolledStudent', function ($query) use ($student_id) {
            $query->where('id', $student_id)->orWhere('id_number', $student_id);
        })
        ->select('id', 'enrolled_student_id', 'batch_id', 'test_ip')
        ->get();

        
        $formattedResults = $results->map(function ($result) {

            // Get the student's ID
            $studentId = $result->enrolled_student_id;
    
            // Get all answers for the student
            $studentAnswers = StudentAnswer::where('student_id', $studentId)->get();
    
            // Calculate total correct answers
            $correctAnswersCount = $studentAnswers->where('is_correct', true)->count();
           
    
        
            if ($correctAnswersCount === 0) {
                $correctAnswersCount = 1;
            }
    
            // Calculate verbal and non-verbal scores 
            $verbalScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'Verbal' && $answer->is_correct;
            })->count();
            // $verbalScore = 5;
    
            $nonVerbalScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'NonVerbal' && $answer->is_correct;
            })->count();
    
            // Specific score types
            $VerbalReasoningScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'Verbal' &&
                       $answer->question->ctype === 'Verbal Reasoning' && $answer->is_correct;
            })->count();
    
            $VerbalComprehensionScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'Verbal' &&
                       $answer->question->ctype === 'Verbal Comprehension' && $answer->is_correct;
            })->count();
    
            $QuantitativeReasoningScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'NonVerbal' &&
                       $answer->question->ctype === 'Quantitative Reasoning' && $answer->is_correct;
            })->count();
    
            $FiguralReasoningScore = $studentAnswers->filter(function ($answer) {
                return $answer->question->test_type === 'NonVerbal' &&
                       $answer->question->ctype === 'Figural Reasoning' && $answer->is_correct;
            })->count();
    
            // Get the student's region
            $re = $result->enrolledStudent->information->region->id ?? null;
    
            $group_abc = $result->enrolledStudent->information->group_abc ?? null;
    
    
           
            // scaled score 
            $scaledScore = Rstoss::where('raw_score_t', $correctAnswersCount)
                ->get();
    
    
            // scaled non verbal
    
    
            // address
            $address = implode(", ", array_filter([
                $result->enrolledStudent->information->city->name ?? null,
                $result->enrolledStudent->information->province->name ?? null,
            ]));
    
    
    
    //get bday
    $birth_date = $result->enrolledStudent->information->birth_date;
    //get create_at
    $create_at =  $result->enrolledStudent->information->created_at;
    
      
    
    //validate bday and create_at
    if ($birth_date && $create_at) {
        $birthDateCarbon = Carbon::parse($birth_date);
        $createAtCarbon = Carbon::parse($create_at);
    
        // calculate the difference between the birth date and creation date
        $diff = $birthDateCarbon->diff($createAtCarbon);
    
        // Extract years, months, and days from the difference
        $age_year = $diff->y;
        // $age_year = 20;
        $age_month = $diff->m;
        // $age_month = 8;
        $age_day = $diff->d;
    } else {
        // set not available
        $age_year = 'N/A';
        $age_month = 'N/A';
        $age_day = 'N/A';
    }
    
    //age table for sstosai_v
    if ($age_year >= 18) {
        $sstosai = "tbl_sstosai_18t";
    } elseif ( $age_month == 17) {
        $sstosai = "tbl_sstosai_17t";
    } elseif ( $age_month <= 16) {
        $sstosai = "tbl_sstosai_16t";
    }
    // Determine the month column and SAI based on the age
    if ($age_month <= 2) {
        $sstosai_m = "month_a";
        $sstosai_sai = "sai_a";
    } elseif ($age_month <= 5) {
        $sstosai_m = "month_b";
        $sstosai_sai = "sai_b"; 
    } elseif ($age_month <= 8) {
        $sstosai_m = "month_c";
        $sstosai_sai = "sai_c";
    } elseif ($age_month <= 11) {
        $sstosai_m = "month_d";
        $sstosai_sai = "sai_d";
    }
    
    
    
    //total Calculation
    $sai_t = DB::table($sstosai)
        ->where($sstosai_m, $scaledScore[0]->scaled_score_t)
        ->pluck($sstosai_sai)
        ->first() ?? 50;
    
    
     
    
        $sai_t = DB::table($sstosai)
        ->where($sstosai_m, $scaledScore[0]->scaled_score_t)
        ->pluck($sstosai_sai)
        ->first();
    
    if ($sai_t == '') {
        $sai_t = $scaledScore[0]->scaled_score_t > 700 ? '150' : '50';
    }
    
    
        $pbg = DB::table('tbl_pbg_prnst')
            ->where('scaled_score',  $scaledScore[0]->scaled_score_t ,)
            ->get();
        
        $p_r = $pbg->isNotEmpty() ? $pbg[0]->percentile_ranks : null;
        $sta9 = $pbg->isNotEmpty() ? $pbg[0]->stanines : null;
      
        if($p_r==''&&$sta9==''){
                if($scaledScore[0]->scaled_score_t >700){
                    $p_r='99';
                    $sta9[0]->stanines='9';
                    }else if($scaledScore[0]->scaled_score_t <700){
                    $p_r ='1';
                    $sta9='1';}
                }
                
        $pba = DB::table('tbl_pba_prns')
                ->where('sai',$sai_t)
                ->get();
                
                
            
    //verbal 
    if($verbalScore=='0'){
        $verbalScore=1;
    }
    
            // scaled verbal
            $verbalscaledScore = Rstoss::where('raw_score_v', $verbalScore)
             ->get();
             if ($age_year >= 18) {
                $sstosai = "tbl_sstosai_18v";
            } elseif ( $age_month == 17) {
                $sstosai = "tbl_sstosai_17v";
            } elseif ( $age_month <= 16) {
                $sstosai = "tbl_sstosai_16v";
            }
    
            $sai_v = DB::table($sstosai)
            ->where($sstosai_m,   $verbalscaledScore[0]->scaled_score_v )
           ->pluck($sstosai_sai); 
    
        if($sstosai_sai==''){
            if(  $verbalscaledScore[0]->scaled_score_v ->scaled_score_v>700){
                $sstosai_sai='150';
                }else
                $sstosai_sai='50';
            }
    
            $pbgv = DB::table('tbl_pbg_prnsv')
            ->where('scaled_score', $verbalscaledScore[0]->scaled_score_v)
            ->get();
                
            $p_rv = $pbgv->isNotEmpty() ? $pbgv[0]->percentile_ranks : null;
            $sta9v = $pbgv->isNotEmpty() ? $pbgv[0]->stanines : null;
    
            
            if($p_rv==''&&$sta9v==''){
                if($verbalscaledScore[0]->scaled_score_v >700){
                    $p_rv='99';
                    $sta9v[0]->stanines='9';
                    }else if($verbalscaledScore[0]->scaled_score_v <700){
                    $p_rv ='1';
                    $sta9v='1';}
                }
    //non verbal calculation
    
                if( $nonVerbalScore=='0'){
                    $nonVerbalScore=1;
                }
                
            $nonverbalscaledScore = Rstoss::where('raw_score_nv',  $nonVerbalScore)
                ->get();
    
                if ($age_year >= 18) {
                    $sstosai = "tbl_sstosai_18nv";
                } elseif ( $age_month == 17) {
                    $sstosai = "tbl_sstosai_17nv";
                } elseif ( $age_month <= 16) {
                    $sstosai = "tbl_sstosai_16nv";
                }
    
                $sai_nv = DB::table($sstosai)
                ->where($sstosai_m,   $nonverbalscaledScore[0]->scaled_score_nv )
               ->pluck($sstosai_sai); 
    
    
               if($sstosai_sai==''){
                if(  $nonverbalscaledScore[0]->scaled_score_nv ->scaled_score_nv>700){
                    $sstosai_sai='150';
                    }else
                    $sstosai_sai='50';
                }
    
                $pbgnv = DB::table('tbl_pbg_prnsnv')
                ->where('scaled_score', $nonverbalscaledScore[0]->scaled_score_nv)
                ->get();
    
    
                $p_rnv = $pbgnv->isNotEmpty() ? $pbgnv[0]->percentile_ranks : null;
            $sta9nv = $pbgnv->isNotEmpty() ? $pbgnv[0]->stanines : null;
          
            if($p_rnv==''&&$sta9nv==''){
                if($nonverbalscaledScore[0]->scaled_score_nv>700){
                    $p_rv='99';
                    $sta9v[0]->stanines='9';
                    }else if($nonverbalscaledScore[0]->scaled_score_nv<700){
                    $p_rv ='1';
                    $sta9v='1';}
                }
    
    $rsc2pc_t = $correctAnswersCount <= 35 ? "BA" : ($VerbalComprehensionScore <= 57 ? "A" : "AA");
    $rsc2pc_v = $verbalScore <= 16 ? "BA" : ($verbalScore <= 28 ? "A" : "Above Average");
    $rsc2pc_vc = $VerbalComprehensionScore <= 5 ? "BA" : ($VerbalComprehensionScore <= 9 ? "A" : "AA");
    $rsc2pc_vr = $VerbalReasoningScore <= 10 ? "BA" : ($VerbalReasoningScore <= 18 ? "A" : "AA");
    $rsc2pc_nv = $nonVerbalScore <= 18 ? "BA" : ($nonVerbalScore <= 30 ? "A" : "AA");
    $rsc2pc_fr = $FiguralReasoningScore <= 8 ? "BA" : ($FiguralReasoningScore <= 14 ? "A" : "AA");
    $rsc2pc_qr = $QuantitativeReasoningScore <= 9 ? "BA" : ($QuantitativeReasoningScore <= 15 ? "A" : "AA");
    
    
    
    
    
    
    
    
    
    
        
    // die($sstosai_m . " - " . $sstosai_sai);
    
    return [
         // Check if not empty
    
        'sai_t' => $sai_t,
        // 'pba' => $pba,
        // // 'percentile_ranks' => $pbg[0]->percentile_ranks,
        // 'pbg' => $pbg,
        'name'                     => $result->enrolledStudent->name ?? 'N/A',
        'gender'                   => $result->enrolledStudent->gender ?? 'N/A',
        'bday'                     => $birth_date,
        'age_year'                 => $age_year,    
        'age_month'                => $age_month, 
        'age_day'                  => $age_day,     
        'id_number'                => $result->enrolledStudent->id_number ?? 'N/A',
        'course'                   => $result->enrolledStudent->course ?? 'N/A',
        'batch'                    => $result->batch->name ?? 'N/A',
        'group_abc'                  => $result->enrolledStudent->information->group_abc ?? null,
        'raw_score_t'              => $correctAnswersCount,
        'verbal_score'             => $verbalScore,
        'verbalReasoning_score'     => $VerbalReasoningScore,
        'verbalComprehension_score' => $VerbalComprehensionScore,
        'non_verbal_score'         => $nonVerbalScore,
        'quantitativeReasoning_score' => $QuantitativeReasoningScore,
        'figuralReasoning_score'    => $FiguralReasoningScore,
        'scaled_score'             => $scaledScore[0]->scaled_score_t,
        'verbalscaledScore'        => $verbalscaledScore[0]->scaled_score_v,
        'nonverbalscaledScore'     => $nonverbalscaledScore[0]->scaled_score_nv,
        'region'                   => $re,
        'address'                  => $address,
        'test_ip'                  => $result->test_ip,
        'percentile_ranks_pba'     => $pba->isNotEmpty() ? $pba[0]->percentile_ranks : 1,
        'stanine_pba'              => $pba->isNotEmpty() ? $pba[0]->stanines : 1,
        'percentile_ranks_pbg'     => $pbg->isNotEmpty() ? $pbg[0]->percentile_ranks : 1,
        'stanine_pbg'              => $pbg->isNotEmpty() ? $pbg[0]->stanines : 1,
        'sai_v'                    => $sai_v,
        'percentile_ranks_pbgv'    => $pbgv->isNotEmpty() ? $pbgv[0]->percentile_ranks : 1,
        'stanine_pbgv'             => $pbgv->isNotEmpty() ? $pbgv[0]->stanines : 1,
        'sai_nv'                   => $sai_nv,
        'percentile_ranks_pbgnv'   => $pbgnv->isNotEmpty() ? $pbgnv[0]->percentile_ranks : 1,
        'stanine_pbgnv'            => $pbgnv->isNotEmpty() ? $pbgnv[0]->stanines : 1,
        'rsc2pc_v' => $rsc2pc_v,
        'rsc2pc_vc' => $rsc2pc_vc,
        'rsc2pc_vr' => $rsc2pc_vr,
        'rsc2pc_nv' => $rsc2pc_nv,
        'rsc2pc_fr' => $rsc2pc_fr,
        'rsc2pc_qr' => $rsc2pc_qr, 
    ];
    
    
    
        });
    
        return response()->json(['data' => $formattedResults]);
    

    }



    public function submit(Request $request)
    {


     


      
        // Validate the incoming data
        $validated = $request->validate([
            'student_id' => 'required|exists:enrolled_students,id', // Ensure student_id exists
            'answers' => 'required|array', // Ensure answers are present and are an array
            'answers.*' => 'required|in:A,B,C,D,E', // Validate each answer
        ]);


        $studentId = $validated['student_id'];

        $examAttempt = ExamAttempt::where('student_id', $studentId)
        ->whereNull('end_time') // Ensure we're updating an active attempt
        ->firstOrFail();        // Throw an error if no active attempt is found

        $examAttempt->update([
        'end_time' => now(),  // Set end time when exam is completed
        ]);

    
        // Loop through each answer and insert it into the database
        foreach ($validated['answers'] as $questionId => $selectedOption) {
            // Fetch the correct answer for this question
            $question = Question::find($questionId);
    
            // Check if the selected option is correct
            $isCorrect = ($selectedOption === $question->option_correct);
    
            // Insert the student's answer into the database
            StudentAnswer::create([
                'student_id' => $validated['student_id'], // Insert the provided student ID
                'question_id' => $questionId, // Question ID from the answers array
                'selected_option' => $selectedOption, // The selected option (A, B, C, D, E)
                'is_correct' => $isCorrect, // True if the selected answer is correct, false otherwise
            ]);
        }
    
        // Redirect to some page or show a success message
        return redirect()->route('exam.results')->with([
            'success' => 'Exam submitted successfully!',
            'studentId' => $studentId,
        ]);
        
        
    }
    

 
    
    public function submitz(Request $request)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                'student_id' => 'required|exists:enrolled_students,id', // Ensure student_id exists
                'answers' => 'required|array', // Ensure answers are present and are an array
                'answers.*' => 'required|in:A,B,C,D,E', // Validate each answer
            ]);
            dd($validated);
    
            // Loop through each answer and insert it into the database
            foreach ($validated['answers'] as $questionId => $selectedOption) {
              
                StudentAnswer::create([
                    'student_id' => $validated['student_id'], // Insert the provided student ID
                    'question_id' => $questionId, // Question ID from the answers array
                    'selected_option' => $selectedOption, // The selected option (A, B, C, D, E)
                ]);
            }
    
            // Redirect to some page or show a success message
            return redirect()->route('exam.results')->with('success', 'Exam submitted successfully!');
    
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-related exceptions (e.g., insert failures)
            \Log::error('Database error during exam submission: ' . $e->getMessage());
    
            return back()->with('error', 'There was a problem saving your answers. Please try again.');
    
        } catch (\Exception $e) {
            // Handle any other exceptions
            \Log::error('General error during exam submission: ' . $e->getMessage());
    
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }


    

    public function ssubmit(Request $request)
    {
        // Validate the request to ensure student_id is provided and answers are selected
        $request->validate([
            'student_id' => 'required|exists:students,id', // Validate that student_id exists in the students table
            'answers' => 'required|array',
            'answers.*' => 'required|in:A,B,C,D,E',
        ]);
    
        $studentId = $request->input('student_id'); // Retrieve student_id from the input
        $answers = $request->input('answers'); // Contains answers[question_id] => selected_option4

        dd($request);
    
        foreach ($answers as $questionId => $selectedOption) {
            // Find the question by ID
            $question = Question::find($questionId);
    
            // Check if the selected option is correct
            $isCorrect = $selectedOption === $question->option_correct;
    
            // Save the student's answer to the database
            StudentAnswer::create([
                'student_id' => $studentId,
                'question_id' => $questionId,
                'selected_option' => $selectedOption,
                'is_correct' => $isCorrect,
            ]);
        }
    
        // Redirect to the results or a thank you page
        return redirect()->route('exam.result');
    }
    
}
    
    
    
    