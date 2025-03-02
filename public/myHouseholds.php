<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db/db_config.php'); // Ensure correct path

$user_id = $_SESSION['user_id'];

try {
    $query = "SELECT 
                h.household_id, 
                CONCAT(su.first_name, ' ', su.last_name) AS household_head, 
                h.block, 
                h.lot, 
                h.street 
              FROM households h
              JOIN homeowners ho ON h.homeowner_id = ho.homeowner_id
              JOIN system_users su ON ho.user_id = su.user_id
              WHERE ho.user_id = :user_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $households = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Households</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Logo linked to Dashboard -->
        <a href="homeownerDashboard.php">
            <img src="assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>My Households</h1>

        <div class="profile-notifications">
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.php">My Profile</a>
                    <a href="#" onclick="confirmLogout()">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <table border="1">
        <thead>
            <tr>
                <th>Household ID</th>
                <th>Household Head</th>
                <th>Block</th>
                <th>Lot</th>
                <th>Street</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($households as $household) : ?>
                <tr>
                    <td><a href="householdDetails.php?id=<?= htmlspecialchars($household['household_id']) ?>">
                        <?= htmlspecialchars($household['household_id']) ?></a>
                    </td>
                    <td><?= htmlspecialchars($household['household_head']) ?></td>
                    <td><?= htmlspecialchars($household['block']) ?></td>
                    <td><?= htmlspecialchars($household['lot']) ?></td>
                    <td><?= htmlspecialchars($household['street']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    
    <script>
        // Profile and logout dropdown
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

        function confirmLogout() {
            let confirmAction = confirm("Are you sure you want to log out?");
            if (confirmAction) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>
