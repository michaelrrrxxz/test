$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    getBatch();

    $('#add').on('click', function (e) {
        e.preventDefault();
    
        // Make an AJAX request to check the total number of questions
        $.ajax({
            url: 'Batch/checkActiveBatch', // URL where you check the total questions (create a route in Laravel)
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // Check if total questions exceed 72
                if (data.data >= true) {
                    // If the total number of questions is 72 or more, show an error notification
                    toastr.error('Cannot add a new batch. An active batch already exists.', 'Error');
                } else {
                 
                }
            },
            error: function () {
                   // If there are less than 72 questions, show the modal to add a new question
                   e.preventDefault();
                   $('.modal-title').html('Add Batch');
                   $('#batch-form')[0].reset();
                   $('#status').attr('hidden', 'hidden');
                   $('#statusLabel').attr('hidden', 'hidden');
                   $('#id').val(''); 
                   $('#batch-modal').modal('show');
              
            }
        });
    });

    // Show the 'Add User' modal
    $('#add').on('click', function (e) {
      
       
    });

    // Show the 'Edit User' modal and populate it with existing data
    $('#batch-table').on('click', '.edit', function (e) {
        e.preventDefault();
        $('.modal-title').html('Update Batch');
        var id = $(this).data('id');

        $.ajax({
            url: 'Batch/edit/' + id,
            type: "GET",
            dataType: 'json'
        })
        .done(function (data, textStatus, jqXHR) {
            if (data != '') {
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#status').val(data.status);
                $('#duration').val(data.duration);
                $('#status').removeAttr('hidden');
                $('#statusLabel').removeAttr('hidden');
                $('#id').val(data.id);
                $('#batch-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error'); 
        });
    });


  

    $('#batch-table').on('click', '.view', function (e) {
        e.preventDefault();
        var batchId = $(this).data('id');
    
        $.ajax({
            url: 'Batch/getStudentsByBatch/' + batchId,
            dataType: 'json',
            success: function (response) {
                console.log("AJAX response:", response); // Log the entire response for inspection
    
                if (response.data && response.data.length > 0) {
                    $('.modal-title').html('View Batch');
                    $('.card-title').html(response.batch_name);
                    $('#desbatch').html('Batch description: ' + response.batch_name);
    
                    if ($.fn.DataTable.isDataTable('#students-table')) {
                        $('#students-table').DataTable().clear().destroy();
                    }
    
                    $('#students-table').DataTable({
                        "ajax": {
                            "url": 'Batch/getStudentsByBatch/' + batchId,
                            "dataSrc": 'data'
                        },
                        "columns": [
                            {data: "id_number"},
                            {data: "name"},
                            {data: "course"},
                            {data: "raw_score_t"},
                            {data: "sai_t"},
                            {data: "percentile_ranks_pba"},
                            {data: "stanine_pba"},
                            {data: "percentile_ranks_pbg"},
                            {data: "stanine_pbg"},
                            {data: "verbalComprehension_score"},
                            {data: "rsc2pc_vc"},
                            {data: "verbalReasoning_score"},
                            {data: "rsc2pc_vr"},
                            {data: "verbal_score"},
                            {data: "quantitativeReasoning_score"},
                            {data: "rsc2pc_qr"},
                            {data: "figuralReasoning_score"},
                            {data: "rsc2pc_fr"},
                            {data: "non_verbal_score"}
                        ],
                        destroy: true,
                        responsive: true,
                        autoWidth: false,
                        buttons: [
                            {
                                extend: 'print',
                                text: 'Print',
                                title: 'Student Results',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            }
                        ],
                        dom: 'Bfrtip'
                    });
    
                    $('#getStudentbyBatch-modal').modal('show');
                } else {
                    toastr.error('No students found in this batch.', 'Error!');
                }
            },
            error: function (xhr, status, error) {
                toastr.error('An error occurred. Please try again.', 'Error!');
                console.log("AJAX Error:", status, error); // Log any AJAX errors
            }
        });
    });
    
    


    // Generate QR code for user
    $('#batch-table').on('click', '.qr', function (e) {
        e.preventDefault();
        $('.modal-title').html('Scan');
        var id = $(this).data('id');

        $.ajax({
            url: 'Batch/edit/' + id,
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

                $('#batch-modal').modal('show');
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error(errorThrown, 'Error');
        });
    });

    // Clear QR code when modal is hidden
    $('#batch-modal').on('hidden.bs.modal', function () {
        $('#qrcode').empty();
    });


    $('#batch-form').on('submit', function (e) {
        e.preventDefault();
    
        var fd = new FormData(this);
        fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
        var id = $('#id').val();
        var url;
    
        if (id != '') {
            url = 'Batch/update/' + id;
        } else {
            url = 'Batch/add';
        }
    
        $.ajax({
            processData: false,
            contentType: false,
            data: fd,
            url: url,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            // Check if the result is success or error
            if (data.result === 'success') {
                toastr.success(data.message, 'Success');  // Display success message
                getBatch();
                $('#batch-modal').modal('hide');
            } else if (data.result === 'error') {
                toastr.error(data.message, 'Error');  // Display error message if result is error
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            // Check if there's a response from the server
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                // Display the server's error message if available
                toastr.error(jqXHR.responseJSON.message, 'Error');
            } else {
                // Fallback to the general error if no message is provided
                toastr.error('Something went wrong. Please try again!', 'Error');
            }
        });
        
        
    });
    
    $('#batch-table').on('click', '.assign', function (e) {
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
                    url: 'assign-all-to-batch/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function (data, textStatus, jqXHR) {
                    toastr.success(data.result, 'Deleted');
                    getBatch();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, 'Error');
                });
            }
        });
    });



    


    // Handle delete user operation
    $('#batch-table').on('click', '.delete', function (e) {
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
                    url: 'Batch/delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function (data, textStatus, jqXHR) {
                    toastr.success(data.result, 'Deleted');
                    getBatch();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    toastr.error(errorThrown, 'Error');
                });
            }
        });
    });

    // Attach click event to dynamically generated .print buttons
