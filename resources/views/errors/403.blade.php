{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied - Permission Required</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            margin: 2rem;
            backdrop-filter: blur(10px);
        }

        .error-icon {
            font-size: 6rem;
            color: #e74c3c;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        .error-code {
            font-size: 3rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 1.5rem;
            color: #34495e;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #7f8c8d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            transition: transform 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .permission-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 4px solid #e74c3c;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .role-badge {
            background: #3498db;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <i class="fas fa-lock error-icon"></i>
            <div class="error-code">403</div>
            <h1 class="error-title">Access Denied</h1>

            <div class="permission-info">
                <h5><i class="fas fa-info-circle text-danger"></i> Permission Required</h5>
                <p class="error-message">
                    Sorry, you don't have the necessary permissions to access this page.
                    @if(auth()->check())
                        Your current role: <span class="role-badge">{{ ucfirst(auth()->user()->role) }}</span>
                    @endif
                </p>

                @if(isset($requiredPermission))
                <p><strong>Required Permission:</strong> {{ $requiredPermission }}</p>
                @endif
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                <a href="{{ route('landing') }}" class="btn-custom">
                    <i class="fas fa-home"></i> Go Home
                </a>

                @auth
                <a href="/dashboard" class="btn-custom">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                @endauth

                <a href="javascript:history.back()" class="btn-custom">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>

            <div class="mt-4">
                <small class="text-muted">
                    Need access? Contact your administrator or
                    <a href="mailto:admin@example.com" class="text-primary">request permission</a>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
