<?php
include '../../includes/db/db_config.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$reservation_id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update reservation status to "Cancelled" and reset payment amount
    $query = "UPDATE amenities_reservations 
              SET status = 'Cancelled', payment_amount = 0.00 
              WHERE reservation_id = :reservation_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: courtadminRes.php?success=cancelled"); // Redirect back to reservation list
    exit();
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
