<?php
include("includes/config.php");

if (isset($_POST['delete_messages'])) {
    // Check if there are any messages to delete
    $sql_check = "SELECT COUNT(*) as count FROM messages";
    $result_check = $mysqli->query($sql_check);

    if ($result_check && $row = $result_check->fetch_assoc()) {
        if ($row['count'] > 0) {
            $sql_delete = "DELETE FROM messages";
            if ($mysqli->query($sql_delete)) {
                // Redirect to chat.php after successful deletion
                header("Location: chat.php");
                exit();
            } else {
                echo "Error deleting messages: " . $mysqli->error;
            }
        } else {
            // No messages to delete, show message and redirect
            echo "No messages to delete.";
            header("refresh:1;url=chat.php"); // Redirect after 3 seconds
            exit();
        }
    } else {
        echo "Error checking messages: " . $mysqli->error;
    }
}
?>
