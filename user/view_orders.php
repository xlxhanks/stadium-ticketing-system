<?php
session_start();
include '../root/db_connect.php';

$user_id = $_SESSION['user']['id'] ?? null;

$orders = [];

if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        body {
            background-image: url('../assets/view_orders.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        table {
            background: rgba(0, 0, 0, 0.6);
            margin: 20px auto;
            width: 90%;
            color: white;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background-color: rgba(0, 0, 0, 0.8);
        }

        td a {
            color: #4caf50;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        p {
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>
<body>

<h2>My Orders</h2>

<?php if (count($orders) > 0): ?>
    <table>
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
                <td><?= number_format($order['total_amount'], 2) ?></td>
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
    <p>Total Orders: <?= count($orders) ?></p>
<?php else: ?>
    <p>You have no orders yet.</p>
<?php endif; ?>

</body>
</html>
