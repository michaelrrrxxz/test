$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {
    getCourses();
    
    $('#add').on('click',function (e) { 
        e.preventDefault();
        $('.modal-title').html('Add Course');
        $('#courses-form')[0].reset(); 
        $('#id').val('');
        $('#courses-modal').modal('show');
        
    });

    $('#courses-form').on('submit', function (e) {
        e.preventDefault();
    
        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url;
    
        if (id != '') {
            url = 'Courses/update/' + id;
        } else {
            url = 'Courses/add';
        }
    
        // Show loading spinner before AJAX request
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process the request.',
            allowOutsideClick: false, // Prevent closing the modal during loading
            didOpen: () => {
                Swal.showLoading(); // Show SweetAlert loading spinner
            }
        });
    
        $.ajax({
            processData: false,
            contentType: false,
            data: fd,
            url: url,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            if (data.result === 'success') {
                toastr.success(data.message, 'Success');
                getCourses();
                $('#courses-modal').modal('hide');
            } else {
                toastr.error(data.message, 'Error');
            }
            Swal.close(); // Close the loading spinner once the request is done
        }).fail(function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                toastr.error(jqXHR.responseJSON.message, 'Error');
            } else {
                toastr.error('An unexpected error occurred. Please try again.', 'Error');
            }
            Swal.close(); // Close the loading spinner in case of an error
        });
    });

    $('#courses-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update Course');
        var id = $(this).data('id');

        $.ajax({
            url: 'Courses/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                $('#name').val(data.name);
                $('#id').val(data.id);
                $('#courses-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    $('#courses-table').on('click', '.delete', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
    
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            preConfirm: () => {
                // Show loading spinner before AJAX request
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the course.',
                    allowOutsideClick: false, // Prevent closing the modal while loading
                    didOpen: () => {
                        Swal.showLoading(); // Show SweetAlert loading spinner
                    }
                });
    
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: 'Courses/delete/' + id,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function (data, textStatus, jqXHR) {
                        toastr.success(data.result, 'Deleted');
                        getCourses();
                        Swal.close(); // Close the loading spinner
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        toastr.error(errorThrown, 'Error');
                        Swal.close(); // Close the loading spinner in case of error
                    });
                });
            }
        });
    });

});



function getCourses() {
    $("#courses-table").DataTable({
        "responsive": true,
        "autoWidth": false,
        "destroy": true,
        "ajax": {
            "url": 'Courses/getCourses',
            "type": 'GET',
        },
        "columns": [
            { data: "department_name", title: "Department" },
            { 
                data: "courses", 
                title: "Courses",
                render: function (data) {
                    if (data.length > 0) {
                        return data.map(course => course.name).join("<br>"); // List courses line by line
                    }
                    return "No Courses Available";
                }
            }
        ],
        "ordering": false,
        "paging": false, // Disable paging for grouped view
        "info": false,
        "rowCallback": function (row, data, index) {
            if (data.department_name) {
                $(row).addClass('department-row');
            } else {
                $(row).addClass('course-row');
            }
        }
    });
}
