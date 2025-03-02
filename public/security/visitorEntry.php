<?php
session_start();
include '../../includes/db/db_config.php';

// Check if user is logged in and is a security guard
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'security') {
    die("Unauthorized access.");
}

$successMessage = $errorMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_name = $_POST['visitor_name'];
    $contact_number = $_POST['contact_number'];
    $household_id = $_POST['household_id'];
    $date_of_visit = date('Y-m-d H:i:s');
    $status = "Entered";  // Default status
    $qr_code = $_POST['qr_code'] ?? null;
    $vehicle_id = $_POST['vehicle_id'] ?? null;
    $security_guard = $_SESSION['user_id'];  // Logged-in guard's ID
    $entry_type = "IN"; // Default entry
    $exit_type = null;
    $log_type = "Visitor Entry";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // Insert into visitor_management
        $queryVisitor = "INSERT INTO visitor_management (qr_code, household_id, visitor_name, contact_number, date_of_visit, status) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        $stmtVisitor = $pdo->prepare($queryVisitor);
        $stmtVisitor->execute([$qr_code, $household_id, $visitor_name, $contact_number, $date_of_visit, $status]);

        $visit_id = $pdo->lastInsertId(); // Get the newly inserted visitor ID

        // Insert into gate_log
        $queryGateLog = "INSERT INTO gate_log (vehicle_id, visit_id, security_guard, timestamp, entry_type, exit_type, log_type) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtGateLog = $pdo->prepare($queryGateLog);
        $stmtGateLog->execute([$vehicle_id, $visit_id, $security_guard, $date_of_visit, $entry_type, $exit_type, $log_type]);

        $pdo->commit();
        $successMessage = "Visitor entry logged successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errorMessage = "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Entry</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="../assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        
        <h1>Visitor Entry</h1>
        <a href="securityDashboard.php">Back to Dashboard</a>
            
    </div>
        
        <?php if ($successMessage): ?>
            <p class="success"><?= $successMessage; ?></p>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <p class="error"><?= $errorMessage; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="visitor_name">Visitor Name:</label>
            <input type="text" name="visitor_name" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" name="contact_number" required>

            <label for="household_id">Household ID:</label>
            <input type="number" name="household_id" required>

            <label for="qr_code">QR Code (Optional):</label>
            <input type="text" name="qr_code">

            <label for="vehicle_id">Vehicle ID (Optional):</label>
            <input type="text" name="vehicle_id">

            <button type="submit">Log Entry</button>
        </form>
<script>
        //logout and profile tab
        document.addEventListener("DOMContentLoaded", function () {
            const profilePic = document.getElementById("profilePic");
            const dropdownMenu = document.getElementById("dropdownMenu");

            profilePic.addEventListener("click", function (event) {
                event.stopPropagation();
                dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
            });

            document.addEventListener("click", function () {
                dropdownMenu.style.display = "none";
            });

            dropdownMenu.addEventListener("click", function (event) {
                event.stopPropagation();
            });
        });
    </script>
</body>
</html>
