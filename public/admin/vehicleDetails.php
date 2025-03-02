<?php
include '../../includes/db/db_config.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$vehicle_id = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT v.*, 
                     h.block, h.lot, h.street 
              FROM vehicles v
              JOIN households h ON v.household_id = h.household_id
              WHERE v.vehicle_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehicle) {
        die("Vehicle not found");
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
    <title>Vehicle Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Vehicle Details</h1>
        <a href="vehiclesList.php">Back to Vehicles List</a>
    </div>

    <table border="1">
        <tr><th>Vehicle ID</th><td><?= htmlspecialchars($vehicle['vehicle_id']) ?></td></tr>
        <tr><th>Plate Number</th><td><?= htmlspecialchars($vehicle['plate_number']) ?></td></tr>
        <tr><th>Sticker Number</th><td><?= htmlspecialchars($vehicle['sticker_number']) ?></td></tr>
        <tr><th>Sticker Type</th><td><?= htmlspecialchars($vehicle['sticker_type']) ?></td></tr>
        <tr><th>Date Bought</th><td><?= htmlspecialchars($vehicle['date_bought']) ?></td></tr>
        <tr><th>QR Code</th><td><img src="../assets/qrcodes/<?= htmlspecialchars($vehicle['vehicle_qr']) ?>" alt="QR Code" width="100"></td></tr>
        <tr><th>Household Address</th><td>Block <?= htmlspecialchars($vehicle['block']) ?>, Lot <?= htmlspecialchars($vehicle['lot']) ?>, <?= htmlspecialchars($vehicle['street']) ?></td></tr>
    </table>

    <h2>Update Sticker Info</h2>
    <form action="updateVehicle.php" method="POST">
        <input type="hidden" name="vehicle_id" value="<?= $vehicle['vehicle_id'] ?>">
        
        <label for="sticker_type">Sticker Type:</label>
        <select name="sticker_type" required>
            <option value="Homeowner" <?= $vehicle['sticker_type'] == 'Homeowner' ? 'selected' : '' ?>>Homeowner</option>
            <option value="Renter/Tenant" <?= $vehicle['sticker_type'] == 'Renter/Tenant' ? 'selected' : '' ?>>Renter/Tenant</option>
        </select>
        
        <label for="date_bought">Date Bought:</label>
        <input type="date" name="date_bought" value="<?= $vehicle['date_bought'] ?>" required>
        
        <button type="submit">Update</button>
    </form>
</body>
</html>
