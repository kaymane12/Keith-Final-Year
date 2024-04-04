<?php
// Start the session
session_start();

// Include necessary files
include('includes/config.php');
include('includes/checklogin.php');

// Check if the user is already logged in
check_login();

// Debugging: Check if $_SESSION['total_fee'] is set and not empty
var_dump($_SESSION['total_fee']); // This will output the value of $_SESSION['total_fee'] for debugging purposes

// If the user is already logged in, fetch their details
if (isset($_SESSION['login'])) {
    // Fetch student details from the database
    $aid = $_SESSION['login'];
    $stmt = $mysqli->prepare("SELECT firstName, lastName, regno, gender  FROM registration WHERE emailid = ?");
    $stmt->bind_param("s", $aid);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $regno, $gender);
    $stmt->fetch();
    $stmt->close();

    // Check if $_SESSION['total_fee'] is set and not empty
    if (isset($_SESSION['total_fee']) && $_SESSION['total_fee'] !== '') {
        $totalFee = $_SESSION['total_fee'];
    } else {
        // Handle the case when $_SESSION['total_fee'] is not set or empty
        $totalFee = "N/A"; // Set a default value or display an error message
    }
}

// Handle Lipa na M-Pesa STK push form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['phone']) && isset($_POST['amount'])) {
    // Process the form data, initiate Lipa na M-Pesa STK push, etc.
    // You will need to integrate with your specific payment gateway or service for this functionality.
    // This part will depend on the API or SDK provided by your payment service provider.
    // Here, we will just display a message for demonstration purposes.
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $message = "Initiating Lipa na M-Pesa STK push for $amount KES to $phone"; // Example message, replace with actual logic
    echo "<script>alert('$message');</script>"; // Display a JavaScript alert with the message
}
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
<title>User Summary</title>
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
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
    }
    .user-card,
    .lipa-card {
        margin-bottom: 20px;
    }
    .card {
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background-color: #f4f4f4;
        padding: 10px;
    }
    .card-body {
        padding: 15px;
    }
    .lipa-card .card-body {
        text-align: center;
    }
    .lipa-card input[type="text"],
    .lipa-card input[type="submit"] {
        padding: 10px;
        margin: 5px;
        width: 40%;
        box-sizing: border-box;
    }
    .lipa-card input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }
    .lipa-card input[type="submit"]:hover {
        background-color: #45a049;
    }
</style>
</head>
<body>
<?php include("includes/header.php"); ?>

<div class="ts-main-content">
    <?php include('includes/sidebar.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <h2>User Summary</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="card user-card">
                        <div class="card-header">
                            <h3 class="card-title">User Details</h3>
                        </div>
                        <div class="card-body">
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
                                    <tr>
                                        <td>Balance</td>
                                        <td><?php echo $totalFee; ?></td>
                                    </tr>
                                    <!-- Add more user details here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card lipa-card">
                        <div class="card-header">
                            <h3 class="card-title">Lipa na M-Pesa</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                Phone Number: <input type="text" name="phone" required><br><br>
                                Amount: <input type="text" name="amount" required><br><br>
                                <input type="submit" value="Pay">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
