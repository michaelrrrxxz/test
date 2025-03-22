$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Initialize DataTable
    var table = $("#noexam-table").DataTable({
        "responsive": true,
        "autoWidth": false,
        "destroy": true,
        // "paging": false,
        "ajax": {
            "url": 'No-Exam/getnoExam'
        },
        "columns": [
            { data: "id_number" },
            { data: "course" },
            { data: "name" },
            { 
                data: "gender", 
                title: "Gender", 
                render: function (data, type, row) {
                    return formatGender(data);
                }
            }
        ],
       "buttons": [
    { extend: "csv", className: "export-csv" }, 
    { extend: "excel", className: "export-excel" }, 
    { 
        extend: "pdf", 
        className: "export-pdf", 
        text: "Export as PDF", 
        title: "Student Information", 
        customize: function(doc) {
            // Base64 encoded image (replace this with your actual base64 string)
            var base64Image = "data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAAAUA..."  // Example, replace with actual base64 string

            // Custom CSS for PDF using $(win.document.body)
            $(doc).find('body').css('font-size', '10pt').css('background-color', '#f9f9f9');
            
            // Center the table
            $(doc).find('table').css('width', '90%').css('margin', '0 auto').addClass('table-bordered');
            
            // Add title above the table
            $(doc).find('body').prepend(
                '<div style="text-align:center; margin-bottom:20px;">' +
                '<h3 style="margin: 10px 0;">No-Exam Students Report</h3>' +
                '</div>'
            );

            // Add header logo using the base64 image
            $(doc).find('body').prepend(
                '<div style="text-align:center; margin-bottom:20px;">' +
                '<img src="' + base64Image + '" alt="Header Logo" style="width: 90%; height: auto; display: block; margin: 0 auto;">' +
                '</div>'
            );

            // Add footer with current date
            $(doc).find('body').append(
                '<footer style="position:fixed; bottom:0; width:100%; text-align:center; font-size: 8pt; padding: 5px 0; background: #ffffff; border-top: 1px solid #ddd;">' +
                'Generated on: ' + new Date().toLocaleString() + 
                '</footer>'
            );

            // Add custom styles for table and text
            $(doc).find('head').append(
                '<style>' +
                '@media print {' +
                '  body { font-family: Arial, sans-serif; color: #333; margin: 20px; }' +
                '  h3 { font-size: 18px; margin-bottom: 10px; text-align: center; color: #333; }' +
                '  table { width: 90%; border-collapse: collapse; font-size: 12px; margin: 0 auto; }' +
                '  th { padding: 6px 12px; text-align: left; border-bottom: 1px solid #ddd; background-color: #f4f4f4; font-weight: bold; }' +
                '  td { padding: 6px 12px; text-align: left; border-bottom: 1px solid #eee; }' +
                '}' +
                '</style>'
            );

            // Set the table layout for borders and alignment
            doc.content[1].layout = {
                hLineWidth: function(i, node) { return 1; },
                vLineWidth: function(i, node) { return 1; },
                hLineColor: function(i, node) { return '#ddd'; },
                vLineColor: function(i, node) { return '#ddd'; },
                paddingLeft: function(i) { return 10; },
                paddingRight: function(i) { return 10; },
                paddingTop: function(i) { return 5; },
                paddingBottom: function(i) { return 5; }
            };
        }
    },
    {
        extend: "print",
        className: "export-print",
        text: "Print Custom Layout",
        title: "",
        customize: function (win) {
            // Set base styling for a more compact and full-width print view
            $(win.document.body).css('font-size', '10pt').css('background-color', '#ffffff');
            $(win.document.body).find('table').addClass('table-bordered table').css('width', '100%');

            
            // Adding a small header with image and title
            $(win.document.body).prepend(
                '<div style="text-align:center; margin-bottom:10px;">' +
                '<img src="img/header-logo.png" alt="Header Logo" style="width: 1000px; height: auto; display: block; margin: 0 auto;">' + '<hr>' + '<hr>'+
                '<h3 style="margin: 5px 0; font-size: 14pt;">No Exam Lists</h3>' +
                '</div>'
            );
    
            // Adding footer with the generation date
            $(win.document.body).append(
                '<footer style="position:fixed; bottom:0; width:100%; text-align:center; font-size: 8pt; padding: 5px 0; background: #ffffff; border-top: 1px solid #ddd;">' +
                'Generated on: ' + new Date().toLocaleString() + // Use .toLocaleString() to include both date and time
                '</footer>'
            );
            
    
            // CSS for full-width printing
            $(win.document.head).append(
                '<style>' +
                '@media print {' +
                '  body { font-family: Arial, sans-serif; color: #333; margin: 10px; }' +
                '  h3 { font-size: 12pt; margin-bottom: 10px; text-align: center; color: #333; }' +
                '  table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 30px; }' +
                '  th, td { padding: 6px 10px; text-align: center; border: 1px solid #ddd; }' +
                '  th[colspan], td[colspan] { text-align: center; font-weight: bold; }' +
                '  thead { display: table-header-group; }' +  // Repeat headers on each page
                '  th { background-color: #f2f2f2; font-weight: bold; font-size: 9pt; }' +
                '  .table-bordered thead th { border-bottom-width: 2px; }' +  // Double border for header rows
                '  table { page-break-inside: auto; }' +  // Prevent page breaks within the table
                '  tr { page-break-inside: avoid; page-break-after: auto; }' +
                '  footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8pt; background: #fff; padding: 5px; }' +
                '}' +
                '</style>'
            );
    
            // Ensure the table fills the printable width
            $(win.document.body).find('table').css('width', '100%');
    
            // Hide the last column if needed
            $(win.document.body).find('table tr').each(function () {
                $(this).find('td:last, th:last').hide();  // Adjust if you want to hide the last column (Options column)
            });
        }
    }
]

    });
    

    $("#export-pdf").click(function() {
        table.button('.export-pdf').trigger();
    });
    
    $("#export-csv").click(function() {
        table.button('.export-csv').trigger();
    });
    
    $("#export-excel").click(function() {
        table.button('.export-excel').trigger();
    });

    $("#export-print").click(function() {
        table.button('.export-print').trigger();
    });

    // Filter by Course
    $('#course-filter').on('keyup change', function() {
        var filterValue = $(this).val();
        
        // Use regex for exact match
        table.column(1).search(filterValue).draw()
    });

    $('#gender-filter').on('change', function() {
        var filterValue = $(this).val();
        
     
        if (filterValue === "") {
            table.column(3).search("").draw();
        } else {
            table.column(3).search("^" + filterValue + "$", true, false).draw();
        }
    });
 
    


});


$(function () {
    
    getStudents();

    $("#export").on('click',function (e) {
        e.preventDefault();
        $("#export-modal").modal('show');
    });

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

        $.ajax({
            processData: false,
            contentType: false,
            data: fd,
            url: url,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            toastr.success(data.message, 'Success');
            getUsers();
            $('#users-modal').modal('hide');
        }).fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    // Handle delete user operation
    $('#students-table').on('click', '.delete', function (e) {
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
                    url: 'Students/delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function (data, textStatus, jqXHR) {
                    toastr.success(data.result, 'Deleted');
                    getStudents();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, 'Error');
                });
            }
        });
    });

    // Function to get and display users
    function getStudents() {
        $("#students-table").dataTable({
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'Students/getStudents'
            },
            "columns": [
                { data: "id_number" },
                { data: "course" },
                { data: "name" },
                { data: "batch.name"},
                { data: "batch.description"},
                { data: "ex_year" },
                {
                    data: null, render: function (data) {
                        var option = '<div style="text-align:center;"> <a href="" class="delete text-danger" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Delete User" data-id="' + data.id + '"><i' +
                            ' class="fa fa-trash"></i></a></div>';
                        return option;
                    }
                }
            ]
        });
    }



    
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















