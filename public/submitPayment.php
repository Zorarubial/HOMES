<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db/db_config.php');

$user_id = $_SESSION['user_id'];

// Fetch household ID associated with the homeowner
$query = "SELECT h.household_id FROM households h
          JOIN homeowners ho ON h.homeowner_id = ho.homeowner_id
          WHERE ho.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$household = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$household) {
    die("Error: Household not found.");
}

$household_id = $household['household_id'];

// Fetch unpaid monthly dues
$query = "SELECT * FROM monthly_dues WHERE household_id = :household_id AND payment_status = 'Unpaid'";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
$stmt->execute();
$dues = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['payment_id'];
    $amount_paid = $_POST['amount_paid'];
    $payment_date = date('Y-m-d');

    // File upload handling
    $target_dir = "../uploads/payments/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $receipt_file = $_FILES['receipt']['name'];
    $receipt_tmp = $_FILES['receipt']['tmp_name'];
    $receipt_path = $target_dir . basename($receipt_file);

    if (move_uploaded_file($receipt_tmp, $receipt_path)) {
        // Store in database
        $query = "UPDATE monthly_dues 
                  SET amount_paid = :amount_paid, payment_status = 'Pending', payment_date = :payment_date
                  WHERE payment_id = :payment_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':amount_paid', $amount_paid, PDO::PARAM_STR);
        $stmt->bindParam(':payment_date', $payment_date, PDO::PARAM_STR);
        $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo "<script>alert('Payment submitted successfully! Pending admin approval.'); window.location.href='myMonthlydues.php';</script>";
        } else {
            echo "<script>alert('Error submitting payment. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('File upload failed.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Proof of Payment</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Submit Proof of Payment</h1>
        <a href="myMonthlydues.php">Back to My Monthly Dues</a>
        
    </div>
        <form action="submitPayment.php" method="POST" enctype="multipart/form-data">
            <label for="payment_id">Select Due:</label>
            <select name="payment_id" required>
                <?php foreach ($dues as $due): ?>
                    <option value="<?= $due['payment_id'] ?>">Due Date: <?= htmlspecialchars($due['due_date']) ?> - ₱<?= htmlspecialchars($due['amount_due']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="amount_paid">Amount Paid:</label>
            <input type="number" name="amount_paid" min="1" step="0.01" required>

            <label for="receipt">Upload Receipt:</label>
            <input type="file" name="receipt" accept="image/*,application/pdf" required>

            <button type="submit">Submit Proof</button>
        </form>
  
</body>
</html>
