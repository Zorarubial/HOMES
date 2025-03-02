<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db/db_config.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amenity_type = $_POST['amenity_type'];
    $reservation_date = $_POST['reservation_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "INSERT INTO amenities_reservations (household_id, amenity_type, reservation_date, start_time, end_time, status, created_at)
                  VALUES ((SELECT household_id FROM system_users WHERE user_id = ?), ?, ?, ?, ?, 'Pending', NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id, $amenity_type, $reservation_date, $start_time, $end_time]);
        
        header("Location: myReservations.php");
        exit();
    } catch (PDOException $e) {
        die("Error adding reservation: " . htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Reservation</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Add Reservation</h1>
    </div>
        <form method="POST" action="">
            <label for="amenity_type">Amenity Type:</label>
            <select name="amenity_type" required>
                <option value="Clubhouse">Clubhouse</option>
                <option value="Court">Court</option>
                <option value="Chapel">Chapel</option>
                <option value="Swimming Pool">Swimming Pool</option>
            </select>
            
            <label for="reservation_date">Date:</label>
            <input type="date" name="reservation_date" required>
            
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" required>
            
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" required>
            
            <button type="submit">Submit Reservation</button>
        </form>
        <a href="myReservations.php">Back to My Reservations</a>
    
</body>
</html>
