$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {
    getDepartments();

    $('#add').on('click',function (e) {
        e.preventDefault();
        $('.modal-title').html('Add Department');
        $('#departments-form')[0].reset();
        $('#id').val('');
        $('#departments-modal').modal('show');

    });

    $('#departments-form').on('submit', function (e) {
        e.preventDefault();

        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url;

        if (id != '') {
            url = 'Departments/update/' + id;
        } else {
            url = 'Departments/add';
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
                getDepartments();
                // $('#departments-modal').modal('hide');
                $('#departments-form')[0].reset();
                $('#departments-modal').modal('show');
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

    $('#departments-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update Department');
        var id = $(this).data('id');

        $.ajax({
            url: 'Departments/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                $('#name').val(data.name);
                $('#id').val(data.id);
                $('#departments-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    $('#departments-table').on('click', '.delete', function (e) {
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
                    text: 'Please wait while we delete the Department.',
                    allowOutsideClick: false, // Prevent closing the modal while loading
                    didOpen: () => {
                        Swal.showLoading(); // Show SweetAlert loading spinner
                    }
                });

                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: 'Departments/delete/' + id,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function (data, textStatus, jqXHR) {
                        toastr.success(data.result, 'Deleted');
                        getDepartments();
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



function getDepartments() {
    $("#departments-table").dataTable({
        "responsive": true,
        "autoWidth": false,
        "destroy": true,
        "ajax": {
            "url": 'Departments/getDepartments'
        },
        "columns": [
            { data: "name" },
            {
                data: null, render: function (data) {
                    var option = '<div style="text-align:center;"><a href="" class="edit" data-toggle="tooltip" ' +
                        'data-placement="bottom" title="Edit Department" data-id="' + data.id + '"><i' +
                        ' class="fa fas fa-pen text-success"></i></a> | <a href="" class="delete text-danger" data-toggle="tooltip" ' +
                        'data-placement="bottom" title="Delete Department" data-id="' + data.id + '"><i' +
                        ' class="fa fa-trash"></i></a></div>';
                    return option;
                }
            }
        ]
    });
}
