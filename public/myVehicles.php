<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db/db_config.php';

$user_id = $_SESSION['user_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user's registered vehicles grouped by household
    $vehicleQuery = "SELECT v.vehicle_id, v.vehicle_type, v.plate_number, v.sticker_number, v.household_id,
                             v.sticker_type, v.date_bought, v.vehicle_make, v.vehicle_model, v.vehicle_color,
                             v.registration_status, v.vehicle_qr, s.status AS sticker_status, s.request_date
                      FROM vehicles v
                      LEFT JOIN sticker_requests s ON v.vehicle_id = s.vehicle_id
                      JOIN households h ON v.household_id = h.household_id
                      JOIN homeowners ho ON h.homeowner_id = ho.homeowner_id
                      WHERE ho.user_id = ?
                      ORDER BY v.household_id";
    
    $stmt = $pdo->prepare($vehicleQuery);
    $stmt->execute([$user_id]);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group vehicles by household
    $groupedVehicles = [];
    foreach ($vehicles as $vehicle) {
        $household_id = $vehicle['household_id'];
        $groupedVehicles[$household_id][] = $vehicle;
    }
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Vehicles</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <a href="homeownerDashboard.php">
            <img src="assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>My Vehicles</h1>

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
     <a href="addVehicle.php" class="btn">Add New Vehicle</a>
        <?php if (empty($groupedVehicles)): ?>
            <p>No registered vehicles found.</p>
        <?php else: ?>
            <?php foreach ($groupedVehicles as $household_id => $vehicles): ?>
                <h2>Household ID: <?= htmlspecialchars($household_id) ?></h2>
                <table border="1">
                    <tr>
                        <th>Vehicle Type</th>
                        <th>Plate Number</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Color</th>
                        <th>Sticker Status</th>
                    </tr>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <?php
                        // Determine sticker renewal status
                        $sticker_status = htmlspecialchars($vehicle['sticker_status'] ?? 'No Request');
                        if ($sticker_status == 'Approved' && strtotime($vehicle['date_requested']) < strtotime('-1 year')) {
                            $sticker_status = 'For Renewal';
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($vehicle['vehicle_type']) ?></td>
                            <td><?= htmlspecialchars($vehicle['plate_number']) ?></td>
                            <td><?= htmlspecialchars($vehicle['vehicle_make']) ?></td>
                            <td><?= htmlspecialchars($vehicle['vehicle_model']) ?></td>
                            <td><?= htmlspecialchars($vehicle['vehicle_color']) ?></td>
                            <td><?= $sticker_status ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
        
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
