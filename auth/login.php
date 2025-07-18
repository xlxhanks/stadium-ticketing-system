<?php
session_start();
include '../root/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row;

            if ($row['role'] === 'admin') {
                header('Location: ../admin/manage_events.php');
            } else {
                header('Location: ../user/dashboard.php');
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid credentials!";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found!";
        header('Location: login.php');
        exit();
    }
}
?>

<!-- Simple Login Form -->
 <?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">' . $_SESSION['error'] . '</p>';
    unset($_SESSION['error']);
}
?>

<h2>Login</h2>
<form action="login.php" method="POST">
    <input type="email" name="email" placeholder="Email Address" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
