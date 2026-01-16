<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .error-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-container h1 {
            font-size: 80px;
            color: #333;
            margin-bottom: 10px;
        }
        .error-container h2 {
            font-size: 28px;
            color: #555;
            margin-bottom: 20px;
        }
        .error-container p {
            color: #777;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .error-container a {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin: 0 10px;
        }
        .error-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Database not found</h2>
        <p>We're sorry, but the database connection could not be established.</p>
        <p>Please check your database configuration or contact the administrator.</p>
        <a href="auth/login.php">Login</a>
        <a href="auth/register.php">Register</a>
    </div>
</body>
</html>