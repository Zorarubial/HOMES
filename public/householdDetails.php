<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db/db_config.php';

if (!isset($_GET['id'])) {
    die("Error: Household ID is missing.");
}

$household_id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch household details
    $query = "SELECT 
                h.household_id, 
                CONCAT(su.first_name, ' ', su.last_name) AS household_head, 
                h.block, 
                h.lot, 
                h.street,  
                COALESCE(md.payment_status, 'Unknown') AS payment_status
              FROM households h
              JOIN homeowners ho ON h.homeowner_id = ho.homeowner_id
              JOIN system_users su ON ho.user_id = su.user_id
              LEFT JOIN monthly_dues md ON h.household_id = md.household_id
              WHERE h.household_id = ?";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([$household_id]);
    $household = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$household) {
        die("Household not found.");
    }

    //  Fetch household residents
    $query = "SELECT 
                CONCAT(first_name, ' ', last_name) AS resident_name, 
                relationship 
              FROM household_members 
              WHERE household_id = ?";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([$household_id]);
    $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Household</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Logo linked to Homeowner Dashboard -->
        <a href="homeownerDashboard.php">
            <img src="assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        <h1>My Household</h1>
    </div>

    <div>
        <!--  Household Details -->
        <table border="1">
            <tr><th>Household ID</th><td><?= htmlspecialchars($household['household_id']) ?></td></tr>
            <tr><th>Household Head</th><td><?= htmlspecialchars($household['household_head']) ?></td></tr>
            <tr><th>Block</th><td><?= htmlspecialchars($household['block']) ?></td></tr>
            <tr><th>Lot</th><td><?= htmlspecialchars($household['lot']) ?></td></tr>
            <tr><th>Street</th><td><?= htmlspecialchars($household['street']) ?></td></tr>
            <tr><th>Payment Status</th><td><?= htmlspecialchars($household['payment_status']) ?></td></tr>
        </table>

        <!--  Household Residents (Restored) -->
        <h2>Household Residents</h2>
        <a href="addHouseholdMember.php?household_id=<?= htmlspecialchars($household_id) ?>">Add Member</a>

        <table border="1">
            <tr><th>Name</th><th>Relationship</th></tr>
            <?php foreach ($residents as $resident): ?>
                <tr>
                    <td><?= htmlspecialchars($resident['resident_name']) ?></td>
                    <td><?= htmlspecialchars($resident['relationship']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!--  Link to View Visitors -->
        <a href="myVisitor.php?household_id=<?= htmlspecialchars($household_id) ?>" class="btn">View Visitors</a>
    </div>
</body>
</html>
