@extends('layouts.exam')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <style>
        /* .card{
            back

        } */
        /* Hide the actual radio input */
        .form-check-input {
            display: none;
        }
    
        /* Style the label to look like a button */
        .form-check-label {
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #048e2b;
            border-radius: 5px;
            background-color: #f8f9fa;
            /* color: #048e2b; */
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
    
        /* Change style when the radio button is selected */
        .form-check-input:checked + .form-check-label {
            background-color: #048e2b;
            color: #fff;
            font-weight: bold;
        }
    
        /* Add margin for the choice label */
        .choice-label {
            margin-right: 10px;
            font-weight: bold;
        }
        .large-card-body {
    padding: 0.1em; /* Adjust padding */
    font-size: 1.2em; /* Adjust font size */
}

    </style>
    
    
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="col-12 content-card">
        <div class="card">
            <div class="card-header" style="background-color: #048e2b; color: white;">
                <h3 class="card-title">Trial Exam</h3>
            </div>
            <div class="card-body  large-card-body">
                <div id="trial-section">
                    <h4 class="text-center mb-4">Trial Questions</h4>
                    <form id="trial-form">
                        @csrf
                        @foreach ($trials as $index => $trial)
                        <div class="question {{ $index % 2 == 0 ? 'question-even' : 'question-odd' }}" id="trial-question-{{ $index + 1 }}" style="{{ $index >= 32 ? 'display:none;' : '' }}">
                            <div class="question-number-box unanswered" id="trial-question-number-{{ $trial->id }}">
                                {{ $index + 1 }}
                            </div>
                            @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch1))
                                <img src="{{ asset($trial->question) }}"  alt="Option A Image">
                            @else
                                <p>{!! $trial->question !!}</p>
                            @endif
                            <!-- Options -->
                            
                            <div class="options-container">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input trial" name="answers[{{ $trial->id }}]" id="optionA_{{$trial->id}}" value="A">
                                    <label class="form-check-label" for="optionA_{{$trial->id}}">
                                        <span class="choice-label">A.</span>
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch1))
                                        <img src="{{ asset($trial->ch1) }}" style="height: 23px;" alt="Option A Image">

                                        @else
                                            {!! $trial->ch1 !!}
                                        @endif
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input type="radio" class="form-check-input trial" name="answers[{{ $trial->id }}]" id="optionB_{{$trial->id}}" value="B">
                                    <label class="form-check-label" for="optionB_{{$trial->id}}">
                                        <span class="choice-label">B.</span>
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch2))
                                            <img src="{{ asset($trial->ch2) }}"  style="height: 23px;" alt="Option B Image">
                                        @else
                                            {!! $trial->ch2 !!}
                                        @endif
                                    </label>
                                </div>
                            
                                <div class="form-check">
                                    <input type="radio" class="form-check-input trial" name="answers[{{ $trial->id }}]" id="optionC_{{$trial->id}}" value="C">
                                    <label class="form-check-label" for="optionC_{{$trial->id}}">
                                        <span class="choice-label">C.</span>
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch3))
                                            <img src="{{ asset($trial->ch3) }}"  style="height: 23px;" alt="Option C Image">
                                        @else
                                            {!! $trial->ch3 !!}
                                        @endif
                                    </label>
                                </div>
                                
                            
                                <div class="form-check">
                                    <input type="radio" class="form-check-input trial" name="answers[{{ $trial->id }}]" id="optionD_{{$trial->id}}" value="D">
                                    <label class="form-check-label" for="optionD_{{$trial->id}}">
                                        <span class="choice-label">D.</span>
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch4))
                                            <img src="{{ asset($trial->ch4) }}"  style="height: 23px;" alt="Option D Image">
                                        @else
                                            {!! $trial->ch4 !!}
                                        @endif
                                    </label>
                                </div>
                            
                                <div class="form-check">
                                    <input type="radio" class="form-check-input trial" name="answers[{{ $trial->id }}]" id="optionE_{{$trial->id}}" value="E">
                                    <label class="form-check-label" for="optionE_{{$trial->id}}">
                                        <span class="choice-label">E.</span>
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch5))
                                            <img src="{{ asset($trial->ch5) }}"  style="height: 23px;" alt="Option E Image">
                                        @else
                                            {!! $trial->ch5 !!}
                                        @endif
                                    </label>
                                </div>
                            </div>
                            
                            
                        </div>
                        @endforeach

                        <div class="text-center mt-4">
                            <button type="button" id="trial-complete-btn" class="btn btn-primary">Proceed to Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card" id="exam-section" style="display:none;">
            <div class="card-header" style="background-color: #048e2b; color: white;">
                <h3 class="card-title">Take Exam</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="text-left mb-4">{{$name}}</h4>
                    <h4 class="text-right mb-4">{{$course}}</h4>
                </div>
                
               
                <h5 class="text-center mb-4">Select the letter of the correct answer for each question.</h5>
                <form action="{{ route('exam.submit') }}" method="POST" id="exam-form">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student_id }}">
                    <div id="question-status">
                        <p>Total Questions: <span id="total-questions">{{ count($questions) }}</span></p>
                        <p>Answered Questions: <span id="answered-questions">0</span></p>
                        <div id="timer">
                            <div class="battery-outer">
                                <div class="battery-inner" id="battery-inner"></div>
                            </div>
                           <div class="d-flex"> <p>Time remaining: </p> <p id="time-left">00:00</p> </div>
                        </div>
                    </div>

                    @foreach ($questions as $index => $question) 
                    <div class="question {{ $index % 2 == 0 ? 'question-even' : 'question-odd' }}" id="question-{{ $index + 1 }}">



                     
                        


                        <div class="question-number-box unanswered" id="question-number-{{ $question->id }}">
                            {{ $index + 1 }}
                        </div>
                
                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_a))
                            <img src="{{ asset($question->question) }}" alt="Option A Image">
                        @else
                            <p>{!! $question->question !!}</p>
                        @endif
                
                        <div class="options-container">
                            <!-- Option A -->
                            <div class="form-check">
                                <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" id="optionA_{{ $question->id }}" value="A">
                                <label class="form-check-label" for="optionA_{{ $question->id }}">
                                    <span class="choice-label">A.</span>
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_a))
                                        <img src="{{ asset($question->option_a) }}" alt="Option A Image">
                                    @else
                                        {!! $question->option_a !!}
                                    @endif
                                </label>
                            </div>
                
                            <!-- Option B -->
                            <div class="form-check">
                                <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" id="optionB_{{ $question->id }}" value="B">
                                <label class="form-check-label" for="optionB_{{ $question->id }}">
                                    <span class="choice-label">B.</span>
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_b))
                                        <img src="{{ asset($question->option_b) }}" alt="Option B Image">
                                    @else
                                        {!! $question->option_b !!}
                                    @endif
                                </label>
                            </div>
                
                            <!-- Option C -->
                            <div class="form-check">
                                <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" id="optionC_{{ $question->id }}" value="C">
                                <label class="form-check-label" for="optionC_{{ $question->id }}">
                                    <span class="choice-label">C.</span>
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_c))
                                        <img src="{{ asset($question->option_c) }}" alt="Option C Image">
                                    @else
                                        {!! $question->option_c !!}
                                    @endif
                                </label>
                            </div>
                
                            <!-- Option D -->
                            <div class="form-check">
                                <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" id="optionD_{{ $question->id }}" value="D">
                                <label class="form-check-label" for="optionD_{{ $question->id }}">
                                    <span class="choice-label">D.</span>
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_d))
                                        <img src="{{ asset($question->option_d) }}" alt="Option D Image">
                                    @else
                                        {!! $question->option_d !!}
                                    @endif
                                </label>
                            </div>
                
                            <!-- Option E -->
                            <div class="form-check">
                                <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" id="optionE_{{ $question->id }}" value="E">
                                <label class="form-check-label" for="optionE_{{ $question->id }}">
                                    <span class="choice-label">E.</span>
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_e))
                                        <img src="{{ asset($question->option_e) }}" alt="Option E Image">
                                    @else
                                        {!! $question->option_e !!}
                                    @endif
                                </label>
                            </div>
                        </div>
                
                    </div>
                    <hr>
                @endforeach
                

                    <button type="submit" id="submit-btn" class="btn btn-primary" style="display:none;">Submit Exam</button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
  <style>
