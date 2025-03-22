$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    getSchools();


    

    // Show the 'Add User' modal
    $('#add').on('click', function (e) {
        e.preventDefault();
        $('.modal-title').html('Add School');
        $('#schools-form')[0].reset(); // Reset form fields
        $('#id').val(''); // Ensure ID is empty for adding new user
        $('#schools-modal').modal('show');
    });

    // Show the 'Edit User' modal and populate it with existing data
    $('#Schools-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update User');
        var id = $(this).data('id');

        $.ajax({
            url: 'Schools/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                $('#username').val(data.username);
                $('#password').val('');
                $('#role').val(data.role);
                $('#id').val(data.id);
                $('#Schools-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            Swal.fire('Error', errorThrown, 'error'); 
        });
    });

    // Generate QR code for user
    $('#Schools-table').on('click', '.qr', function (e) {
        e.preventDefault();
        $('.modal-title').html('Scan');
        var id = $(this).data('id');

        $.ajax({
            url: 'Schools/edit/' + id,
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
            Swal.fire('Error', errorThrown, 'error'); 
        });
    });

    // Clear QR code when modal is hidden
    $('#user-modal').on('hidden.bs.modal', function () {
        $('#qrcode').empty();
    });

  
    $('#schools-form').on('submit', function (e) {
        e.preventDefault();
    
        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        console.log('ID:', id); // Add this line to check the value of id
        var url;
    
        if (id) { // Check if id is not empty or undefined
            url = 'Schools/update/' + id;
        } else {
            url = 'Schools/add';
        }
    
        $.ajax({
            processData: false,
            contentType: false,
            data: fd,
            url: url,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            Swal.fire('Success!', data.message, 'success');
            $('#schools-modal').modal('hide');
            getSchools();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error Details:', jqXHR.responseText); // Log detailed error information
            Swal.fire('Error!', errorThrown, 'error');
        });
    });
    
    // Handle delete user operation
    $('#schools-table').on('click', '.delete', function (e) {
        e.preventDefault();
        
        // Get the ID from the data-id attribute of the clicked button
        var id = $(this).data('id');
        console.log('ID:', id);
        
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
                    url: 'Schools/delete/' + id,
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Set CSRF token in headers
                    },
                    success: function (data) {
                        Swal.fire('Deleted!', data.message, 'success');
                        $('#schools-table').DataTable().ajax.reload(); // Reload the DataTable to reflect changes
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Error!', 'Failed to delete school: ' + errorThrown, 'error');
                        console.error('Error details:', jqXHR.responseText);
                    }
                });
            }
        });
   
    












        
    });

    // Function to get and display Schools
    function getSchools() {
        $("#schools-table").dataTable({
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'Schools/getSchools'
            },
            "columns": [
                { data: "school_name" },
                {data:"city"},
                { data: "province" },
                {data:"region"},
                {
                    data: null, render: function (data) {
                        var option = '<div style="text-align:center;"><a href="" class="qr" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="View User" data-id="' + data.id + '"><i' +
                            ' class="fa fas fa-qrcode text-primary"></i></a> |<a href="" class="edit" data-toggle="tooltip" ' +
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
