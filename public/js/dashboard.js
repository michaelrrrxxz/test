function loadGenderChart(examYear, department, course) {
    // Make a request to get the count based on selected examYear, department, and course
    $.getJSON('Dashboard/countEnrolledStudentsbyGender', { exam_year: examYear, department: department, course: course }, function(data) {
        const maleCount = data.male_students_count;
        const femaleCount = data.female_students_count;

        var options = {
            chart: {
                type: 'pie',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        customIcons: [{
                            icon: '<i class="fas fa-file-download text-grey"></i>',
                            title: 'Print',
                            class: 'custom-print',
                            index: -1,
                            click: function(chart, options, e) {
                                chart.dataURI().then(({ imgURI, svgURI }) => {
                                    // Get the current date and time
                                    const currentDate = new Date();
                                    const formattedDate = currentDate.toLocaleString();
                            
                                    // Open a new window
                                    let printWindow = window.open('', '_blank');
                                    if (!printWindow) {
                                        console.error('Unable to open print window. Check for popup blockers.');
                                        return;
                                    }
                            
                                    // Write HTML content for the print window
                                    printWindow.document.write(`
                                        <html>
                                            <head>
                                                <title>Print Chart</title>
                                                <style>
                                                    body {
                                                        margin: 0;
                                                        padding: 0;
                                                        text-align: center;
                                                    }
                                                    img {
                                                        max-width: 100%;
                                                        height: auto;
                                                    }
                                                    footer {
                                                        text-align: center;
                                                        margin-top: 20px;
                                                        font-size: 14px;
                                                        color: #555;
                                                    }
                                                </style>
                                            </head>
                                            <body>
                                                <!-- Header Image -->
                                                <img src="img/header-logo.png" alt="Header Logo" 
                                                     style="width: 1000px; height: auto; display: block; margin: 0 auto;">
                                                <!-- Chart Image -->
                                                <img src="${imgURI}" alt="Chart Image" />
                                                <!-- Generated At Footer -->
                                                <footer>
                                                    <p><em>Generated at: ${formattedDate}</em></p>
                                                </footer>
                                            </body>
                                        </html>
                                    `);
                            
                                    // Ensure content is loaded before printing
                                    printWindow.document.close();
                                    printWindow.onload = function() {
                                        printWindow.print();
                                        printWindow.close();
                                    };
                                }).catch((error) => {
                                    console.error('Error generating chart data URI:', error);
                                });
                            }
                            
                        }]
                    }
                }
            },
            series: [maleCount, femaleCount],
            labels: ['Male Students', 'Female Students'],
            title: {
                text: `Enrollment by Gender for ${examYear || 'All Years'} ${department ? 'in ' + department : ''} ${course ? ' - ' + course : ''}`,
                align: 'center'
            },
            legend: {
                position: 'bottom'
            }
        };

        var genderChart = new ApexCharts(document.querySelector("#genderBarChart"), options);
        genderChart.render();
    });
}

// Initial chart load for all years, departments, and courses
loadGenderChart('', '', '');

// Update chart when year, department, or course selection changes
$('#examYear, #department, #course').change(function() {
    $('#genderBarChart').empty(); // Clear previous chart
    loadGenderChart($('#examYear').val(), $('#department').val(), $('#course').val());
});


