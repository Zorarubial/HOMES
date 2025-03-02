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

    // Fetch user's reservations
    $query = "SELECT reservation_id, amenity_type, reservation_date, start_time, end_time, status, payment_amount, amenity_receipt 
              FROM amenities_reservations 
              WHERE household_id = (SELECT household_id FROM system_users WHERE user_id = ?) 
              ORDER BY reservation_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];
    try {
        $cancelQuery = "DELETE FROM amenities_reservations WHERE reservation_id = ? AND household_id = (SELECT household_id FROM system_users WHERE user_id = ?) AND status NOT IN ('Paid', 'Approved')";
        $cancelStmt = $pdo->prepare($cancelQuery);
        $cancelStmt->execute([$reservation_id, $user_id]);
        header("Location: myReservations.php");
        exit();
    } catch (PDOException $e) {
        die("Error cancelling reservation: " . htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>My Reservations</h1>
    </div>
        <a href="reservationAdd.php" class="btn">Add Reservation</a>

        <table>

            <tr>
                <th>Amenity</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Receipt</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($reservations as $res): ?>
                <tr>
                    <td><?= htmlspecialchars($res['amenity_type']) ?></td>
                    <td><?= htmlspecialchars($res['reservation_date']) ?></td>
                    <td><?= htmlspecialchars($res['start_time']) ?></td>
                    <td><?= htmlspecialchars($res['end_time']) ?></td>
                    <td><?= htmlspecialchars($res['status']) ?></td>
                    <td><?= $res['payment_amount'] ? '₱' . number_format($res['payment_amount'], 2) : 'Not Paid' ?></td>
                    <td>
                        <?php if ($res['amenity_receipt']): ?>
                            <a href="uploads/<?= htmlspecialchars($res['amenity_receipt']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            No Receipt
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!in_array($res['status'], ['Paid', 'Approved'])): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
                                <button type="submit" name="cancel_reservation">Cancel</button>
                            </form>
                            <a href="editReservation.php?id=<?= $res['reservation_id'] ?>">Modify</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
  
</body>
</html>
