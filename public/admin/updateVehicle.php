<?php
include '../../includes/db/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $sticker_type = $_POST['sticker_type'];
    $date_bought = $_POST['date_bought'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "UPDATE vehicles SET sticker_type = ?, date_bought = ? WHERE vehicle_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$sticker_type, $date_bought, $vehicle_id]);
        
        header("Location: vehiclesList.php?success=Vehicle updated successfully");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    die("Invalid request.");
}
?>
