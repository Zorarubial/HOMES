<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

include('../includes/db/db_config.php');

$user_id = $_SESSION['user_id'];

if (!isset($_FILES['profile_pic'])) {
    echo json_encode(["status" => "error", "message" => "No file uploaded."]);
    exit();
}

$file = $_FILES['profile_pic'];
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG and PNG are allowed."]);
    exit();
}

$upload_dir = "uploads/";
$filename = "profile_" . $user_id . "_" . time() . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
$file_path = $upload_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $file_path)) {
    try {
        $stmt = $pdo->prepare("UPDATE system_users SET profile_pic = ? WHERE user_id = ?");
        $stmt->execute([$filename, $user_id]);
        echo json_encode(["status" => "success", "filename" => $filename]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database update failed."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "File upload failed."]);
}
?>
