<?php
include '../../includes/db/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $household_id = $_POST['household_id'];
        $vehicle_type = $_POST['vehicle_type'];
        $plate_number = $_POST['plate_number'];
        $sticker_number = $_POST['sticker_number'];
        $sticker_type = $_POST['sticker_type'];
        $date_bought = $_POST['date_bought'];
        $vehicle_make = $_POST['vehicle_make'];
        $vehicle_model = $_POST['vehicle_model'];
        $vehicle_color = $_POST['vehicle_color'];
        $registration_status = $_POST['registration_status'];
        
        // Handle file upload
        $vehicle_qr = '';
        if (!empty($_FILES['vehicle_qr']['name'])) {
            $target_dir = "../uploads/vehicle_qr/";
            $vehicle_qr = basename($_FILES['vehicle_qr']['name']);
            $target_file = $target_dir . $vehicle_qr;
            move_uploaded_file($_FILES['vehicle_qr']['tmp_name'], $target_file);
        }

        $query = "INSERT INTO vehicles (household_id, vehicle_type, plate_number, sticker_number, sticker_type, date_bought, vehicle_make, vehicle_model, vehicle_color, registration_status, vehicle_qr) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$household_id, $$vehicle_type, $plate_number, $sticker_number, $sticker_type, $date_bought, $vehicle_make, $vehicle_model, $vehicle_color, $registration_status, $vehicle_qr]);
        
        echo "<script>alert('Vehicle added successfully!'); window.location.href='vehiclesList.php';</script>";
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        <h1>Add Vehicle</h1>
        <a href="vehiclesList.php">Back to Vehicles List</a>
    </div>

        
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="household_id">Household ID:</label>
            <input type="number" name="household_id" required>

            <label for="vehicle_type">Vehicle Type:</label>
            <input type="text" name="vehicle_type" required>
            
            <label for="plate_number">Plate Number:</label>
            <input type="text" name="plate_number" required>

            <label for="sticker_number">Sticker Number:</label>
            <input type="text" name="sticker_number" required>

            <label for="sticker_type">Sticker Type:</label>
            <select name="sticker_type" required>
                <option value="Homeowner">Homeowner</option>
                <option value="Renter">Renter</option>
                <option value="Tenant">Tenant</option>
            </select>

            <label for="date_bought">Date Bought:</label>
            <input type="date" name="date_bought" required>

            <label for="vehicle_make">Vehicle Make:</label>
            <input type="text" name="vehicle_make" required>

            <label for="vehicle_model">Vehicle Model:</label>
            <input type="text" name="vehicle_model" required>

            <label for="vehicle_color">Vehicle Color:</label>
            <input type="text" name="vehicle_color" required>

            <label for="registration_status">Registration Status:</label>
            <select name="registration_status" required>
                <option value="Active">Active</option>
                <option value="Expired">Expired</option>
            </select>

            <label for="vehicle_qr">Vehicle QR Code:</label>
            <input type="file" name="vehicle_qr" accept="image/*">

            <button type="submit">Add Vehicle</button>
        </form>
        
    
</body>
</html>
