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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch visitors for this household
    $query = "SELECT visit_id, visitor_name, contact_number, date_of_visit, status, qr_code, purpose
              FROM visitor_management 
              WHERE household_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$household_id]);
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Visitors</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <a href="homeownerDashboard.php?id=<?= htmlspecialchars($household_id) ?>">
            <img src="assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        <h1>Visitors for Household ID: <?= htmlspecialchars($household_id) ?></h1>
        <a href="householdDetails.php?id=<?= htmlspecialchars($household_id) ?>">Back to Household Details</a>

    </div>

    <!-- Add Visitor Button -->
    <a href="addVisitor.php?household_id=<?= htmlspecialchars($household_id) ?>" class="btn">Add Visitor</a>

    <!-- Visitors List (Now Includes QR Code & Purpose) -->
    <h2>Visitors</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Contact Number</th>
            <th>Date of Visit</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>QR Code</th>
        </tr>
        <?php foreach ($visitors as $visitor): ?>
            <tr>
                <td><?= htmlspecialchars($visitor['visitor_name']) ?></td>
                <td><?= htmlspecialchars($visitor['contact_number']) ?></td>
                <td><?= htmlspecialchars($visitor['date_of_visit']) ?></td>
                <td><?= htmlspecialchars($visitor['purpose']) ?></td>
                <td><?= htmlspecialchars($visitor['status']) ?></td>
                <td>
                    <?php if (!empty($visitor['qr_code'])): ?>
                        <img src="../uploads/qr_codes/<?= htmlspecialchars($visitor['qr_code']) ?>" alt="QR Code" width="100">
                    <?php else: ?>
                        No QR Code
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
