<?php
include '../../includes/db/db_config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch visitor logs
    $query = "SELECT visit_id, qr_code, household_id, visitor_name, contact_number, date_of_visit, status, purpose FROM visitor_management";
    $stmt = $pdo->query($query);
    $visitorLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Logs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Visitor Logs</h1>
        <a href="adminDashboard.php">Back to Dashboard</a>
        <br><br>
    </div>
    
    <input type="text" id="search" placeholder="Search visitors..." onkeyup="filterTable()">
    
    <table id="visitorTable">
        <thead>
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">QR Code</th>
                <th onclick="sortTable(2)">Household ID</th>
                <th onclick="sortTable(3)">Visitor Name</th>
                <th onclick="sortTable(4)">Contact Number</th>
                <th onclick="sortTable(5)">Date of Visit</th>
                <th onclick="sortTable(6)">Status</th>
                <th onclick="sortTable(7)">Purpose</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visitorLogs as $log) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['visit_id']); ?></td>
                    <td><?php echo htmlspecialchars($log['qr_code']); ?></td>
                    <td><?php echo htmlspecialchars($log['household_id']); ?></td>
                    <td><?php echo htmlspecialchars($log['visitor_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($log['date_of_visit']); ?></td>
                    <td><?php echo htmlspecialchars($log['status']); ?></td>
                    <td><?php echo htmlspecialchars($log['purpose']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <script>
        function filterTable() {
            var input = document.getElementById("search").value.toLowerCase();
            var rows = document.querySelectorAll("#visitorTable tbody tr");
            rows.forEach(row => {
                var name = row.cells[3].textContent.toLowerCase();
                row.style.display = name.includes(input) ? "" : "none";
            });
        }

        function sortTable(columnIndex) {
            var table = document.getElementById("visitorTable");
            var rows = Array.from(table.rows).slice(1);
            var ascending = table.dataset.order !== "asc";

            rows.sort((rowA, rowB) => {
                var cellA = rowA.cells[columnIndex].textContent.trim().toLowerCase();
                var cellB = rowB.cells[columnIndex].textContent.trim().toLowerCase();
                
                return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });

            rows.forEach(row => table.appendChild(row));
            table.dataset.order = ascending ? "asc" : "desc";
        }
    </script>
</body>
</html>
