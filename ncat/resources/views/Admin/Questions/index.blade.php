@extends('layouts.default')
@section('title', 'Questions')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Questions</li>
@endsection
@section('content')
<style>
    .option-container {
    margin: 10px 0;
}

.option-container .form-control {
    margin-bottom: 10px;
}

.option-container .btn {
    width: 100%;
    padding: 10px;
    text-align: center;
    cursor: pointer;
}

.option-container .choice-image {
    display: none; /* This ensures the file input is hidden */
}

</style>
<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">Questions List</h3>
            <div class="card-tools">
                <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add Question">
                    <i class="fas fa-plus text-white"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="questionTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="all-questions-tab" data-toggle="tab" href="#all-questions" role="tab" aria-controls="all-questions" aria-selected="true">All Questions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="verbal-tab" data-toggle="tab" href="#verbal" role="tab" aria-controls="verbal" aria-selected="false">Verbal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="non-verbal-tab" data-toggle="tab" href="#non-verbal" role="tab" aria-controls="non-verbal" aria-selected="false">Non-Verbal</a>
                </li>
            </ul>
            <br>

            <!-- Tab panes -->
            <div class="tab-content text-capitalize">
                <!-- All Questions Tab -->
                <div class="tab-pane fade show active" id="all-questions" role="tabpanel" aria-labelledby="all-questions-tab">
                    <table id="questions-table" class="table table-bordered table-responsive table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Test Type</th>
                            <th>Question Type</th>
                            <th>Question</th>
                            <th>Option A</th>
                            <th>Option B</th>
                            <th>Option C</th>
                            <th>Option D</th>
                            <th>Option E</th>
                            <th>Correct Answer</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                    </table>
                </div>

                <!-- Verbal Tab -->
                <div class="tab-pane fade" id="verbal" role="tabpanel" aria-labelledby="verbal-tab">
                    <table id="verbal-questions-table" class="table table-bordered table-responsive table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Test Type</th>
                            <th>Question Type</th>
                            <th>Question</th>
                            <th>Option A</th>
                            <th>Option B</th>
                            <th>Option C</th>
                            <th>Option D</th>
                            <th>Option E</th>
                            <th>Correct Answer</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                    </table>
                </div>

                <!-- Non-Verbal Tab -->
                <div class="tab-pane fade" id="non-verbal" role="tabpanel" aria-labelledby="verbal-tab">
                    <table id="non-verbal-questions-table" class="table table-bordered table-responsive table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Test Type</th>
                            <th>Question Type</th>
                            <th>Question</th>
                            <th>Option A</th>
                            <th>Option B</th>
                            <th>Option C</th>
                            <th>Option D</th>
                            <th>Option E</th>
                            <th>Correct Answer</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-md" tabindex="-1" role="dialog" aria-hidden="true" id="questions-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

                <form id="questions-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Test Type</label>
                        <select name="test_type" id="test_type" class="form-control">
                            <option value="verbal">Verbal</option>
                            <option value="non-verbal">Non-Verbal</option>
                        </select>

                        <label>Question Category</label>
                        <select name="ctype" id="ctype" class="form-control">
                        
                        </select>               
                            <label>Question</label> 
                            <label>Upload Type</label>
                                <select name="upload_type" id="upload_type" class="form-control">
                                    <option value="text">Text</option>
                                    <option value="photo">Photo</option>
                                </select>
                                <div id="text_area_container">
                                    <label>Question (Text)</label>
                                    <textarea class="form-control" name="question" id="question"></textarea>
                                </div>
                                <div id="photo_upload_container" style="display: none;">
                                    <label>Upload Photo</label>
                                    <input type="file" class="form-control" name="question_photo" id="question_photo" accept="image/*">
                                </div>

                            <div>
                                <div class="option-switch">
                                    <label>Choices Type</label>
                                    <select name="choices_type" id="choices_type" class="form-control">
                                        <option value="text">Text</option>
                                        <option value="image">Image</option>
                                    </select>
                                </div>
        
                                <div class="option-container">
                                    <label>Option A</label>
                                    <div id="option_a_container">
                                        <input class="form-control choice-text" name="option_a_text" id="option_a_text" placeholder="A">
                                        <input class="form-control choice-image" type="file" name="option_a_image" id="option_a_image" style="display: none;">
                                    </div>
                                </div>
        
                                <div class="option-container">
                                    <label>Option B</label>
                                    <div id="option_b_container">
                                        <input class="form-control choice-text" name="option_b_text" id="option_b_text" placeholder="B">
                                        <input class="form-control choice-image" type="file" name="option_b_image" id="option_b_image" style="display: none;">
                                    </div>
                                </div>
        
                                <div class="option-container">
                                    <label>Option C</label>
                                    <div id="option_c_container">
                                        <input class="form-control choice-text" name="option_c_text" id="option_c_text" placeholder="C">
                                        <input class="form-control choice-image" type="file" name="option_c_image" id="option_c_image" style="display: none;">
                                    </div>
                                </div>
        
                                <div class="option-container">
                                    <label>Option D</label>
                                    <div id="option_d_container">
                                        <input class="form-control choice-text" name="option_d_text" id="option_d_text" placeholder="D">
                                        <input class="form-control choice-image" type="file" name="option_d_image" id="option_d_image" style="display: none;">
                                    </div>
                                </div>
                                <div class="option-container">
                                    <label>Option E</label>
                                    <div id="option_e_container">
                                        <input class="form-control choice-text" name="option_e_text" id="option_e_text" placeholder="E">
                                        <input class="form-control choice-image" type="file" name="option_e_image" id="option_e_image" style="display: none;">
                                    </div>
                                </div>
        
                                
                            </div>
    
                            

                            <div class="option-container">
                                <label>Correct Option</label>
                               
                                    <select name="option_correct" id="option_correct" class="form-select">
                                        <option name="A" id="">A</option>
                                        <option name="B" id="">B</option>
                                        <option name="C" id="">C</option>
                                        <option name="D" id="">D</option>
                                        <option name="E" id="">E</option>
                                    </select>
                               
                            </div>


                            {{-- <label>Question Type</label>
                            <select name="ctype" id="ctype" class="form-control">
                                <option value="Verbal Comprehension">Verbal Comprehension</option>
                                <option value="Quantitative Reasoning">Quantitative Reasoning</option>
                                <option value="Figural Reasoning">Figural Reasoning</option>
                            </select> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="id" id="id">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" name="save">Save</button>
                    </div>
                </form>
        </div>
    </div>
</div>
<script>
    ClassicEditor
        .create(document.querySelector('#question'), {
            toolbar: ['bold', 'italic']
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#option_a'), {
            toolbar: ['bold', 'italic']
        })
        .catch(error => {
            console.error(error);
        });
</script>
<script>
     function updateQuestionCategory() {
        var testType = $('#test_type').val();
        var $ctypeSelect = $('#ctype');

        // Clear the current options
        $ctypeSelect.empty();

        if (testType === 'verbal') {
            // Add verbal options
            var verbalOptions = [
                { value: 'Verbal Reasoning', text: 'Verbal Reasoning' },
                { value: 'Verbal Comprehension', text: 'Verbal Comprehension' }
            ];
            $.each(verbalOptions, function(index, option) {
                $ctypeSelect.append($('<option>', { value: option.value, text: option.text }));
            });
        } else {
            // Add non-verbal options
            var nonVerbalOptions = [
                { value: 'Quantitative Reasoning', text: 'Quantitative Reasoning' },
                { value: 'Figural Reasoning', text: 'Figural Reasoning' }
            ];
            $.each(nonVerbalOptions, function(index, option) {
                $ctypeSelect.append($('<option>', { value: option.value, text: option.text }));
            });
        }
    }


$(document).ready(function() {

    $('#upload_type').change(function() {
            var selectedType = $(this).val();

            if (selectedType === 'text') {
                $('#text_area_container').show();
                $('#photo_upload_container').hide();
            } else if (selectedType === 'photo') {
                $('#text_area_container').hide();
                $('#photo_upload_container').show();
            }
    });
    updateQuestionCategory();
        
    $('#test_type').change(function() {
        updateQuestionCategory();
    });

    $('#choices_type').change(function() {
        var choiceType = $(this).val();
        
        if (choiceType == 'text') {
            $('.choice-text').show();
            $('.choice-image').hide();
        } else {
            $('.choice-text').hide();
            $('.choice-image').show();
        }
    });

    // Trigger change to set the initial state based on the current selection
    $('#choices_type').trigger('change');
});
</script>


<div class="modal fade" id="question-view" tabindex="-1" role="dialog" aria-labelledby="question-view-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="question-view-title">View Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Question</label>
                    <textarea type="text" class="form-control" id="question-view-question" readonly> </textarea>
                </div>

                <div class="form-group">
                    <label>Options</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="option_a_view">Option A</label>
                            <div id="option_a_view"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="option_b_view">Option B</label>
                            <div id="option_b_view"></div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="option_c_view">Option C</label>
                            <div id="option_c_view"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="option_d_view">Option D</label>
                            <div id="option_d_view"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Correct Option</label>
                    <input type="text" class="form-control" id="option_correct_view" readonly>
                </div>

                <div class="form-group">
                    <label>Question Type</label>
                    <input type="text" class="form-control" id="ctype_view" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="loading-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uploading...</h5>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div id="upload-progress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        0%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{asset('js/Questions.js')}}"></script>
@endsection