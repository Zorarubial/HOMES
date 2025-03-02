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
    
    // Fetch reservations
    $query = "SELECT ar.reservation_id, h.household_id, ar.reservation_date, 
                 ar.start_time, ar.end_time, u.user_id AS custodian, 
                 ar.status, ar.payment_amount 
          FROM amenities_reservations ar 
          JOIN households h ON ar.household_id = h.household_id 
          LEFT JOIN system_users u ON ar.custodian_id = u.user_id 
          WHERE ar.amenity_type = :amenity 
          ORDER BY ar.reservation_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':amenity', $amenity, PDO::PARAM_STR);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch households for dropdown
    $householdStmt = $pdo->query("SELECT household_id FROM households");
    $households = $householdStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch custodians for dropdown
    $custodianStmt = $pdo->query("SELECT user_id FROM system_users WHERE type = 'Custodian'");
    $custodians = $custodianStmt->fetchAll(PDO::FETCH_ASSOC);
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
</head>
<body>
    <div class="container">
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        <h1><?php echo htmlspecialchars($amenity); ?> Reservations</h1>
        <a href="amenitiesReservation.php">Back to Amenities </a>  
    </div>

    <h2>Create New Reservation</h2>
    <form action="addReservation.php" method="POST">
        <input type="hidden" name="amenity_type" value="<?php echo htmlspecialchars($amenity); ?>">
        
        <label for="household_id">Household ID:</label>
        <select name="household_id" required>
            <?php foreach ($households as $household) { ?>
                <option value="<?php echo $household['household_id']; ?>">
                    <?php echo htmlspecialchars($household['household_id']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="reservation_date">Date:</label>
        <input type="date" name="reservation_date" required>

        <label for="start_time">Start Time:</label>
        <input type="time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="time" name="end_time" required>

        <label for="custodian_id">Custodian:</label>
        <select name="custodian_id">
            <option value="">None</option>
            <?php foreach ($custodians as $custodian) { ?>
                <option value="<?php echo $custodian['user_id']; ?>">

                    <?php echo htmlspecialchars($custodian['user_id']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="status">Status:</label>
        <select name="status">
            <option value="Pending">Pending</option>
            <option value="Paid">Paid</option>
            <option value="Approved">Approved</option>
        </select>

        <button type="submit">Create Reservation</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Household</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Custodian</th>
                <th>Payment Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['household_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                    <td><?php echo isset($row['custodian']) ? htmlspecialchars($row['custodian']) : 'N/A'; ?></td>
                    <td><?php echo htmlspecialchars(number_format($row['payment_amount'], 2)); ?></td>
                    <td style="color: <?php echo ($row['status'] === 'Paid') ? 'green' : 'black'; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </td>
                    <td>
                        <a href="approveReservation.php?id=<?php echo htmlspecialchars($row['reservation_id']); ?>">Approve</a> |
                        <a href="declineReservation.php?id=<?php echo htmlspecialchars($row['reservation_id']); ?>">Decline</a>

                        <?php if ($row['status'] !== 'Paid') { ?>
                            | <a href="markAsPaid.php?id=<?php echo htmlspecialchars($row['reservation_id']); ?>" style="color: green;">Mark as Paid</a>
                        <?php } ?>
                        
                        <?php if ($row['status'] !== 'Cancelled') { ?>
                        <a href="cancelReservation.php?id=<?php echo $row['reservation_id']; ?>" 
                           onclick="return confirm('Are you sure you want to cancel this reservation?');">
                           Cancel
                        </a>
                    <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