.question-even {
    background-color: ;
    border-left: 5px solid #048e2b;
}

.question-odd {
    background-color: #fff8dc;
    border-left: 5px solid #DAA520;
}

</style>

</style>
<script>
    $(document).ready(function() {
    let totalQuestions = $('#total-questions').text();
    let totalSeconds = {{$duration}}; // Duration in seconds (e.g., 45 minutes = 2700 seconds)

function startTimer() {
    let countdown = setInterval(function() {
        if (totalSeconds <= 0) {
            clearInterval(countdown);
            $('#battery-inner').css('width', '0%');
            $('#exam-form').submit();
        } else {
            totalSeconds--;
            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;
            // Calculate the percentage based on the total duration dynamically
            let percentage = (totalSeconds / {{$duration}}) * 100; // Calculate based on original duration
            $('#battery-inner').css('width', percentage + '%');
            $('#time-left').text(`${minutes}:${seconds < 10 ? '0' : ''}${seconds}`);
        }
    }, 1000);
}


$('#trial-complete-btn').on('click', function() {
    // Check if all questions are answered
    let allAnswered = true;

    // Loop through all questions and check if an answer is selected
    $('#trial-form .question').each(function() {
        const questionId = $(this).attr('id');
        // Check if any radio button in this question group is checked
        if ($(this).find('input[type="radio"]:checked').length === 0) {
            allAnswered = false;
            // Highlight unanswered question (optional visual feedback)
            $(this).css('border', '2px solid #ff5733');
        } else {
            $(this).css('border', ''); // Reset border for answered questions
        }
    });

    if (!allAnswered) {
        toastr.error('Please answer all trial questions before proceeding to the exam.');
        return; // Stop further execution
    }

    // Hide trial section and show exam section if all questions are answered
    $('#trial-section').hide();
    $('#exam-section').show();
    startTimer();
});


    $('.answer').on('change', function() {
    let answeredQuestions = 0;
    $('.question').each(function() {
        if ($(this).find('input[type="radio"]:checked').length > 0) {
            answeredQuestions++;
            $(this).find('.question-number-box').removeClass('unanswered').addClass('answered');
        }
    });

    // Subtract 5 from answeredQuestions, ensuring it doesn't go below 0
    answeredQuestions = Math.max(0, answeredQuestions - 5);

    $('#answered-questions').text(answeredQuestions);
    $('#submit-btn').toggle(answeredQuestions === parseInt(totalQuestions));
});

});

