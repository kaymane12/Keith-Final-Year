<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

// Process form submission to add a notice
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // Handle image upload if provided
    $image = '';
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image = "uploads/" . $image_name; // Assuming 'uploads' is your upload directory
        move_uploaded_file($image_tmp, $image);
    }

    // Insert notice into the database
    $stmt = $mysqli->prepare("INSERT INTO notices (title, content, image) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $title, $content, $image);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Notice added successfully";
    } else {
        $_SESSION['error'] = "Error adding notice: " . $stmt->error;
    }
    $stmt->close();
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Delete notice
if (isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $mysqli->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Notice deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting notice: " . $stmt->error;
    }
    $stmt->close();
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch all notices from the database
$stmt = $mysqli->query("SELECT * FROM notices");
$notices = $stmt->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Admin Noticeboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

    

        .btn-info {
            background-color: #17a2b8;
            color: #fff;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #117a8b;
            border-color: #117a8b;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="ts-main-content">
        <?php include('includes/sidebar.php');?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Add Notice Form -->
                <div class="add-notice-form">
                    <h2>Add Notice</h2>
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>
                        <label for="content">Content:</label>
                        <textarea id="content" name="content" rows="4" required></textarea>
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <button type="submit" name="submit">Add Notice</button>
                    </form>
                </div>
                <!-- Adding Notice -->
                <div class="adding-notice">
                    <h2>Adding Notice</h2>
                    <p>Here you can add notices. Fill out the form above to add a new notice.</p>
                </div>
                <!-- List of Notices -->
                <div class="notice-list">
                    <h2>Notices</h2>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                    <td><?php echo $notice['id']; ?></td>
                                    <td><?php echo $notice['title']; ?></td>
                                    <td><?php echo $notice['content']; ?></td>
                                    <td><img src="<?php echo $notice['image']; ?>" alt="Notice Image" style=" max-width: 100px; border-radius: 5px;"></td>                                            max-width: 100px; border-radius: 5px;"></td>
                                    <td><?php echo $notice['timestamp']; ?></td>
                                    <td>
                                        <a href="edit-notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                        <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $notice['id']; ?>">
                                            <button type="submit" name="delete" class="btn btn-danger btn-sm"><i class="fa fa-close"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
