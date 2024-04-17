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
        header("Location: notice.php"); // Redirect back to noticeboard page
        exit();
    }
} else {
    $_SESSION['error'] = "Notice ID not specified";
    header("Location: notice.php"); // Redirect back to noticeboard page
    exit();
}

// Handle form submission to update notice
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Input validation
    if (empty($title) || empty($content)) {
        $_SESSION['error'] = "Title and content are required.";
        header("Location: edit_notice.php?id=$notice_id");
        exit();
    }

    // Initialize variables for file upload
    $image_name = $notice['image'];
    $image_updated = false;

    // File upload handling (optional)
    if (!empty($_FILES['image']['name'])) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $image_name = basename($_FILES['image']['name']);
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_path = 'uploads/' . $notice_id . '_' . time() . '.' . $image_extension; // Save with unique filename

        // Validate file extension
        if (!in_array($image_extension, $allowed_extensions)) {
            $_SESSION['error'] = "Invalid image file type.";
            header("Location: edit_notice.php?id=$notice_id");
            exit();
        }

        // Check if the uploads directory exists, and if not, create it
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($image_tmp, $image_path)) {
            // If the previous image exists and is different, delete it
            if ($notice['image'] && file_exists($notice['image']) && $notice['image'] !== $image_path) {
                unlink($notice['image']);
            }
            $image_updated = true;
        } else {
            $_SESSION['error'] = "Failed to upload the image.";
            header("Location: edit_notice.php?id=$notice_id");
            exit();
        }
    }

    // Determine the appropriate SQL statement
    if ($image_updated) {
        $stmt = $mysqli->prepare("UPDATE notices SET title = ?, content = ?, image = ? WHERE id = ?");
        $stmt->bind_param('sssi', $title, $content, $image_path, $notice_id);
    } else {
        $stmt = $mysqli->prepare("UPDATE notices SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param('ssi', $title, $content, $notice_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Notice updated successfully";
        header("Location: notice.php"); // Redirect back to noticeboard page
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
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($notice['title'], ENT_QUOTES); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Content:</label>
                            <textarea name="content" class="form-control" rows="4" required><?php echo htmlspecialchars($notice['content'], ENT_QUOTES); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Current Image:</label>
                            <?php if (!empty($notice['image'])): ?>
                                <img src="<?php echo htmlspecialchars($notice['image'], ENT_QUOTES); ?>" alt="Current Notice Image" style="max-width: 100px;">
                            <?php else: ?>
                                <p>No image available</p>
                            <?php endif; ?>
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
