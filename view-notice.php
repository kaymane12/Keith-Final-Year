<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $mysqli->prepare("SELECT id FROM userregistration WHERE email=? AND password=? ");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    
    if($id) {
        $_SESSION['id'] = $id;
        $_SESSION['login'] = $email;}}

// Proceed with the query

$notices = []; // Initialize $notices as an empty array

// Fetch all notices from the database
$stmt = $mysqli->query("SELECT * FROM notices");

if ($stmt) { // Check if the query executed successfully
    $notices = $stmt->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Handle query error
    $_SESSION['error'] = "Error fetching notices: " . $mysqli->error;
}

// Handle adding a new notice
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // File upload handling
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = 'uploads/' . $image_name; // Specify the upload directory

    // Check if the directory exists, and if not, create it
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($image_tmp, $image_path)) {
        $stmt = $mysqli->prepare("INSERT INTO notices (title, content, image, read_by) VALUES (?, ?, ?, NULL)");
        $stmt->bind_param('sss', $title, $content, $image_path);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Notice added successfully";
            echo '<script>alert("Notice added successfully");</script>';
        } else {
            $_SESSION['error'] = "Error adding notice: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to upload the image";
    }
}

// Fetch all notices from the database
// Mark notice as read and update the database
if (isset($_POST['mark_as_read'])) {
    $notice_id = $_POST['notice_id'];
    $user_id = $_SESSION['id'];
    $uemail=$_SESSION['login'];
    $read_date = date('Y-m-d H:i:s');
    

    // Check if the entry already exists
    $check_stmt = $mysqli->prepare("SELECT COUNT(*) FROM notice_readers WHERE notice_id = ? AND user_id = ?");
    $check_stmt->bind_param('ii', $notice_id, $user_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // Entry already exists, handle accordingly (e.g., update the read_date)
        // Example using UPDATE statement
        $update_stmt = $mysqli->prepare("UPDATE notice_readers SET read_date = ? WHERE notice_id = ? AND user_id = ?");
        $update_stmt->bind_param('sii', $read_date, $notice_id, $user_id);
        if ($update_stmt->execute()) {
            $_SESSION['success'] = "Notice marked as read successfully (updated existing entry)";
            echo '<script>alert("Notice marked as read successfully (updated existing entry)");</script>';
        } else {
            $_SESSION['error'] = "Error updating notice read status: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        // Entry does not exist, insert new entry
        $insert_stmt = $mysqli->prepare("INSERT INTO notice_readers (notice_id, user_id, emailid, read_date) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param('iiss', $notice_id, $user_id, $uemail, $read_date);
        if ($insert_stmt->execute()) {
            $_SESSION['success'] = "Notice marked as read successfully (new entry)";
            echo '<script>alert("Notice marked as read successfully (new entry)");</script>';
        } else {
            $_SESSION['error'] = "Error marking notice as read: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<meta name="theme-color" content="#3e454c">
<title>Noticeboard</title>
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-social.css">
<link rel="stylesheet" href="css/bootstrap-select.css">
<link rel="stylesheet" href="css/fileinput.min.css">
<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
<link rel="stylesheet" href="css/style.css">
<style>
    .card-img-top {
        height: 200px; /* Set the desired height for all card images */
        object-fit: cover; /* Ensure the image covers the entire space */
    }
</style>
<!-- Include your CSS and meta tags -->
</head>
<body>
<?php include('includes/header.php');?>
<div class="ts-main-content">
    <?php include('includes/sidebar.php');?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="container mt-5">
                        <h1 class="mb-4">Notices</h1>
                        <div class="row">
                            <?php foreach ($notices as $notice) : ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card">
                                        <img src="<?php echo $notice['image']; ?>" class="card-img-top" alt="Notice Image">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $notice['title']; ?></h5>
                                            <p class="card-text"><?php echo $notice['content']; ?></p>
                                            <p class="card-text"><small class="text-muted">Added: <?php echo $notice['timestamp']; ?></small></p>
                                            <?php if (isset($notice['read_by']) && $notice['read_by'] !== null) : ?>
                                                <p class="card-text"><small class="text-muted">Read by: <?php echo $notice['read_by']; ?></small></p>
                                            <?php else : ?>
                                                <form method="post">
                                                    <input type="hidden" name="notice_id" value="<?php echo $notice['id']; ?>">
                                                    <button type="submit" name="mark_as_read" id="mark_as_read_<?php echo $notice['id']; ?>" class="btn btn-success">Mark as Read</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

