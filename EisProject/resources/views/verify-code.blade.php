<div>
    <!-- Simplicity is the consequence of refined emotions. - Jean D'Alembert -->
</div>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h3 class="text-center">Verify Your Email</h3>
        <form method="POST" action="{{ route('verification.check') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Verification Code</label>
                <input type="text" name="verification_code" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>

        @if ($errors->any())
            <div class="mt-3 alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p class="m-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
