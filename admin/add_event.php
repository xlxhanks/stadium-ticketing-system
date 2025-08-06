<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

require __DIR__ . '/../root/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $stadium_id = (int) $_POST['stadium_id'];
    $category = htmlspecialchars(trim($_POST['category']));
    $event_date = $_POST['event_date'];
    $price = (float) $_POST['price'];
    $tickets = (int) $_POST['tickets'];

    try {
        $stmt = $conn->prepare("INSERT INTO events (title, description, stadium_id, category, date, price_per_ticket, tickets_available, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$title, $description, $stadium_id, $category, $event_date, $price, $tickets]);

        $_SESSION['success'] = "Event added successfully!";
        header('Location: manage_events.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding event: " . $e->getMessage();
        header('Location: add_event.php');
        exit();
    }
}
?>

<!-- HTML starts here -->
<?php include '../root/navbar.php'; ?>

<!-- Your add event form HTML goes below as usual -->
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../assets/add_event.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            margin: 100px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Event</h2>

    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <form method="POST" action="">
        <div class="mb-3">
            <input type="text" name="title" class="form-control" placeholder="Event Title" required>
        </div>

        <div class="mb-3">
            <textarea name="description" class="form-control" placeholder="Description" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <input type="datetime-local" name="event_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <input type="number" name="price" class="form-control" step="0.01" placeholder="Price per Ticket (USD)" required>
        </div>

        <div class="mb-3">
            <input type="number" name="tickets" class="form-control" placeholder="Total Tickets Available" required>
        </div>
        <div class="mb-3">
         <label for="stadium_id">Choose Stadium:</label>
         <select name="stadium_id" class="form-select" required>
          <?php
            $stadiums = $conn->query("SELECT id, name FROM stadiums");
            while ($s = $stadiums->fetch(PDO::FETCH_ASSOC)) {
               echo "<option value='{$s['id']}'>{$s['name']}</option>";
           }
          ?>
         </select>
        </div>
        
        <div class="mb-3">
            <input type="text" name="category" class="form-control" placeholder="Category" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Add Event</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
