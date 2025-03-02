<?php
include '../../includes/db/db_config.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$reservation_id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch existing reservation details
    $query = "SELECT payment_amount, amenity_receipt FROM amenities_reservations WHERE reservation_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        die("Reservation not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $payment_amount = $_POST['payment_amount'];
        
        // Handle receipt upload
        if (!empty($_FILES['amenity_receipt']['name'])) {
            $targetDir = "../../uploads/receipts/";
            $fileName = basename($_FILES["amenity_receipt"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            move_uploaded_file($_FILES["amenity_receipt"]["tmp_name"], $targetFilePath);
        } else {
            $fileName = $reservation['amenity_receipt']; // Keep the existing receipt if no new upload
        }

        // Update payment status and amount
        $updateQuery = "UPDATE amenities_reservations SET status = 'Paid', payment_amount = ?, amenity_receipt = ? WHERE reservation_id = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([$payment_amount, $fileName, $reservation_id]);

        header("Location: courtadminRes.php"); // Redirect back to reservations page
        exit();
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
    <title>Mark as Paid</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Mark Reservation as Paid</h1>
        <a href="courtadminRes.php">Back to Reservations</a>
    </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="payment_amount">Payment Amount:</label>
            <input type="number" step="0.01" name="payment_amount" value="<?= htmlspecialchars($reservation['payment_amount']) ?>" required>

            <label for="amenity_receipt">Upload Receipt:</label>
            <input type="file" name="amenity_receipt" accept="image/*,application/pdf">

            <?php if (!empty($reservation['amenity_receipt'])): ?>
                <p>Existing Receipt: <a href="../../uploads/receipts/<?= htmlspecialchars($reservation['amenity_receipt']) ?>" target="_blank">View Receipt</a></p>
            <?php endif; ?>

            <button type="submit">Confirm Payment</button>
        </form>
        
  
</body>
</html>