$.getJSON('Dashboard/getExamYearData', function(data) {
    const years = data.map(item => item.exam_year);
    const counts = data.map(item => item.count);

    var options = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    customIcons: [{
                        icon: '<i class="fas fa-file-download text-grey"></i>',
                        title: 'Print',
                        class: 'custom-print',
                        index: -1,
                        click: function(chart, options, e) {
                            chart.dataURI().then(({ imgURI, svgURI }) => {
                                // Check if imgURI is generated correctly
                                if (!imgURI) {
                                    console.error('Chart image URI not generated.');
                                    return;
                                }
                        
                                // Get the current date and time
                                const currentDate = new Date();
                                const formattedDate = currentDate.toLocaleString();
                        
                                // Open a new window and print the content
                                let printWindow = window.open('', '_blank');
                                if (!printWindow) {
                                    console.error('Unable to open print window. Check for popup blockers.');
                                    return;
                                }
                        
                                printWindow.document.write(`
                                    <html>
                                        <head>
                                            <title>Print Chart</title>
                                        </head>
                                        <body>
                                            <img src="img/header-logo.png" alt="Header Logo" style="width: 1000px; height: auto; display: block; margin: 0 auto;">
                                            <div style="text-align: center; margin-top: 20px;">
                                                <img src="${imgURI}" alt="Chart" style="max-width: 100%; height: auto; display: inline-block;" />
                                            </div>
                                            <footer style="text-align: center; margin-top: 20px;">
                                                <p><em>Generated at: ${formattedDate}</em></p>
                                            </footer>
                                        </body>
                                    </html>
                                `);
                        
                                // Wait for content to load and trigger print
                                printWindow.document.close();
                                printWindow.onload = function() {
                                    printWindow.print();
                                    printWindow.close();
                                };
                            }).catch((error) => {
                                console.error('Error generating chart data URI:', error);
                            });
                        }
                        
                    }]
                }
            }
        },
        series: [{
            name: 'Student Count',
            data: counts
        }],
        xaxis: {
            categories: years,
            title: {
                text: 'Exam Year'
            }
        },
        yaxis: {
            title: {
                text: 'Total Students'
            }
        },
        title: {
            text: 'Student Count by Exam Year',
            align: 'center'
        }
    };

    var chart = new ApexCharts(document.querySelector("#examYearChart"), options);
    chart.render();
});

// loadCourseChart('', '', '');

// // Update chart when year, department, or course selection changes
// $('#examYear, #department, #course').change(function() {
//     $('#genderBarChart').empty(); // Clear previous chart
//     loadCourseChart($('#examYear').val(), $('#department').val(), $('#course').val());
// });

$.getJSON('Dashboard/countStudentsbyCourse', function(data) {
    const years = data.map(item => item.course);
    const counts = data.map(item => item.count);

    var options = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    customIcons: [{
                        icon: '<i class="fas fa-file-download text-grey"></i>',
                        title: 'Print',
                        class: 'custom-print',
                        index: -1,
                        click: function(chart, options, e) {
                            chart.dataURI().then(({ imgURI, svgURI }) => {
                                // Get the current date and time
                                const currentDate = new Date();
                                const formattedDate = currentDate.toLocaleString();
    
                                // Open a new window
                                let printWindow = window.open('', '_blank');
                                if (!printWindow) {
                                    console.error('Unable to open print window. Check for popup blockers.');
                                    return;
                                }
    
                                // Write HTML content for the print window
                                printWindow.document.write(`
                                    <html>
                                        <head>
                                            <title>Print Chart</title>
                                            <style>
                                                body {
                                                    margin: 0;
                                                    padding: 0;
                                                    text-align: center;
                                                    font-family: Arial, sans-serif;
                                                }
                                                img {
                                                    max-width: 100%;
                                                    height: auto;
                                                }
                                                footer {
                                                    text-align: center;
                                                    margin-top: 20px;
                                                    font-size: 14px;
                                                    color: #555;
                                                }
                                            </style>
                                        </head>
                                        <body>
                                            <!-- Header Logo -->
                                            <img src="img/header-logo.png" alt="Header Logo" 
                                                 style="width: 1000px; height: auto; display: block; margin: 0 auto;">
                                            <!-- Chart Image -->
                                            <img src="${imgURI}" alt="Chart Image" />
                                            <!-- Generated At Footer -->
                                            <footer>
                                                <p><em>Generated at: ${formattedDate}</em></p>
                                            </footer>
                                        </body>
                                    </html>
                                `);
    
                                // Ensure content is loaded before printing
                                printWindow.document.close();
                                printWindow.onload = function() {
                                    printWindow.print();
                                    printWindow.close();
                                };
                            }).catch((error) => {
                                console.error('Error generating chart data URI:', error);
                            });
                        }
                    }]
                }
            }
        },
        series: [{
            name: 'Student Count',
            data: counts
        }],
        xaxis: {
            categories: years,
            title: {
                text: 'Course'
            }
        },
        yaxis: {
            title: {
                text: 'Total Students'
            }
        },
        title: {
            text: 'Student Count by Course',
            align: 'center'
        }
    };
    

    var chart = new ApexCharts(document.querySelector("#countstudentbycourse"), options);
    chart.render();
});

