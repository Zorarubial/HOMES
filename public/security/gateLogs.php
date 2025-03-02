<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'security') {
    header("Location: login.php");
    exit();
}

include '../../includes/db/db_config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $access_type = $_POST['access_type']; // New access_type field
    $security_guard = $_SESSION['user_id']; // system_user ID of the logged-in account
    $timestamp = date('Y-m-d H:i:s');
    
    $vehicle_id = null;
    $visit_id = null;
    
    // Simulate parking space count change
    $parking_space_count_change = 0;

    // Check if vehicle entry is being logged
    if ($access_type === 'sticker' || $access_type === 'one-time qr code') {
        // Decrease parking space count by 1 when a vehicle enters
        $parking_space_count_change = -1;
    }
    // Check if visitor entry (no vehicle)
    elseif ($access_type === 'one time qr_code') {
        // No change in parking spaces for visitors without a vehicle
        $parking_space_count_change = 0;
    }

    // Update parking space count if necessary
    if ($parking_space_count_change !== 0) {
        // Update the space count in the parking_space table
        $update_query = "UPDATE parking_space SET space_count = space_count + ? WHERE area_name = 'Main Area'"; // Change 'Main Area' if necessary
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([$parking_space_count_change]);
    }

    // Simulate a visit entry (no need for actual vehicle data processing here)
    if (!empty($_POST['visit_id'])) {
        $visit_id = $_POST['visit_id'];
    }

    // Log the entry (vehicle or visitor)
    if ($vehicle_id || $visit_id) {
        $query = "INSERT INTO gate_logs (vehicle_id, visit_id, security_guard, timestamp, access_type) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$vehicle_id, $visit_id, $security_guard, $timestamp, $access_type]);

        $message = "Entry recorded successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Logs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Gate Logs</h1>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <label>Access Type:</label>
        <select name="access_type" required>
            <option value="one time qr_code">One time qr code (visitor without vehicle)</option>
            <option value="one-time qr code">One-time qr code (visitor's vehicle)</option>
            <option value="purchased qr code">Purchased qr code (frequent_visitor)</option>
            <option value="sticker">Sticker (homeowner's vehicle)</option>
        </select>
        <label>Visit ID (for guests):</label>
        <input type="text" name="visit_id">
        <button type="submit">Log Entry</button>
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>