$('#batch-table').on('click', '.print', function (e) {
    e.preventDefault();
    var batchId = $(this).data('id'); // Get the batch ID from the data-id attribute

    // AJAX request to fetch batch data by ID
    $.ajax({
        url: 'Batch/getStudentsByBatch/' + batchId,
        dataType: 'json',
        success: function (response) {
            if (response.data) {
                // Generate printable content from the response data
                var printContent = '<h3>Batch Information</h3>';
                printContent += '<p><strong>Batch Name:</strong> ' + response.batch_name + '</p>';
                printContent += '<p><strong>Batch ID:</strong> ' + response.description + '</p>';
                printContent += '<table border="1" cellspacing="0" cellpadding="5" width="100%">';
            
                // Correct the header row
                printContent += '<tr>';
                printContent += '<th>ID No.</th>';
                printContent += '<th>Name</th>';
                printContent += '<th>Course</th>';
                printContent += '<th>Age</th>';
                printContent += '<th>Address</th>';
                printContent += '<th>RS</th>';
                printContent += '<th>SAI</th>';
                printContent += '<th>PR PBA</th>';
                printContent += '<th>S PBA</th>';
                printContent += '<th>PR PBG</th>';
                printContent += '<th>S PBG</th>';
                printContent += '<th>VC Score</th>';
                printContent += '<th>PC VC</th>';
                printContent += '<th>VC Score</th>';
                printContent += '<th>PC VR</th>';
                printContent += '<th>VS</th>';
                printContent += '<th>QR Score</th>';
                printContent += '<th>PC QR</th>';
                printContent += '<th>FR Score</th>';
                printContent += '<th>PC FR</th>';
                printContent += '<th>NV Score</th>';
                printContent += '</tr>';
            
                // Loop through the data to add each student row
                response.data.forEach(function(student) {
                    printContent += '<tr>';
                    printContent += '<td>' + student.id_number + '</td>';
                    printContent += '<td>' + student.name + '</td>';
                    printContent += '<td>' + student.course + '</td>';
                    printContent += '<td>' + student.age_year + '</td>';
                    printContent += '<td>' + student.address + '</td>';
                    printContent += '<td><strong>' + student.raw_score_t + '</strong></td>';
                    printContent += '<td>' + student.sai_t + '</td>';
                    printContent += '<td>' + student.percentile_ranks_pba + '</td>';
                    printContent += '<td>' + student.stanine_pba + '</td>';
                    printContent += '<td>' + student.percentile_ranks_pbg + '</td>';
                    printContent += '<td>' + student.stanine_pbg + '</td>';
                    printContent += '<td>' + student.verbalComprehension_score + '</td>';
                    printContent += '<td>' + student.rsc2pc_vc + '</td>';
                    printContent += '<td>' + student.verbalReasoning_score + '</td>';
                    printContent += '<td>' + student.rsc2pc_vr + '</td>';
                    printContent += '<td><strong>' + student.verbal_score + '</strong></td>';
                    printContent += '<td>' + student.quantitativeReasoning_score + '</td>';
                    printContent += '<td>' + student.rsc2pc_qr + '</td>';
                    printContent += '<td>' + student.figuralReasoning_score + '</td>';
                    printContent += '<td>' + student.rsc2pc_fr + '</td>';
                    printContent += '<td><strong>' + student.non_verbal_score + '</strong></td>';
                    printContent += '</tr>';
                }); 
                printContent += '</table>';
                var imageUrl = "{{ asset('img/header-logo.png') }}";

                // Open a new window and print the content
                var printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print Batch: ' + response.batch_name + '</title></head><body>');
                printWindow.document.write('<img src="' + imageUrl + '" alt="Header Logo"></img>'); // Corrected img tag
                printWindow.document.write(printContent);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                
                // Wait for content to load before printing
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.close();
                };
            } else {
                toastr.error('No data found for this batch.', 'Error!');
            }
        },
        error: function (xhr, status, error) {
            toastr.error('An error occurred while fetching batch data.', 'Error!');
        }
    });
});


