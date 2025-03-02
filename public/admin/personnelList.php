<?php
include '../../includes/db/db_config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch personnel (Custodian, Security, Maintenance)
    $query = "SELECT user_id, username, CONCAT(first_name, ' ', last_name) AS full_name, email, phone, type, status 
              FROM system_users WHERE LOWER(type) IN ('custodian', 'security', 'maintenance')";
    
    $stmt = $pdo->query($query);
    $personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}

// Handle new personnel creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $type = $_POST['type'];
    $password = password_hash("default123", PASSWORD_DEFAULT); // Default password
    
    $insertQuery = "INSERT INTO system_users (username, first_name, last_name, email, phone, type, password_hash, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute([$username, $first_name, $last_name, $email, $phone, $type, $password]);
    
    header("Location: personnelList.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Personnel Management</h1>
        <a href="adminDashboard.php">Back to Dashboard</a>
        <br><br>
    </div>
        
        <h2>Create New Personnel</h2>
        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>First Name:</label>
            <input type="text" name="first_name" required>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Phone Number:</label>
            <input type="text" name="phone" required>
            <label>Type:</label>
            <select name="type" required>
                <option value="Custodian">Custodian</option>
                <option value="Security">Security</option>
                <option value="Maintenance">Maintenance</option>
            </select>
            <button type="submit">Create</button>
        </form>
        
        <input type="text" id="search" placeholder="Search personnel..." onkeyup="filterTable()">
        
        <table id="personnelTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($personnel as $person) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($person['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($person['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($person['email']); ?></td>
                        <td><?php echo htmlspecialchars($person['phone']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($person['type'])); ?></td>
                        <td><?php echo htmlspecialchars($person['status']); ?></td>
                        <td>
                            <a href="editPersonnel.php?id=<?php echo $person['user_id']; ?>">Edit</a> |
                            <a href="deactivatePersonnel.php?id=<?php echo $person['user_id']; ?>" 
                                onclick="return confirm('Are you sure you want to deactivate this user?');">Deactivate</a> 
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
   

    <script>
        function filterTable() {
            var input = document.getElementById("search").value.toLowerCase();
            var rows = document.querySelectorAll("#personnelTable tbody tr");
            rows.forEach(row => {
                var name = row.cells[1].textContent.toLowerCase();
                row.style.display = name.includes(input) ? "" : "none";
            });
        }
    </script>
</body>
</html>
