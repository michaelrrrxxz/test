$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    getInstructors();

    // Show the 'Add Instructor' modal
    $('#add').on('click', function (e) {
        e.preventDefault();
        $('.modal-title').html('Add Instructor');
        $('#instructors-form')[0].reset(); // Reset form fields
        $('#id').val(''); // Ensure ID is empty for adding new instructor
        $('#instructors-modal').modal('show');
    });

    // Show the 'Edit Instructor' modal and populate it with existing data
    $('#instructors-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update Instructor');
        var id = $(this).data('id');

        $.ajax({
            url: 'Instructors/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {

            
                
                $('#id_number').val(data.id_number)
                $('#fullname').val(data.fullname);
                $('#username').val(data.username);
                $('#password').val('');
                $('#id').val(data.id);
                $('#instructors-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    // Generate QR code for instructor
    $('#instructors-table').on('click', '.qr', function (e) {
        e.preventDefault();
        $('.modal-title').html('Scan');
        var id = $(this).data('id');

        $.ajax({
            url: 'Instructors/edit/' + id,
            type: 'GET',
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                var instructorData = {
                    username: data.username,
                    fullname: data.fullname
                };

                var instructorDataString = JSON.stringify(instructorData);

                var qrcode = new QRCode(document.getElementById("qrcode"), {
                    text: instructorDataString,
                    width: 250, 
                    height: 250  
                });

                $('#instructor-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    // Clear QR code when modal is hidden
    $('#instructor-modal').on('hidden.bs.modal', function () {
        $('#qrcode').empty();
    });

    // Handle form submission for adding/updating instructors
    $('#instructors-form').on('submit', function (e) {
        e.preventDefault();

        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url;

        if (id != '') {
            url = 'Instructors/update/' + id;
        } else {
            url = 'Instructors/add';
        }

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
                getInstructors();
                $('#instructors-modal').modal('hide');
            } else {
                toastr.error(data.message, 'Error');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                toastr.error(jqXHR.responseJSON.message, 'Error');
            } else {
                toastr.error('An unexpected error occurred. Please try again.', 'Error');
            }
        });
    });

    // Handle delete instructor operation
    $('#instructors-table').on('click', '.delete', function (e) {
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
                    url: 'Instructors/delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function (data, textStatus, jqXHR) {
                    toastr.success(data.message, 'Deleted');
                    getInstructors();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, 'Error');
                });
            }
        });
    });

    // Function to get and display instructors
    function getInstructors() {
        $("#instructors-table").dataTable({
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'Instructors/getInstructors'
            },
            "columns": [
                { data: "id_number" },
                { data: "fullname" },
                { data: "username" },
                { data: "department" },
                {
                    data: null, render: function (data) {
                        var option = '<div style="text-align:center;"><a href="" class="qr" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="View Instructor" data-id="' + data.id + '"><i' +
                            ' class="fa fas fa-qrcode text-primary"></i></a> |<a href="" class="edit" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Edit Instructor" data-id="' + data.id + '"><i' +
                            ' class="fa fas fa-pen text-success"></i></a> | <a href="" class="delete text-danger" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Delete Instructor" data-id="' + data.id + '"><i' +
                            ' class="fa fa-trash"></i></a></div>';
                        return option;
                    }
                }
            ]
        });
    }
});
