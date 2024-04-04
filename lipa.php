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

    // Update the balance in the finance table
    $updateBalanceStmt = $mysqli->prepare("UPDATE finance SET balance = ? WHERE emailid = ?");
    $updateBalanceStmt->bind_param("ds", $balance, $_SESSION['login']);
    $updateBalanceStmt->execute();
    $updateBalanceStmt->close();

    // Check if $_SESSION['total_fee'] is set and not empty
    if (isset($_SESSION['total_fee']) && $_SESSION['total_fee'] !== '') {
        $totalFee = $_SESSION['total_fee'];
    } else {
        // Handle the case when $_SESSION['total_fee'] is not set or empty
        $totalFee = "N/A"; // Set a default value or display an error message
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPESA STK Push</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include("includes/header.php");?>

    <div class="ts-main-content">
    <div class="ts-main-content">
        <?php include("includes/sidebar.php");?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <!-- User Info Card -->
                        <div class="card mt-5">
                            <div class="card-body">
                                <h5 class="card-title">User Info</h5>
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
                                                    <td>Email</td>
                                                    <td><?php echo $emailid; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Balance:</b></td>
                                                    <td><?php echo isset($balance) ? $balance : 'N/A'; ?></td>
                                                </tr>
                                        <!-- Add other user info fields as needed -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Payment Card -->
                        <div class="card mt-5">
                            <div class="card-body">
                            <p class="heading">PAYMENT DETAILS</p>
					<form class="card-details " method="POST" action="./mpesa.php" id="form">
						<div class="form-group mb-0">
                                <div class="feedback" id="feedback"></div>
								<p class="text-warning mb-0">Phone Number</p> 
                          		<input type="text" name="phone-num" placeholder="254" size="17" id="cno" minlength="12" maxlength="12" id="phone">
								<img src="./uploads/mpesa.png" width="64px" height="60px" />
                        </div>

                        <div class="form-group">
                            <p class="text-warning mb-0">Amount</p> <input type="text" name="amount" placeholder="" size="17" id="amount">
                        </div>
                        <div class="form-group pt-2">
                        	<div class="row d-flex">

                        		<div class="col-sm-7 pt-0">
                        			<button type="submit" class="btn btn-primary" id="pay" class="pay-button" name="pay">  Pay<i class="fas fa-arrow-right px-3 py-2"></i></button>
                        		</div>
                        	</div>
                        </div>		
					</form>
			</div>
		</div>
	</div>
</div>
<script>
   $(() => {
        $("#pay").on('click', async (e) => {
            e.preventDefault()

            $("#pay").text('Please wait...').attr('disabled', true)
            const form = $('#form').serializeArray()

            var indexed_array = {};
            $.map(form, function(n, i) {
                indexed_array[n['name']] = n['value'];
            });

            const _response = await fetch('mpesa.php', {
                method: 'post',
                body: JSON.stringify(indexed_array),
                mode: 'no-cors',
            })

            const response = await _response.json()
            $("#pay").text('Pay').attr('disabled', false)
            $("#pay").html(`Pay <i class="fas fa-arrow-right px-3 py-2"></i>`).attr('disabled', false)

            if (response && response.ResponseCode == 0) {
                $('#feedback').html(`<p class='alert alert-success'>${response.CustomerMessage}</p>`)
            } else {
                $('#feedback').html(`<p class='alert alert-danger'>Error! ${response.errorMessage}</p>`)
            }
        })
    })
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>