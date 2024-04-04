<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

// Process form submission to add a notice
if (isset($_POST['submit'])) {
    // Add notice handling code
}

// Delete notice
if (isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $mysqli->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Notice deleted successfully";
        echo '<script>alert("Notice deleted successfully");</script>';
    } else {
        $_SESSION['error'] = "Error deleting notice: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all notices from the database
$stmt = $mysqli->query("SELECT * FROM notices");
$notices = $stmt->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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
	<title>Delete Notice</title>
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">
    <!-- Include your CSS and meta tags -->
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="ts-main-content">
        <?php include('includes/sidebar.php');?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Add Notice Form -->
                <!-- List of Notices -->
            </div>
            <div class="container mt-5">
                <h1 class="mb-4">Notices</h1>
                <?php if (isset($_SESSION['error'])) : ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])) : ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Image</th>
                            <th>Time Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notices as $notice) : ?>
                            <tr>
                                <td><?php echo $notice['title']; ?></td>
                                <td><?php echo $notice['content']; ?></td>
                                <td><img src="<?php echo $notice['image']; ?>" alt="Notice Image" style="max-width: 100px;"></td>
                                <td><?php echo $notice['timestamp']; ?></td>
                                <td>
                                    <a href="view_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-info btn-sm">View</a>
                                    <a href="edit_notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $notice['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
