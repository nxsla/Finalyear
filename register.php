<?php
session_start(); // Start the session at the very top!
include 'connect.php';

// Debugging: Check if the script is being called
error_log("register.php script started.", 3, "C:/xampp/htdocs/login/debug.log");

// Function to log data into a JSON file
function logData($data) {
    $logFile = 'C:\\xampp\\htdocs\\login\\logs.json'; // File to store logs
    error_log("logData() called. logFile = " . $logFile, 3, "C:/xampp/htdocs/login/debug.log");

    $logData = [];

    // Check if the log file exists
    if (file_exists($logFile)) {
        error_log("File exists: " . $logFile, 3, "C:/xampp/htdocs/login/debug.log");
        // Read the existing log data
        $logData = json_decode(file_get_contents($logFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg(), 3, "C:/xampp/htdocs/login/debug.log");
            $logData = []; // Reset to empty array if corrupted
        } else {
            error_log("JSON decode successful.", 3, "C:/xampp/htdocs/login/debug.log");
        }
    } else {
        error_log("File does not exist: " . $logFile, 3, "C:/xampp/htdocs/login/debug.log");
    }

    // Append new log data
    $logData[] = $data;
    error_log("Data to be logged: " . print_r($data, true), 3, "C:/xampp/htdocs/login/debug.log");

    // Save the updated log data back to the file
    $jsonData = json_encode($logData, JSON_PRETTY_PRINT);

    if ($jsonData === false) {
        $error = json_last_error_msg();
        error_log("JSON encode error: " . $error, 3, "C:/xampp/htdocs/login/debug.log");
        return;
    } else {
        error_log("JSON encode successful.", 3, "C:/xampp/htdocs/login/debug.log");
    }

    $result = file_put_contents($logFile, $jsonData);

    if ($result === false) {
        $error = error_get_last();
        error_log("File write error: " . print_r($error, true), 3, "C:/xampp/htdocs/login/debug.log");
        return;
    } else {
        error_log("File write successful. Bytes written: " . $result, 3, "C:/xampp/htdocs/login/debug.log");
    }
}

// Function to get the user's IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Initialize session variables if they don't exist
if (!isset($_SESSION['page_views'])) {
    $_SESSION['page_views'] = 0;
}
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time(); // Record the start time
}
if (!isset($_SESSION['previous_visits'])) {
    $_SESSION['previous_visits'] = 0;
}
if (!isset($_SESSION['traffic_source'])) {
    $_SESSION['traffic_source'] = get_client_ip(); // Simplistic approach
}

// Increment page views on each page load
$_SESSION['page_views']++;

// Handle Registration
if (isset($_POST['signUp'])) {
    error_log("Sign Up form submitted.", 3, "C:/xampp/htdocs/login/debug.log");

    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $keystrokeData = json_decode($_POST['keystrokeData'], true); // Decode keystroke data

    // Debugging: Check if keystroke data is received
    error_log("Keystroke Data: " . print_r($keystrokeData, true), 3, "C:/xampp/htdocs/login/debug.log");

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        // Insert new user into the database
        $insertQuery = "INSERT INTO users (firstName, lastName, email, password)
                        VALUES ('$firstName', '$lastName', '$email', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            // Log registration data
            $logData = [
                'Name' => $firstName . ' ' . $lastName,
                'Username' => $email,
                'Password' => $password, // Note: In a real-world scenario, never log plaintext passwords
                'Action' => 'Registration',
                'Timestamp' => date('Y-m-d H:i:s'), // Current timestamp
                'Keystroke Data' => $keystrokeData, // Log keystroke data
                'Place' => 'Registration Page',
                'Page Views' => $_SESSION['page_views'],
                'Session Duration' => time() - $_SESSION['session_start_time'], // Seconds
                'Bounce Rate' => 0, // Requires more sophisticated tracking
                'Traffic Source' => $_SESSION['traffic_source'],
                'Time on Page' => 0, // Requires JavaScript tracking
                'Previous Visits' => $_SESSION['previous_visits'],
                'Conversion Rate' => 0 // Requires more tracking
            ];
            logData($logData); // Log the data

            // Increment previous visits (after registration, treat as first visit)
            $_SESSION['previous_visits']++;

            // Redirect to the homepage or another page
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Handle Login
if (isset($_POST['signIn'])) {
    error_log("Sign In form submitted.", 3, "C:/xampp/htdocs/login/debug.log");

    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $keystrokeData = json_decode($_POST['keystrokeData'], true); // Decode keystroke data

    // Debugging: Check if keystroke data is received
    error_log("Keystroke Data: " . print_r($keystrokeData, true), 3, "C:/xampp/htdocs/login/debug.log");

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];

        // Log login data
        $logData = [
            'Name' => $row['firstName'] . ' ' . $row['lastName'],
            'Username' => $email,
            'Password' => $password, // Note: In a real-world scenario, never log plaintext passwords
            'Action' => 'Login',
            'Timestamp' => date('Y-m-d H:i:s'), // Current timestamp
            'Keystroke Data' => $keystrokeData, // Log keystroke data
            'Place' => 'Login Page',
            'Page Views' => $_SESSION['page_views'],
            'Session Duration' => time() - $_SESSION['session_start_time'], // Seconds
            'Bounce Rate' => 0, // Requires more sophisticated tracking
            'Traffic Source' => $_SESSION['traffic_source'],
            'Time on Page' => 0, // Requires JavaScript tracking
            'Previous Visits' => $_SESSION['previous_visits'],
            'Conversion Rate' => 0 // Requires more tracking
        ];
        logData($logData); // Log the data

        // Redirect to the homepage or another page
        header("Location: homepage.php");
        exit();
    } else {
        echo "Not Found, Incorrect Email or Password";
    }
}
?>
