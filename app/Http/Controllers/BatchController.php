<?php

namespace App\Http\Controllers;
use App\Models\{Course,Batch,EnrolledStudent,Result,StudentAnswer,Rstoss,ActivityLog};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
      
        return view('Admin.Batch.index');
    }
    
    public function getviewBatch(){
        return view('Admin.Batch.show');
    }
    
    public function getBatch(Request $request)
    {
       
        $year = $request->input('year');
    
       
        $query = Batch::query();
    
      
        if ($year) {
            $query->whereYear('created_at', $year);
        }
    
      
        $batch = $query->get();
    
        // Return the data as JSON
        return response()->json(['data' => $batch]);
    }
    

    public function checkActiveBatch()
    {
        // Get the total number of questions
      

        $activeBatchExists = Batch::where('status', 'active')->exists();
       

        // dd($activeBatchExists);
     
        if ($activeBatchExists) {
            return response()->json([
                'data' => $activeBatchExists
            ]);
     
        }
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    
    
 

     
     public function store(Request $request)
     {
         // Validate the request
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|unique:batches,name|min:4',
             'description' => 'required|string|min:4', // Adjust the min length if needed
             'duration' => 'required',
         ]);
     
         // If validation fails, return the error response
         if ($validator->fails()) {
             return response()->json(['result' => 'error', 'message' => $validator->errors()->first()]);
         }
     
         // Check if any batch already has an 'active' status
         $activeBatchExists = Batch::where('status', 'active')->exists();
     
         if ($activeBatchExists) {
             // Return an error response if an active batch exists
             return response()->json(['result' => 'error', 'message' => 'Cannot add a new batch. An active batch already exists.']);
         }
     
         // Generate a unique access key
         do {
             $access_key = mt_rand(1000000000, 9999999999);
             $exists = Batch::where('access_key', $access_key)->exists();
         } while ($exists);
     
         // Add the access key to the request data
         $requestData = $request->all();
         $requestData['access_key'] = $access_key;
     
         $batchname = $requestData['name'];
     
         // Create the batch with the modified request data
         $batch = Batch::create($requestData);
         ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'added',
            'description' => 'Added batch' . $batchname,
        ]);
     
         if ($batch) {
             return response()->json(['result' => 'success', 'message' => 'The batch has been saved']);
         } else {
             return response()->json(['result' => 'error', 'message' => 'The batch could not be saved. Please try again!']);
         }
     }
     
     
     
     
     
   
    /**
     * Display the specified resource.
     */
    public function getStudentbyBatch($batchId)
    {
        // Find the batch by its ID and load its students
        $batch = Batch::with('students')->find($batchId);

        if ($batch) {
            return response()->json([
                'batch' => $batch->name,
                'students' => $batch->students
            ]);
        } else {
            return response()->json([
                'error' => 'Batch not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $batch = Batch::find($id);
        return response()->json($batch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Check if any active batch exists (outside of the current batch)
        $activeBatchExists = Batch::where('status', 'active')->where('id', '!=', $id)->exists();
        
        // Find the batch being updated
        $batch = Batch::find($id);
        
        if (!$batch) {
            return response()->json(['result' => 'error', 'message' => 'Batch not found.']);
        }
    
        // If trying to update the status to active, but another batch is already active
        if ($request->input('status') == 'active' && $activeBatchExists) {
            return response()->json(['result' => 'error', 'message' => 'Cannot activate this batch. Another batch is already active.']);
        }
    
        // Store the old values for comparison
        $oldValues = $batch->only(['name', 'description', 'status']);
        
        // Perform the update
        $updated = $batch->update($request->all());
        
        if ($updated) {
            // Fetch the new values after the update
            $newValues = $batch->only(['name', 'description', 'status']);
            
            // Prepare a description of changes
            $changes = [];
            foreach ($oldValues as $field => $oldValue) {
                $newValue = $newValues[$field];
                if ($oldValue !== $newValue) {
                    // Customize the message for the status field
                    if ($field === 'status') {
                        $changes[] = "Status of batch '{$batch->name}' changed from '$oldValue' to '$newValue'";
                    } else {
                        $changes[] = ucfirst($field) . " changed from '$oldValue' to '$newValue'";
                    }
                }
            }
            
            // Log activity only if there are changes
            if (!empty($changes)) {
                ActivityLog::create([
                    'user_id' => auth()->id(), // Current logged-in user ID
                    'action' => 'updated',
                    'name' => "Batch Update - ID $id",
                    'description' => implode(', ', $changes),
                ]);
            }
    
            return response()->json(['result' => 'success', 'message' => 'The Batch has been saved']);
        } else {
            return response()->json(['result' => 'error', 'message' => 'The Batch could not be saved. Please try again!']);
        }
    }
    
    
    

    public function destroy(string $id)
    {
        $batch = Batch::find($id)->delete();
        if($batch){
            return response()->json(['result'=>'success','message'=>'The user has been deleted']);
         }else{
            return response()->json(['result'=>'error','message'=>'The user could not be deleted. Please try again!']);
         }
    }
    /**
     * Remove the specified resource from storage.
     */


     public function getStudentsByBatch($batchId)
     {
        
         // Fetch the batch and load its students and the results for each student
         $batch = Batch::with(['students.results', 'students.information'])->find($batchId);
     
         // Check if the batch exists
         if (!$batch) {
             return response()->json(['message' => 'Batch not found'], 404);
         }
     
         // Get the results for students in the batch
         $results = Result::with([
                 'enrolledStudent.information.region', 
                 'enrolledStudent.information.city', 
                 'enrolledStudent.information.province', 
                 'enrolledStudent.information.school',
                 'batch'
             ])
             ->where('batch_id', $batchId)
             ->select('id', 'enrolled_student_id', 'batch_id', 'test_ip')
             ->get();
     
         // Format the results for DataTables
         $formattedResults = $results->map(function ($result) {
             $studentId = $result->enrolled_student_id;
             $studentAnswers = StudentAnswer::where('student_id', $studentId)->get();
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

             
             $ddate = '2005-06-26 00:00:00';
             $ca = '2024-06-26 00:00:00';
     
     
     //get bday
     $birth_date = $result->enrolledStudent->information->birth_date ?? $ddate;
     //get create_at
     $create_at =  $result->enrolledStudent->information->created_at ?? $ca;
     
       
     
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
     $rsc2pc_qr = $QuantitativeReasoningScore <= 9 ? "Below Average" : ($QuantitativeReasoningScore <= 15 ? "Average" : "Above Average");
     
     
     
     
     
     
     
     
     
     
         
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
     
         return response()->json(['data' => $formattedResults, 
         'batch_name' => $batch->name,
         'description' => $batch->description,
         'batch_id' => $batch->id,]);
     }

   

     public function printall()
     {
         // Fetch all batches and their associated data
         $batches = Batch::with(['students.results', 'students.information'])->get();
     
         // Check if any batches exist
         if ($batches->isEmpty()) {
             return response()->json(['message' => 'No batches found'], 404);
         }
     
         // Initialize an array to store all batch data
         $allBatchData = [];
     
         foreach ($batches as $batch) {
             // Get the results for students in the batch
             $results = Result::with([
                 'enrolledStudent.information.region',
                 'enrolledStudent.information.city',
                 'enrolledStudent.information.province',
                 'enrolledStudent.information.school',
                 'batch'
             ])
                 ->where('batch_id', $batch->id)
                 ->select('id', 'enrolled_student_id', 'test_ip')
                 ->get();
     
             // Format the results for each batch
             $formattedResults = $results->map(function ($result) {
                 $studentId = $result->enrolled_student_id;
     
                 // Fetch student answers
                 $studentAnswers = StudentAnswer::where('student_id', $studentId)->get();
                 $correctAnswersCount = $studentAnswers->where('is_correct', true)->count() ?: 1;
     
                 // Calculate verbal and non-verbal scores
                 $verbalScore = $studentAnswers->filter(fn($answer) => $answer->question->test_type === 'Verbal' && $answer->is_correct)->count();
                 $nonVerbalScore = $studentAnswers->filter(fn($answer) => $answer->question->test_type === 'NonVerbal' && $answer->is_correct)->count();
     
                 // Specific score types
                 $VerbalReasoningScore = $studentAnswers->filter(fn($answer) => $answer->question->ctype === 'Verbal Reasoning' && $answer->is_correct)->count();
                 $VerbalComprehensionScore = $studentAnswers->filter(fn($answer) => $answer->question->ctype === 'Verbal Comprehension' && $answer->is_correct)->count();
                 $QuantitativeReasoningScore = $studentAnswers->filter(fn($answer) => $answer->question->ctype === 'Quantitative Reasoning' && $answer->is_correct)->count();
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
   
                
                $ddate = '2005-06-26 00:00:00';
                $ca = '2024-06-26 00:00:00';
        
        
        //get bday
        $birth_date = $result->enrolledStudent->information->birth_date ?? $ddate;
        //get create_at
        $create_at =  $result->enrolledStudent->information->created_at ?? $ca;
        
          
        
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
                 $rsc2pc_qr = $QuantitativeReasoningScore <= 9 ? "Below Average" : ($QuantitativeReasoningScore <= 15 ? "Average" : "Above Average");
                 
                 
     
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
     
             // Add batch data to the response
             $allBatchData[] = [
                 'batch_name' => $batch->name,
                 'description' => $batch->description,
                 'batch_id' => $batch->id,
                 'results' => $formattedResults,
             ];
         }
     
         // Return all batches with results
         return response()->json(['data' => $allBatchData]);
     }
     
  
}