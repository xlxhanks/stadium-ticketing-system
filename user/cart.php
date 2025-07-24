<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

// Initialize cart if not set
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: url('../assets/cart.jpg') no-repeat center center fixed;
            background-size: cover !important;
            font-family: Arial, sans-serif;
            color: #fff;
        }
        main {
            padding: 20px;
        }
        table {
            background-color: rgba(0, 0, 0, 0.75);
            color: #fff;
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ccc;
        }
        th {
            background-color: #333;
        }
        h2, p {
            text-align: center;
        }
        button {
            display: block;
            margin: 20px auto;
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<main>
    <h2>Your Cart</h2>

    <table>
        <tr>
            <th>Event</th>
            <th>Seats Selected</th>
            <th>Price per Ticket</th>
            <th>Total Price</th>
        </tr>

        <?php
        $grandTotal = 0;

        foreach ($_SESSION['cart'] as $event_id => $seats):
            $stmt = $conn->prepare("SELECT title, price_per_ticket FROM events WHERE id = ?");
            $stmt->execute([$event_id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            $quantity = count($seats);
            $totalPrice = $quantity * $event['price_per_ticket'];
            $grandTotal += $totalPrice;
        ?>
            <tr>
                <td><?= htmlspecialchars($event['title']); ?></td>
                <td><?= implode(', ', $seats); ?> (<?= $quantity; ?>)</td>
                <td>$<?= number_format($event['price_per_ticket'], 2); ?></td>
                <td>$<?= number_format($totalPrice, 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Grand Total: $<?= number_format($grandTotal, 2); ?></strong></p>

    <a href="checkout.php"><button>Proceed to Checkout</button></a>
</main>

</body>
</html>
