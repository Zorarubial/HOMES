<?php
session_start();

// Check if the admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'homeowner') {
    header("Location: login.php");
    exit();
}

include('../includes/db/db_config.php'); // Ensure correct path

$user_id = $_SESSION['user_id'];
$type = $_SESSION['type'];

// Fetch user profile picture and name from system_users
$stmt = $pdo->prepare("
    SELECT u.profile_pic, u.first_name, u.last_name, h.homeowner_id 
    FROM system_users u
    LEFT JOIN households hh ON u.household_id = hh.household_id
    LEFT JOIN homeowners h ON hh.homeowner_id = h.homeowner_id
    WHERE u.user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Profile picture
$profilePic = !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'default-profile.png';

// Use system_users name by default
$firstName = $user['first_name'] ?? "User";
$lastName = $user['last_name'] ?? "";

// If homeowner_id exists, check homeowners table for a name override
if ($user['homeowner_id']) {
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM homeowners WHERE homeowner_id = ?");
    $stmt->execute([$user['homeowner_id']]);
    $homeowner = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($homeowner) {
        $firstName = $homeowner['first_name'] ?? $firstName;
        $lastName = $homeowner['last_name'] ?? $lastName;
    }
}

// Fetch announcements
$announcements = [];
$annStmt = $pdo->query("SELECT message FROM announcements ORDER BY created_at DESC LIMIT 5");
while ($row = $annStmt->fetch(PDO::FETCH_ASSOC)) {
    $announcements[] = $row['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Community</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>!</h1>
        <p>Your go-to platform for managing all your community needs.</p>

        <div class="profile-menu">
            <img src="assets/img/<?php echo $profilePic; ?>" alt="User Profile" class="profile-pic" id="profilePic">
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profile.php">My Profile</a>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </div>

    <div class="menu-container">
            <div class="menu-card">
                <i class="fas fa-home"></i>
                <a href="myHouseholds.php">Our Household</a>
                <p>My household details.</p>
            </div>
            <div class="menu-card">
                <i class="fas fa-receipt"></i>
                <a href="myMonthlyDues.php" class="btn">View Dues</a>
                <p>Check and settle your monthly dues.</p>
            </div>
            <div class="menu-card">
                <i class="fas fa-car"></i>
                <a href="myVehicles.php" class="btn">Manage Vehicles</a>
                <p>Manage your registered vehicles and parking.</p>               
            </div>
            <div class="menu-card">
                <i class="fas fa-parking"></i>
                <a href="myParking.php">My Parking Space</a>
                <p>Check real-time parking availability.</p>
            </div>
            
            <div class="menu-card">
                <i class="fas fa-calendar-check"></i>
                <a href="myReservations.php">Reservations</a>
                <p>Reserve community amenities: clubhouse, pool, court, gazebo.</p>
            </div>

            <!--
            <div class="menu-card">
                <i class="fas fa-user-shield"></i>
                <a href="myVisitor.php">My Visitor</a>
                <p>Manage visitors and check entry logs.</p>
            </div>
            <div class="menu-card">
                <i class="fas fa-bullhorn"></i>
                <a href="announcements.php">View Announcements</a>
                <p>Post and update community news.</p>
            </div>
            <div class="menu-card">
                <i class="fas fa-building"></i>
                <a href="settings.php">Settings</a>
                <p>Manage community-wide settings.</p>
            </div>          
            -->
    </div>

    <div class="sliding_updates">
        <div class="updates-content">
            <?php echo implode(" | ", array_map("htmlspecialchars", $announcements)); ?>
        </div>
    </div>

     <script>
        //logout and profile tab
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
