<!-- resources/views/exam/show.blade.php -->
@extends('layouts.exam') <!-- Assuming you have a layout file -->

@section('content')

<div class="container">
    <h1>Exam</h1>
    <form action="{{ route('exam.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="student_id" id="student_id" value="">
        @foreach ($questions as $question)
            <div class="question mb-4">
                <!-- Display the question (check if it's an image or text) -->
                @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->question))
                    <img src="{{ asset($question->question) }}" class="img-fluid mb-2" alt="Question Image">
                @else
                    <p>{!! $question->question !!}</p>
                @endif

                <!-- Option A -->
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="A">
                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_a))
                            <img src="{{ asset($question->option_a) }}" class="img-fluid mb-2" alt="Option A Image">
                        @else
                            {!! $question->option_a !!}
                        @endif
                    </label>
                </div>

                <!-- Option B -->
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="B">
                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_b))
                            <img src="{{ asset($question->option_b) }}" class="img-fluid mb-2" alt="Option B Image">
                        @else
                            {!! $question->option_b !!}
                        @endif
                    </label>
                </div>

                <!-- Option C -->
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="C">
                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_c))
                            <img src="{{ asset($question->option_c) }}" class="img-fluid mb-2" alt="Option C Image">
                        @else
                            {!! $question->option_c !!}
                        @endif
                    </label>
                </div>

                <!-- Option D -->
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="D">
                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_d))
                            <img src="{{ asset($question->option_d) }}" class="img-fluid mb-2" alt="Option D Image">
                        @else
                            {!! $question->option_d !!}
                        @endif
                    </label>
                </div>

                <!-- Option E -->
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]" value="E">
                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_e))
                            <img src="{{ asset($question->option_e) }}" class="img-fluid mb-2" alt="Option E Image">
                        @else
                            {!! $question->option_e !!}
                        @endif
                    </label>
                </div>
            </div>
            <hr>
        @endforeach
        <button type="submit" class="btn btn-primary">Submit Exam</button>
    </form>
</div>

@endsection
