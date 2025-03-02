<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db/db_config.php';

$user_id = $_SESSION['user_id'];

// Fetch user's households
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $householdQuery = "SELECT h.household_id, h.block, h.lot, h.street 
                       FROM households h
                       JOIN homeowners ho ON h.homeowner_id = ho.homeowner_id
                       WHERE ho.user_id = ?";
    $householdStmt = $pdo->prepare($householdQuery);
    $householdStmt->execute([$user_id]);
    $households = $householdStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $household_id = $_POST['household_id'];
        $vehicle_type = $_POST['vehicle_type'];
        $plate_number = $_POST['plate_number'];
        $vehicle_make = $_POST['vehicle_make'];
        $vehicle_model = $_POST['vehicle_model'];
        $vehicle_color = $_POST['vehicle_color'];
        $date_bought = $_POST['date_bought'];
        
        // Insert vehicle
        $insertQuery = "INSERT INTO vehicles (household_id, vehicle_type, plate_number, vehicle_make, vehicle_model, vehicle_color, date_bought) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([$household_id, $vehicle_type, $plate_number, $vehicle_make, $vehicle_model, $vehicle_color, $date_bought]);

        $vehicle_id = $pdo->lastInsertId(); // Get the inserted vehicle's ID

        // Insert sticker request automatically
        $stickerQuery = "INSERT INTO sticker_requests (vehicle_id, user_id, sticker_type, status) 
                         VALUES (?, ?, 'Pending', 'Pending')";
        $stickerStmt = $pdo->prepare($stickerQuery);
        $stickerStmt->execute([$vehicle_id, $user_id]);

        header("Location: myVehicles.php");
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
    <title>Millfora Homes</title>
    <style>
        body {
            background-color: #ECECA4;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            justify-content: flex-start;
            font-family: 'Garamond', sans-serif;
        }

        .header {
            width: 100%;
            background: linear-gradient(to right,hsl(110, 44.40%, 64.70%), #2B7F49);
            padding: 25px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: start;
            font-size: 35px;
            font-weight: bold;
        }

        .header a {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 35px;
            font-weight: bold;
        }

        .header img {
            height: 60px;
            margin-right: 15px;
            margin-left: 10px;
        }

        .back-button {
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            color: white;
            background-color: #85BE91;
            padding: 5px 10px; 
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            font-weight: bold;
            font-size: 12px; 
            position: absolute;
            right: 20px; 
            border: none; 
            cursor: pointer; 
        }

        .back-button:hover {
            background-color: #67A473; 
        }

        .container {
            background-color: #FFFFFF;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            margin: 2rem auto;
        }

        h1 {
            color: #287F49;
            text-align: center;
            margin-bottom: 1.5rem;
            font-family: 'Inter', sans-serif;
        }

        form {
            display: flex;
            flex-direction: column;
            font-family: 'Inter', sans-serif;
        }

        label {
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #5D591E;
        }

        select, input {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #AEDEC2;
            border-radius: 5px;
            font-size: 1rem;
            color: #161A07;
            background-color: #FFFFFF;
        }

        select:focus, input:focus {
            border-color: #67A473;
            outline: none;
        }

        button {
            background-color: #67A473;
            color: #FFFFFF;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #287F49;
        }

        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <a href="myVehicles.php" class="back-button">Back</a>
        <img src="assets/icons/templogo.png" alt="Logo">
        <a href="homeownerDashboard.php">Milfora Homes</a>
    </div>

    <!-- Form Container -->
    <div class="container">
        <h1>Register New Vehicle</h1>
        <form method="POST" action="">
            <!-- Household Selection -->
            <div class="form-group">
                <label for="household_id">Select Household:</label>
                <select name="household_id" required>
                    <?php foreach ($households as $household): ?>
                        <option value="<?= htmlspecialchars($household['household_id']) ?>">
                            Block <?= htmlspecialchars($household['block']) ?>, Lot <?= htmlspecialchars($household['lot']) ?>, <?= htmlspecialchars($household['street']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Vehicle Type -->
            <div class="form-group">
                <label for="vehicle_type">Vehicle Type:</label>
                <select name="vehicle_type" required>
                    <option value="Car">Car</option>
                    <option value="Motorcycle">Motorcycle</option>
                    <option value="E-bike">E-bike</option>
                    <option value="Truck">Truck</option>
                    <option value="Van">Van</option>
                    <option value="Others">Others</option>
                </select>
            </div>

            <!-- Plate Number -->
            <div class="form-group">
                <label for="plate_number">Plate Number:</label>
                <input type="text" name="plate_number" required>
            </div>

            <!-- Vehicle Make -->
            <div class="form-group">
                <label for="vehicle_make">Vehicle Make:</label>
                <input type="text" name="vehicle_make" required>
            </div>

            <!-- Vehicle Model -->
            <div class="form-group">
                <label for="vehicle_model">Vehicle Model:</label>
                <input type="text" name="vehicle_model" required>
            </div>

            <!-- Vehicle Color -->
            <div class="form-group">
                <label for="vehicle_color">Vehicle Color:</label>
                <input type="text" name="vehicle_color" required>
            </div>

            <!-- Date Bought -->
            <div class="form-group">
                <label for="date_bought">Date Bought:</label>
                <input type="date" name="date_bought" required>
            </div>

            <!-- Submit Button -->
            <button type="submit">Register Vehicle</button>
        </form>
    </div>
</body>
</html>