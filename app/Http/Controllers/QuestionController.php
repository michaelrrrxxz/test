<?php

namespace App\Http\Controllers;
use App\Models\{Question,ActivityLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class QuestionController extends Controller
{


    public function getQuestions()
    {
        // Fetch only the questions that are not marked as deleted
        $questions = Question::active()->get();
     
        return response()->json(['data' => $questions]);
    }
    
    public function checkTotalQuestions()
{
    // Get the total number of questions
    $totalQuestions = Question::active()->count();

    // Return the count as a JSON response
    return response()->json([
        'total_questions' => $totalQuestions
    ]);
}


    public function getVerbalQuestions()
    {
        $verbal = Question::where('test_type', "verbal")->get();
       
        return response()->json(['data'=>$verbal]);
    }
    public function getNonVerbalQuestions()
    {
        $nonverbal = Question::where('test_type', "nonverbal")->get();
       
        return response()->json(['data'=>$nonverbal]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view ('Admin.Questions.index');
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
             $data = $request->validate([
                 'test_type' => 'required|string',
                 'ctype' => 'required|string',
                 'upload_type' => 'required|string|in:text,photo',
                 'question' => 'required_if:upload_type,text|string|nullable',
                 'question_photo' => 'required_if:upload_type,photo|image|max:2048',
     
                 // Validate options
                 'option_a_text' => 'required_if:choices_type,text|string|nullable',
                 'option_a_image' => 'required_if:choices_type,image|image|max:2048|nullable',
                 'option_b_text' => 'required_if:choices_type,text|string|nullable',
                 'option_b_image' => 'required_if:choices_type,image|image|max:2048|nullable',
                 'option_c_text' => 'required_if:choices_type,text|string|nullable',
                 'option_c_image' => 'required_if:choices_type,image|image|max:2048|nullable',
                 'option_d_text' => 'required_if:choices_type,text|string|nullable',
                 'option_d_image' => 'required_if:choices_type,image|image|max:2048|nullable',
                 'option_e_text' => 'required_if:choices_type,text|string|nullable',
                 'option_e_image' => 'required_if:choices_type,image|image|max:2048|nullable',
                 'option_correct' => 'required',
     
                
             ]);

             
     
             // Handle question based on upload type
             if ($request->upload_type == 'photo') {
                 $file = $request->file('question_photo');
                 $fileName = time() . '_' . $file->getClientOriginalName();
                 $destinationPath = public_path('uploads/questions');
     
                 if (!File::exists($destinationPath)) {
                     File::makeDirectory($destinationPath, 0755, true);
                 }
     
                 $file->move($destinationPath, $fileName);
                 $data['question'] = 'uploads/questions/' . $fileName;
             } else {
                 $data['question'] = $request->input('question');
             }
     
             // Handle options based on choices type
             foreach (['a', 'b', 'c', 'd','e'] as $option) {
                 if ($request->choices_type == 'image') {
                     $file = $request->file('option_' . $option . '_image');
                     if ($file) {
                         $fileName = time() . '_' . $file->getClientOriginalName();
                         $destinationPath = public_path('uploads/options');
     
                         if (!File::exists($destinationPath)) {
                             File::makeDirectory($destinationPath, 0755, true);
                         }
     
                         $file->move($destinationPath, $fileName);
                         $data['option_' . $option] = 'uploads/options/' . $fileName;
                     }
                 } else {
                     $data['option_' . $option] = $request->input('option_' . $option . '_text');
                 }
             }
  
             // Create the question in the database
             Question::create($data);
             ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'added',
                'description' => 'Added new question ',
            ]);
     
             return response()->json(['message' => 'Question saved successfully']);
         }
     
     


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $question = Question::find($id);
    
        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }
    
        // You should modify this part based on how your images are stored and retrieved
        // Assuming option_a, option_b, etc., are image paths stored in your database
        // You might need to adjust this based on your actual database structure
        return response()->json($question);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       $questions = Question::find($id);
        return response()->json($questions);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request);
        $q = Question::find($id);
      
        $q = $q->update($request->all());
        if($q)
        {
            ActivityLog::create([
                'user_id' => auth()->id(), // Current logged-in user ID
                'action' => 'updated',
                'description' => "Updated a Question",
            ]);
        }
        if($q){
            return response()->json(['result'=>'success','message'=>'The Question has been saved']);
         }else{
            return response()->json(['result'=>'error','message'=>'The Question could not be saved. Please try again!']);
         }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $q = Question::find($id); // Find the question by ID
    
        if ($q) {
            // Mark as deleted by setting the isDeleted flag to 1
            $q->isDeleted = 1;
            $q->save(); // Save the changes
    
            // Log the activity
            ActivityLog::create([
                'user_id' => auth()->id(), // Current logged-in user ID
                'action' => 'deleted',
                'description' => "Deleted a Question",
            ]);
    
            return response()->json([
                'result' => 'success',
                'message' => 'The question has been marked as deleted.'
            ]);
        }
    
        return response()->json([
            'result' => 'error',
            'message' => 'The question could not be found. Please try again!'
        ]);
    }
    
    
}