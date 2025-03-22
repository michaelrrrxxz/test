<?php

namespace App\Http\Controllers;
use App\Models\{Result,StudentAnswer,Course};
use App\Models\Rstoss;
use App\Models\Information;
use Illuminate\Http\Request;
use GuzzleHttp\Client; 
use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;
class ResultController extends Controller
{
    public function index(){
        $course = Course::get();
        
        // die($course);
        return view ('Admin.Results.index',compact(['course']));
    }


    


public function getResults()
{
    $results = Result::with([ 
        'enrolledStudent.information.region', 
        'enrolledStudent.information.city', 
        'enrolledStudent.information.province', 
        'enrolledStudent.information.school',
        'enrolledStudent.information.school',
        'batch'
    ])
    ->select('id', 'enrolled_student_id', 'batch_id', 'test_ip')

    ->get();

// // Check if results are empty
// if ($results->isEmpty()) {
// // Optionally, handle the case where no results are found
// return response()->json(['message' => 'No results found'], 404); // Or any other action you want
// }

// Proceed with further processing if results are not empty
// Your code here...


    // Format the results for DataTables
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
$birth_date = $result->enrolledStudent->information->birth_date ?? '2005-06-26 00:00:00';

//get create_at
$create_at =  $result->enrolledStudent->information->created_at ?? '2024-11-19 14:25:10';
  

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
    'created'  => $create_at,
];



    });

    return response()->json(['data' => $formattedResults]);
}



public function getResult()
{
    $studentId = 1931;
    // Retrieve the result for a specific student using their studentId
    $result = Result::with([
            'enrolledStudent.information.region', 
            'enrolledStudent.information.city', 
            'enrolledStudent.information.province', 
            'enrolledStudent.information.school',
            'batch'
        ])
        ->where('enrolled_student_id', $studentId) // Filter by student ID
        ->select('id', 'enrolled_student_id', 'batch_id', 'test_ip')
        ->first(); // Retrieve a single record

    // If no result is found, return a response indicating the student was not found
    if (!$result) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    // Get the student's ID
    $studentAnswers = StudentAnswer::where('student_id', $studentId)->get();

    // Calculate total correct answers
    $correctAnswersCount = $studentAnswers->where('is_correct', true)->count();
    if ($correctAnswersCount === 0) {
        $correctAnswersCount = 1;
    }

    // Calculate scores for different categories
    $verbalScore = $studentAnswers->filter(fn($answer) => $answer->question->test_type === 'Verbal' && $answer->is_correct)->count();
    $nonVerbalScore = $studentAnswers->filter(fn($answer) => $answer->question->test_type === 'NonVerbal' && $answer->is_correct)->count();

    $VerbalReasoningScore = $studentAnswers->filter(fn($answer) => $answer->question->ctype === 'Verbal Reasoning' && $answer->is_correct)->count();
    $VerbalComprehensionScore = $studentAnswers->filter(fn($answer) => $answer->question->ctype === 'Verbal Comprehension' && $answer->is_correct)->count();
    $QuantitativeReasoningScore = $studentAnswers->filter(fn($answer) => $answer->question->ctype === 'Quantitative Reasoning' && $answer->is_correct)->count();
    $FiguralReasoningScore = $studentAnswers->filter(fn($answer) => $answer->question->ctype === 'Figural Reasoning' && $answer->is_correct)->count();

    // Get student's region, address, and other personal information
    $re = $result->enrolledStudent->information->region->id ?? null;
    $group_abc = $result->enrolledStudent->information->group_abc ?? null;

    // Scaled score calculations
    $scaledScore = Rstoss::where('raw_score_t', $correctAnswersCount)->get();
    $verbalscaledScore = Rstoss::where('raw_score_v', $verbalScore)->get();
    $nonverbalscaledScore = Rstoss::where('raw_score_nv', $nonVerbalScore)->get();

    // Get student's birth date and age information
    $birth_date = $result->enrolledStudent->information->birth_date;
    $create_at =  $result->enrolledStudent->information->created_at;

    // Calculate age if birth_date and create_at are available
    if ($birth_date && $create_at) {
        $birthDateCarbon = Carbon::parse($birth_date);
        $createAtCarbon = Carbon::parse($create_at);
        $diff = $birthDateCarbon->diff($createAtCarbon);
        $age_year = $diff->y;
        $age_month = $diff->m;
        $age_day = $diff->d;
    } else {
        $age_year = 'N/A';
        $age_month = 'N/A';
        $age_day = 'N/A';
    }

    // Further SAI and Percentile Rank calculations (as in your original function)

    // Format the result for response
    $formattedResult = [
        'name'                     => $result->enrolledStudent->name ?? 'N/A',
        'gender'                   => $result->enrolledStudent->gender ?? 'N/A',
        'bday'                     => $birth_date,
        'age_year'                 => $age_year,    
        'age_month'                => $age_month, 
        'age_day'                  => $age_day,     
        'id_number'                => $result->enrolledStudent->id_number ?? 'N/A',
        'course'                   => $result->enrolledStudent->course ?? 'N/A',
        'batch'                    => $result->batch->name ?? 'N/A',
        'group_abc'                => $group_abc,
        'raw_score_t'              => $correctAnswersCount,
        'verbal_score'             => $verbalScore,
        'verbalReasoning_score'     => $VerbalReasoningScore,
        'verbalComprehension_score' => $VerbalComprehensionScore,
        'non_verbal_score'         => $nonVerbalScore,
        'quantitativeReasoning_score' => $QuantitativeReasoningScore,
        'figuralReasoning_score'    => $FiguralReasoningScore,
        'scaled_score'             => $scaledScore[0]->scaled_score_t ?? 'N/A',
        'verbalscaledScore'        => $verbalscaledScore[0]->scaled_score_v ?? 'N/A',
        'nonverbalscaledScore'     => $nonverbalscaledScore[0]->scaled_score_nv ?? 'N/A',
        'region'                   => $re,
        'test_ip'                  => $result->test_ip,
        
        // Add any other specific calculations as needed
    ];

    return response()->json(['data' => $formattedResult]);
}




public function exportPdf()
{
    $data = [
        // Fetch the data you want to display in the table, for example:
        // 'results' => YourModel::all(),
    ];

    $pdf = Pdf::loadView('Admin.Results.pdf', $data);
    return $pdf->download('results.pdf');
}




}