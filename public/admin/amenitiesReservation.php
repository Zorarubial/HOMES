<?php
include '../../includes/db/db_config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch amenities reservation statuses
    $query = "SELECT amenity_type, status FROM amenities_reservations GROUP BY amenity_type, status";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert amenities into an associative array for easy lookup
    $amenityStatus = [];
    foreach ($amenities as $amenity) {
        $amenityStatus[$amenity['amenity_type']] = $amenity['status'];
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
    <title>Amenities Reservation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/png" href="../assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Amenities Reservation</h1>
    </div>

    <div class="menu-container">
        <?php
        $amenitiesList = [
            'Clubhouse' => 'clubadminRes.php',
            'Court' => 'courtadminRes.php',
            'Chapel' => 'chapeladminRes.php',
            'Swimming Pool' => 'pooladminRes.php'
        ];
        
        foreach ($amenitiesList as $name => $link):
            $status = $amenityStatus[$name] ?? 'Available';
            $isDisabled = ($status !== 'Available');
        ?>
        <div class="menu-card <?php echo $isDisabled ? 'disabled' : ''; ?>">
            <i class="<?php echo $name === 'Clubhouse' ? 'fas fa-users-cog' : ($name === 'Court' ? 'fas fa-basketball-ball' : ($name === 'Chapel' ? 'fas fa-church' : 'fas fa-swimmer')); ?>"></i>
            <a href="<?php echo $link; ?>" class="<?php echo $isDisabled ? 'hidden-link' : ''; ?>"><?php echo $name; ?></a>
            <span class="status-text">(<?php echo $status; ?>)</span>
            <p><?php echo ($status === 'Available') ? "Manage and reserve the $name." : "Currently $status."; ?></p>
            <button class="toggle-status" data-amenity="<?php echo $name; ?>" data-status="<?php echo $status; ?>">
                <?php echo ($status === 'Unavailable') ? 'Enable' : 'Disable'; ?>
            </button>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.querySelectorAll(".toggle-status").forEach(button => {
            button.addEventListener("click", function() {
                const amenity = this.getAttribute("data-amenity");
                const currentStatus = this.getAttribute("data-status");
                const newStatus = (currentStatus === "Unavailable") ? "Available" : "Unavailable";
                
                fetch("updateAmenityStatus.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `amenity=${encodeURIComponent(amenity)}&status=${encodeURIComponent(newStatus)}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === "success") {
                        location.reload();
                    } else {
                        alert("Failed to update status.");
                    }
                });
            });
        });
    </script>

    <style>
        .disabled .hidden-link {
            pointer-events: none;
            opacity: 0.5;
            text-decoration: line-through;
        }
        .status-text {
            color: red;
            font-weight: bold;
        }
        .toggle-status {
            margin-top: 10px;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
    </style>
</body>
</html>
