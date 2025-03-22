<?php

namespace App\Http\Controllers;
use App\Models\{Result,StudentAnswer};
use App\Models\Rstoss;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client; 
use Carbon\Carbon;
use DB;
class NLPController extends Controller
{




public function getResults()
{
    // result with enrolled students, batch, and information
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
    ->where($sstosai_m, $scaledScore[0]->scaled_score_t )
   ->pluck($sstosai_sai);  

    if($sstosai_sai==''){
        if(  $scaledScore[0]->scaled_score_t ->scaled_score_t>700){
            $sstosai_sai='150';
            }else
            $sstosai_sai='50';
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
$rsc2pc_qr = $QuantitativeReasoningScore <= 9 ? "Below Average" : ($QuantitativeReasoningScore <= 15 ? "Average" : "Above Average");










    
// die($sstosai_m . " - " . $sstosai_sai);

return [
     // Check if not empty

'sai_t' => $sai_t,
    // 'pba' => $pba,
    // // 'percentile_ranks' => $pbg[0]->percentile_ranks,
    // 'pbg' => $pbg,
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


    
    
];



    });

    return response()->json(['data' => $formattedResults]);
}



public function getNLPSuggestions(Request $request)
{
    // Inputs - these could be derived from your actual data inputs
    $correctAnswersCount = $request->input('raw_score_t');
    $verbalScore = $request->input('verbal_score');
    $VerbalReasoningScore = $request->input('verbalReasoning_score');
    $VerbalComprehensionScore = $request->input('verbalComprehension_score');
    $nonVerbalScore = $request->input('non_verbal_score');
    $QuantitativeReasoningScore = $request->input('quantitativeReasoning_score');
    $FiguralReasoningScore = $request->input('figuralReasoning_score');

    // Classification Logic based on thresholds
    $rsc2pc_t = $correctAnswersCount <= 35 ? "Below Average" : ($VerbalComprehensionScore <= 57 ? "Average" : "Above Average");
    $rsc2pc_v = $verbalScore <= 16 ? "Below Average" : ($verbalScore <= 28 ? "Average" : "Above Average");
    $rsc2pc_vc = $VerbalComprehensionScore <= 5 ? "Below Average" : ($VerbalComprehensionScore <= 9 ? "Average" : "Above Average");
    $rsc2pc_vr = $VerbalReasoningScore <= 10 ? "Below Average" : ($VerbalReasoningScore <= 18 ? "Average" : "Above Average");
    $rsc2pc_nv = $nonVerbalScore <= 18 ? "Below Average" : ($nonVerbalScore <= 30 ? "Average" : "Above Average");
    $rsc2pc_fr = $FiguralReasoningScore <= 8 ? "Below Average" : ($FiguralReasoningScore <= 14 ? "Average" : "Above Average");
    $rsc2pc_qr = $QuantitativeReasoningScore <= 9 ? "Below Average" : ($QuantitativeReasoningScore <= 15 ? "Average" : "Above Average");

    // Call function to generate personalized NLP suggestions for each category
    $suggestions = $this->generateNLPSuggestions([
        'rsc2pc_t'  => $rsc2pc_t,
        'rsc2pc_v'  => $rsc2pc_v,
        'rsc2pc_vc' => $rsc2pc_vc,
        'rsc2pc_vr' => $rsc2pc_vr,
        'rsc2pc_nv' => $rsc2pc_nv,
        'rsc2pc_fr' => $rsc2pc_fr,
        'rsc2pc_qr' => $rsc2pc_qr
    ], [
        'verbal_score' => $verbalScore,
        'verbalReasoning_score' => $VerbalReasoningScore,
        'verbalComprehension_score' => $VerbalComprehensionScore,
        'non_verbal_score' => $nonVerbalScore,
        'quantitativeReasoning_score' => $QuantitativeReasoningScore,
        'figuralReasoning_score' => $FiguralReasoningScore,
        'raw_score_t' => $correctAnswersCount
    ]);

    // Return the results and suggestions
    return response()->json([
        'classifications' => [
            'rsc2pc_t' => $rsc2pc_t,
            'rsc2pc_v' => $rsc2pc_v,
            'rsc2pc_vc' => $rsc2pc_vc,
            'rsc2pc_vr' => $rsc2pc_vr,
            'rsc2pc_nv' => $rsc2pc_nv,
            'rsc2pc_fr' => $rsc2pc_fr,
            'rsc2pc_qr' => $rsc2pc_qr,
        ],
        'suggestions' => $suggestions
    ]);
}

// Function to generate suggestions based on the calculated classifications
private function generateNLPSuggestions($classifications, $scores)
{
    $suggestions = [];

    // Call NLP-based suggestion generator for each category
    $suggestions['verbal_feedback'] = $this->generateFeedbackUsingNLP($scores['verbal_score'], 'verbal');
    $suggestions['verbal_reasoning_feedback'] = $this->generateFeedbackUsingNLP($scores['verbalReasoning_score'], 'verbal reasoning');
    $suggestions['verbal_comprehension_feedback'] = $this->generateFeedbackUsingNLP($scores['verbalComprehension_score'], 'verbal comprehension');
    $suggestions['non_verbal_feedback'] = $this->generateFeedbackUsingNLP($scores['non_verbal_score'], 'non-verbal');
    $suggestions['quantitative_feedback'] = $this->generateFeedbackUsingNLP($scores['quantitativeReasoning_score'], 'quantitative reasoning');
    $suggestions['figural_reasoning_feedback'] = $this->generateFeedbackUsingNLP($scores['figuralReasoning_score'], 'figural reasoning');
    $suggestions['total_feedback'] = $this->generateFeedbackUsingNLP($scores['raw_score_t'], 'overall performance');

    return $suggestions;
}

private function generateFeedbackUsingNLP($score, $category)
{
    // Example prompt for NLP API
    $prompt = "Generate a detailed teaching suggestion for a student with a $category score of $score.";

    // Set your Hugging Face API key
    $apiKey = env('HUGGINGFACE_API_KEY'); // Store this key in your .env file

    // Send a request to the Hugging Face Inference API
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
    ])->post('https://api-inference.huggingface.co/models/gpt2', [
        'inputs' => $prompt,
    ]);

    // Check for a successful response and return the feedback
    if ($response->successful()) {
        return $response->json()[0]['generated_text'] ?? 'Unable to generate feedback at this time.';
    } else {
        return 'Unable to generate feedback at this time. Please try again later.';
    }
}




    
}