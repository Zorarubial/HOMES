<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db/db_config.php'); // Ensure correct path

$user_id = $_SESSION['user_id'];
$type = $_SESSION['type'];

try {
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone, u.date_of_birth, u.created_at, u.status, 
               u.profile_pic, 
               h.homeowner_id, hh.block, hh.lot, hh.street 
        FROM homeowners h
        JOIN system_users u ON h.user_id = u.user_id
        JOIN households hh ON hh.homeowner_id = h.homeowner_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $homeowner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$homeowner) {
        die("User not found.");
    }

    // Assign values safely with fallback defaults
    $first_name = htmlspecialchars($homeowner['first_name'] ?? "User");
    $last_name = htmlspecialchars($homeowner['last_name'] ?? "");
    $email = htmlspecialchars($homeowner['email'] ?? "");
    $phone = htmlspecialchars($homeowner['phone'] ?? "N/A");
    $date_of_birth = htmlspecialchars($homeowner['date_of_birth'] ?? "Unknown");
    $registration_date = htmlspecialchars($homeowner['registration_date'] ?? "Unknown");
    $status = htmlspecialchars($homeowner['status'] ?? "Inactive");

    $profile_pic = !empty($homeowner['profile_pic']) ? htmlspecialchars($homeowner['profile_pic']) : "default-profile.png";

    // Address Formatting
    $block = htmlspecialchars($homeowner['block'] ?? "N/A");
    $lot = htmlspecialchars($homeowner['lot'] ?? "N/A");
    $street = htmlspecialchars($homeowner['street'] ?? "N/A");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homeowner Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/ab77a77ccc.js" crossorigin="anonymous"></script>
    <script defer src="script.js"></script>

    <style>
        .profile-container {
            width: 50%;
            max-width: 600px; /* Ensures it doesn't get too wide */
            margin: auto; /* Centers horizontally */
            padding: 20px;
            background: #FDFDF0; /* Light background */
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            
           
        }

    </style>
</head>
<body>

    <div class="container">
        <!-- Logo linked to Admin Dashboard -->
        <a href="homeownerDashboard.php">
            <img src="assets/icons/templogo.png" alt="Logo" class="logo">
        </a>
        
        <h1>Homeowner Profile</h1>

        <div class="profile-notifications">
            <!-- Bell Icon for Notifications -->
            <i class="fas fa-bell" id="notificationIcon"></i>
            
            <div class="profile-menu">
                <img src="assets/img/<?php echo $profilePic; ?>" alt="User Profile" class="profile-pic" id="profilePic">
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>
    

