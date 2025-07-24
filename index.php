<?php
session_start();

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: admin/manage_events.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadium Ticketing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        body {
            background-image: url('assets/index.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .centered-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            width: 350px;
        }

        .button-group a {
            text-decoration: none;
        }

        .primary-btn, .secondary-btn {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .primary-btn {
            background-color: #007bff;
            color: white;
        }

        .secondary-btn {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

<div class="centered-container">
    <h1>Welcome to the Stadium Ticketing System</h1>

    <div class="button-group">
        <a href="auth/login.php"><button class="primary-btn">Login</button></a>
        <a href="auth/register.php"><button class="secondary-btn">Register</button></a>
    </div>
</div>

</body>
</html>
