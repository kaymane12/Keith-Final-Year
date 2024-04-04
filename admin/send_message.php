<?php
// Start the session
session_start();

// Include necessary files
include('includes/config.php');

// Debug: Check session variables
var_dump($_SESSION);

// Set the admin's email in the session
$_SESSION['admin_email'] = 'admin@gmail.com';


// Check if the form is submitted for sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    // Get user input from the form
    $message = $_POST['message'];
    
    // Get admin's email from the admin session
    $admin_email = $_SESSION['admin_email'];
    
    // Insert message into the database using admin's email
    $stmt = $mysqli->prepare("INSERT INTO messages (email, time, message) VALUES (?, NOW(), ?)");
    $stmt->bind_param('ss', $admin_email, $message);
    
    if ($stmt->execute()) {
        // Message sent successfully
        $_SESSION['success_message'] = "Message sent successfully.";
    } else {
        // Error sending message
        $_SESSION['error_message'] = "Error sending message: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    // Redirect to chat page after sending the message
    header("location: chat.php");
    exit();
} else {
    // Handle invalid request or redirect as needed
    $_SESSION['error_message'] = "Invalid request.";
    header("location: chat.php");
    exit();
}
?>
