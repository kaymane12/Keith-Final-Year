<?php
session_start();

$mysql_hostname = "localhost";
$mysql_user = "root";
$mysql_password = "";
$mysql_database = "hostel";

// Create connection
$mysqli = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);

// Check connection
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

// Fetch data using prepared statement
$id = $_GET['id'];
$stmt = $mysqli->prepare("SELECT * FROM registration WHERE emailid = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

// Display fetched data
if ($row = $result->fetch_assoc()) {
    // Your HTML and PHP code to display data goes here
    // For example:
    $firstName = ucfirst($row['firstName']);
    $lastName = ucfirst($row['lastName']);
    $postingDate = $row['postingDate'];
    $roomNo = $row['roomno'];
    $seater = $row['seater'];
    $feesPm = $row['feespm'];
    $foodStatus = $row['foodstatus'];
    $stayFrom = $row['stayfrom'];
    $duration = $row['duration'];
    $regNo = $row['regno'];
    $middleName = $row['middleName'];
    $gender = $row['gender'];
    $contactNo = $row['contactno'];
    $emailId = $row['emailid'];
    $egyContactNo = $row['egycontactno'];
    $guardianName = $row['guardianName'];
    $guardianRelation = $row['guardianRelation'];
    $guardianContactNo = $row['guardianContactno'];
    $corresAddress = $row['corresAddress'];
    $corresCity = $row['corresCIty'];
    $corresState = $row['corresState'];
    $corresPincode = $row['corresPincode'];
    $pmntAddress = $row['pmntAddress'];
    $pmntCity = $row['pmntCity'];
    $pmntState = $row['pmnatetState'];
    $pmntPincode = $row['pmntPincode'];
} else {
    echo "No records found";
}

$stmt->close();
$mysqli->close();
?>
<!-- Your HTML code continues from here -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Student Information</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="hostel.css" rel="stylesheet" type="text/css">
<script>
function printDocument() {
    window.print();
    return false; // Prevent the default form submission
}
</script>
</head>

<body>
<table width="100%" border="0">
    <tr>
        <td colspan="2" align="center" class="font1">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" align="center" class="font1">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" class="font"><?php echo "$firstName $lastName's"; ?> <span class="font1"> information &raquo;</span> </td>
    </tr>
    <tr>
        <td colspan="2" class="font">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <div align="right">Reg Date : <span class="comb-value"><?php echo $postingDate; ?></span></div>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="heading" style="color: red;">Room Related Info &raquo; </td>
    </tr>
    <tr>
        <td colspan="2" class="font1">
            <table width="100%" border="0">
                <tr>
                    <td width="32%" valign="top" class="heading">Room no : </td>
                    <td class="comb-value1"><span class="comb-value"><?php echo $roomNo; ?></span></td>
                </tr>
                <tr>
                    <td width="22%" valign="top" class="heading">Seater : </td>
                    <td class="comb-value1"><span class="comb-value"><?php echo $seater; ?></span></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Fees PM : </td>
                    <td class="comb-value1"><?php echo $feesPm; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Food Status: </td>
                    <td class="comb-value1"><?php echo $foodStatus == 0 ? "Without Food" : "With Food"; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Staying From: </td>
                    <td class="comb-value1"><?php echo $stayFrom; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Duration: </td>
                    <td class="comb-value1"><?php echo $duration; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Total Fee: </td>
                    <td class="comb-value1">
                        <?php
                        if ($foodStatus == 1) {
                            $fd = 2000;
                            echo (($duration * $feesPm) + $fd);
                        } else {
                            echo $duration * $feesPm;
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="left" class="heading" style="color: red;">Personal Info &raquo; </td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Course: </td>
                    <td class="comb-value1"><?php echo $row['course']; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Reg no: </td>
                    <td class="comb-value1"><?php echo $regNo; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">First Name: </td>
                    <td class="comb-value1"><?php echo $firstName; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Middle name: </td>
                    <td class="comb-value1"><?php echo $middleName; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Last: </td>
                    <td class="comb-value1"><?php echo $lastName; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Gender: </td>
                    <td class="comb-value1"><?php echo $gender; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Contact No: </td>
                    <td class="comb-value1"><?php echo $contactNo; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Email id: </td>
                    <td class="comb-value1"><?php echo $emailId; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Emergency Contact: </td>
                    <td class="comb-value1"><?php echo $egyContactNo; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Guardian Name: </td>
                    <td class="comb-value1"><?php echo $guardianName; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Guardian Relation: </td>
                    <td class="comb-value1"><?php echo $guardianRelation; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Guardian Contact: </td>
                    <td class="comb-value1"><?php echo $guardianContactNo; ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="heading" style="color: red;">Correspondence Address &raquo; </td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Address: </td>
                    <td class="comb-value1"><?php echo $corresAddress; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">City: </td>
                    <td class="comb-value1"><?php echo $corresCity; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">State: </td>
                    <td class="comb-value1"><?php echo $corresState; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Pincode: </td>
                    <td class="comb-value1"><?php echo $corresPincode; ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="heading" style="color: red;">Permanent Address &raquo; </td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Address: </td>
                    <td class="comb-value1"><?php echo $pmntAddress; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">City: </td>
                    <td class="comb-value1"><?php echo $pmntCity; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">State: </td>
                    <td class="comb-value1"><?php echo $pmntState; ?></td>
                </tr>
                <tr>
                    <td width="12%" valign="top" class="heading">Pincode: </td>
                    <td class="comb-value1"><?php echo $pmntPincode; ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<form id="form1" name="form1" method="post" action="">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="14%">&nbsp;</td>
            <td width="35%" class="comb-value">
                <input type="button" class="txtbox4" value="Print this Document" onclick="printDocument();" />
            </td>
            <td width="3%">&nbsp;</td>
            <td width="26%"><input name="Submit2" type="submit" class="txtbox4" value="Close this document" /></td>
            <td width="8%">&nbsp;</td>
            <td width="14%">&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>
