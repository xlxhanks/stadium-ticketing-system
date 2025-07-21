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

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $mpesa_pin = $_POST['mpesa_pin'] ?? null; // optional, only for M-Pesa

    // Basic validation
    if (!in_array($payment_method, ['mpesa', 'paypal'])) {
        $errors[] = "Please select a valid payment method.";
    }

    if ($payment_method === 'mpesa' && (empty($mpesa_pin) || !preg_match('/^\d{4}$/', $mpesa_pin))) {
        $errors[] = "Please enter a valid 4-digit M-Pesa PIN.";
    }

    if (empty($errors)) {
        $user_id = $_SESSION['user']['id'];
        $status = 'pending';

        $stmt = $conn->prepare("INSERT INTO payments (order_id, user_id, amount, payment_method, payment_details, payment_status, payment_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");

        // Store M-Pesa PIN securely (in real apps, hash/encrypt or tokenize!)
        $payment_details = $payment_method === 'mpesa' ? "M-Pesa PIN: " . htmlspecialchars($mpesa_pin) : '';

        $stmt->execute([
            $order_id,
            $user_id,
            $order['total_amount'],
            $payment_method,
            $payment_details,
            $status
        ]);

        $success = "Payment request recorded successfully. Waiting for admin approval.";
    }
}

?>

<h2>Make Payment for Order #<?= htmlspecialchars($order['id']); ?></h2>
<p>Amount to Pay: $<?= number_format($order['total_amount'], 2); ?></p>

<?php if ($errors): ?>
    <div style="color:red;">
        <?php foreach ($errors as $error) echo "<p>" . htmlspecialchars($error) . "</p>"; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="color:green; font-weight:bold;">
        <?= htmlspecialchars($success); ?>
    </div>
<?php else: ?>

<form method="POST" action="">
    <label>
        <input type="radio" name="payment_method" value="mpesa" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'mpesa') ? 'checked' : '' ?>>
        M-Pesa
    </label>
    <label>
        <input type="radio" name="payment_method" value="paypal" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'paypal') ? 'checked' : '' ?>>
        PayPal
    </label>

    <div id="mpesa_pin_field" style="margin-top:10px; display: <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'mpesa') ? 'block' : 'none' ?>;">
        <label>M-Pesa PIN (4 digits): <input type="password" name="mpesa_pin" maxlength="4" pattern="\d{4}" value="<?= isset($_POST['mpesa_pin']) ? htmlspecialchars($_POST['mpesa_pin']) : '' ?>"></label>
    </div>

    <br>
    <button type="submit">Submit Payment</button>
</form>

<script>
// Show/hide M-Pesa PIN field dynamically
document.querySelectorAll('input[name="payment_method"]').forEach(el => {
    el.addEventListener('change', function() {
        const mpesaField = document.getElementById('mpesa_pin_field');
        if (this.value === 'mpesa') {
            mpesaField.style.display = 'block';
        } else {
            mpesaField.style.display = 'none';
        }
    });
});
</script>

<?php endif; ?>
