<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['order_id'])) {
    echo "No order selected for payment.";
    exit();
}

$order_id = (int) $_GET['order_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user']['id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float) $_POST['amount'];
    $status = 'pending';

    $stmt = $conn->prepare("INSERT INTO payments (order_id, amount, payment_status, payment_date) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$order_id, $amount, $status]);

    $_SESSION['success'] = "Payment recorded successfully. Pending confirmation.";
    header('Location: view_orders.php');
    exit();
}
?>

<h2>Make Payment for Order #<?= $order['id']; ?></h2>
<p>Amount to Pay: $<?= number_format($order['total_amount'], 2); ?></p>

<form method="POST" action="">
    <input type="hidden" name="amount" value="<?= $order['total_amount']; ?>">
    <button type="submit">Pay Now</button>
</form>
