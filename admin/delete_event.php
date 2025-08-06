<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No event selected.";
    header('Location: manage_events.php');
    exit();
}

require __DIR__ . '/../root/db_connect.php';

try {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Event deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting event: " . $e->getMessage();
}

header('Location: manage_events.php');
exit();
