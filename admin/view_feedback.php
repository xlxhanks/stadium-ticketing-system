<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch all feedback with user details
try {
    $stmt = $conn->query("
        SELECT f.*, u.name AS username 
        FROM feedback f 
        JOIN users u ON f.user_id = u.id 
        ORDER BY f.created_at DESC
    ");
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching feedbacks: " . $e->getMessage();
    exit();
}
?>

<div class="container mt-4">
    <h2>All User Feedback</h2>

    <?php if (count($feedbacks) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($row['comment'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($row['created_at'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No feedback found.</p>
    <?php endif; ?>
</div>
