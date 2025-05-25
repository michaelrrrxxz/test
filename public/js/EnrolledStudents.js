$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {

    getEnrolledStudents();

    var currentYear = new Date().getFullYear();
    $('#exam_year').val(currentYear);

    $('#upload').on('click', function (e) {
        e.preventDefault();
        $('.modal-title').html('Upload Enrolled Students');
        $('#id').val('');
        $('#upload-modal').modal('show');
    });

    $('#add').on('click', function (e) {
        e.preventDefault();
        $('.modal-title').html('Add Enrolled Students');
        $('#id').val('');
        $('#enrolledstudents-modal').modal('show');
    });

    $('#enrolledstudents-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update Enrolled');
        var id = $(this).data('id');

        $.ajax({
            url: 'EnrolledStudents/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                $('#name').val(data.name);
                $('#id_number').val(data.id_number)
                $('#id').val(data.id);
                $('#enrolledstudents-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });


    $('#enrolledstudens-form').on('submit', function (e) {
        e.preventDefault();

        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url = 'EnrolledStudents/update/' + id;

        $.ajax({
            processData: false,
            contentType: false,
            data: fd,
            url: url,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            toastr.success(data.message, 'Success');
            getEnrolledStudents();
            $('#enrolledstudents-modal').modal('hide');
        }).fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    $('#add-enrolledstudents-form').on('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: 'EnrolledStudents/add',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Success logic
                getEnrolledStudents();
                $('#enrolledstudents-modal').modal('hide');

                // Toastr success notification
                toastr.success(response.message, 'Success!');

                document.getElementById('enrolledstudents-form').reset();
            },
            error: function(result) {

                if (result.status === 422) {
                    var errors = result.responseJSON.errors;


                    $.each(errors, function(field, messages) {

                        toastr.error(messages.join(', '), 'Validation Error');
                    });
                } else {

                    toastr.error('An error occurred. Please try again.', 'Error!');
                }
            }
        });
    });

    $('#enrolledstudents-form').on('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        // Show Swal loading
        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait while we process the data.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'EnrolledStudents/upload',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                Swal.close(); // Close the loading alert

                let insertedCount = response.inserted_count;

                Swal.fire({
                    title: `Are you sure?`,
                    text: `You are about to insert ${insertedCount} students.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, insert them!',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        getEnrolledStudents();
                        $('#upload-modal').modal('hide');

                        toastr.success(`${insertedCount} students have been successfully inserted.`, 'Success!');
                        document.getElementById('enrolledstudents-form').reset();
                    } else {
                        toastr.info('Operation cancelled.', 'Cancelled');
                    }
                });
            },
            error: function(result) {
                Swal.close(); // Close the loading alert on error
                toastr.error('An error occurred. Please try again.', 'Error!');
            }
        });
    });






    // Handle delete user operation
    $('#enrolledstudents-table').on('click', '.delete', function (e) {
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
                    url: 'EnrolledStudents/delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function (data, textStatus, jqXHR) {
                    toastr.success(data.message, 'Success');
                    getEnrolledStudents();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, 'Error');
                });
            }
        });
    });

    function getEnrolledStudents() {
        var selectedYear = $("#year-filter").val();
        // Initialize the DataTable with filtering
        var table = $("#enrolledstudents-table").DataTable({
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'EnrolledStudents/getEnrolledStudents',
                "data": function(d) {
                    // Add the selected year to the data sent to the server
                    d.year = selectedYear; // 'year' is the parameter name
                }
            },
            "columns": [
                { data: "id_number" },
                { data: "course" },
                { data: "department" },
                { data: "name" },
                {
                    data: "gender",
                    title: "Gender",
                    render: function (data, type, row) {
                        return formatGender(data);
                    }
                },
                { data: "exam_year" },
                {
                    data: null, render: function (data) {
                        var option = '<div  style="text-align: center;"><a href="" class="edit" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Edit Enrolled Students" data-id="' + data.id + '"><i' +
                            ' class="fa fas fa-pen text-success"></i></a></div>';
                        return option;
                    }
                }
            ]
        });

        $("#year-filter").change(function() {
            getEnrolledStudents(); // Fetch batches with the selected year
        });

    $('#course-filter-enrolled').on('keyup change', function() {
        // Get the value of the input field
        var filterValue = $(this).val();
        // Use the search method to filter the DataTable
        table.column(1).search(filterValue).draw(); // Index 1 corresponds to the 'course' column
    });
    $('#department-filter').on('keyup change', function() {
        // Get the value of the input field
        var filterValue = $(this).val();
        // Use the search method to filter the DataTable
        table.column(2).search(filterValue).draw(); // Index 1 corresponds to the 'course' column
    });

    }



    $(document).ready(function() {
        // Call the function when the document is ready
        getEnrolledStudents();
    });

});


function formatGender(gender) {
    switch (gender) {
        case 'M':
            return 'Male';
        case 'F':
            return 'Female';
        default:
            return '<i class="fas fa-genderless"></i> Unknown';
    }
}
