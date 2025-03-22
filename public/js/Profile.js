$(document).ready(function () {

    $('#delete-profile').on('click', function (e) {
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
                    text: 'Please wait while we delete the user.',
                    allowOutsideClick: false, // Prevent closing the modal while loading
                    didOpen: () => {
                        Swal.showLoading(); // Show SweetAlert loading spinner
                    }
                });
    
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: 'Profile/delete',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function (data, textStatus, jqXHR) {
                        toastr.success(data.result, 'Deleted');

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

    

    $('#edit-profile').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'Profile/getProfile/',
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                $('#username-edit').val(data.username);
                $('#password-edit').val('');
                $('#role-edit').val(data.role);
                $('#id-edit').val(data.id);
                $('.modal-title').html('Edit User');
             
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
      
        $('#profile-modal').modal('show');
    });

    $('#profile-form').on('submit', function (e) {
        e.preventDefault();
    
        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url = 'Profile/update';
    
  
    
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
               
                $('#profile-modal').modal('hide');
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

  
     
       
 });