$('#printall').on('click', function (e) {
    e.preventDefault();

    // AJAX request to fetch batch data by ID
    $.ajax({
        url: 'Batch/printall',
        dataType: 'json',
        success: function (response) {
            if (response.data && response.data.length > 0) {
                let printContent = '';

                response.data.forEach(function(batch, index) {
                    // Add header logo for each batch
                    const imageUrl = "img/header-logo.png";
                    printContent += `
                        <div style="${index > 0 ? 'page-break-before: always;' : ''}">
                           <img src="${imageUrl}" alt="Header Logo" style="width: 60%; max-height: 150px; display: block; margin: 0 auto;">
                            <h3>Batch Information</h3>
                            <p><strong>Batch Name:</strong> ${batch.batch_name}</p>
                            <p><strong>Description:</strong> ${batch.description}</p>
                        
                            <table border="1" cellspacing="0" cellpadding="5" width="100%">
                                <tr>
                                    <th>ID No.</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Age</th>
                                    <th>Address</th>
                                    <th>RS</th>
                                    <th>SAI</th>
                                    <th>PR PBA</th>
                                    <th>S PBA</th>
                                    <th>PR PBG</th>
                                    <th>S PBG</th>
                                    <th>VC Score</th>
                                    <th>PC VC</th>
                                    <th>VR Score</th>
                                    <th>PC VR</th>
                                    <th>VS</th>
                                    <th>QR Score</th>
                                    <th>PC QR</th>
                                    <th>FR Score</th>
                                    <th>PC FR</th>
                                    <th>NV Score</th>
                                </tr>
                    `;

                    // Student rows
                    batch.results.forEach(function(student) {
                        printContent += `
                            <tr>
                                <td>${student.id_number || 'N/A'}</td>
                                <td>${student.name || 'N/A'}</td>
                                <td>${student.course || 'N/A'}</td>
                                <td>${student.age_year || 'N/A'}</td>
                                <td>${student.address || 'N/A'}</td>
                                <td><strong>${student.raw_score_t || 'N/A'}</strong></td>
                                <td>${student.sai_t || 'N/A'}</td>
                                <td>${student.percentile_ranks_pba || 'N/A'}</td>
                                <td>${student.stanine_pba || 'N/A'}</td>
                                <td>${student.percentile_ranks_pbg || 'N/A'}</td>
                                <td>${student.stanine_pbg || 'N/A'}</td>
                                <td>${student.verbalComprehension_score || 'N/A'}</td>
                                <td>${student.rsc2pc_vc || 'N/A'}</td>
                                <td>${student.verbalReasoning_score || 'N/A'}</td>
                                <td>${student.rsc2pc_vr || 'N/A'}</td>
                                <td><strong>${student.verbal_score || 'N/A'}</strong></td>
                                <td>${student.quantitativeReasoning_score || 'N/A'}</td>
                                <td>${student.rsc2pc_qr || 'N/A'}</td>
                                <td>${student.figuralReasoning_score || 'N/A'}</td>
                                <td>${student.rsc2pc_fr || 'N/A'}</td>
                                <td><strong>${student.non_verbal_score || 'N/A'}</strong></td>
                            </tr>
                        `;
                    });

                    printContent += '</table></div>';
                });

                const printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print Batch Details</title>');
                printWindow.document.write('<style>div { page-break-inside: avoid; }</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContent);
                printWindow.document.write('</body></html>');
                printWindow.document.close();

                // Wait for content to load before printing
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.close();
                };
            } else {
                toastr.error('No data found for this batch.', 'Error!');
            }
        },
        error: function (xhr, status, error) {
            toastr.error('An error occurred while fetching batch data: ' + error, 'Error!');
        }
    });
});



    // Function to get and display users
    function getBatch() {
        $("#batch-table").dataTable({
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'Batch/getBatch'
            },
            "columns": [
                { data: "name" },
                { data: "description" },
                {data: "access_key"},
                {
                    data: null, // This column doesn't directly fetch data from the source
                    defaultContent: "45 Minutes", // Static text for all rows in this column
                    title: "Duration" // Column header (optional)
                },
                {
                    data: "status",
                    name: "status",
                    render: function (data, type, row) {
                        if (data === 'locked') {
                          
                            return '<a href="" class="status" data-toggle="tooltip" ' +
                            'data-placement="bottom" title="Edit User" data-id="' + data.id + '" ><span class="text-danger"><i class="fas fa-lock"></i> Locked</span></a>';
                        } else if (data === 'active') {
                           
                            return '<span class="text-success"><i class="fas fa-check-circle"></i> Active</span>';
                        } else {
                            
                            return '<span class="text-secondary"><i class="fas fa-question-circle"></i> Unknown</span>';
                        }
                    }
                },
                {
                    data: "created_at",
                    render: function(data, type, row) {
                        // Format the date (assuming `data` is in ISO format)
                        var date = new Date(data);
                        var formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                        return formattedDate;
                    }
                },
                {
                    
                    data: null, render: function (data) {
                      
                        var option = '<a href="" class="print" data-toggle="tooltip" ' +
                        'data-placement="bottom" title="Print Batch" data-id="' + data.id + '">' +
                        '<i class="fa fas fa-print text-secondary"></i></a> |'  +
                        '<a href="" class="edit" data-toggle="tooltip" ' +
                        'data-placement="bottom" title="Edit Batch" data-id="' + data.id + '">' +
                        '<i class="fa fas fa-pen text-success"></i></a> |'  +
                        '<a href="" class="view" data-toggle="tooltip" ' +
                        'data-placement="bottom" title="View Batch" data-id="' + data.id + '">' +
                        '<i class="fa fas fa-eye text-primary"></i></a>';
                    
                        return option;
                    }
                }
            ]
        });
    }
});



