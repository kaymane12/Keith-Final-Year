<?php
include('includes/config.php');

$sql = "SELECT * FROM messages ORDER BY id ASC";
$result = $mysqli->query($sql);

if ($result) {
    $messages = array(); // Initialize an array to store messages

    // Fetch associative array of messages
    while ($row = $result->fetch_assoc()) {
        $messages[] = array(
            'id' => $row['id'],
            'email' => $row['email'],
            'time' => $row['time'],
            'message' => $row['message']
        );
    }

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($messages);
} else {
    // Handle query error
    echo json_encode(array('error' => 'Error fetching messages.'));
}

$mysqli->close();
?>
