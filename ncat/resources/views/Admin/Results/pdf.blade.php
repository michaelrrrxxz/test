<!DOCTYPE html>
<html>
<head>
    <title>Results Table</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .text-center { text-align: center; }
        .align-middle { vertical-align: middle; }
        .text-capitalize { text-transform: capitalize; }
        .table { width: 100%; }
    </style>
</head>
<body>
    <h1>Results</h1>
    <table class="table table-bordered table-responsive table-hover table-striped text-center text-capitalize">
        <thead class="bg-light">
            <!-- Copy the entire table structure from the HTML provided -->
            <!-- Including the `<thead>` and `<tbody>` contents. -->
        </thead>
        <tbody>
            @foreach ($results as $result)
            <tr>
                <td>{{ $result->id }}</td>
                <td>{{ $result->name }}</td>
                <td>{{ $result->course }}</td>
                <td>{{ $result->raw_score }}</td>
                <td>{{ $result->sai }}</td>
                <td>{{ $result->pba_percentile_rank }}</td>
                <td>{{ $result->pba_stanine }}</td>
                <td>{{ $result->pbg_percentile_rank }}</td>
                <td>{{ $result->pbg_stanine }}</td>
                <td>{{ $result->verbal_comprehension_score }}</td>
                <td>{{ $result->verbal_comprehension_category }}</td>
                <td>{{ $result->verbal_reasoning_score }}</td>
                <td>{{ $result->verbal_reasoning_category }}</td>
                <td>{{ $result->verbal_total }}</td>
                <td>{{ $result->qr_score }}</td>
                <td>{{ $result->qr_category }}</td>
                <td>{{ $result->fr_score }}</td>
                <td>{{ $result->fr_category }}</td>
                <td>{{ $result->non_verbal_total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
