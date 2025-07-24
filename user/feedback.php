<?php
session_start();
include __DIR__ . '/../root/db_connect.php';
include '../root/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'], $_POST['comment'])) {
    $event_id = (int) $_POST['event_id'];
    $comment = htmlspecialchars(trim($_POST['comment']));

    try {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, event_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $event_id, $comment]);

        $_SESSION['success'] = "Feedback submitted successfully!";
        header('Location: feedback.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error submitting feedback: " . $e->getMessage();
        header('Location: feedback.php');
        exit();
    }
}

// Fetch user’s events (to give feedback)
try {
    $stmt = $conn->prepare("
        SELECT DISTINCT events.id, events.title 
        FROM tickets 
        JOIN events ON tickets.event_id = events.id 
        WHERE tickets.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching events: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Feedback</title>
    <style>
        body {
            background-image: url('../assets/feedback.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        select, textarea, button {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Leave Feedback</h2>

    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="message success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="message error">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <form method="POST" action="feedback.php">
        <label for="event_id">Select Event:</label>
        <select name="event_id" required>
            <?php foreach ($events as $event): ?>
                <option value="<?= $event['id']; ?>"><?= htmlspecialchars($event['title']); ?></option>
            <?php endforeach; ?>
        </select>

        <textarea name="comment" rows="4" placeholder="Your feedback..." required></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
</div>

</body>
</html>
