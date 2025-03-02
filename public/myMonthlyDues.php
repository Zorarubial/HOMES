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

    // Fetch all household IDs for the logged-in homeowner
    $query = "SELECT h.household_id
              FROM households h
              JOIN homeowners ho ON h.homeowner_id = ho.homeowner_id
              WHERE ho.user_id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $households = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$households) {
        die("No households found.");
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
    <title>My Monthly Dues</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <a href="homeownerDashboard.php">
            <img src="assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        <h1>My Monthly Dues</h1>
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

    <?php foreach ($households as $household) : ?>
        <h2>Household ID: <?= htmlspecialchars($household['household_id']) ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Amount Due</th>
                    <th>Amount Paid</th>
                    <th>Payment Status</th>
                    <th>Due Date</th>
                    <th>Payment Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $query = "SELECT md.payment_id, md.amount_due, md.amount_paid, md.payment_status, md.due_date, md.payment_date
                              FROM monthly_dues md
                              WHERE md.household_id = ?";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$household['household_id']]);
                    $dues = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    die("Database error: " . htmlspecialchars($e->getMessage()));
                }
                ?>
                <?php foreach ($dues as $due) : ?>
                    <tr>
                        <td><?= number_format($due['amount_due'], 2) ?></td>
                        <td><?= number_format($due['amount_paid'], 2) ?></td>
                        <td><?= htmlspecialchars($due['payment_status']) ?></td>
                        <td><?= htmlspecialchars($due['due_date']) ?></td>
                        <td><?= $due['payment_date'] ? htmlspecialchars($due['payment_date']) : 'N/A' ?></td>
                        <td>
                            <?php if ($due['payment_status'] !== 'Paid') : ?>
                                <a href="submitPayment.php?payment_id=<?= $due['payment_id'] ?>">Upload proof of payment</a>
                            <?php else : ?>
                                <span>Paid</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

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
