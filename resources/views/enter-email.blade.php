<!DOCTYPE html>
<html>
<head>
    <title>Enter Email to Receive OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center mb-4">Enter Your Email to Receive OTP</h3>
            <div class="card">
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('Users/send-otp') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="email">Email Address:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Send OTP</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
