<?php
require("config.php");

$sql = "SELECT * FROM messages ORDER BY id ASC";
$res = $mysqli->query($sql);
if (!$res) {
    die("Error fetching messages: " . $mysqli->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<meta name="theme-color" content="#3e454c">
<title>Chat</title>
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-social.css">
<link rel="stylesheet" href="css/bootstrap-select.css">
<link rel="stylesheet" href="css/fileinput.min.css">
<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
<link rel="stylesheet" href="css/style.css">
<style>
    body {
        background-color: #f8f9fa; /* Light gray background */
        padding: 20px;
    }
    .chat-container {
        max-width: 800px;
        margin: auto;
    }
    .chat-header {
        background-color: #007bff; /* Bootstrap primary color */
        color: #fff;
        padding: 10px;
        border-radius: 5px 5px 0 0;
    }
    .chat-messages {
        list-style-type: none;
        padding: 0;
    }
    .chat-message {
        background-color: #f1f1f1; /* Light gray for messages */
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 5px;
    }
    .chat-message span {
        display: block;
        margin-bottom: 5px;
    }
    .chat-input {
        margin-top: 20px;
    }
</style>
</head>
<body>
<?php include('includes/header.php');?>
<div class="ts-main-content">
    <?php include('includes/sidebar.php');?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div id="chat" class="chat-container">
                        <div class="chat-header">
                            <h3 class="text-center">Chat</h3>
                        </div>
                        <ul id="messages" class="chat-messages">
                            <?php if ($res->num_rows > 0) { ?>
                                <?php while ($message = $res->fetch_assoc()) { ?>
                                    <li id="<?php echo $message['id']; ?>" class="chat-message">
                                        <span><?php echo $message['email']; ?></span>
                                        <span><?php echo $message['time']; ?></span>
                                        <span><?php echo $message['message']; ?></span>
                                    </li>
                                <?php } ?>
                            <?php } else { ?>
                                <li id="0" class="chat-message">No messages sent yet</li>
                            <?php } ?>
                        </ul>
                        <div id="message" class="chat-input">
                       <!-- Modify the form action to point to send_message.php -->
<form action="send_message.php" method="POST">
    <input type="hidden" name="send_message" value="1">
    <textarea id="message" name="message" rows="4" cols="80" class="form-control"></textarea>
    <input type="submit" value="Send" class="btn btn-primary mt-2" />
</form>
    <form id="deleteForm" action="delete_messages.php" method="POST">
        <input type="hidden" name="delete_messages" value="1">
        <button id="deleteBtn" type="button" class="btn btn-danger mt-2" onclick="confirmDelete()">Delete All Messages</button>

    </form>
</div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS and jQuery (for AJAX) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type='text/javascript'>
    $(document).ready(function() {
        $('#deleteBtn').click(function() {
            // Check if there are messages to delete
            if ($('#messages li').length === 0) {
                alert('No messages to delete yet.');
                return; // Exit function if no messages
            }

            // Show confirmation dialog
            if (confirm('Are you sure you want to delete all messages?')) {
                // If user confirms, submit the form
                $('#deleteForm').submit();
            }
        });
    });
</script>
<script type='text/javascript'>
    var format_message = function(json) {
        var user = '<span>' + json.email + '</span>'; // Assuming 'user' column holds email
        var time = '<span>' + json.time + '</span>';
        var message = '<span>' + json.message + '</span>';
        return '<li id="' + json.id + '" class="chat-message">' +
                      user +
                      time +
                      message +
                      '</li>';
    };
    var getChatMessages = function() {
        var since_id = $("#messages li:last-child").attr('id');
        $.ajax({
            url: 'get_messages.php',
            type: 'GET',
            data: { 'since_id': since_id },
            success: function(data, textStatus) {
                if (data.length) {
                    $.each(data, function(i) {
                        var msg = format_message(this);
                        $("#messages").append(msg);
                    });
                }
            },
            dataType: "json"
        });
    };
    var polling = setInterval(getChatMessages, 1000);
</script>
</body>
</html>
