<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TSVIYO Backend API</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            background-color: #f8fafc;
            color: #333;
        }

        .version {
            font-weight: 600;
            margin-bottom: 16px;
        }

        .link {
            margin-top: 20px;
        }

        a {
            color: #0c66ee;
            text-decoration: none;
            font-weight: 600;
        }

        .outline {
            margin-top: 30px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <h1>🎯 TSVIYO Laravel API</h1>

    <div class="version">
        Laravel Version: <strong>{{ Illuminate\Foundation\Application::VERSION }}</strong>
    </div>

    <div class="link">
        📘 <a href="{{ url('/docs') }}" target="_blank">Browse API Documentation</a>
    </div>

    <div class="outline">
        <h3>What This API Does</h3>
        <ul>
            <li>🔐 Token-based authentication using Laravel Sanctum</li>
            <li>📩 Email verification & password reset flows</li>
            <li>🧑 Get authenticated user profile via `/api/auth/me`</li>
            <li>📦 Scribe-powered browsable documentation</li>
            <li>⚙️ Designed for integration with a Next.js frontend</li>
        </ul>
    </div>
</body>
</html>
