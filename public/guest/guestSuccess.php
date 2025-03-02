<?php
include '../../includes/db/db_config.php';

// Get visitor ID from URL
if (!isset($_GET['visit_id'])) {

    die("Invalid access!");
}

$visit_id = $_GET['visit_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve visitor details
    $stmt = $pdo->prepare("SELECT * FROM visitor_management WHERE visit_id = ?");
    $stmt->execute([$visit_id]);
    $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$visitor) {
        die("Visitor not found!");
    }

    // Generate QR Code (For now, just display the placeholder)
    $qr_code = $visitor['qr_code'] ?: "placeholder.png";

} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Adjust CSS path -->
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        img.qr-code {
            width: 200px;
            height: 200px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>🎉 Registration Successful!</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($visitor['visitor_name']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($visitor['contact_number']) ?></p>
        <p><strong>Date of Visit:</strong> <?= htmlspecialchars($visitor['date_of_visit']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($visitor['status']) ?></p>

        <h3>Your QR Code</h3>
        <img class="qr-code" src="../../uploads/<?= htmlspecialchars($qr_code) ?>" alt="QR Code">


        <br>
        <a class="btn" href="guestRegistration.php">Register Another Visitor</a>
    </div>

</body>
</html>
