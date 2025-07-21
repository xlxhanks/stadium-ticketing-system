<?php
session_start();
include __DIR__ . '/../root/db_connect.php';

include '../root/navbar.php';

// Only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header('Location: manage_events.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    header('Location: manage_events.php');
    exit();
}
?>
