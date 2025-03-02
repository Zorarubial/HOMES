<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Successful</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/icons/templogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script>
        // Redirect to index.php after 5 seconds
        setTimeout(function() {
            window.location.href = "index.php";
        }, 5000);
    </script>

    <style>
        .success-container {
            max-width: 500px;
            margin: 100px auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .success-container h1 {
            color: green;
        }
        .redirect-text {
            margin-top: 15px;
            font-size: 14px;
        }
        .redirect-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .redirect-link:hover {
            background: #0056b3;
        }
    </style>

</head>
<body>

    <div class="success-container">
        <h1>🎉 Signup Successful!</h1>
        <p>Your account has been created successfully.</p>
        <p class="redirect-text">You will be redirected to the homepage in 5 seconds.</p>
        <a class="redirect-link" href="index.php">Return to Homepage Now</a>
    </div>

</body>
</html>
