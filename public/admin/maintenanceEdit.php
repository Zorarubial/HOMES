<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../../includes/db/db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $category = trim($_POST['category']);
    $start_time = $_POST['start_time'];
    $end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : NULL;
    $expense = $_POST['expense'];
    $note = trim($_POST['note']);
    $personnel_id = $_POST['personnel_id'];
    $maint_status = $_POST['maint_status'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "UPDATE maintenance_tasks SET category = :category, start_time = :start_time, end_time = :end_time, expense = :expense, note = :note, personnel_id = :personnel_id, maint_status = :maint_status WHERE task_id = :task_id";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':expense', $expense);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':personnel_id', $personnel_id);
        $stmt->bindParam(':maint_status', $maint_status);
        $stmt->bindParam(':task_id', $task_id);
        
        $stmt->execute();

        header("Location: maintenance.php?success=Task updated successfully");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: maintenance.php");
    exit();
}
