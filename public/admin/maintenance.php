<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../../includes/db/db_config.php'; 

try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch maintenance tasks
        $query = "SELECT mt.*, CONCAT(su.first_name, ' ', su.last_name) AS personnel_name
                  FROM maintenance_tasks mt
                  JOIN system_users su ON mt.personnel_id = su.user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch maintenance personnel with concatenated names
        $personnelQuery = "SELECT user_id, CONCAT(first_name, ' ', last_name) AS full_name 
                           FROM system_users WHERE type = 'maintenance'";
        $personnelStmt = $pdo->prepare($personnelQuery);
        $personnelStmt->execute();
        $personnel = $personnelStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Maintenance Management</h1>
        <a href="adminDashboard.php">Back to Dashboard</a>
    </div>
        <h2>Add New Maintenance Task</h2>
        <form action="maintenanceAdd.php" method="post">
            <label>Category:</label>
            <input type="text" name="category" required>
            <label>Start Time:</label>
            <input type="datetime-local" name="start_time" required>
            <label>End Time:</label>
            <input type="datetime-local" name="end_time">
            <label>Expense:</label>
            <input type="number" step="0.01" name="expense" required>
            <label>Note:</label>
            <textarea name="note"></textarea>
            <label>Personnel:</label>
            <select name="personnel_id" required>
                <?php foreach ($personnel as $p) : ?>
                    <option value="<?= $p['user_id'] ?>"> <?= htmlspecialchars($p['full_name']) ?> </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Add Task</button>
        </form>

        <h2>Existing Maintenance Tasks</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Expense</th>
                    <th>Note</th>
                    <th>Personnel</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task) : ?>
                    <tr>
                        <td><?= htmlspecialchars($task['category']) ?></td>
                        <td><?= htmlspecialchars($task['start_time']) ?></td>
                        <td><?= htmlspecialchars($task['end_time'] ?? 'N/A') ?></td>
                        <td><?= number_format($task['expense'], 2) ?></td>
                        <td><?= htmlspecialchars($task['note']) ?></td>
                        <td><?= htmlspecialchars($task['personnel_name']) ?></td>
                        <td><?= htmlspecialchars($task['maint_status']) ?></td>
                        <td>
                            <!-- Buttons to update maintenance status -->
                            <form action="maintenanceUpdateStatus.php" method="post" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                <?php if ($task['maint_status'] !== 'ongoing') : ?>
                                    <button type="submit" name="status" value="ongoing">Mark as Ongoing</button>
                                <?php endif; ?>
                                <?php if ($task['maint_status'] !== 'accomplished') : ?>
                                    <button type="submit" name="status" value="accomplished">Mark as Accomplished</button>
                                <?php endif; ?>
                                <?php if ($task['maint_status'] !== 'cancelled') : ?>
                                    <button type="submit" name="status" value="cancelled" onclick="return confirm('Are you sure?')">Cancel Task</button>
                                <?php endif; ?>
                            </form>
                            
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
</body>
</html>
