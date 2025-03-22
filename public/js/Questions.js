$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    // Load default 'All Questions' when the page loads
    getQuestions();

    // Add button click handler
    $('#add').on('click', function (e) {
        e.preventDefault();
    
        // Make an AJAX request to check the total number of questions
        $.ajax({
            url: 'Questions/check-total-questions', // URL where you check the total questions (create a route in Laravel)
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // Check if total questions exceed 72
                if (data.total_questions >= 72) {
                    // If the total number of questions is 72 or more, show an error notification
                    toastr.error('You cannot add more than 72 questions.', 'Error');
                } else {
                    // If there are less than 72 questions, show the modal to add a new question
                    $('.modal-title').html('Add Question');
                    $('#questions-form')[0].reset(); // Reset form
                    $('#id').val(''); // Clear the id field
                    $('#questions-modal').modal('show'); // Show the modal
                }
            },
            error: function () {
                toastr.error('Error checking total questions.', 'Error');
            }
        });
    });
    

    // Edit button handler
    $('#questions-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update question');
        var id = $(this).data('id');

        $.ajax({
            url: 'Questions/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data) {
            if (data != '') {
                $('#question_type').val(data.question_type);
                $('#question').val(data.question);
                $('#option_a_text').val(data.option_a);
                $('#option_b_text').val(data.option_b);
                $('#option_c_text').val(data.option_c);
                $('#option_d_text').val(data.option_d);
                $('#option_e_text').val(data.option_e);
                $('#option_correct').val(data.option_correct);
                $('#ctype').val(data.ctype);
                $('#id').val(data.id);
                $('#questions-modal').modal('show');
            }
        })
        .fail(function () {
            Swal.fire('Error', 'Failed to fetch question data', 'error');
        });
    });

    // View button handler
    $('#questions-table').on('click', '.view', function (e) {
        e.preventDefault();
        $('.modal-title').html('View Question');
        var id = $(this).data('id');

        $.ajax({
            url: 'Questions/show/' + id,
            type: 'GET',
            dataType: 'json'
        })
        .done(function (data) {
            if (data) {
                $('#question-view-question').html(formatHtml(data.question));
                $('#option_a_view').html(getOptionHtml(data.option_a));
                $('#option_b_view').html(getOptionHtml(data.option_b));
                $('#option_c_view').html(getOptionHtml(data.option_c));
                $('#option_d_view').html(getOptionHtml(data.option_d));
                $('#option_correct_view').val(data.option_correct);
                $('#ctype_view').val(data.ctype);

                $('#question-view').modal('show');
            } else {
                Swal.fire('Error', 'Failed to retrieve question data', 'error');
            }
        })
        .fail(function () {
            Swal.fire('Error', 'Failed to fetch question data', 'error');
        });
    });

    // Delete button handler
    $('#questions-table').on('click', '.delete', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'Questions/delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function (data) {
                    toastr.success(data.result, 'Deleted');
                    getQuestions();
                })
                .fail(function () {
                    toastr.error('Error deleting question', 'Error');
                });
            }
        });
    });

    // Submit form handler
    $('#questions-form').on('submit', function (e) {
        e.preventDefault();
        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url = id ? 'Questions/update/' + id : 'Questions/add';

        // $('#loading-modal').modal('show');

        $.ajax({
            xhr: function () {
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        var percentComplete = Math.round((e.loaded / e.total) * 100);
                        $('#upload-progress').css('width', percentComplete + '%');
                        $('#upload-progress').attr('aria-valuenow', percentComplete);
                        $('#upload-progress').text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            processData: false,
            contentType: false,
            data: fd,
            url: url,
            type: 'POST',
            dataType: 'json'
        })
        .done(function (data) {
            toastr.success(data.message, 'Success');
            $('#loading-modal').modal('hide');
            $('#questions-modal').modal('hide');
            getQuestions();
        })
        .fail(function () {
            toastr.error('Error saving question', 'Error');
            $('#loading-modal').modal('hide');
        });
    });

    // Handle tab click event to load the right questions
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if (target === "#verbal") {
            getVerbalQuestions();
        } else if (target === "#non-verbal") {
            getNonVerbalQuestions();
        } else {
            getQuestions(); // Default to All Questions
        }
    });

    // Function to load all questions
    function getQuestions() {
        $("#questions-table").dataTable({
            "responsive": true,
            "autoWidth": true,
            
            "searching": false,
            "ordering": false,
            "lengthChange": false,
            "destroy": true,
            "ajax": {
                "url": 'Questions/getQuestions'
            },
            "columns": tableColumns()
        });
    }

    // Function to load verbal questions
    function getVerbalQuestions() {
        $("#verbal-questions-table").dataTable({
            "responsive": true,
            "autoWidth": true,

            "searching": false,
            "ordering": false,
            "lengthChange": false,
            "destroy": true,
            "ajax": {
                "url": 'Questions/getVerbalQuestions'
            },
            "columns": tableColumns()
        });
    }

    // Function to load non-verbal questions
    function getNonVerbalQuestions() {
        $("#non-verbal-questions-table").dataTable({
            "responsive": true,
            "autoWidth": true,
           
            "searching": false,
            "ordering": false,
            "lengthChange": false,
            "destroy": true,
            "ajax": {
                "url": 'Questions/getNonVerbalQuestions'
            },
            "columns": tableColumns()
        });
    }

    // Helper to define table columns
    function tableColumns() {
        return [
            { 
                "data": null, 
                "render": function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                } 
            },
      
            { data: "test_type" },
            { data: "ctype" },
            { data: "question", render: formatQuestionContent },
            { data: "option_a", render: formatOptionContent },
            { data: "option_b", render: formatOptionContent },
            { data: "option_c", render: formatOptionContent },
            { data: "option_d", render: formatOptionContent },
            { data: "option_e", render: formatOptionContent },
            { data: "option_correct", render: formatOptionContent },
            { data: null, render: questionActions }
        ];
    }

    // Function to render question content
    function formatQuestionContent(data) {
        if (data && data.match(/\.(jpeg|jpg|gif|png)$/)) {
            return '<img src="' + data + '" alt="Image" style="max-width: 1800px; max-height: 90px;">';
        } else {
            return data;
        }
    }

    // Function to render options (A, B, C, etc.)
    function formatOptionContent(data) {
        if (data && data.match(/\.(jpeg|jpg|gif|png)$/)) {
            return '<img src="' + data + '" alt="Image" style="max-width: 60px; max-height: 60px;">';
        } else {
            return data;
        }
    }

    // Function to render action buttons (View, Edit, Delete)
    function questionActions(data) {
        return `
            <div style="text-align:center;">
                
                <a href="" class="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Question" data-id="${data.id}">
                    <i class="fa fas fa-pen text-success"></i>
                </a> |
                <a href="" class="delete" data-toggle="tooltip" data-placement="bottom" title="Delete Question" data-id="${data.id}">
                    <i class="fa fas fa-trash text-danger"></i>
                </a> 
                
            </div>
        `;
    }

    // Utility to format HTML content for the View modal
    function formatHtml(data) {
        if (data && data.match(/\.(jpeg|jpg|gif|png)$/)) {
            return '<img src="' + data + '" alt="Image" style="max-width: 100px; max-height: 100px;">';
        } else {
            return data;
        }
    }

    // Utility to get formatted options for the View modal
    function getOptionHtml(data) {
        return formatHtml(data) || 'N/A';
    }
});
