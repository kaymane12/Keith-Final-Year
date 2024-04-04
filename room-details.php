<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');

check_login();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $mysqli->prepare("SELECT id FROM userregistration WHERE email=? AND password=? ");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    
    if ($id) {
        $_SESSION['id'] = $id;
        $_SESSION['login'] = $email;
    }
}

// Proceed with updating or inserting total_fee and emailid into the finance table

if (isset($_SESSION['login'])) {
    $uemail = $_SESSION['login']; // Get the user's email from session
    $totalFee = $_SESSION['total_fee']; // Get the total fee from session
    
    // Check if the emailid already exists in the finance table
    $check_stmt = $mysqli->prepare("SELECT COUNT(*) FROM finance WHERE emailid = ?");
    $check_stmt->bind_param('s', $uemail);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // Email ID already exists, update the total_fee for the existing entry
        $update_stmt = $mysqli->prepare("UPDATE finance SET total_fee = ? WHERE emailid = ?");
        $update_stmt->bind_param('ds', $totalFee, $uemail);
        if ($update_stmt->execute()) {
            echo '<script>alert("Total fee updated in finance table");</script>';
        } else {
            echo '<script>alert("Error updating total fee in finance table");</script>';
        }
        $update_stmt->close();
    } else {
        // Email ID does not exist, insert a new entry with total_fee
        $insert_stmt = $mysqli->prepare("INSERT INTO finance (emailid, total_fee) VALUES (?, ?)");
        $insert_stmt->bind_param('sd', $uemail, $totalFee);
        if ($insert_stmt->execute()) {
            echo '<script>alert("New entry with total fee inserted into finance table");</script>';
        } else {
            echo '<script>alert("Error inserting new entry with total fee into finance table");</script>';
        }
        $insert_stmt->close();
    }
}
?>




        // Insert or update total fee and email ID into finance table using ON DUPLICATE KEY UPDATE
        $stmtInsert = $mysqli->prepare("INSERT INTO finance (emailid, total_fee) VALUES (?, ?) ON DUPLICATE KEY UPDATE total_fee = ?");
        $stmtInsert->bind_param("sdd", $emailid, $totalFee, $totalFee);
        $stmtInsert->execute();
        $stmtInsert->close();
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
	<title>Room Details</title>
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">
<script language="javascript" type="text/javascript">
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
 if(popUpWin)
{
if(!popUpWin.closed) popUpWin.close();
}
popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+510+',height='+430+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}

</script>

</head>

<body>
	<?php include('includes/header.php');?>

	<div class="ts-main-content">
			<?php include('includes/sidebar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h2 class="page-title">Rooms Details</h2>
						<div class="panel panel-default">
							<div class="panel-heading">All Room Details</div>
							<div class="panel-body">
								<table id="zctb" class="table table-bordered " cellspacing="0" width="100%">
									
									
									<tbody>
<?php	
$aid=$_SESSION['login'];
	$ret="select * from registration where emailid=?";
$stmt= $mysqli->prepare($ret) ;
$stmt->bind_param('s',$aid);
$stmt->execute() ;
$res=$stmt->get_result();
$cnt=1;
while($row=$res->fetch_object())
	  {
	  	?>

<tr>
<td colspan="4"><h4>Room Realted Info</h4></td>
<td><a href="javascript:void(0);"  onClick="popUpWindow('http://localhost/hostel/full-profile.php?id=<?php echo $row->emailid;?>');" title="View Full Details">Print Data</a></td>
</tr>
<tr>
<td colspan="6"><b>Reg no. :<?php echo $row->postingDate;?></b></td>
</tr>



<tr>
<td><b>Room no :</b></td>
<td><?php echo $row->roomno;?></td>
<td><b>Seater :</b></td>
<td><?php echo $row->seater;?></td>
<td><b>Fees PM :</b></td>
<td><?php echo $fpm=$row->feespm;?></td>
</tr>

<tr>
<td><b>Food Status:</b></td>
<td>
<?php if($row->foodstatus==0)
{
echo "Without Food";
}
else
{
echo "With Food";
}
;?></td>
<td><b>Stay From :</b></td>
<td><?php echo $row->stayfrom;?></td>
<td><b>Duration:</b></td>
<td><?php echo $dr=$row->duration;?> Months</td>
</tr>

<tr>
<td colspan="6"><b>Total Fee : 
<?php
// Your calculation logic here
if ($row->foodstatus == 1) { 
    $fd = 2000; 
    $totalFee = (($dr * $fpm) + $fd);
} else {
    $totalFee = $dr * $fpm;
}

// Store the total fee in the session
$_SESSION['total_fee'] = $totalFee;
$uemail=$_SESSION['login'];

// Display the total fee
echo '<tr>';
echo '<td colspan="6"><b>Total Fee : ' . $totalFee . '</b></td>';
echo '</tr>';

// Insert the fee into the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hostel', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement
    $stmt = $pdo->prepare("INSERT INTO finance (total_fee) VALUES (:total_fee)");

    // Bind the parameter
    $stmt->bindParam(':total_fee', $totalFee);

    // Execute the query
    $stmt->execute();

    // Optionally, you can handle success or show a message to the user
    echo '<p>Fee inserted into the database successfully.</p>';
} catch (PDOException $e) {
    // Handle database errors here
    echo '<p>Error: ' . $e->getMessage() . '</p>';
}
?>

</b></td>
</tr>
<tr>
<td colspan="6"><h4>Personal Info Info</h4></td>
</tr>

<tr>
<td><b>Reg No. :</b></td>
<td><?php echo $row->regno;?></td>
<td><b>Full Name :</b></td>
<td><?php echo $row->firstName;?><?php echo $row->middleName;?><?php echo $row->lastName;?></td>
<td><b>Email :</b></td>
<td><?php echo $row->emailid;?></td>
</tr>


<tr>
<td><b>Contact No. :</b></td>
<td><?php echo $row->contactno;?></td>
<td><b>Gender :</b></td>
<td><?php echo $row->gender;?></td>
<td><b>Course :</b></td>
<td><?php echo $row->course;?></td>
</tr>


<tr>
<td><b>Emergency Contact No. :</b></td>
<td><?php echo $row->egycontactno;?></td>
<td><b>Guardian Name :</b></td>
<td><?php echo $row->guardianName;?></td>
<td><b>Guardian Relation :</b></td>
<td><?php echo $row->guardianRelation;?></td>
</tr>

<tr>
<td><b>Guardian Contact No. :</b></td>
<td colspan="6"><?php echo $row->guardianContactno;?></td>
</tr>

<tr>
<td colspan="6"><h4>Addresses</h4></td>
</tr>
<tr>
<td><b>Correspondense Address</b></td>
<td colspan="2">
<?php echo $row->corresAddress;?><br />
<?php echo $row->corresCIty;?>, <?php echo $row->corresPincode;?><br />
<?php echo $row->corresState;?>


</td>
<td><b>Permanent Address</b></td>
<td colspan="2">
<?php echo $row->pmntAddress;?><br />
<?php echo $row->pmntCity;?>, <?php echo $row->pmntPincode;?><br />
<?php echo $row->pmnatetState;?>	

</td>
</tr>


<?php
$cnt=$cnt+1;
} ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

</body>

</html>
