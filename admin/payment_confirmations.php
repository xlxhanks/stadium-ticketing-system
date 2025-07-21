<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Handle payment status update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'], $_POST['new_status'])) {
    $payment_id = (int) $_POST['payment_id'];
    $new_status = $_POST['new_status'];

    // Validate new status
    if (in_array($new_status, ['confirmed', 'rejected'])) {
        try {
            // Update payment status
            $stmt = $conn->prepare("UPDATE payments SET payment_status = ? WHERE id = ?");
            $stmt->execute([$new_status, $payment_id]);

            // If confirmed, update order status to 'paid'
            if ($new_status === 'confirmed') {
                // Get order_id for this payment
                $stmt = $conn->prepare("SELECT order_id FROM payments WHERE id = ?");
                $stmt->execute([$payment_id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($order) {
                    $stmt = $conn->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
                    $stmt->execute([$order['order_id']]);
                }
            }

            $_SESSION['success'] = "Payment status updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating payment status: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid status selected.";
    }

    header('Location: payment_confirmations.php');
    exit();
}

// Fetch all payments with order details
try {
    $stmt = $conn->query("
        SELECT p.*, o.total_amount, o.status AS order_status, u.name AS username 
        FROM payments p 
        JOIN orders o ON p.order_id = o.id 
        JOIN users u ON o.user_id = u.id 
        ORDER BY p.payment_date DESC
    ");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching payments: " . $e->getMessage();
    exit();
}
?>

<h2>Payment Confirmations</h2>

<?php if (isset($_SESSION['success'])): ?>
    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color: red; font-weight: bold;"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (count($payments) > 0): ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>User</th>
            <th>Order ID</th>
            <th>Amount Paid</th>
            <th>Payment Status</th>
            <th>Order Status</th>
            <th>Payment Date</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= htmlspecialchars($payment['username']); ?></td>
                <td><?= htmlspecialchars($payment['order_id']); ?></td>
                <td>$<?= number_format($payment['amount'], 2); ?></td>
                <td><?= htmlspecialchars($payment['payment_status']); ?></td>
                <td><?= htmlspecialchars($payment['order_status']); ?></td>
                <td><?= htmlspecialchars($payment['payment_date']); ?></td>
                <td>
                    <?php if ($payment['payment_status'] === 'pending'): ?>
                        <form method="POST" action="payment_confirmations.php" style="display:inline;">
                            <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                            <input type="hidden" name="new_status" value="confirmed">
                            <button type="submit" onclick="return confirm('Are you sure you want to approve this payment?')">Approve</button>
                        </form>
                        <form method="POST" action="payment_confirmations.php" style="display:inline; margin-left:5px;">
                            <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                            <input type="hidden" name="new_status" value="rejected">
                            <button type="submit" onclick="return confirm('Are you sure you want to reject this payment?')">Reject</button>
                        </form>
                    <?php else: ?>
                        <em>No actions available</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No payments recorded yet.</p>
<?php endif; ?>