</script>
<style>
    /* Floating box for question count and timer */
    #question-status {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        padding: 10px;
        z-index: 1000;
        border-radius: 10px;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        width: 200px;
    }

    /* Battery-style countdown timer */
    .battery-outer {
        width: 100%;
        height: 20px;
        background-color: #ddd;
        border-radius: 5px;
        margin: 5px 0;
        position: relative;
    }

    .battery-inner {
        width: 100%;
        height: 100%;
        background-color: #048e2b;
        transition: width 1s linear;
    }

    /* Style for each question block */
    .question {
        border: 0.5px solid #ddd;
        padding: 10px;
        margin-bottom: 20px;
        position: relative;
        
    }

    /* Container for the question options (flexbox) */
    .options-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Style for each answer option */
    .form-check {
        flex: 1;
        text-align: center;
        margin: 5px;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    /* Hover effect for answer options */
    .form-check:hover {
        background-color: #e6f7ff;
        border-color: #048e2b;
    }

    /* Question number box on the side */
    .question-number-box {
        position: absolute;
        left: -60px;
        top: 10px;
        width: 50px;
        height: 50px;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.2em;
        font-weight: bold;
        border-radius: 5px;
    }

    /* Default background color for unanswered question numbers */
    .question-number-box.unanswered {
        background-color: #f0f0f0;
    }

    /* Red background for answered questions */
    .question-number-box.answered {
        background-color: #048e2b;
        color: white;
    }
    .question-number-box.answered {
        background-color: #048e2b;
        color: white;
    }

    /* Hide submit button initially */
    #submit-btn {
        display: none;
    }

    /* Subtle color usage for buttons */
    .btn-primary {
        background-color: #048e2b;
        border-color: #048e2b;
    }

    .btn-primary:hover {
        background-color: #036824;
        border-color: #036824;
    }
</style>
@endsection
