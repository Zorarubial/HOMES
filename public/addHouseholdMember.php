<?php
include '../includes/db/db_config.php';

if (!isset($_GET['household_id'])) {
    die("Invalid request");
}

$household_id = $_GET['household_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $relationship = $_POST['relationship'];

    try {
        $stmt = $pdo->prepare("INSERT INTO household_members (household_id, first_name, last_name, contact_number, relationship) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$household_id, $first_name, $last_name, $phone, $relationship]);

        echo "<script>alert('Household member added successfully!'); window.location.href='householdDetails.php?id=$household_id';</script>";
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
    <title>Add Household Member</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <h2>Add Household Member</h2>
    <form method="post">
        <input type="hidden" name="household_id" value="<?= htmlspecialchars($household_id) ?>">

        <label>First Name:</label>
        <input type="text" name="first_name" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" required>

        <label>Phone:</label>
        <input type="text" name="phone" required>

        <label>Relationship:</label>
        <input type="text" name="relationship" required>

        <button type="submit">Add Member</button>
    </form>

    <a href="householdDetails.php?id=<?= htmlspecialchars($household_id) ?>">Back to Household Details</a>

</body>
</html>
