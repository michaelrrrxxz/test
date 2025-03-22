@extends('layouts.exam')

@section('title', 'Exam Results')

@section('content_header')
    <h1 class="text-center">Exam Results</h1>
@endsection

@section('content')
<style>
    .centered-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .result-card {
        width: 100%;
        max-width: 1200px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
    }

    #results-table th {
        vertical-align: middle !important;
        text-align: center;
        background-color: #f8f9fa;
        font-weight: bold;
    }

    #results-table td, #results-table th {
        padding: 8px;
    }
</style>

<div class="container centered-container">
    <div class="result-card">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title text-center">Your Results</h3>
                @foreach ($formattedResults as $result)
                    <h3 class="box-title text-center">
                        {{ $result['raw_score_t'] }} 
                        <span style="font-weight: normal; font-size: 16px;">/ 72</span>
                    </h3>
                @endforeach
            </div>
            
            <div class="box-body">
                <!-- Chart at the top -->
                <div id="chart" class="my-4"></div>

                <!-- Score Summary Table -->
                <table id="results-table" class="table table-bordered table-hover table-striped text-center">
                    <thead>
                        <tr>
                            <th rowspan="3" class="align-middle">ID No.</th>
                            <th rowspan="3" style="width: 200px;" class="align-middle">Name</th>
                            <th rowspan="3" class="align-middle">Course</th>
                            <th rowspan="3" class="align-middle" title="Raw Score">Raw Score</th>
                            <th rowspan="3" class="align-middle" title="Student Ability Index">SAI</th>
                            <th colspan="2" title="Performance By Age">PBA</th>
                            <th colspan="2" title="Performance By Grade">PBG</th>
                            <th colspan="5">Verbal</th>
                            <th colspan="5">Non-Verbal</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="align-middle" title="Percentile Rank">PR</th>
                            <th rowspan="2" class="align-middle" title="Stanine">S</th>
                            <th rowspan="2" class="align-middle" title="Percentile Rank">PR</th>
                            <th rowspan="2" class="align-middle" title="Stanine">S</th>
                            <th colspan="2" title="Verbal Comprehension">VC</th>
                            <th colspan="2" title="Verbal Reasoning">VR</th>
                            <th rowspan="2" class="align-middle">Total</th>
                            <th colspan="2" title="Quantitative Reasoning">QR</th>
                            <th colspan="2" title="Figural Reasoning">FR</th>
                            <th rowspan="2" class="align-middle">Total</th>
                        </tr>
                        <tr>
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        @foreach ($formattedResults as $result)
                            <tr>
                                <td>{{ $result['id_number'] }}</td>
                                <td>{{ $result['name'] }}</td>
                                <td>{{ $result['course'] }}</td>
                                <td>{{ $result['raw_score_t'] }}</td>
                                <td>{{ $result['sai_t'] }}</td>
                                <td>{{ $result['percentile_ranks_pba'] }}</td>
                                <td>{{ $result['stanine_pba'] }}</td>
                                <td>{{ $result['percentile_ranks_pbg'] }}</td>
                                <td>{{ $result['stanine_pbg'] }}</td>
                                <td>{{ $result['verbalComprehension_score'] }}</td>
                                <td>{{ $result['rsc2pc_vc'] }}</td>
                                <td>{{ $result['verbalReasoning_score'] }}</td>
                                <td>{{ $result['rsc2pc_vr'] }}</td>
                                <td>{{ $result['verbal_score'] }}</td>
                                <td>{{ $result['quantitativeReasoning_score'] }}</td>
                                <td>{{ $result['rsc2pc_qr'] }}</td>
                                <td>{{ $result['figuralReasoning_score'] }}</td>
                                <td>{{ $result['rsc2pc_fr'] }}</td>
                                <td>{{ $result['non_verbal_score'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-3">
            <a href="{{ route('clear.session') }}" class="btn btn-primary">Done</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{asset('js/no-right-click.js')}}"></script>
<script src="{{asset('js/no-reload.js')}}"></script>


@php
    // Prepare the series data in PHP with scores and descriptions
    $seriesData = [];
    foreach ($formattedResults as $formattedResult) {
        $seriesData[] = [
            'type' => $formattedResult['rsc2pc_vc'], // E.g., "Above Average"
            'score' => $formattedResult['verbalComprehension_score'],
            'category' => 'Verbal Comprehension'
        ];
        $seriesData[] = [
            'type' => $formattedResult['rsc2pc_vr'], // E.g., "Above Average"
            'score' => $formattedResult['verbalReasoning_score'],
            'category' => 'Verbal Reasoning'
        ];
        $seriesData[] = [
            'type' => $formattedResult['rsc2pc_qr'], // E.g., "Above Average"
            'score' => $formattedResult['quantitativeReasoning_score'],
            'category' => 'Quantitative Reasoning'
        ];
        $seriesData[] = [
            'type' => $formattedResult['rsc2pc_fr'], // E.g., "Above Average"
            'score' => $formattedResult['figuralReasoning_score'],
            'category' => 'Figural Reasoning'
        ];
    }
@endphp

<script src="{{ asset('plugin/apex/apexcharts.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Parse the PHP seriesData into JavaScript
    const seriesData = @json($seriesData);

    // Extract data for chart
    const categories = [];
    const scores = [];
    const descriptions = [];
    const colors = []; // Array to hold colors for each bar

    // Define a color map based on the 'type'
    const typeColorMap = {
        "Above Average": "#048e2b", // Green
        "Average": "#ffcc00", // Yellow
        "Below Average": "#d9534f", // Red
        "Exceptional": "#5bc0de", // Blue
        "Low": "#6c757d" // Gray
    };

    seriesData.forEach(data => {
        if (!categories.includes(data.category)) {
            categories.push(data.category);
        }
        scores.push(data.score);
        descriptions.push(data.type);

        // Assign color based on 'type'
        colors.push(typeColorMap[data.type] || "#000000"); // Default to black if no match
    });

    // Chart options
    const options = {
        chart: {
            type: 'bar',
            height: 400
        },
        series: [{
            name: 'Score',
            data: scores, // Use scores from PHP-prepared data
        }],
        xaxis: {
            categories: categories // Use categories from PHP-prepared data
        },
        title: {
            text: 'Score Distribution',
            align: 'center'
        },
        tooltip: {
            y: {
                formatter: function(value, opts) {
                    const index = opts.dataPointIndex;
                    return `${value} (${descriptions[index]})`; // Show description with score
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false // Change to true for horizontal bars
            }
        },
        colors: colors // Dynamically set colors for each bar
    };

    const chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
});





</script>

@endsection
