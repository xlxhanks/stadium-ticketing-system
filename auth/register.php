<?php
session_start();
include '../root/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';  // Default role

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header('Location: register.php');
        exit();
    }
}
?>

<!-- Registration Form -->
<link rel="stylesheet" href="../assets/styles.css">

<h2>Register</h2>

<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">' . $_SESSION['error'] . '</p>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<p style="color:green;">' . $_SESSION['success'] . '</p>';
    unset($_SESSION['success']);
}
?>

<form action="register.php" method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Email Address" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>
