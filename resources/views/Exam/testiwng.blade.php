fix my design i want the color is #DAA520 and#048e2b(my primary color)

@extends('layouts.exam') <!-- Assuming you have a layout file -->

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
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
            <div class="card-body">
                <div id="trial-section">
                    <h4 class="text-center mb-4">Trial Questions</h4>
                    <form id="trial-form">
                        @csrf
                        @foreach ($trials as $index => $trial)
                        <div class="question" id="trial-question-{{ $index + 1 }}" style="{{ $index >= 32 ? 'display:none;' : '' }}">
                            <div class="question-number-box unanswered" id="trial-question-number-{{ $trial->id }}">
                                {{ $index + 1 }}
                            </div>

                            @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->question))
                            <img src="{{ asset($trial->question) }}" class="img-fluid mb-2" alt="Question Image">
                            @else
                            <p>{!! $trial->question !!}</p>
                            @endif

                            <!-- Options -->
                            <div class="options-container">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input trial-answer" name="trial_answers[{{ $trial->id }}]" value="A">
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch1))
                                        <img src="{{ asset($trial->ch1) }}" alt="Option A Image">
                                        @else
                                        {!! $trial->ch1 !!}
                                        @endif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input trial-answer" name="trial_answers[{{ $trial->id }}]" value="B">
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch2))
                                        <img src="{{ asset($trial->ch2) }}" alt="Option B Image">
                                        @else
                                        {!! $trial->ch2 !!}
                                        @endif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input trial-answer" name="trial_answers[{{ $trial->id }}]" value="C">
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch3))
                                        <img src="{{ asset($trial->ch3) }}" alt="Option C Image">
                                        @else
                                        {!!$trial->ch3 !!}
                                        @endif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input trial-answer" name="trial_answers[{{ $trial->id }}]" value="D">
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch4))
                                        <img src="{{ asset($trial->ch4) }}" alt="Option D Image">
                                        @else
                                        {!! $trial->ch4 !!}
                                        @endif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input trial-answer" name="trial_answers[{{ $trial->id }}]" value="E">
                                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $trial->ch5))
                                        <img src="{{ asset($trial->ch5) }}" alt="Option E Image">
                                        @else
                                        {!! $trial->ch5 !!}
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Submit Trial Questions -->
                        <div class="text-center mt-4">
                            <button type="button" id="trial-complete-btn" class="btn btn-primary">Proceed to Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card" id="exam-section">
            <div class="card-header" style="background-color: #048e2b; color: white;">
                <h3 class="card-title">Take Exam</h3>
                <div class="card-tools">
                    <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add User">
                        <i class="fas fa-plus" style="color: #048e2b;"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('exam.submit') }}" method="POST">
                    @csrf
                    <input class="form-control mb-4" name="student_id" id="student_id" value="{{ $student_id }}" hidden>

                    <!-- Floating box to show question count and timer -->
                    <div id="question-status">
                        <p>Total Questions: <span id="total-questions"></span></p>
                        <p>Answered Questions: <span id="answered-questions">0</span></p>
                        <div id="timer">
                            <div class="battery-outer">
                                <div class="battery-inner" id="battery-inner"></div>
                            </div>
                            <p id="time-left">45:00</p>
                        </div>
                    </div>

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
                            border: 1px solid #ddd;
                            padding: 20px;
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

                    @foreach ($questions as $index => $question)
                    <div class="question">
                        <!-- Box to display the question number -->
                        <div class="question-number-box unanswered" id="question-number-{{ $question->id }}">
                            {{ $index + 1 }}
                        </div>

                        <!-- Display the question (check if it's an image or text) -->
                        @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->question))
                        <img src="{{ asset($question->question) }}" class="img-fluid mb-2" alt="Question Image">
                        @else
                        <p>{!! $question->question !!}</p>
                        @endif

                        <!-- Options in a horizontal flex container -->
                        <div class="options-container">
                            <!-- Option A -->
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" value="A">
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_a))
                                    <img src="{{ asset($question->option_a) }}" alt="Option A Image">
                                    @else
                                    {!! $question->option_a !!}
                                    @endif
                                </label>
                            </div>

                            <!-- Option B -->
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" value="B">
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_b))
                                    <img src="{{ asset($question->option_b) }}" alt="Option B Image">
                                    @else
                                    {!! $question->option_b !!}
                                    @endif
                                </label>
                            </div>

                            <!-- Option C -->
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" value="C">
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_c))
                                    <img src="{{ asset($question->option_c) }}" alt="Option C Image">
                                    @else
                                    {!! $question->option_c !!}
                                    @endif
                                </label>
                            </div>

                            <!-- Option D -->
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" value="D">
                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $question->option_d))
                                    <img src="{{ asset($question->option_d) }}" alt="Option D Image">
                                    @else
                                    {!! $question->option_d !!}
                                    @endif
                                </label>
                            </div>

                            <!-- Option E -->
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input answer" name="answers[{{ $question->id }}]" value="E">
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

                    <!-- Submit button -->
                    <button type="submit" id="submit-btn" class="btn btn-primary">Submit Exam</button>

                    <!-- Include jQuery -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                    <script>
                        $(document).ready(function() {
                            // Get the total number of questions
                            var totalQuestions = {{ count($questions) }};
                            $('#total-questions').text(totalQuestions);
                    
                            // Countdown timer setup for testing (30 seconds)
                            var totalSeconds = 2700; // Set to 30 seconds for testing
                            var batteryWidth = 100; // Start with full battery width
                    
                            function startTimer() {
                                var countdown = setInterval(function() {
                                    if (totalSeconds <= 0) {
    clearInterval(countdown);
    $('#battery-inner').css('width', '0%');
    
    // Submit the form via AJAX
    $.ajax({
        url: $('form').attr('action'),  // Get the form action URL
        type: $('form').attr('method'), // Get the form method (POST/GET)
        data: $('form').serialize(),    // Serialize form data
        success: function(response) {
            // Show SweetAlert2 after form submission
            Swal.fire({
                title: 'Time is up!',
                text: 'Your exam has been submitted.',
                icon: 'success',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Optionally, redirect or reload the page
                    window.location.href = "/ncat";  // Example redirection
                }
            });
        },
        error: function() {
            Swal.fire({
                title: 'Submission failed!',
                text: 'There was an issue submitting your exam. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

                                else {
                                        totalSeconds--;
                                        var minutes = Math.floor(totalSeconds / 60);
                                        var seconds = totalSeconds % 60;
                                        var percentage = (totalSeconds / 30) * 100; // Calculate percentage of battery left
                                        $('#battery-inner').css('width', percentage + '%');
                    
                                        if (percentage <= 30) {
                                            $('#battery-inner').css('background-color', 'red');
                                        }
                    
                                        $('#time-left').text(minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
                                    }
                                }, 1000); // Countdown every second
                            }
                            $('#trial-complete-btn').on('click', function() {
            $('#trial-section').hide();
            $('#exam-section').show();
            startTimer();
        });
                        
                    
                            // Track the number of answered questions
                            function updateAnsweredCount() {
                                var answeredQuestions = 0;
                                $('.question').each(function() {
                                    var questionId = $(this).find('.question-number-box').attr('id').split('-')[2]; // Get question ID
                                    if ($(this).find('input[type="radio"]:checked').length > 0) {
                                        answeredQuestions++;
                                        // Change the background color of the question number to red when answered
                                        $('#question-number-' + questionId).removeClass('unanswered').addClass('answered');
                                    } else {
                                        // Reset the color if it's no longer answered
                                        $('#question-number-' + questionId).removeClass('answered').addClass('unanswered');
                                    }
                                });
                                $('#answered-questions').text(answeredQuestions);
                    
                                // Show the submit button only when all questions are answered
                                if (answeredQuestions === totalQuestions) {
                                    $('#submit-btn').show();
                                } else {
                                    $('#submit-btn').hide();
                                }
                            }
                    
                            // Call the function when a radio button is clicked
                            $('.answer').on('change', function() {
                                updateAnsweredCount();
                            });
                    
                            // Initial check (in case there are already selected answers on page load)
                            updateAnsweredCount();
                        });


                        document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent the default action (e.g., form submission)
    }
});
    
                    </script>
                    
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
