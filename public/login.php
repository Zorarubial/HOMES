<?php
include('../scripts/loginHandler.php');
include('../includes/db/db_config.php'); // Ensure this path is correct

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = authenticateUser($username, $password, $pdo); // Pass $pdo to function

    if (is_array($result)) { // If login is successful, $result contains user data
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['type'] = $result['type'];

        $redirectPage = match ($result['type']) {
            'homeowner', 'renter' => 'homeownerDashboard.php',
            'security' => 'security/securityDashboard.php', 
            'admin' => 'admin/adminDashboard.php',
            default => 'index.php'
        };

        header("Location: $redirectPage");
        exit();
    } else {
        $feedback = "<p style='color: red;'>Invalid username or password!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milflora Homes - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/icons/templogo.png">
    <style>
        body {
            background-color: #ECECA4;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            justify-content: flex-start;
            font-family: 'Garamond', sans-serif;
        }
        .header {
            width: 100%;
            background: linear-gradient(to right,hsl(110, 44.40%, 64.70%), #2B7F49);
            padding: 25px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: start;
            font-size: 35px;
            font-weight: bold;
        }
        .header img {
            height: 60px;
            margin-right: 10px;
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
            font-size: 12px;
            position: absolute;
            right: 100px;
        }
        .back-button i {
            margin-right: 5px;
        }
        .back-button:hover {
            background-color: red;
        }
        .login-container {
            background: #2B7F49;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            height: 400px;
            margin-top: 50px;
            color: white;
            font-size: 14px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-family: 'Inter', sans-serif;
        }
        .form-label {
            font-family: 'Inter', sans-serif;
        }
        .form-control {
            font-family: 'Inter', sans-serif;
            background-color: #85BE91;
            border: none;
            color: black;
            font-size: 12px;
        }
        .form-control::placeholder {
            font-family: 'Inter', sans-serif;
            color: #EBEFE0;
        }
        .btn-login {
            font-family: 'Inter', sans-serif;
            background-color: #161A07;
            border: none;
            width: 100%;
            color: white;
            font-size: 13px;
        }
        .btn-login:hover {
            background-color:#161A07;
        }
        .forgot-password {
            font-family: 'Inter', sans-serif;
            text-align: right;
            display: block;
            margin-top: 10px;
            color: #E9EEDE;
            text-decoration: underline;
            font-size: 12px;
        }
        .register-link {
            font-family: 'Inter', sans-serif;
            text-align: center;
            display: block;
            margin-top: 10px;
            color: #E9EEDE;
            text-decoration: none;
            font-size: 14px;
        }
        .register-link strong {
            font-weight: bold;
        }
        </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="assets/icons/templogo.png" alt="Milflora Homes Logo">
            Milflora Homes
        </div>
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="login-container">
        <h2>Welcome back👋</h2>
        <?php echo $feedback; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <a href="#" class="forgot-password">Forgot password?</a>
            <button type="submit" class="btn btn-login mt-3">Login</button>
            <a href="signup.php" class="register-link">Don't have an account? <strong>Register here</strong></a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>