<?php
session_start();

// Function to generate a random 8-character code
function generateCode() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&()';
    $code = '';
    for ($i = 0; $i < 8; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

$bodyContent = file_get_contents('php://input');
$data = json_decode($bodyContent, true);


if (!isset($data['department'])) {
    die('Error: Department not specified.');
}

$departmentName = $data['department'];
echo $departmentName;
// Load department emails from a JSON file
$jsonFile = file_get_contents('emails.json');
$departmentEmails = json_decode($jsonFile, true);

if (isset($departmentEmails[$departmentName])) {
    $recipientEmail = $departmentEmails[$departmentName];
    $verificationCode = generateCode();

    // Store the code, email, and department in a JSON file
    $codesFile = 'codes.json';
    $codesData = file_exists($codesFile) ? json_decode(file_get_contents($codesFile), true) : [];
    $codesData[] = [
        'email' => $recipientEmail,
        'code' => $verificationCode,
        'timestamp' => time(),
        'department' => $departmentName
    ];
    file_put_contents($codesFile, json_encode($codesData));

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
