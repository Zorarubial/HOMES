<?php
include '../../includes/db/db_config.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Soft delete: update status to 'Inactive'
        $query = "UPDATE system_users SET status = 'Inactive' WHERE user_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);

        header("Location: personnelList.php");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: personnelList.php");
    exit();
}
?>
