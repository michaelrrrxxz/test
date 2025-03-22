<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ExampleController extends Controller
{
    public function index()
    {
        
        $questions = DB::table('questions')->orderBy('id', 'ASC')->limit(36)->get();

        return view('Exam.example', compact('questions'));
    }
}