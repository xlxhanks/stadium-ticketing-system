<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch all orders with user details
try {
    $stmt = $conn->query("
        SELECT o.*, u.name AS username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
    exit();
}
?>

<h2>All Orders</h2>

<?php if (count($orders) > 0): ?>
    <table border="1">
        <tr>
            <th>User</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Placed On</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['username']); ?></td>
                <td>$<?= number_format($order['total_amount'], 2); ?></td>
                <td><?= htmlspecialchars($order['status']); ?></td>
                <td><?= htmlspecialchars($order['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No orders placed yet.</p>
<?php endif; ?>
