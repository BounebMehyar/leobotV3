<?php
session_start();  // Start the session to access session variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = $_POST['verification_code'];  // Code entered by the user

    if (isset($_SESSION['verification_code']) && $inputCode == $_SESSION['verification_code']) {
            // Correct code, proceed further
            header("Location: request.php");  // Redirect to main interface
            exit;
        // Redirect or perform other actions
    } else {
        // Code is incorrect
        echo "Invalid verification code. Please try again.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Code</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            
            /* Set the background image */
            background-image: url('image/background2.jpg'); /* Update 'path_to_your_image.jpg' to the path of your actual image */
            
            /* Center the image */
            background-position: center;
            
            /* Scale the background to cover the entire container */
            background-size: cover;
            
            /* Make the background fixed, so it does not scroll with the content */
            background-attachment: fixed;

            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Use full height to center vertically */
            margin: 0;
        }
        .form-container {
            width: 100%;
            max-width: 400px; /* Maximum width of the form */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* subtle shadow */
            text-align: center; /* Center-align text */
        }
        .logo {
            margin-bottom: 20px; /* Space below the logo */
        }
        .form-control {
            margin-bottom: 10px; /* spacing between input and button */
        }
    </style>
</head>
<body>
    <div class="form-container">
        <img src="image\leoni.png" alt="Company Logo" class="logo" width="200" height="100"> <!-- Logo Placeholder -->
        <h2 class="text-blue" style="color: #4286f4;">Enter Verification Code</h2>
        <form method="post" action="">
            <input type="text" name="verification_code" class="form-control" placeholder="Enter your 4-digit code here">
            <input type="submit" value="Verify" class="btn btn-light"> <!-- Use btn-light for contrast -->
        </form>
    </div>
</body>
</html>
