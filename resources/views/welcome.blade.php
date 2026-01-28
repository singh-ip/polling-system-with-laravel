<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 30px;
        }
        a {
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            display: inline-block;
            background: #333;
            color: white;
            transition: background 0.3s ease;
        }
        a:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Poll Management System</h1>
        <a href="{{ route('login') }}">Admin Login</a>
    </div>
</body>
</html>
