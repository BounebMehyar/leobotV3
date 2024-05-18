<?php
session_start();

// Function to generate a random 4-digit code
function generateCode() {
    return rand(1000, 9999);
}

$bodyContent = file_get_contents('php://input');
$data = json_decode($bodyContent, true);
$departmentName = 'HR';  // Example department name

// Load department emails from a JSON file
$jsonFile = file_get_contents('emails.json');
$departmentEmails = json_decode($jsonFile, true);

if (isset($departmentEmails[$departmentName])) {
    $recipientEmail = $departmentEmails[$departmentName];
    $verificationCode = generateCode();

    // Store the code in a session
    $_SESSION['verification_code'] = $verificationCode;
    echo "Verification code set in session: " . $_SESSION['verification_code']; 
    echo 'Session ID in send_email.php: ' . session_id();


    $to = $recipientEmail;
    $subject = 'New Question - Verification Required';
    $message = "Please enter the following code to access the interface: $verificationCode\nLink: http://localhost/leobot/venv%20-%20Copie/leobotV3/verification.php";
    $headers = "From: mehyarbouneb222@gmail.com\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully with verification code.";
    } else {
        echo "Email sending failed.";
    }
} else {
    echo 'Department not found';
}

