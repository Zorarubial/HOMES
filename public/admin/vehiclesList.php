<?php
include '../../includes/db/db_config.php';

$sort = $_GET['sort'] ?? 'vehicle_id ASC';
$search = $_GET['search'] ?? '';
$filterColumn = $_GET['filterColumn'] ?? '';
$filterValue = $_GET['filterValue'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT v.vehicle_id, v.vehicle_type, v.plate_number, v.sticker_number, 
                     v.sticker_type, v.date_bought, v.vehicle_qr, h.household_id
              FROM vehicles v
              JOIN households h ON v.household_id = h.household_id
              WHERE (v.plate_number LIKE :search)";

    if (!empty($filterColumn) && !empty($filterValue)) {
        $query .= " AND $filterColumn LIKE :filterValue";
    }

    $validSortColumns = ['vehicle_id ASC', 'vehicle_id DESC', 'vehicle_type ASC', 'vehicle_type DESC', 'date_bought ASC', 'date_bought DESC'];
    if (in_array($sort, $validSortColumns)) {
        $query .= " ORDER BY $sort";
    } else {
        $query .= " ORDER BY vehicle_id ASC";
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    if (!empty($filterColumn) && !empty($filterValue)) {
        $stmt->bindValue(':filterValue', "%$filterValue%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicles List</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Vehicles List</h1>

        <div class="profile-notifications">
            <i class="fas fa-bell" id="notificationIcon"></i>
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Admin Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.php">My Profile</a>
                    <a href="#" onclick="confirmLogout()">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <div class="controls">
        <label for="sort">Sort By:</label>
        <select id="sort" name="sort">
            <option value="vehicle_id ASC">Vehicle ID (Ascending)</option>
            <option value="vehicle_id DESC">Vehicle ID (Descending)</option>
            <option value="vehicle_type ASC">Vehicle Type (A-Z)</option>
            <option value="vehicle_type DESC">Vehicle Type (Z-A)</option>
            <option value="date_bought ASC">Date Bought (Oldest First)</option>
            <option value="date_bought DESC">Date Bought (Newest First)</option>
        </select>
        <button id="sortBtn"><i class="fas fa-sort"></i> Sort</button>
        
        <input type="text" id="search" placeholder="Search Plate Number...">
        <button id="searchBtn"><i class="fas fa-search"></i> Search</button>
        
        <label for="filterColumn">Filter By:</label>
        <select id="filterColumn">
            <option value="vehicle_type">Vehicle Type</option>
            <option value="sticker_type">Sticker Type</option>
        </select>
        <input type="text" id="filterValue" placeholder="Enter value...">
        <button id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <table border="1">
        <thead>
            <tr>
                <th>Vehicle ID</th>
                <th>Vehicle Type</th>
                <th>Plate Number</th>
                <th>Sticker Number</th>
                <th>Sticker Type</th>
                <th>Date Bought</th>
                <th>QR Code</th>
                <th>Household ID</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vehicles as $vehicle): ?>
                <tr>
                    <td><a href='vehicleDetails.php?id=<?= htmlspecialchars($vehicle['vehicle_id']) ?>'><?= htmlspecialchars($vehicle['vehicle_id']) ?></a></td>
                    <td><?= htmlspecialchars($vehicle['vehicle_type']) ?></td>
                    <td><?= htmlspecialchars($vehicle['plate_number']) ?></td>
                    <td><?= htmlspecialchars($vehicle['sticker_number']) ?></td>
                    <td><?= htmlspecialchars($vehicle['sticker_type']) ?></td>
                    <td><?= htmlspecialchars($vehicle['date_bought']) ?></td>
                    <td><img src="../assets/qrcodes/<?= htmlspecialchars($vehicle['vehicle_qr']) ?>" alt="QR Code" width="50"></td>
                    <td><?= htmlspecialchars($vehicle['household_id']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    document.getElementById("sortBtn").addEventListener("click", function () {
        const sort = document.getElementById("sort").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("sort", sort);
        window.location.href = "vehiclesList.php?" + queryParams.toString();
    });

    document.getElementById("searchBtn").addEventListener("click", function () {
        const search = document.getElementById("search").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("search", search);
        window.location.href = "vehiclesList.php?" + queryParams.toString();
    });

    document.getElementById("filterBtn").addEventListener("click", function () {
        const filterColumn = document.getElementById("filterColumn").value;
        const filterValue = document.getElementById("filterValue").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("filterColumn", filterColumn);
        queryParams.set("filterValue", filterValue);
        window.location.href = "vehiclesList.php?" + queryParams.toString();
    });
    </script>
</body>
</html>
