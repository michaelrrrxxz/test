function loadGenderChart(examYear) {
    $.getJSON('countEnrolledStudentsbyGender', { exam_year: examYear }, function(data) {
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
                                    let printWindow = window.open('', '_blank');
                                    printWindow.document.write(`
                                        <html>
                                            <head>
                                                <title>Print Chart</title>
                                            </head>
                                            <body>
                                                <img src="${imgURI}" />
                                            </body>
                                        </html>
                                    `);
                                    printWindow.document.close();
                                    printWindow.print();
                                });
                            }
                        }]
                    }
                }
            },
            series: [maleCount, femaleCount],
            labels: ['Male Students', 'Female Students'],
            title: {
                text: `Enrollment by Gender for ${examYear || 'All Years'}`,
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

// Load chart for "All" years initially
loadGenderChart('');

// Update chart when year selection changes
$('#examYear').change(function() {
    $('#genderBarChart').empty(); // Clear previous chart
    loadGenderChart($(this).val());
});


$.getJSON('exam-year-data', function(data) {
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
                                let printWindow = window.open('', '_blank');
                                printWindow.document.write(`
                                    <html>
                                        <head>
                                            <title>Print Chart</title>
                                        </head>
                                        <body>
                                            <img src="${imgURI}" />
                                        </body>
                                    </html>
                                `);
                                printWindow.document.close();
                                printWindow.print();
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

$(document).ready(function() {
    // Cache DOM elements
    const examYearSelect = $('#exam-Year');
    const printButton = $('#printButton');
    let chart; // Store chart instance for re-rendering

    // Function to fetch data from the backend
    function fetchExamParticipationData(examYear) {
        $.ajax({
            url: 'exam-participation',
            method: 'GET',
            data: { exam_year: examYear },
            success: function(data) {
                renderChart(data); // Pass the received data to the chart rendering function
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    // Function to render the chart
    function renderChart(data) {
        const chartOptions = {
            chart: {
                type: 'bar',
                height: 350,
                stacked: true // Enable stacking of bars
            },
            series: [
                {
                    name: 'Without Exam',
                    data: [data.without_exam]
                },
                {
                    name: 'With Exam',
                    data: [data.with_exam]
                }
            ],
            xaxis: {
                categories: ['Exam Participation']
            },
            yaxis: {
                title: {
                    text: 'Total Students'
                }
            },
            title: {
                text: 'Exam Participation Statistics',
                align: 'center'
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + " students";
                    }
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            },
            annotations: {
                yaxis: [
                    {
                        y: data.total_enrolled,
                        borderColor: '#775DD0',
                        label: {
                            borderColor: '#775DD0',
                            style: {
                                color: '#fff',
                                background: '#775DD0'
                            },
                            text: `Total Enrolled: ${data.total_enrolled}`
                        }
                    }
                ]
            }
        };

        // Destroy the previous chart if it exists and create a new one
        if (chart) {
            chart.destroy();
        }
        chart = new ApexCharts($('#examParticipationChart')[0], chartOptions);
        chart.render();
    }

    // Event listener for when the year is changed in the dropdown
    examYearSelect.change(function() {
        const selectedYear = examYearSelect.val();
        fetchExamParticipationData(selectedYear);  // Fetch data with the selected year
    });

    // Event listener for the print button
    printButton.click(function() {
        window.print(); // Trigger the browser's print functionality
    });

    // Initial data load (without any selected year)
    fetchExamParticipationData('');
});


let chartInstance = null; // Global variable to hold the chart instance

function updateChart() {
    // Get the selected department and year from the dropdowns
    const department = document.getElementById("department-select").value;
    const year = document.getElementById("examyear").value;

    // Fetch the data with the selected filters as query parameters
    fetch(`getCategory?department=${department}&year=${year}`)
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
