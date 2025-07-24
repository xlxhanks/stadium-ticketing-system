<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

$event_id = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 0;
if (!$event_id) {
    echo "Invalid Event.";
    exit();
}

// Fetch booked seats for this event
$stmt = $conn->prepare("SELECT seat_number FROM tickets WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$booked_seats = [];
while ($row = $result->fetch_assoc()) {
    $booked_seats[] = $row['seat_number'];
}

// Generate all seats A100 - K1000
$available_seats = [];
$rows = range('A', 'K');
for ($n = 100; $n <= 1000; $n++) {
    foreach ($rows as $row) {
        $seat = $row . $n;
        if (!in_array($seat, $booked_seats)) {
            $available_seats[] = $seat;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Seat</title>
    <style>
        body {
            background-image: url('seat_selection.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .seat-selection-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.75);
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px #000;
        }

        select, button {
            padding: 10px;
            margin-top: 15px;
            width: 100%;
            font-size: 16px;
            border-radius: 5px;
            border: none;
        }

        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        label {
            font-size: 18px;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="seat-selection-container">
    <h2>Select Seat for Booking</h2>
    <form action="checkout.php" method="POST">
        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
        <label for="seat_number">Choose Your Seat:</label>
        <select name="seat_number" required>
            <?php foreach ($available_seats as $seat): ?>
                <option value="<?php echo $seat; ?>"><?php echo $seat; ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <button type="submit">Confirm Booking</button>
    </form>
</div>

</body>
</html>
