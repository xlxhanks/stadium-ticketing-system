<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

require __DIR__ . '/../root/db_connect.php';

if (!isset($_GET['event_id'])) {
    echo "No event selected.";
    exit();
}

$event_id = $_GET['event_id'];

try {
    $stmt = $conn->prepare("SELECT e.*, s.capacity FROM events e JOIN stadiums s ON e.stadium_id = s.id WHERE e.id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "Event not found.";
        exit();
    }

    $capacity = (int) $event['capacity'];

    $stmtSeats = $conn->prepare("SELECT seat_number FROM tickets WHERE event_id = ?");
    $stmtSeats->execute([$event_id]);
    $bookedSeats = array_column($stmtSeats->fetchAll(PDO::FETCH_ASSOC), 'seat_number');

    function generateAllSeats($capacity) {
        $rows = range('A', 'Z');
        $maxRow = min(ceil($capacity / 50), count($rows));
        $seatRows = array_slice($rows, 0, $maxRow);
        $seatNumbers = range(1, 50);

        $seats = [];
        foreach ($seatRows as $row) {
            foreach ($seatNumbers as $num) {
                $seat = $row . str_pad($num, 2, '0', STR_PAD_LEFT);
                $seats[] = $seat;
                if (count($seats) >= $capacity) break 2;
            }
        }
        return $seats;
    }

    $allSeats = generateAllSeats($capacity);
    $availableSeats = array_diff($allSeats, $bookedSeats);

    function groupSeatRanges($seats) {
        sort($seats);
        $ranges = [];
        $start = $prev = null;

        foreach ($seats as $seat) {
            if (!$start) {
                $start = $seat;
                $prev = $seat;
                continue;
            }
            $expected = $prev[0] . str_pad((int)substr($prev, 1) + 1, 2, '0', STR_PAD_LEFT);
            if ($seat === $expected) {
                $prev = $seat;
            } else {
                $ranges[] = ($start === $prev) ? $start : "$start-$prev";
                $start = $seat;
                $prev = $seat;
            }
        }
        if ($start) {
            $ranges[] = ($start === $prev) ? $start : "$start-$prev";
        }
        return $ranges;
    }

    $bookedRanges = groupSeatRanges($bookedSeats);
    $availableRanges = groupSeatRanges($availableSeats);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seats']) && is_array($_POST['seats'])) {
    $validSeats = [];
    foreach ($_POST['seats'] as $seatInput) {
        $seat = strtoupper(trim($seatInput));
        if (!preg_match('/^[A-Z]\d{2}$/', $seat)) continue;
        if (!in_array($seat, $allSeats)) continue;
        if (in_array($seat, $bookedSeats)) continue;
        $validSeats[] = $seat;
    }

    if (empty($validSeats)) {
        $_SESSION['error'] = "No valid or available seats selected.";
        header("Location: book_tickets.php?event_id=$event_id");
        exit();
    }

    foreach ($validSeats as $seat) {
        $_SESSION['cart'][$event_id][] = $seat;
    }
    header("Location: cart.php");
    exit();
}
?>

<?php include '../root/navbar.php'; ?>

<div style="background-image: url('../assets/book_tickets.jpg'); background-size: cover; background-position: center; min-height: 100vh; padding: 40px; color: #fff;">
  <div style="background-color: rgba(0, 0, 0, 0.7); padding: 20px; border-radius: 10px; max-width: 700px; margin: auto;">
    <h2>Book Tickets for <?= htmlspecialchars($event['title']); ?></h2>
    <p>Date: <?= htmlspecialchars($event['date']); ?></p>
    <p>Stadium Capacity: <?= $event['capacity']; ?> seats</p>
    <p>Price per Ticket: $<?= number_format($event['price_per_ticket'], 2); ?></p>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger mt-3"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="mb-3 mt-4">
      <strong><?= count($bookedSeats); ?></strong> seats booked.<br>
      <strong><?= count($availableSeats); ?></strong> seats available.
    </div>

    <h5>Booked Seat Ranges:</h5>
    <div style="background: #fff; color: #000; padding: 10px; border-radius: 5px;">
      <?= empty($bookedRanges) ? 'None yet!' : implode(', ', $bookedRanges); ?>
    </div>

    <h5 class="mt-3">Available Seat Ranges:</h5>
    <div style="background: #fff; color: #000; padding: 10px; border-radius: 5px;">
      <?= empty($availableRanges) ? 'None available.' : implode(', ', $availableRanges); ?>
    </div>

    <form method="POST" action="" class="mt-4">
      <label for="seats">Enter Seat Numbers (e.g. A01, B12):</label>
      <input type="text" name="seats[]" class="form-control mb-2" placeholder="A01" required>
      <input type="text" name="seats[]" class="form-control mb-2" placeholder="B12">
      <input type="text" name="seats[]" class="form-control mb-2" placeholder="C05">
      <button type="submit" class="btn btn-success w-100">Add to Cart</button>
    </form>
  </div>
</div>
