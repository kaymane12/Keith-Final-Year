<?php
// Start the session
session_start();

// Include necessary files
include('includes/config.php');

// Check if the user is logged in
if (!isset($_SESSION['login'])) {
    // User is not logged in, redirect to login page or handle as needed
    header("location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    // Get user input from the form
    $message = $_POST['message'];
    $email = $_SESSION['login'];

    // Insert message into the database
    $stmt = $mysqli->prepare("INSERT INTO messages (email, time, message) VALUES (?, NOW(), ?)");
    $stmt->bind_param('ss', $email, $message);
    
    if ($stmt->execute()) {
        // Message sent successfully
        $_SESSION['success_message'] = "Message sent successfully.";
        
        // Close the statement
        $stmt->close();

        // Redirect to a specific page after sending the message
        header("location: livechat.php");
        exit();
    } else {
        // Error sending message
        $_SESSION['error_message'] = "Error sending message: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle invalid request or redirect as needed
    echo "Invalid request.";
}
?>
