<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

include('../includes/db/db_config.php'); // Ensure correct path

$user_id = $_SESSION['user_id'];
$response = ["status" => "error", "message" => "Something went wrong."];

try {
    // Fetch current user data
    $stmt = $pdo->prepare("SELECT * FROM system_users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found."]);
        exit();
    }

    // Prepare update query
    $updateFields = [];
    $updateValues = [];

    if (!empty($_POST['first_name'])) {
        $updateFields[] = "first_name = ?";
        $updateValues[] = $_POST['first_name'];
    }
    if (!empty($_POST['last_name'])) {
        $updateFields[] = "last_name = ?";
        $updateValues[] = $_POST['last_name'];
    }
    if (!empty($_POST['phone'])) {
        $updateFields[] = "phone = ?";
        $updateValues[] = $_POST['phone'];
    }
    if (!empty($_POST['email'])) {
        $updateFields[] = "email = ?";
        $updateValues[] = $_POST['email'];
    }
    if (!empty($_POST['username'])) {
        $updateFields[] = "username = ?";
        $updateValues[] = $_POST['username'];
    }
    
    // Password update logic
    if (!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
        if (password_verify($_POST['old_password'], $user['password'])) {
            $newPasswordHash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $updateFields[] = "password = ?";
            $updateValues[] = $newPasswordHash;
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect old password."]);
            exit();
        }
    }
    
    if (!empty($updateFields)) {
        $updateValues[] = $user_id;
        $stmt = $pdo->prepare("UPDATE system_users SET " . implode(", ", $updateFields) . " WHERE user_id = ?");
        $stmt->execute($updateValues);
    }
    
    // Update address
    if (!empty($_POST['block']) || !empty($_POST['lot']) || !empty($_POST['street'])) {
        $stmt = $pdo->prepare("UPDATE households SET block = ?, lot = ?, street = ? WHERE homeowner_id = (SELECT homeowner_id FROM homeowners WHERE user_id = ?)");
        $stmt->execute([
            $_POST['block'] ?? '', 
            $_POST['lot'] ?? '', 
            $_POST['street'] ?? '', 
            $user_id
        ]);
    }
    
    echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
