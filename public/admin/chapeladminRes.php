<?php
include '../../includes/db/db_config.php';

// Fetch reservations for the specific amenity based on the page
$amenity = '';
if (basename($_SERVER['PHP_SELF']) == 'clubadminRes.php') {
    $amenity = 'Clubhouse';
} elseif (basename($_SERVER['PHP_SELF']) == 'courtadminRes.php') {
    $amenity = 'Court';
} elseif (basename($_SERVER['PHP_SELF']) == 'chapeladminRes.php') {
    $amenity = 'Chapel';
} elseif (basename($_SERVER['PHP_SELF']) == 'pooladminRes.php') {
    $amenity = 'Swimming Pool';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = "SELECT ar.reservation_id, h.household_id, ar.reservation_date, ar.start_time, ar.end_time, u.user_id AS custodian, ar.status 

              FROM amenities_reservations ar 
              JOIN households h ON ar.household_id = h.household_id 
              JOIN system_users u ON ar.custodian_id = u.user_id 
              WHERE ar.amenity_type = :amenity 
              ORDER BY ar.reservation_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':amenity', $amenity, PDO::PARAM_STR);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($amenity); ?> Reservations</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/icons/templogo.png">
</head>
<body>
    <div class="container">
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        <h1><?php echo htmlspecialchars($amenity); ?> Reservations</h1>
        <a href="amenitiesReservation.php">Back to Amenities </a>
     </div>
        
        
        <table>
            <thead>
                <tr>
                    <th>Household</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Custodian</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['household_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['custodian']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a href="approveReservation.php?id=<?php echo htmlspecialchars($row['reservation_id']); ?>">Approve</a> |
                            <a href="declineReservation.php?id=<?php echo htmlspecialchars($row['reservation_id']); ?>">Decline</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
   
</body>
</html>
