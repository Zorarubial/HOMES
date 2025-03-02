<?php
include '../../includes/db/db_config.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$reservation_id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update reservation status to 'Declined'
    $query = "UPDATE amenities_reservations SET status = 'Declined' WHERE reservation_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$reservation_id]);

    echo "<script>alert('Reservation declined successfully!'); window.history.back();</script>";
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
