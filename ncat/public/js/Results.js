$(document).ready(function () {
    getResults();
});

function getResults() {
    var table = $("#results-table").DataTable({
        "responsive": false,
        "paging" : false,
        "autoWidth": true,
        "destroy": true,
        "scrollX": true,
       
        
        "ajax": {
            "url": 'Results/getResults',
            "dataSrc": 'data'
        },
        "columns": [
            { data: "id_number" },
            {
                data: "name",
                createdCell: function (td, cellData, rowData, row, col) {
                    tippy(td, {
                        content: `<div class="tooltip-bg">
                            <div class="tooltip-content">
                                <strong>Test IP: </strong>${rowData.test_ip} <br>
                                <strong>ID Number: </strong>${rowData.id_number} <br>
                                <strong>Name:</strong> ${cellData} <br>
                                <strong>Course:</strong> ${rowData.course} <br>
                                <strong>Age:</strong> ${rowData.age_year} <br>
                                <strong>Address:</strong> ${rowData.address} <br>
                                <strong>Batch:</strong> ${rowData.batch} <br>
                                <strong>Group:</strong> ${rowData.group_abc} 
                            </div>
                          </div>`,
                        theme: 'dark',
                        placement: 'bottom',
                        arrow: true,
                        animation: 'fade',
                        duration: 300,
                        allowHTML: true
                    });
                }
            },
            { data: "course" },
            { data: "raw_score_t", render: data => `<strong>${data}</strong>` },
            { data: "sai_t" },
            { data: "percentile_ranks_pba" },
            { data: "stanine_pba" },
            { data: "percentile_ranks_pbg" },
            { data: "stanine_pbg" },
            { data: "verbalComprehension_score" },
            { data: "rsc2pc_vc",
                createdCell: function (td, cellData, rowData, row, col) {
                    var tooltip = getTooltip(cellData);
                    $(td).attr('title', tooltip);
                  }
             },
            { data: "verbalReasoning_score" },
            { data: "rsc2pc_vr",
                createdCell: function (td, cellData, rowData, row, col) {
                    var tooltip = getTooltip(cellData);
                    $(td).attr('title', tooltip);
                  }
             },
            { data: "verbal_score", render: data => `<strong>${data}</strong>` },
            { data: "quantitativeReasoning_score" },
            { data: "rsc2pc_qr",
                createdCell: function (td, cellData, rowData, row, col) {
                    var tooltip = getTooltip(cellData);
                    $(td).attr('title', tooltip);
                  }
             },
            { data: "figuralReasoning_score" },
            { data: "rsc2pc_fr",
                createdCell: function (td, cellData, rowData, row, col) {
                    var tooltip = getTooltip(cellData);
                    $(td).attr('title', tooltip);
                  }
             },
            { data: "non_verbal_score", render: data => `<strong>${data}</strong>` },
            {
                data: null, render: function (data, type, row) {
                    return `<div style="text-align:center;">
                        <a href="javascript:void(0);" class="print" data-id="${row.id_number}">
                            <i class="fa fas fa-print text-primary"></i>
                        </a> 
                    </div>`;
                }
            }
        ],
        "buttons": [
            { extend: "csv", className: "export-csv" }, 
            { extend: "excel", className: "export-excel" }, 
            { extend: "pdf", className: "export-pdf" }, 
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

    // Function to get the tooltip based on cell data
function getTooltip(cellData) {
    let tooltip = '';
    
    if (cellData === 'A') {
      tooltip = 'Average';
    } else if (cellData === 'BA') {
      tooltip = 'Below Average';
    } else if (cellData === 'AA') {
      tooltip = 'Above Average';
    }
    
    return tooltip;
  }
  

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


    $('#course-filter').on('keyup change', function() {
        // Get the value of the input field
        var filterValue = $(this).val();
        // Use the search method to filter the DataTable
        table.column(2).search(filterValue).draw(); // Index 1 corresponds to the 'course' column
    });

    $('#print').on('click', function (e) {
        e.preventDefault();
        $('.modal-title').html('Print');
        $('#export-modal').modal('show');
    });

    // Event listener for print icon click
    $('#results-table').on('click', '.print', function (e) {
        e.preventDefault();
        var currentDate = new Date();
        var formattedDate = currentDate.toLocaleDateString();
        var formattedTime = currentDate.toLocaleTimeString();

        // Get the data for the row
        var data = table.row($(this).parents('tr')).data();
        
        // Format data into printable content
        var printContent = `
        <div>
            <img src="{{asset('img/1.jpeg')}}" alt="Northeastern College Logo">
            <h3>Student Information</h3>
            <h4> Name: <strong> ${data.name} </strong> </h4>
            <!-- General Scores Table -->
            <table border="1" cellspacing="0" cellpadding="4" style="width:100%; margin-top: 20px; border-collapse: collapse;">
                <thead style="background-color: #f0f0f0;">
                    <tr>
                        <th>Raw Score</th>
                        <th>SAI</th>
                        <th>Percentile Rank (Age)</th>
                        <th>Stanine (Age)</th>
                        <th>Percentile Rank (Grade)</th>
                        <th>Stanine (Grade)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${data.raw_score_t}</td>
                        <td>${data.sai_t}</td>
                        <td>${data.percentile_ranks_pba}</td>
                        <td>${data.stanine_pba}</td>
                        <td>${data.percentile_ranks_pbg}</td>
                        <td>${data.stanine_pbg}</td>
                    </tr>
                </tbody>
            </table>
    
            <!-- Verbal Scores Table -->
            <h4 style="margin-top: 20px;">Verbal Scores</h4>
            <table border="1" cellspacing="0" cellpadding="4" style="width:100%; margin-top: 10px; border-collapse: collapse;">
                <thead style="background-color: #f0f0f0;">
                    <tr>
                        <th>Verbal Comprehension Score</th>
                        <th>Performance Category</th>
                        <th>Verbal Reasoning Score</th>
                        <th>Performance Category</th>
                        <th>Total Verbal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${data.verbalComprehension_score}</td>
                        <td>${data.rsc2pc_vc}</td>
                        <td>${data.verbalReasoning_score}</td>
                        <td>${data.rsc2pc_vr}</td>
                        <td>${data.verbal_score}</td>
                    </tr>
                </tbody>
            </table>
    
            <!-- Non-Verbal Scores Table -->
            <h4 style="margin-top: 20px;">Non-Verbal Scores</h4>
            <table border="1" cellspacing="0" cellpadding="4" style="width:100%; margin-top: 10px; border-collapse: collapse;">
                <thead style="background-color: #f0f0f0;">
                    <tr>
                        <th>QR Score</th>
                        <th>Performance Category</th>
                        <th>FR Score</th>
                        <th>Performance Category</th>
                        <th>Total Non-Verbal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${data.quantitativeReasoning_score}</td>
                        <td>${data.rsc2pc_qr}</td>
                        <td>${data.figuralReasoning_score}</td>
                        <td>${data.rsc2pc_fr}</td>
                        <td>${data.non_verbal_score}</td>
                    </tr>
                </tbody>
            </table>
               <p><strong>Date Generated:</strong> ${formattedDate} ${formattedTime}</p>
        </div>
    `;
    


        // Open a new window and print the content
        var printWindow = window.open('', '', 'height=600,width=900');
        printWindow.document.write('<html><head><title>Print Row</title></head><body>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
}


function formatGender(gender) {
    switch (gender) {
        case 'M':
            return '<i class="fas fa-mars"></i> Male'; // Font Awesome Mars icon
        case 'F':
            return '<i class="fas fa-venus"></i> Female'; // Font Awesome Venus icon
        default:
            return '<i class="fas fa-genderless"></i> Unknown'; // Genderless icon
    }
}



