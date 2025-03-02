<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeowners List</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <a href="adminDashboard.php">
            <img src="../assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Homeowners List</h1>

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
            <option value="homeowner_id ASC">Homeowner ID (Ascending)</option>
            <option value="homeowner_id DESC">Homeowner ID (Descending)</option>
            <option value="name ASC">Name (A-Z)</option>
            <option value="name DESC">Name (Z-A)</option>
        </select>
        <button id="sortBtn"><i class="fas fa-sort"></i> Sort</button>
        
        <input type="text" id="search" placeholder="Search...">
        <button id="searchBtn"><i class="fas fa-search"></i> Search</button>
        
        <label for="filterColumn">Filter By:</label>
        <select id="filterColumn">
            <option value="status">Status</option>
            <option value="type">Type</option>
        </select>
        <input type="text" id="filterValue" placeholder="Enter value...">
        <button id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
    </div>

    <table border="1">
        <thead>
            <tr>
                <th>Homeowner ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody id="homeownerTableBody">
            <?php
            include '../../includes/db/db_config.php';

            $sort = $_GET['sort'] ?? 'homeowner_id ASC';
            $search = $_GET['search'] ?? '';
            $filterColumn = $_GET['filterColumn'] ?? '';
            $filterValue = $_GET['filterValue'] ?? '';

            try {
                $query = "SELECT h.homeowner_id, 
                            CONCAT(su.first_name, ' ', su.last_name) AS name, 
                            su.email, 
                            su.phone, 
                            su.status, 
                            su.created_at, 
                            su.type 
                          FROM homeowners h
                          JOIN system_users su ON h.user_id = su.user_id
                          WHERE (su.first_name LIKE :search OR su.last_name LIKE :search OR su.email LIKE :search)";

                if (!empty($filterColumn) && !empty($filterValue)) {
                    $query .= " AND $filterColumn LIKE :filterValue";
                }

                $validSortColumns = ['homeowner_id ASC', 'homeowner_id DESC', 'name ASC', 'name DESC'];
                if (in_array($sort, $validSortColumns)) {
                    $query .= " ORDER BY $sort";
                } else {
                    $query .= " ORDER BY homeowner_id ASC";
                }

                $stmt = $pdo->prepare($query);
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
                if (!empty($filterColumn) && !empty($filterValue)) {
                    $stmt->bindValue(':filterValue', "%$filterValue%", PDO::PARAM_STR);
                }
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><a href='homeowners_details.php?id=" . htmlspecialchars($row['homeowner_id']) . "'>" . htmlspecialchars($row['homeowner_id']) . "</a></td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='7'>Database error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>

        // for profile and logout dropdown
        document.addEventListener("DOMContentLoaded", function () {
            const profilePic = document.getElementById("profilePic");
            const dropdownMenu = document.getElementById("dropdownMenu");

            // Toggle dropdown on profile picture click
            profilePic.addEventListener("click", function (event) {
                event.stopPropagation(); // Prevent immediate closing when clicking profile picture
                dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
            });

            // Close dropdown when clicking anywhere else
            document.addEventListener("click", function () {
                dropdownMenu.style.display = "none";
            });

            // Prevent closing when clicking inside the dropdown menu
            dropdownMenu.addEventListener("click", function (event) {
                event.stopPropagation();
            });
        });

        function confirmLogout() {
            let confirmAction = confirm("Are you sure you want to log out?");
            if (confirmAction) {
                window.location.href = "../logout.php"; // Redirect to logout.php if confirmed
            }
        } 

        
    document.getElementById("sortBtn").addEventListener("click", function () {
        const sort = document.getElementById("sort").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("sort", sort);
        window.location.href = "homeownersList.php?" + queryParams.toString();
    });

    document.getElementById("searchBtn").addEventListener("click", function () {
        const search = document.getElementById("search").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("search", search);
        window.location.href = "homeownersList.php?" + queryParams.toString();
    });

    document.getElementById("filterBtn").addEventListener("click", function () {
        const filterColumn = document.getElementById("filterColumn").value;
        const filterValue = document.getElementById("filterValue").value;
        let queryParams = new URLSearchParams(window.location.search);
        queryParams.set("filterColumn", filterColumn);
        queryParams.set("filterValue", filterValue);
        window.location.href = "homeownersList.php?" + queryParams.toString();
    });
    </script>
</body>
</html>
