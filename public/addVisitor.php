<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db/db_config.php';

if (!isset($_GET['household_id'])) {
    die("Error: Household ID is missing.");
}
$household_id = $_GET['household_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_name = $_POST['visitor_name'];
    $contact_number = $_POST['contact_number'];
    $date_of_visit = $_POST['date_of_visit'];
    $purpose = $_POST['purpose'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert new visitor record
        $query = "INSERT INTO visitor_management (household_id, visitor_name, contact_number, date_of_visit, purpose, status) 
                  VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$household_id, $visitor_name, $contact_number, $date_of_visit, $purpose]);

        header("Location: myVisitor.php?household_id=" . $household_id);
        exit();
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Visitor</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <a href="myVisitor.php?household_id=<?= htmlspecialchars($household_id) ?>">
            <img src="assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        <h1>Add Visitor</h1>
       <a href="myVisitor.php?household_id=<?= htmlspecialchars($household_id) ?>">Back to My Visitors</a>

    </div>

    <!--  Add Visitor Form -->
    <form action="" method="POST">
        <label for="visitor_name">Visitor Name:</label>
        <input type="text" name="visitor_name" required>

        <label for="contact_number">Contact Number:</label>
        <input type="text" name="contact_number" required>

        <label for="date_of_visit">Date of Visit:</label>
        <input type="date" name="date_of_visit" required>

        <label for="purpose">Purpose:</label>
        <textarea name="purpose" required></textarea>

        <button type="submit">Add Visitor</button>
    </form>
</body>
</html>
