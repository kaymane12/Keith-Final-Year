<?php
// Start the session
session_start();

// Include necessary files
include('includes/config.php');
include('includes/checklogin.php');

// Check if the user is already logged in
check_login();

// Process login form submission
if (isset($_POST['submit'])) {
    // Get user input from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to verify user credentials
    $stmt = $mysqli->prepare("SELECT id, email FROM userregistration WHERE email=? AND password=?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->store_result();

    // If the user exists, bind the result and fetch the user details
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $email);
        $stmt->fetch();

        // Store user information in session variables
        $_SESSION['id'] = $id;
        $_SESSION['login'] = $email;

        // Close the statement
        $stmt->close();

        // Update last login details
        $uip = $_SERVER['REMOTE_ADDR'];
        $ldate = date('d/m/Y h:i:s', time());

        // Redirect to the dashboard or any other page
        header("location: dashboard.php");
        exit();
    } else {
        // If login fails, redirect back to the login page with an error message
        $_SESSION['errmsg'] = "Invalid email or password";
        header("location: login.php");
        exit();
    }
}

// If the user is already logged in, fetch their details
if (isset($_SESSION['login'])) {
    // Fetch student details from the database
    $aid = $_SESSION['login'];
    $stmt = $mysqli->prepare("SELECT firstName, lastName, regno, gender, contactno, egycontactno, emailid, corresPincode, corresCity, foodstatus FROM registration WHERE emailid = ?");
    $stmt->bind_param("s", $aid);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $regno, $gender, $contactno, $egycontactno, $emailid, $corresPincode, $corresCity, $foodstatus);
    $stmt->fetch();
    $stmt->close();

    // Fetch balance from the finance table
    $stmtBalance = $mysqli->prepare("SELECT total_fee, amount_paid FROM finance WHERE emailid = ?");
    $stmtBalance->bind_param("s", $_SESSION['login']);
    $stmtBalance->execute();
    $stmtBalance->bind_result($totalFee, $amountPaid);
    $stmtBalance->fetch();
    $stmtBalance->close();

    // Calculate the balance
    $balance = $totalFee - $amountPaid;

    // Check if $_SESSION['total_fee'] is set and not empty
    if (isset($_SESSION['total_fee']) && $_SESSION['total_fee'] !== '') {
        $totalFee = $_SESSION['total_fee'];
    } else {
        // Handle the case when $_SESSION['total_fee'] is not set or empty
        $totalFee = "N/A"; // Set a default value or display an error message
    }
}
?>


<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">

    <title>DashBoard</title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .panel-heading {
            background-color: #5bc0de;
            color: white;
        }

        .panel-body {
            min-height: 20px;
            align-content: right;
        }

        .panel-footer {
            background-color: #5bc0de;
            color: white;
            text-align: center;
        }

        .stat-panel-number {
            font-size: 34px;
        }

        .block-anchor {
            color: #5bc0de;
        }

        .block-anchor:hover {
            color: #31708f;
            text-decoration: none;
        }

        .footer {
            text-align: center;
            border: 1px solid black;
            margin-top: 20px;
            padding: 10px;
        }

        .content-wrapper {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include("includes/header.php");?>

    <div class="ts-main-content">
        <?php include("includes/sidebar.php");?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title">Dashboard</h2>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-body bk-primary text-light">
                                                <div class="stat-panel text-center">
                                                    <div class="stat-panel-number h1">My Profile</div>
                                                </div>
                                            </div>
                                            <a href="my-profile.php" class="block-anchor panel-footer">Full Detail <i class="fa fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-body bk-primary text-light">
                                                <div class="stat-panel text-center">
                                                    <div class="stat-panel-number h1">My Room</div>
                                                </div>
                                            </div>
                                            <a href="room-details.php" class="block-anchor panel-footer">See All <i class="fa fa-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Basic Info</div>
                                    <div class="panel-body">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td>Name</td>
                                                    <td><?php echo $firstName . ' ' . $lastName; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Registration Number</td>
                                                    <td><?php echo $regno; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Gender</td>
                                                    <td><?php echo $gender; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Contact Info</div>
                                    <div class="panel-body">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td>Contact Number</td>
                                                    <td><?php echo $contactno; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Emergency Contact</td>
                                                    <td><?php echo $egycontactno; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><?php echo $emailid; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Postal Code</td>
                                                    <td><?php echo $corresPincode; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Town</td>
                                                    <td><?php echo $corresCity; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Financial Info</div>
                                    <div class="panel-body">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td><b>Total Fee:</b></td>
                                                    <td><?php echo isset($totalFee) ? $totalFee : 'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Amount Paid:</b></td>
                                                    <td><?php echo isset($amountPaid) ? $amountPaid : 'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Balance:</b></td>
                                                    <td><?php echo isset($balance) ? $balance : 'N/A'; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript imports and scripts -->

    <div class="footer">
        <footer>
            <p> Brought To You By <a href="https://code-projects.org/">Code-Projects</a></p>
        </footer>
    </div>
</body>

</html>
