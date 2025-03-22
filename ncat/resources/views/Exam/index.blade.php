<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam</title>
    <!-- AdminLTE CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css" rel="stylesheet">
    <style>
        .bg-answer {
            background-color: #f8d7da !important; /* Red background for selected answer row */
        }
    </style>
    <script>
        function gavs(rowId) {
            var row = document.getElementById(rowId);
            row.classList.add('bg-answer');
        }

        function resetRowColors() {
            var rows = document.querySelectorAll("tr");
            rows.forEach(function(row) {
                row.classList.remove('bg-answer');
            });
        }

        function handleAnswerSelection(rowId) {
            resetRowColors();
            gavs(rowId);
        }
    </script>
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h2 class="text-center">Exam Questions</h2>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="exam_table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" style="width: 50px;"><b>No.</b></th>
                                        <th colspan="5"><b>First Page</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $num = 0;
                                        $vc = $vr = $fr = $qr = 0;
                                    @endphp

                                    @foreach($questions as $row)
                                        @php
                                            $num++;
                                            $kulay = ($num % 2) ? "#table-success" : "table-light";
                                            $ch1 = ($num % 2) ? "A:" : "F:";
                                            $ch2 = ($num % 2) ? "B:" : "G:";
                                            $ch3 = ($num % 2) ? "C:" : "H:";
                                            $ch4 = ($num % 2) ? "D:" : "J:";
                                            $ch5 = ($num % 2) ? "E:" : "K:";

                                            switch ($row->ctype) {
                                                case 'Verbal Comprehension':
                                                    $vc++;
                                                    $myanswer = "vc" . $vc;
                                                    break;
                                                case 'Verbal Reasoning':
                                                    $vr++;
                                                    $myanswer = "vr" . $vr;
                                                    break;
                                                case 'Figural Reasoning':
                                                    $fr++;
                                                    $myanswer = "fr" . $fr;
                                                    break;
                                                case 'Quantitative Reasoning':
                                                    $qr++;
                                                    $myanswer = "qr" . $qr;
                                                    break;
                                            }
                                        @endphp

                                        <tr id="{{ $row->id }}" class="{{ $kulay }}">
                                            <td rowspan="2" class="align-middle text-center">{{ $num }}</td>
                                            <td colspan="5">
                                                <b>Question: </b>
                                                @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $row->question))
                                                    <img src="{{ asset($row->question) }}" class="img-fluid" alt="Question Image">
                                                @else
                                                    {!! $row->question !!}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr id="options-{{ $row->id }}" class="{{ $kulay }}">
                                            @for($i = 0; $i < 5; $i++)
                                                @php
                                                    $option = 'option_' . chr(97 + $i); // Generates option_a to option_e
                                                    $label = chr(65 + $i) . ':'; // Generates A: to E:
                                                @endphp
                                                <td>
                                                    <input type="radio" value="{{ $row->$option }}" onchange="handleAnswerSelection('{{ $row->id }}')" name="{{ $myanswer }}"> 
                                                    <b>{{ $label }}</b>
                                                    @if(preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $row->$option))
                                                        <img src="{{ asset($row->$option) }}" class="img-fluid" alt="{{ $label }} Image">
                                                    @else
                                                        {{ $row->$option }}
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AdminLTE JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
</body>

</html>
