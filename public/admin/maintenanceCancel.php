<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../../includes/db/db_config.php';

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "UPDATE maintenance_tasks SET maint_status = 'cancelled' WHERE task_id = :task_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        
        $stmt->execute();

        header("Location: maintenance.php?success=Task cancelled successfully");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: maintenance.php");
    exit();
}
