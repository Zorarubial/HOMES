<?php
include '../../includes/db/db_config.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$homeowner_id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch Homeowner Details
    $query = "SELECT h.homeowner_id, su.first_name, su.last_name, su.phone, su.email
              FROM homeowners h
              JOIN system_users su ON h.user_id = su.user_id
              WHERE h.homeowner_id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$homeowner_id]);
    $homeowner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$homeowner) {
        die("Homeowner not found");
    }

    // Fetch Associated Households
    $query = "SELECT h.household_id,
                     CONCAT('BLK ', h.block, ' LOT ', h.lot, ' ', h.street) AS address,
                     COALESCE(md.payment_status, 'Unknown') AS payment_status
              FROM households h
              LEFT JOIN monthly_dues md ON h.household_id = md.household_id
              WHERE h.homeowner_id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$homeowner_id]);
    $households = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Household Users (Excluding Admin & Security)
    $query = "SELECT s.username, s.type, s.status, s.created_at, s.last_updated
              FROM system_users s 
              JOIN households h ON s.household_id = h.household_id 
              WHERE h.homeowner_id = ? 
              AND s.type NOT IN ('admin', 'security')";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$homeowner_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeowner Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Logo linked to Admin Dashboard -->
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>

        <h1>Homeowner Details</h1>

        <div class="profile-notifications">
            <!-- Bell Icon for Notifications -->
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <!-- Profile Menu -->
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Admin Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.php">My Profile</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <a href="homeownersList.php">Back to Homeowners List</a>

    <!-- Homeowner Details -->
    <h2>Homeowner Details</h2>
    <table border="1">
        <tr><th>Full Name</th><td><?= htmlspecialchars($homeowner['first_name'] . " " . $homeowner['last_name']) ?></td></tr>    
        <tr><th>Phone</th><td><?= htmlspecialchars($homeowner['phone']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($homeowner['email']) ?></td></tr>
    </table>

    <!-- Associated Households -->
    <h2>Associated Households</h2>
    <table border="1">
        <tr><th>Household ID</th><th>Address</th><th>Payment Status</th></tr>
        <?php if (!empty($households)): ?>
            <?php foreach ($households as $household): ?>
                <tr>
                    <td><?= htmlspecialchars($household['household_id']) ?></td>
                    <td><?= htmlspecialchars($household['address']) ?></td>
                    <td><?= htmlspecialchars($household['payment_status']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No households found.</td></tr>
        <?php endif; ?>
    </table>

    <!-- Household Users -->
    <h2>Household Users</h2>
    <table border="1">
        <tr><th>Username</th><th>Type</th><th>Status</th><th>Created At</th><th>Updated At</th></tr>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['type']) ?></td>
                    <td><?= htmlspecialchars($user['status']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td><?= htmlspecialchars($user['updated_at']) ?></td>    
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No users found.</td></tr>
        <?php endif; ?>
    </table>

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
