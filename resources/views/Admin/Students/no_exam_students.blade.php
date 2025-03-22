<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>No Exam Students</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>No Exam Students</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Course</th>
                <th>Name</th>
                <th>Gender</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->id_number }}</td>
                    <td>{{ $student->course }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->gender }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
