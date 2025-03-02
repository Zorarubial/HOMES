<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Households List</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Households List</h1>

        <div class="profile-notifications">
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <div class="profile-menu">
                <img src="../assets/img/profile.jpg" alt="Admin Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.html">My Profile</a>
                    <a href="#" onclick="confirmLogout()">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <div class="controls">
        <label for="sort">Sort By:</label>
        <select id="sort" name="sort">
            <option value="household_id ASC">Household ID (Ascending)</option>
            <option value="household_id DESC">Household ID (Descending)</option>
        </select>
        <button id="sortBtn"><i class="fas fa-sort"></i> Sort</button>
        
        <input type="text" id="search" placeholder="Search...">
        <button id="searchBtn"><i class="fas fa-search"></i> Search</button>
        
        <label for="filterColumn">Filter By:</label>
        <select id="filterColumn">
            
            <option value="household_head">Household Head</option>
            <option value="block">Block</option>
            <option value="lot">Lot</option>
            <option value="street">Street</option>
        </select>
        <input type="text" id="filterValue" placeholder="Enter value...">
        <button id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <table border="1">
        <thead>
            <tr>
                <th>Household ID</th>
                <th>Household Head</th>
                <th>Block</th>
                <th>Lot</th>
                <th>Street</th>
            </tr>
        </thead>
        <tbody id="householdTableBody">
            <?php
            include '../../includes/db/db_config.php';

            $sort = $_GET['sort'] ?? 'household_id';
            $search = $_GET['search'] ?? '';
            $filterColumn = $_GET['filterColumn'] ?? '';
            $filterValue = $_GET['filterValue'] ?? '';

            try {
                $query = "SELECT 
                    h.household_id, 
                    CONCAT(su.first_name, ' ', su.last_name) AS household_head, 
                    h.block, 
                    h.lot, 
                    h.street, 
                    ho.homeowner_id  
                  FROM households h
                  JOIN homeowners ho ON h.homeowner_id = ho.homeowner_id
                  JOIN system_users su ON ho.user_id = su.user_id
                  WHERE (su.first_name LIKE :search OR su.last_name LIKE :search OR h.block LIKE :search OR h.lot LIKE :search OR h.street LIKE :search)";

                if (!empty($filterColumn) && !empty($filterValue)) {
                    if ($filterColumn == 'household_head') {
                        $query .= " AND CONCAT(su.first_name, ' ', su.last_name) LIKE :filterValue";
                    } else {
                        $query .= " AND $filterColumn LIKE :filterValue";
                    }
                }

                $validSortColumns = ['household_id', 'household_id DESC'];
                if (in_array($sort, $validSortColumns)) {
                    $query .= " ORDER BY $sort";
                } else {
                    $query .= " ORDER BY household_id";
                }
                
                $stmt = $pdo->prepare($query);
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
                if (!empty($filterColumn) && !empty($filterValue)) {
                    $stmt->bindValue(':filterValue', "%$filterValue%", PDO::PARAM_STR);
                }
                
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><a href='household_details.php?id=" . htmlspecialchars($row['household_id']) . "'>" . htmlspecialchars($row['household_id']) . "</a></td>";
                    echo "<td><a href='homeowner_details.php?id=" . htmlspecialchars($row['household_id']) . "'>" . htmlspecialchars($row['household_head']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($row['block']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['lot']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['street']) . "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='5'>Database error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
    document.getElementById("sortBtn").addEventListener("click", function () {
        const sort = document.getElementById("sort").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("sort", sort);
        window.location.href = "householdsList.php?" + queryParams.toString();
    });

    document.getElementById("searchBtn").addEventListener("click", function () {
        const search = document.getElementById("search").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("search", search);
        window.location.href = "householdsList.php?" + queryParams.toString();
    });

    document.getElementById("filterBtn").addEventListener("click", function () {
        const filterColumn = document.getElementById("filterColumn").value;
        const filterValue = document.getElementById("filterValue").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("filterColumn", filterColumn);
        queryParams.set("filterValue", filterValue);
        window.location.href = "householdsList.php?" + queryParams.toString();
    });
    </script>
</body>
</html>
