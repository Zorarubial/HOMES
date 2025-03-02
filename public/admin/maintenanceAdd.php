<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../../includes/db/db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = trim($_POST['category']);
    $start_time = $_POST['start_time'];
    $end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : NULL;
    $expense = $_POST['expense'];
    $note = trim($_POST['note']);
    $personnel_id = $_POST['personnel_id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "INSERT INTO maintenance_tasks (category, start_time, end_time, expense, note, personnel_id) 
                  VALUES (:category, :start_time, :end_time, :expense, :note, :personnel_id)";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':expense', $expense);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':personnel_id', $personnel_id);
        
        $stmt->execute();

        header("Location: maintenance.php?success=Task added successfully");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: maintenance.php");
    exit();
}
