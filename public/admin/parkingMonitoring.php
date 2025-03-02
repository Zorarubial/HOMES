<?php
include '../../includes/db/db_config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define max parking capacity
    $maxParkingSlots = 100;
    
    // Count vehicles currently parked
    $query = "SELECT 
                (SELECT COUNT(*) FROM gate_logs WHERE log_type = 'Ingress') - 
                (SELECT COUNT(*) FROM gate_logs WHERE log_type = 'Egress') AS occupied_spaces";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $occupiedSpaces = $result['occupied_spaces'] ?? 0;
    $availableSpaces = max(0, $maxParkingSlots - $occupiedSpaces);
    
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Monitoring</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Logo linked to Admin Dashboard -->
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Parking Monitoring</h1>

        <div class="profile-notifications">
            <!-- Bell Icon for Notifications -->
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <!-- Profile Menu -->
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Admin Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.html">My Profile</a>
                    <a href="#" onclick="confirmLogout()">Log Out</a>
                </div>
            </div>
        </div>
    </div>
    
        
        <div class="parking-status">
            <h2>Available Parking Slots: <span id="availableSpaces"><?php echo $availableSpaces; ?> / 100</span></h2>
        </div>

    <script>
        function updateParkingStatus() {
            $.ajax({
                url: 'parkingStatus.php', // Separate script to fetch live updates
                method: 'GET',
                success: function(response) {
                    $('#availableSpaces').text(response);
                }
            });
        }
        
        setInterval(updateParkingStatus, 5000); // Refresh every 5 seconds
    </script>
</body>
</html>
