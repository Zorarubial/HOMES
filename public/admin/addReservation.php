<?php
include '../../includes/db/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "INSERT INTO amenities_reservations (household_id, reservation_date, start_time, end_time, custodian_id, status, amenity_type)
                  VALUES (:household_id, :reservation_date, :start_time, :end_time, :custodian_id, :status, :amenity_type)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':household_id' => $_POST['household_id'],
            ':reservation_date' => $_POST['reservation_date'],
            ':start_time' => $_POST['start_time'],
            ':end_time' => $_POST['end_time'],
            ':custodian_id' => !empty($_POST['custodian_id']) ? $_POST['custodian_id'] : null,
            ':status' => $_POST['status'],
            ':amenity_type' => $_POST['amenity_type']
        ]);

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
}
?>
    