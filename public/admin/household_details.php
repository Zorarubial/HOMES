<?php
include '../../includes/db/db_config.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$household_id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch household details
    $query = "SELECT 
                h.household_id, 
                ho.homeowner_id, 
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
        die("Household not found");
    }

    // Fetch household residents
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
    <title>Household Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table {
            width: 60%;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo linked to Admin Dashboard -->
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>

        <h1>Household Details</h1>

        <div class="profile-notifications">
            <i class="fas fa-bell" id="notificationIcon"></i>
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Admin Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.html">My Profile</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <a href="householdsList.php">Back to Households List</a>
    
    <div>  
        <table border="1">
            <tr><th>Household ID</th><td><?= htmlspecialchars($household['household_id']) ?></td></tr>
            <tr><th>Household Head</th><td><?= htmlspecialchars($household['household_head']) ?></td></tr>
            <tr><th>Block</th><td><?= htmlspecialchars($household['block']) ?></td></tr>
            <tr><th>Lot</th><td><?= htmlspecialchars($household['lot']) ?></td></tr>
            <tr><th>Street</th><td><?= htmlspecialchars($household['street']) ?></td></tr>
            <tr><th>Payment Status</th><td><?= htmlspecialchars($household['payment_status']) ?></td></tr>
        </table>

        <h2>Household Residents</h2>
        <table border="1">
            <tr><th>Name</th><th>Relationship</th></tr>
            <?php foreach ($residents as $resident): ?>
                <tr>
                    <td><?= htmlspecialchars($resident['resident_name']) ?></td>
                    <td><?= htmlspecialchars($resident['relationship']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <script>
        // Profile and Logout Dropdown
        document.addEventListener("DOMContentLoaded", function () {
            const profilePic = document.getElementById("profilePic");
            const dropdownMenu = document.getElementById("dropdownMenu");

            profilePic.addEventListener("click", function (event) {
                event.stopPropagation();
                dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
            });

            document.addEventListener("click", function () {
                dropdownMenu.style.display = "none";
            });

            dropdownMenu.addEventListener("click", function (event) {
                event.stopPropagation();
            });
        });
    </script>
</body>
</html>