<div class="profile-container">

    <!-- Profile Picture Section -->
    <div class="profile-pic-container">
        <div class="profile-pic">
            <img id="profilePic" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">

        </div>
        <input type="file" id="profilePicInput" accept="image/*" style="display: none;">
        <button type="button" class="change-pic-btn" onclick="document.getElementById('profilePicInput').click();">
            Change Picture
        </button>
    </div>

    <form id="updateProfileForm">
    
    <!-- BASIC INFORMATION -->
    <div class="profile-section">
        <h3>Basic Information</h3>

        <label>Homeowner ID</label>
        <input type="text" id="homeowner_id" name="homeowner_id" value="<?php echo $homeowner['homeowner_id']; ?>" disabled>

        <label>First Name</label>
        <div class="input-container">
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($homeowner['first_name']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('first_name')"></i>
        </div>

        <label>Last Name</label>
        <div class="input-container">
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($homeowner['last_name']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('last_name')"></i>
        </div>

        <label>Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($homeowner['date_of_birth']); ?>" disabled>
    </div>

    <!-- CONTACT INFORMATION -->
    <div class="profile-section">
        <h3>Contact Information</h3>

        <label>Contact Number</label>
        <div class="input-container">
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($homeowner['phone']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('phone')"></i>
        </div>

        <h3>Address</h3>
    <div class="input-container">
        <label>Block</label>
        
            <input type="text" id="block" name="block" value="<?php echo htmlspecialchars($homeowner['block']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('block')"></i>
        

        <label>Lot</label>
            <input type="text" id="lot" name="lot" value="<?php echo htmlspecialchars($homeowner['lot']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('lot')"></i>


        <label>Street</label>

            <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($homeowner['street']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('street')"></i>
    </div>

    <!-- ACCOUNT INFORMATION -->
    <div class="profile-section">
        <h3>Account Details</h3>

        <label>Email</label>
        <div class="input-container">
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($homeowner['email']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('email')"></i>
        </div>

        <label>Username</label>
        <div class="input-container">
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('username')"></i>
        </div>

        <label>Password</label>
        <div class="input-container">
            <input type="password" id="password" name="password" value="********" disabled>
            <i class="fa-solid fa-pen-to-square edit-icon" onclick="enableEdit('password')"></i>
        </div>

        <label>Registration Date</label>
        <input type="text" id="created_at" name="created_at" value="<?php echo htmlspecialchars($homeowner['created_at']); ?>" disabled>

        <label>Account Status</label>
        <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($homeowner['status']); ?>" disabled>
    </div>

    <!-- Save & Cancel Buttons -->
    <div class="button-container">
        <button type="button" id="saveBtn" onclick="saveChanges()" style="display: none;">Save Changes</button>

        <button type="button" id="cancelBtn" onclick="cancelEdit()" style="display: none;">Cancel</button>
    </div>

</form>

</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    let editableFields = document.querySelectorAll(".input-container input");
    let editIcons = document.querySelectorAll(".edit-icon");
    let saveBtn = document.getElementById("saveBtn");
    let cancelBtn = document.getElementById("cancelBtn");

    let originalValues = {}; // Store original values for cancel function

    // Enable field editing
    window.enableEdit = function (fieldId) {
        let inputField = document.getElementById(fieldId);

        if (!originalValues[fieldId]) {
            originalValues[fieldId] = inputField.value; // Save original value
        }

        if (fieldId === "password") {
            inputField.style.display = "none"; // Hide original password field
            let passwordContainer = inputField.parentElement;

            // Only create fields if they don't already exist
            if (!document.getElementById("old_password") && !document.getElementById("new_password")) {
                let oldPasswordInput = document.createElement("input");
                oldPasswordInput.type = "password";
                oldPasswordInput.id = "old_password";
                oldPasswordInput.name = "old_password";
                oldPasswordInput.placeholder = "Enter old password";
                oldPasswordInput.required = true;

                let newPasswordInput = document.createElement("input");
                newPasswordInput.type = "password";
                newPasswordInput.id = "new_password";
                newPasswordInput.name = "new_password";
                newPasswordInput.placeholder = "Enter new password";
                newPasswordInput.required = true;

                passwordContainer.appendChild(oldPasswordInput);
                passwordContainer.appendChild(newPasswordInput);
            }
        } else {
            inputField.disabled = false;
            inputField.focus();
        }

        saveBtn.style.display = "inline-block";
        cancelBtn.style.display = "inline-block";
    };

    // Function to cancel edit and restore original values
    window.cancelEdit = function () {
        editableFields.forEach(input => {
            if (originalValues[input.id]) {
                input.value = originalValues[input.id]; // Restore original value
            }
            input.disabled = true;
        });

        // Handle password field separately
        let passwordField = document.getElementById("password");
        let oldPassword = document.getElementById("old_password");
        let newPassword = document.getElementById("new_password");

        if (oldPassword && newPassword) {
            oldPassword.remove();
            newPassword.remove();
            passwordField.style.display = "block"; // Show original field
        }

        // Hide Save & Cancel buttons
        saveBtn.style.display = "none";
        cancelBtn.style.display = "none";
        originalValues = {}; // Clear saved values
    };

    // Save changes to PHP
    saveBtn.addEventListener("click", function () {
        let formData = new FormData();
        formData.append("first_name", document.getElementById("first_name").value);
        formData.append("last_name", document.getElementById("last_name").value);
        formData.append("phone", document.getElementById("phone").value);
        formData.append("email", document.getElementById("email").value);
        formData.append("username", document.getElementById("username").value);
        formData.append("block", document.getElementById("block").value);
        formData.append("lot", document.getElementById("lot").value);
        formData.append("street", document.getElementById("street").value);

        let oldPasswordField = document.getElementById("old_password");
        let newPasswordField = document.getElementById("new_password");

        if (oldPasswordField && newPasswordField) {
            formData.append("old_password", oldPasswordField.value);
            formData.append("new_password", newPasswordField.value);
        }

        // Debugging: Log password values to verify they are set
        console.log("Old Password:", oldPasswordField ? oldPasswordField.value : "Not found");
        console.log("New Password:", newPasswordField ? newPasswordField.value : "Not found");
        console.log("Sending data:", Object.fromEntries(formData));

        // Send data to PHP
        fetch("update_profile.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Response received:", data); // Debug response

            if (data.status === "success") {
                alert(data.message);
                location.reload(); // Ensure the new username is reflected
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // for debugging purposes
    console.log("Old Password (Before Editing):", document.getElementById("old_password")?.value);
    console.log("New Password (Before Editing):", document.getElementById("new_password")?.value);
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("updateProfileForm").addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent full page reload

        let formData = new FormData(this);

        fetch("update_profile.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Profile updated successfully!");
                location.reload(); // Refresh the page to show changes
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Something went wrong!");
        });
    });
});

//logout
document.addEventListener("DOMContentLoaded", function () {
            const profilePic = document.getElementById("profilePic");
            const dropdownMenu = document.getElementById("dropdownMenu");

            profilePic.addEventListener("click", function (event) {
                event.stopPropagation();
                dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
            });

            document.addEventListener("click", function () {
                dropdownMenu.style.display = "none";
            });

            dropdownMenu.addEventListener("click", function (event) {
                event.stopPropagation();
            });
        });


</script>

</body>
</html>

