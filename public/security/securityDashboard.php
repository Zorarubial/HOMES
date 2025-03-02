<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'security') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://kit.fontawesome.com/ab77a77ccc.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h1>Security Dashboard</h1>
        <div class="profile-notifications">
            
        
            <!-- Bell Icon for Notifications -->
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <div class="profile-menu">
                <img src="../assets/img/<?php echo $profilePic; ?>" alt="User Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="logout.php">Log Out</a>
                </div>
            
        </div>
        </div>
     </div>

    <div class="menu-container">
        <div class="menu-card">
            <h2>Visitor Entry</h2>
            <p>Manage and log visitor entries.</p>
            <a href="visitorEntry.php">Go to Visitor Entry</a>
        </div>
        <div class="menu-card">
            <h2>Security Profile</h2>
            <p>View and update your profile information.</p>
            <a href="securityProfile.php">Go to Profile</a>
        </div>
        <div class="menu-card">
            <h2>Gate Logs</h2>
            <p>View logs of visitors and vehicles entering the subdivision.</p>
            <a href="gateLogs.php">Go to Gate Logs</a>
        </div>
    </div>


    <script>
    //logout
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
