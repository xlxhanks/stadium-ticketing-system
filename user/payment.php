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

<!DOCTYPE html>
<html>
<head>
    <title>Make Payment</title>
    <style>
        body {
            background-image: url('../assets/payment.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .payment-wrapper {
            background-color: rgba(0, 0, 0, 0.7);
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px #000;
        }
        h2, p {
            text-align: center;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin: 10px 0;
        }
        input[type="password"] {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #218838;
        }
        .error, .success {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .error {
            background-color: rgba(255, 0, 0, 0.2);
            color: #ffcccc;
        }
        .success {
            background-color: rgba(0, 128, 0, 0.2);
            color: #c2f0c2;
        }
    </style>
</head>
<body>

<div class="payment-wrapper">
    <h2>Make Payment for Order #<?= htmlspecialchars($order['id']); ?></h2>
    <p>Amount to Pay: $<?= number_format($order['total_amount'], 2); ?></p>

    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $error) echo "<p>" . htmlspecialchars($error) . "</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success); ?></div>
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

        <div id="mpesa_pin_field" style="display: <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'mpesa') ? 'block' : 'none' ?>;">
            <label>M-Pesa PIN (4 digits):</label>
            <input type="password" name="mpesa_pin" maxlength="4" pattern="\d{4}" value="<?= isset($_POST['mpesa_pin']) ? htmlspecialchars($_POST['mpesa_pin']) : '' ?>">
        </div>

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
</div>

</body>
</html>
