<?php
session_start();
include '../root/db_connect.php';
include '../root/navbar.php';


// Allow only logged-in users
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['event_id'])) {
    header('Location: view_events.php');
    exit();
}

$event_id = (int) $_GET['event_id'];

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seat_no = htmlspecialchars(trim($_POST['seat_no']));

    // Check if seat is already booked for this event
    $stmt = $conn->prepare("SELECT * FROM tickets WHERE event_id = ? AND seat_no = ?");
    $stmt->bind_param("is", $event_id, $seat_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Sorry, seat already booked!");
    }

    // Book the seat
    $insert = $conn->prepare("INSERT INTO tickets (user_id, event_id, seat_no) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $_SESSION['user']['id'], $event_id, $seat_no);
    $insert->execute();

    echo "Seat $seat_no booked successfully!";
    exit();
}
?>

<h2>Book a Seat</h2>

<form action="book_ticket.php?event_id=<?php echo $event_id; ?>" method="POST">
    <input type="text" name="seat_no" placeholder="Seat Number (e.g., A12)" required><br>
    <button type="submit">Book Seat</button>
</form>
