$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    
    getUsers();
    toggleDepartmentField();

    // Show the 'Add User' modal
    $('#add').on('click', function (e) {
        e.preventDefault();
        $('.modal-title').html('Add User');
        $('#users-form')[0].reset(); // Reset form fields
        $('#id').val(''); // Ensure ID is empty for adding new user
        $('#users-modal').modal('show');
    });

    // Show the 'Edit User' modal and populate it with existing data
    $('#users-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update User');
        var id = $(this).data('id');

        $.ajax({
            url: 'Users/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                $('#username').val(data.username);
                $('#password').val('');
                $('#role').val(data.role);
                $('#id').val(data.id);
                $('#users-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    // Generate QR code for user
    $('#users-table').on('click', '.qr', function (e) {
        e.preventDefault();
        $('.modal-title').html('Scan');
        var id = $(this).data('id');

        $.ajax({
            url: 'Users/edit/' + id,
            type: 'GET',
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                var userData = {
                    username: data.username,
                    role: data.role
                };

                var userDataString = JSON.stringify(userData);

                var qrcode = new QRCode(document.getElementById("qrcode"), {
                    text: userDataString,
                    width: 250, 
                    height: 250  
                });

                $('#user-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    // Clear QR code when modal is hidden
    $('#user-modal').on('hidden.bs.modal', function () {
        $('#qrcode').empty();
    });

    // Handle form submission for adding/updating users
    $('#users-form').on('submit', function (e) {
        e.preventDefault();
    
        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url;
    
        if (id != '') {
            url = 'Users/update/' + id;
        } else {
            url = 'Users/add';
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
                getUsers();
                $('#users-modal').modal('hide');
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
    

    // Handle delete user operation
    $('#users-table').on('click', '.delete', function (e) {
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
                    text: 'Please wait while we delete the user.',
                    allowOutsideClick: false, // Prevent closing the modal while loading
                    didOpen: () => {
                        Swal.showLoading(); // Show SweetAlert loading spinner
                    }
                });
    
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: 'Users/delete/' + id,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function (data, textStatus, jqXHR) {
                        toastr.success(data.result, 'Deleted');
                        getUsers();
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
    
    
    // Function to get and display users
    function getUsers() {
        $("#users-table").dataTable({
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'Users/getUsers'
            },
            "columns": [
                { data: "username" },
                { data: "role",
                    // createdCell: function (td, cellData, rowData, row, col) {
                    //     tippy(td, {
                    //         content: `<div class="tooltip-bg">
                    //             <div class="tooltip-content">
                                   
                    //                 <strong>Name:</strong> ${cellData} <br>
                        
                    //             </div>
                    //           </div>`,
                    //         theme: 'dark',
                    //         placement: 'bottom',
                    //         arrow: true,
                    //         animation: 'fade',
                    //         duration: 300,
                    //         allowHTML: true
                    //     });
                    // }
                },
                {
                    data: null, render: function (data) {
                        var option = '<div style="text-align:center;"><a href="" class="edit" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Edit User" data-id="' + data.id + '"><i' +
                            ' class="fa fas fa-pen text-success"></i></a> | <a href="" class="delete text-danger" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Delete User" data-id="' + data.id + '"><i' +
                            ' class="fa fa-trash"></i></a></div>';
                        return option;
                    }
                }
            ]
        });
    }
});


function toggleDepartmentField() {
    var roleSelect = document.getElementById("role");
    var departmentField = document.getElementById("departmentField");

    if (roleSelect.value === "instructor") {
        departmentField.style.display = "block";
    } else {
        departmentField.style.display = "none";
        document.getElementById("department").value = ""; // Clear department value if hidden
    }
}

// Initialize department field visibility on page load

