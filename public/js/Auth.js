$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$(document).ready(function() {

    $(document).ready(function() {
        $('#agreement-show').on('click', function (e) {
        $('#agreement-modal').modal('show');
    });
        $('#agreement-showw').on('click', function (e) {
        $('#agreement-modal').modal('show');
    });


        $('#userAgreement').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('#usingemail').prop('disabled', !isChecked);
            });
        $('#userAgreementt').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('#usingkey').prop('disabled', !isChecked);
            });
        // Switch to Access Key Form
        $('#switchToAccessKey').click(function(e) {
            e.preventDefault(); // Prevent default anchor behavior
            $('#emailForm').hide(); // Hide email form
            $('#accessKeyForm').show(); // Show access key form
        });

        // Switch back to Email Form
        $('#switchToEmail').click(function(e) {
            e.preventDefault(); // Prevent default anchor behavior
            $('#accessKeyForm').hide(); // Hide access key form
            $('#emailForm').show(); // Show email form
        });
    });
     toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000",
        "extendedTimeOut": "1000"
    };
    $('#login-admin').on('click', function (e) {
        e.preventDefault();
        $('#login-modal').modal('show');  
    });
    $('#login-instructor').on('click', function (e) {
        e.preventDefault();
        $('#instructor-login-modal').modal('show');  
    });
    

    $('#no-email').on('click', function (e) {
        e.preventDefault();
        $('#no-email-modal').modal('show');  
    });

    $('form#loginForm').on('submit', function(event) {
        event.preventDefault(); 

        var formData = {
            username: $('#username').val(),
            password: $('#password').val(),
                _token: $('input[name="_token"]').val() // Include CSRF token
            };

      
        $.ajax({
            type: 'POST',
            url: login,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.result === 'success') {
                    toastr.success(response.message) // Show success message
                    window.location.href = response.redirect; // Redirect on success
                } else {
                    toastr.error(response.message) // Show error message
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Display validation errors
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]); // Show the first error message
                    });
                } else {
                    toastr.error('An errr occurred. Please try again.');
                }
            }
        });
    });


    $('#instructor-login-form').on('submit', function(event) {
        event.preventDefault(); 

        var formData = {
            username: $('#username-ins').val(),
            password: $('#password-ins').val(),
            _token: $('input[name="_token"]').val()

            };

      
        $.ajax({
            type: 'POST',
            url: logininstructor,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.result === 'success') {
                    toastr.success(response.message) // Show success message
                    window.location.href = response.redirect; // Redirect on success
                } else {
                    toastr.error(response.message) // Show error message
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Display validation errors
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]); // Show the first error message
                    });
                } else {
                    toastr.error('An errr occurred. Please try again.');
                }
            }
        });
    });


    $('#no-email-form').on('submit', function(event) {
        event.preventDefault(); 
    
        // Get form data
        var fd = new FormData(this);
    
        // Add CSRF token from the meta tag (Laravel includes it in the page's head section)
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        // Ensure the token is in the headers
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
    
        var url = 'EnrolledStudents/verifyAccessKey';
    
        $.ajax({
            processData: false,
            contentType: false,
            data: fd,
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            if (response.result === 'success') {
                alert(response.message); // Show success message
                window.location.href = response.redirect; // Redirect on success
            } else {
                alert(response.message); // Show error message
            }
        }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // Display error message
            toastr.error(jqXHR.responseJSON.message || 'An error occurred', 'Error');
        });
    });


    
   
    
});