$(document).ready(function () {
    // Cache DOM elements
    const printButton = $('#printButton');
    let chart; // Store chart instance for re-rendering

    // Function to fetch data from the backend
    function fetchExamParticipationData() {
        $.ajax({
            url: 'Dashboard/getExamParticipationData', // Make the request without the year filter
            method: 'GET',
            success: function (data) {
                renderChart(data); // Pass the received data to the chart rendering function
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            },
        });
    }

    // Function to render the chart
    function renderChart(data) {
        const years = data.map(item => item.exam_year); // Get the list of years
        const withoutExamData = data.map(item => item.without_exam); // Get the 'without_exam' counts
        const withExamData = data.map(item => item.with_exam); // Get the 'with_exam' counts

        const chartOptions = {
            chart: {
                type: 'line', // Line chart
                height: 350,
            },
            series: [
                {
                    name: 'Without Exam',
                    data: withoutExamData,
                },
                {
                    name: 'With Exam',
                    data: withExamData,
                },
            ],
            xaxis: {
                categories: years, // Display the exam years on the x-axis
            },
            yaxis: {
                title: {
                    text: 'Total Students',
                },
            },
            title: {
                text: 'Exam Participation Statistics',
                align: 'center',
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' students';
                    },
                },
            },
            stroke: {
                width: 3, // Makes the line thicker
            },
            markers: {
                size: 4, // Sets the size of data point markers
                hover: {
                    size: 6, // Increases the size of markers on hover
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
            },
        };

        // Destroy the previous chart if it exists and create a new one
        if (chart) {
            chart.destroy();
        }
        chart = new ApexCharts($('#examParticipationChart')[0], chartOptions);
        chart.render();
    }

    // Event listener for the print button
    printButton.click(function () {
        // Get the current date and time
        const currentDate = new Date();
        const formattedDate = currentDate.toLocaleString();

        // Get the chart's data URI
        chart.dataURI().then(({ imgURI }) => {
            // Open a new window for printing
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                console.error('Unable to open print window. Check for popup blockers.');
                return;
            }

            // Write custom printable content
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Chart</title>
                        <style>
                            body {
                                margin: 0;
                                padding: 0;
                                text-align: center;
                                font-family: Arial, sans-serif;
                            }
                            header {
                                margin-bottom: 20px;
                                text-align: center;
                            }
                            img.logo {
                                max-width: 800px;
                                height: auto;
                                display: block;
                                margin: 0 auto;
                            }
                            img.chart {
                                margin-top: 20px;
                                max-width: 90%;
                                height: auto;
                                display: block;
                            }
                            footer {
                                margin-top: 30px;
                                font-size: 14px;
                                color: #555;
                                text-align: center;
                            }
                        </style>
                    </head>
                    <body>
                        <!-- Header Logo -->
                        <header>
                            <img src="img/header-logo.png" alt="Header Logo" class="logo">
                        </header>
                        <!-- Chart Image -->
                        <img src="${imgURI}" alt="Chart Image" class="chart">
                        <!-- Generated At Footer -->
                        <footer>
                            <p><em>Generated at: ${formattedDate}</em></p>
                        </footer>
                    </body>
                </html>
            `);

            // Ensure the content is loaded before printing
            printWindow.document.close();
            printWindow.onload = function () {
                printWindow.print();
                printWindow.close();
            };
        }).catch(error => {
            console.error('Error generating chart data URI:', error);
        });
    });

    // Initial data load (no need for user input, automatically fetch data)
    fetchExamParticipationData();
});





let chartInstance = null; // Global variable to hold the chart instance

function updateChart() {
    // Get the selected department and year from the dropdowns
    const department = document.getElementById("department-select").value;
    const year = document.getElementById("examyear").value;

    // Fetch the data with the selected filters as query parameters
    fetch(`Dashboard/getCategory?department=${department}&year=${year}`)
        .then(response => {
            if (!response.ok) {
                // Handle cases where no data is found (404)
                if (response.status === 404) {
                    return {
                        below_average: 0,
                        average: 0,
                        above_average: 0
                    };
                }
                throw new Error('Failed to fetch data');
            }
            return response.json();
        })
        .then(data => {
            // Prepare the data for the donut chart
            const categories = ['Below Average', 'Average', 'Above Average'];
            const series = [data.below_average, data.average, data.above_average];

            // Calculate total students
            const totalStudents = series.reduce((acc, value) => acc + value, 0);
            const belowAverage = data.below_average; // Store below average count

            const options = {
                chart: {
                    type: 'donut',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            customIcons: [{
                                icon: '<i class="fas fa-file-download text-grey"></i>',
                                title: 'Print',
                                class: 'custom-print',
                                index: -1,
                                click: function(chart, options, e) {
                                    chart.dataURI().then(({ imgURI, svgURI }) => {
                                        // Get the current date and time
                                        const currentDate = new Date();
                                        const formattedDate = currentDate.toLocaleString();
            
                                        // Open a new window
                                        let printWindow = window.open('', '_blank');
                                        if (!printWindow) {
                                            console.error('Unable to open print window. Check for popup blockers.');
                                            return;
                                        }
            
                                        // Write HTML content for the print window
                                        printWindow.document.write(`
                                            <html>
                                                <head>
                                                    <title>Print Chart</title>
                                                    <style>
                                                        body {
                                                            margin: 0;
                                                            padding: 0;
                                                            text-align: center;
                                                            font-family: Arial, sans-serif;
                                                        }
                                                        img {
                                                            max-width: 100%;
                                                            height: auto;
                                                        }
                                                        footer {
                                                            text-align: center;
                                                            margin-top: 20px;
                                                            font-size: 14px;
                                                            color: #555;
                                                        }
                                                    </style>
                                                </head>
                                                <body>
                                                    <!-- Header Logo -->
                                                    <img src="img/header-logo.png" alt="Header Logo" 
                                                         style="width: 1000px; height: auto; display: block; margin: 0 auto;">
                                                    <!-- Chart Image -->
                                                    <img src="${imgURI}" alt="Chart Image" />
                                                    <!-- Generated At Footer -->
                                                    <footer>
                                                        <p><em>Generated at: ${formattedDate}</em></p>
                                                    </footer>
                                                </body>
                                            </html>
                                        `);
            
                                        // Ensure content is loaded before printing
                                        printWindow.document.close();
                                        printWindow.onload = function() {
                                            printWindow.print();
                                            printWindow.close();
                                        };
                                    }).catch((error) => {
                                        console.error('Error generating chart data URI:', error);
                                    });
                                }
                            }]
                        }
                    }
                },
                series: series,
                labels: categories,
                title: {
                    text: 'Performance Category',
                    align: 'center'
                },
                legend: {
                    position: 'bottom',
                    formatter: (seriesName, opts) => {
                        const value = opts.w.globals.series[opts.seriesIndex];
                        return `${seriesName}: ${value}`;
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Students',
                                    formatter: () => `${totalStudents}`
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                }
            };

            // Update the chart if it already exists
            if (chartInstance) {
                chartInstance.updateOptions(options);
                chartInstance.updateSeries(series);
            } else {
                // Create a new chart instance if it doesn't exist
                chartInstance = new ApexCharts(document.querySelector("#categorychart"), options);
                chartInstance.render();
            }

            // Display the number of below-average students below the chart
            document.getElementById("below-average-count").textContent = `Total Below Average Students: ${belowAverage}`;
        })
        .catch(error => console.error('Error fetching data:', error));
}


// Call updateChart initially to display the chart with the default filter
updateChart();