$(document).ready(function () {
    getResults();
});

function getResults() {
    $("#results-table").dataTable({
        "responsive":false,
        "autoWidth":false ,
        "destroy": true,
        "scrollX": true,   // Enable horizontal scrolling
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'print',
                text: 'Print Results',
                title: '', // Keep title empty for a clean print view
                exportOptions: {
                    columns: ':visible' // Print only visible columns
                },
                customize: function (win) {
                    $(win.document.body)
                        .css('font-size', '10pt') // Set font size
                        .css('overflow-x', 'auto') // Enable horizontal scrolling if needed
                        .find('table')
                        .addClass('compact') // Add class for reduced padding
                        .css('width', '100%'); // Full width in print
                }
            }
        ],
        "ajax": {
            "url": 'Batch/getStudentsByBatch/' + batchId,
            "dataSrc": 'data'
        },
        "columns": [
            {data : "id_number"},
            { 
                data: "name",
                createdCell: function (td, cellData, rowData, row, col) {
                   
                    tippy(td, {
                        content: `<div class="tooltip-bg">
                        
                        <div class="tooltip-content">
                            <strong>ID Number: </strong>${rowData.id_number} <br>
                            <strong>Name:</strong> ${cellData} <br>
                            <strong>Course:</strong> ${rowData.course} <br>
                            <strong>Age:</strong> ${rowData.age_year} <br>
                            <strong>Address:</strong> ${rowData.address} <br>
                            <strong>Batch:</strong> ${rowData.batch} 
                            <strong>Group:</strong> ${rowData.group_abc} 
                        </div>
                      </div>`,
                        theme: 'light',  // Use a predefined or custom theme
                        placement: 'bottom', // Tooltip placement
                        arrow: true,      // Show arrow
                        animation: 'fade', // Animation for appearance
                        duration: 300,     // Animation duration
                        allowHTML: true    // Allow HTML content in tooltip
                    });
                }
            },
            
            { data: "course"},
            { data: "raw_score_t",
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>'; 
                }
            },
            { data: "sai_t"},
            { data:"percentile_ranks_pba"},
            { data: "stanine_pba"},
            { data:"percentile_ranks_pbg"},
            { data: "stanine_pbg"},
            { data: "verbalComprehension_score"},
            { data: "rsc2pc_vc"},
            { data: "verbalReasoning_score"},
           
            { data:"rsc2pc_vr"
            },
            { data: "verbal_score",
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>'; 
                }
            },
            { data: "quantitativeReasoning_score"},
            { data: "rsc2pc_qr"},
            {data :"figuralReasoning_score"},
            { data: "rsc2pc_fr"},
            { data: "non_verbal_score",
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>'; 
                }
             },
        ]
    });
}






