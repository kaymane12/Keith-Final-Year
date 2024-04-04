<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

if (isset($_GET['id'])) {
    $notice_id = $_GET['id'];

    // Fetch notice details based on ID
    $stmt = $mysqli->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->bind_param('i', $notice_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notice = $result->fetch_assoc();
    $stmt->close();

    if (!$notice) {
        $_SESSION['error'] = "Notice not found";
        header("Location: noticeboard.php"); // Redirect back to noticeboard page
        exit();
    }
} else {
    $_SESSION['error'] = "Notice ID not specified";
    header("Location: noticeboard.php"); // Redirect back to noticeboard page
    exit();
}

// Handle form submission to update notice
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // File upload handling (optional)
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = 'uploads/' . $image_name; // Specify the upload directory

        // Check if the directory exists, and if not, create it
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Update notice with new image path
            $stmt = $mysqli->prepare("UPDATE notices SET title = ?, content = ?, image = ? WHERE id = ?");
            $stmt->bind_param('sssi', $title, $content, $image_path, $notice_id);
        } else {
            $_SESSION['error'] = "Failed to upload the image";
            header("Location: edit_notice.php?id=$notice_id"); // Redirect back to edit page
            exit();
        }
    } else {
        // Update notice without changing the image
        $stmt = $mysqli->prepare("UPDATE notices SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param('ssi', $title, $content, $notice_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Notice updated successfully";
        header("Location: noticeboard.php"); // Redirect back to noticeboard page
        exit();
    } else {
        $_SESSION['error'] = "Error updating notice: " . $stmt->error;
        header("Location: edit_notice.php?id=$notice_id"); // Redirect back to edit page
        exit();
    }

    $stmt->close();
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
	<title>Edit Notice</title>
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
                <div class="container mt-5">
                    <h1 class="mb-4">Edit Notice</h1>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" name="title" class="form-control" value="<?php echo $notice['title']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Content:</label>
                            <textarea name="content" class="form-control" rows="4" required><?php echo $notice['content']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Current Image:</label>
                            <img src="<?php echo $notice['image']; ?>" alt="Current Notice Image" style="max-width: 100px;">
                        </div>
                        <div class="form-group">
                            <label>Upload New Image (optional):</label>
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Update Notice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
