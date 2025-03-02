<?php
session_start();

// Check if the admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Logo linked to Admin Dashboard -->
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Hi, Admin</h1>

        <div class="profile-notifications">
            <!-- Bell Icon for Notifications -->
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <!-- Profile Menu -->
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Admin Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.php">My Profile</a>
                    <a href="#" onclick="confirmLogout()">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="menu-container">
        <div class="menu-card">
            <i class="fas fa-home"></i>
            <a href="householdsList.php">Households</a>
            <p>Manage and view all registered households.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-users"></i>
            <a href="homeownersList.php">Homeowners</a>
            <p>List of homeowners with profile details.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-car"></i>
            <a href="vehiclesList.php">Vehicles</a>
            <p>Track registered vehicles in the community.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-user-shield"></i>
            <a href="visitorLogs.php">Visitor Management</a>
            <p>Monitor visitor entries and security logs.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-parking"></i>
            <a href="parkingMonitoring.php">Parking Spaces</a>
            <p>Check parking availability in real-time.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-calendar-check"></i>
            <a href="amenitiesReservation.php">Reservations</a>
            <p>Reserve and manage community amenities.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-coins"></i>
            <a href="treasury.php">Treasury</a>
            <p>Manage finances, dues, and expenses.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-user-tie"></i>
            <a href="personnelList.php">Personnel</a>
            <p>Manage staff and personnel details.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-chart-bar"></i>
            <a href="reports.php">Reports & Analytics</a>
            <p>Generate reports and view analytics.</p>
        </div>
        <div class="menu-card">
            <i class="fas fa-chart-bar"></i>
            <a href="maintenance.php">Maintenance</a>
            <p>Generate reports and view analytics.</p>
        </div>
        <!-- <div class="menu-card">
            <i class="fas fa-bullhorn"></i>
            <a href="announce.php">Create Announcement</a>
            <p>Generate community announcement and updates.</p>
        </div> -->
    </div>


    <script>
        // for profile and logout dropdown
        document.addEventListener("DOMContentLoaded", function () {
            const profilePic = document.getElementById("profilePic");
            const dropdownMenu = document.getElementById("dropdownMenu");

            // Toggle dropdown on profile picture click
            profilePic.addEventListener("click", function (event) {
                event.stopPropagation(); // Prevent immediate closing when clicking profile picture
                dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
            });

            // Close dropdown when clicking anywhere else
            document.addEventListener("click", function () {
                dropdownMenu.style.display = "none";
            });

            // Prevent closing when clicking inside the dropdown menu
            dropdownMenu.addEventListener("click", function (event) {
                event.stopPropagation();
            });
        });

        function confirmLogout() {
            let confirmAction = confirm("Are you sure you want to log out?");
            if (confirmAction) {
                window.location.href = "../logout.php"; // Redirect to logout.php if confirmed
            }
        }    
    </script>
</body>
</html>
