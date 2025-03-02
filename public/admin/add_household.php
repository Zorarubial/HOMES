<?php
session_start();
include('../../includes/db/db_config.php'); // Adjust path if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input values
    $homeowner_id = $_POST['homeowner_id'];
    $block = $_POST['block'];
    $lot = $_POST['lot'];
    $street = $_POST['street'];

    try {
        // Insert into households table
        $query = "INSERT INTO households (homeowner_id, block, lot, street) 
                  VALUES (:homeowner_id, :block, :lot, :street)";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':homeowner_id', $homeowner_id, PDO::PARAM_INT);
        $stmt->bindParam(':block', $block, PDO::PARAM_STR);
        $stmt->bindParam(':lot', $lot, PDO::PARAM_STR);
        $stmt->bindParam(':street', $street, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Household successfully added!";
        } else {
            echo "Failed to add household.";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: adminDashboard.php"); // Redirect if accessed without POST
    exit();
}
?>
