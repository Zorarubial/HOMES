<?php
session_start();
include '../../includes/db/db_config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitor_name = $_POST['visitor_name'];
    $contact_number = $_POST['contact_number'];
    $purpose = $_POST['purpose'];

    // Validate and format date_of_visit
    if (!empty($_POST['date_of_visit'])) {
        $date_of_visit = date('Y-m-d H:i:s', strtotime($_POST['date_of_visit']));
    } else {
        $date_of_visit = null;
    }

    // Get Block, Lot, and Street
    $block = $_POST['block'];
    $lot = $_POST['lot'];
    $street = $_POST['street'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch the matching household_id
        $query = "SELECT household_id FROM households WHERE block = ? AND lot = ? AND street = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$block, $lot, $street]);
        $household = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$household) {
            throw new Exception("The entered address does not match any existing household.");
        }

        $household_id = $household['household_id'];

        // Insert into visitor_management table
        $query = "INSERT INTO visitor_management (qr_code, household_id, visitor_name, contact_number, date_of_visit, status, purpose) 
                  VALUES (?, ?, ?, ?, ?, 'Pending', ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(["placeholder.png", $household_id, $visitor_name, $contact_number, $date_of_visit, $purpose]);

        // Redirect to success page
        header("Location: guestSuccess.php?visit_id=" . $pdo->lastInsertId());

        exit();
    } catch (Exception $e) {
        $message = "<p class='text-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milflora Homes - Guest Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/icons/templogo.png">
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            let dateInput = document.querySelector("input[name='date_of_visit']");
            let today = new Date().toISOString().slice(0, 16); // Get current date-time in YYYY-MM-DDTHH:MM format
            dateInput.setAttribute("min", today); // Disable past dates
        });
    </script>
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
        .header img {
            height: 60px;
            margin-right: 10px;
        }
        .back-button {
            text-decoration: none;
            color: white;
            background-color: #85BE91;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            font-weight: bold;
            font-size: 12px;
            position: absolute;
            right: 100px;
        }
        .back-button i {
            margin-right: 5px;
        }
        .back-button:hover {
            background-color: red;
        }
        .form-container {
            background: #2B7F49;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            margin-top: 50px;
            color: white;
            font-size: 14px;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-family: 'Inter', sans-serif;
        }
        .form-label {
            font-family: 'Inter', sans-serif;
        }
        .form-control {
            font-family: 'Inter', sans-serif;
            background-color: #85BE91;
            border: none;
            color: black;
            font-size: 12px;
        }
        .form-control::placeholder {
            color: #EBEFE0;
        }
        .btn-register {
            background-color: #161A07;
            border: none;
            width: 100%;
            color: white;
            font-size: 13px;
        }
        .btn-register:hover {
            background-color: #161A07;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="../assets/icons/templogo.png" alt="Milflora Homes Logo">
            Milflora Homes
        </div>
        <a href="../index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="form-container">
        <h2>Guest Registration</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="visitor_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date and Time of Visit</label>
                <input type="datetime-local" name="date_of_visit" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Purpose</label>
                <select name="purpose" class="form-control" required>
                    <option value="Visiting Household">Visiting Household</option>
                    <option value="Using Amenity">Using Amenity</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Block</label>
                <select name="block" class="form-control" required>
                    <option value="" disabled selected>Select Block</option>
                    <?php
                    $query = "SELECT DISTINCT block FROM households ORDER BY block ASC";
                    $result = $pdo->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['block']}'>Block {$row['block']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Lot</label>
                <select name="lot" class="form-control" required>
                    <option value="" disabled selected>Select Lot</option>
                    <?php
                    $query = "SELECT DISTINCT lot FROM households ORDER BY lot ASC";
                    $result = $pdo->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['lot']}'>Lot {$row['lot']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Street</label>
                <select name="street" class="form-control" required>
                    <option value="" disabled selected>Select Street</option>
                    <?php
                    $query = "SELECT DISTINCT street FROM households ORDER BY street ASC";
                    $result = $pdo->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['street']}'>{$row['street']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-register mb-3">Register</button>
        </form>

        <?php if (!empty($qr_code)): ?>
            <div class="qr-code">
                <h3>Your QR Code</h3>
                <img src="../../uploads/qrcodes/<?= $qr_code ?>" alt="QR Code" width="200">
                <p>Show this QR code at the gate for entry.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let today = new Date().toISOString().split('T')[0];
        document.getElementById("dateInput").setAttribute("min", today);
    });
</script>
</body>
</html>
