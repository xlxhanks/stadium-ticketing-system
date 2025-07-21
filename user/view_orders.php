<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
</head>
<body>

<h2>My Orders</h2>

<?php if (count($orders) > 0): ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Order ID</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Placed On</th>
            <th>Action</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td>
                    <?php if ($order['status'] === 'unpaid' || $order['status'] === 'pending'): ?>
                        <a href="payment.php?order_id=<?= $order['id'] ?>">Pay Now</a>
                    <?php else: ?>
                        <span>—</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>You have no orders yet.</p>
<?php endif; ?>

</body>
</html>