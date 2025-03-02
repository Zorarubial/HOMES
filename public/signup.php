<?php
$successMessage = isset($_GET['success']) ? "Account successfully created!" : '';
$errorMessage = isset($_GET['error']) ? urldecode($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subdivision Sign-Up</title>
    <link rel="icon" type="image/png" href="assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #ECECA4;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            justify-content: flex-start;
            font-family: 'Garamond', sans-serif;
            padding-bottom: 50px;
        }
        .header {
            width: 100%;
            background: linear-gradient(to right,hsl(110, 44.40%, 64.70%), #2B7F49);
            padding: 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: start;
            font-size: 35px;
            font-weight: bold;
        }
        .header img {
            height: 60px;
            margin-right: 15px;
            margin-left: 10px;
        }
        .header h1 {
            color: #fff;
            font-size: 35px;
            margin: 5;
        }
        .back-button {
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            color: white;
            background-color: #85BE91;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            font-weight: bold;
            font-size: 14px;
            position: absolute;
            right: 100px;
        }
        .back-button i {
            margin-right: 5px;
        }
        .back-button:hover {
            background-color:red;
        }
        .container {
            width: 100%;
            max-width: 450px;
            background-color: #2B7F49;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            margin-top: 50px;
            font-family: 'Inter', sans-serif;
            margin-bottom: 50px;
        }
        .container h2 {
            font-size: 30px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .form-group {
            width: 90%;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
        }
        .form-group input, .form-group select {
            background-color: #85BE91;
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            color: black;
        }
        .form-group select {
            height: 50px;
            width: 105%;
        }
        .form-control:focus, .form-group input:focus, .form-group select:focus {
            background-color: white;
            color: black;
            outline: none;
        }
        button {
            width: 105%;
            background-color: #161A07;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
        }
        button:hover {
            opacity: 0.8;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Show messages -->
    <?php if ($successMessage): ?>
        <p class="success-message"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p class="error-message"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <div class="header">
        <img src="assets/icons/templogo.png" alt="Logo">
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h1>Milflora Homes</h1>
    </div>
    <div class="container">
        <h2>Create Account</h2>
        <form action="../scripts/signupHandler.php" method="POST" class="form-group" onsubmit="return validatePassword()">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" placeholder="Enter first name" required>

            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" placeholder="Enter last name" required>

            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Enter username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter password" required minlength="6">

            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required minlength="6">
            <div id="passwordError" class="error"></div>

            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Enter email address" required>

            <label for="phone">Phone</label>
            <input type="tel" name="phone" placeholder="Enter phone number" required>

            <label for="resident_type">Select resident type:</label>
            <select name="resident_type" required>
                <option value="" disabled selected>Resident Type</option>
                <option value="homeowner">Homeowner</option>
                <option value="renter">Renter</option>
            </select>

            <label for="block">Block</label>
            <select name="block" required>
                <option value="" disabled selected>Select block</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>

            <label for="lot">Lot</label>
            <input type="text" name="lot" placeholder="Enter lot number" required>
            
            <label for="street">Street</label>
            <select name="street" required>
                <option value="" disabled selected>Select street</option>
                <option value="Sampaguita">Sampaguita</option>
                <option value="Adelfa">Adelfa</option>
                <option value="Champaca">Champaca</option>
                <option value="Bougainvilla">Bougainvilla</option>
                <option value="Santan">Santan</option>
                <option value="Dahlia">Dahlia</option>
                <option value="Gumamela">Gumamela</option>
                <option value="Ilang-ilang">Ilang-ilang</option>
                <option value="Kalachuchi">Kalachuchi</option>
                <option value="Lilac">Lilac</option>
                <option value="Rosal">Rosal</option>
                <option value="Waling-waling">Waling-waling</option>
                <option value="Hasmin">Hasmin</option>
            </select>
            
            <button type="submit">Sign Up</button>
        </form>
    </div>

    <script>
        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorElement = document.getElementById('passwordError');

            if (password !== confirmPassword) {
                errorElement.textContent = "Passwords do not match!";
                return false;
            } else {
                errorElement.textContent = "";
                return true;
            }
        }
    </script>
</body>
</html>