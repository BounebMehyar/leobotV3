<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();  // Start the session to access session variables

$invalidCode = false;  // Flag to track invalid code

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = $_POST['verification_code'];  // Code entered by the user
    $recipientEmail = $_POST['email'];  // Email entered by the user

    // Load the codes data
    $codesFile = 'codes.json';
    $codesData = file_exists($codesFile) ? json_decode(file_get_contents($codesFile), true) : [];

    foreach ($codesData as $entry) {
        if ($entry['email'] === $recipientEmail && $entry['code'] == $inputCode) {
            // Correct code, proceed further
            $_SESSION['verification_code'] = $inputCode; // Store verified code in session
            $_SESSION['verified_email'] = $recipientEmail; // Store verified email in session
            header("Location: request.php");  // Redirect to main interface
            exit;
        }
    }
    // If no match found
    $invalidCode = true;
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
            background-image: url('image/background2.jpg');
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            background-color: white;
        }
        .logo {
            margin-bottom: 20px;
        }
        .form-control {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <img src="image/leoni.png" alt="Company Logo" class="logo" width="200" height="100">
        <h2 class="text-blue" style="color: #4286f4;">Enter Verification Code</h2>
        <?php if ($invalidCode): ?>
            <div class="alert alert-danger" role="alert">
                Invalid verification code. Please try again.
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($recipientEmail) ? htmlspecialchars($recipientEmail) : ''; ?>" required>
            <input type="text" name="verification_code" class="form-control" placeholder="Enter your 8-character code here" value="<?php echo isset($inputCode) ? htmlspecialchars($inputCode) : ''; ?>" required>
            <input type="submit" value="Verify" class="btn btn-light">
        </form>
    </div>
</body>
</html>
