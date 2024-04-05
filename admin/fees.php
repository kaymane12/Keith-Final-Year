<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
  <title>Fee Collection Pie Chart</title>
  <link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include('includes/header.php');?>

<div class="ts-main-content">
        <?php include('includes/sidebar.php');?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
 
  <?php
    // Assuming $mysqli is your database connection
    include("includes/config.php");

    // Query to get fee collection status
    $query = "SELECT SUM(amount_paid) AS total_collected, SUM(balance) AS total_due
              FROM finance";

    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();

    // Free result set
    $result->free();

    // Close connection
    $mysqli->close();

    // Data for pie chart
    $total_collected = $row['total_collected'];
    $total_due = $row['total_due'];
    $total = $total_collected + $total_due;

    // Calculate percentages
    $collected_percentage = ($total_collected / $total) * 100;
    $due_percentage = ($total_due / $total) * 100;

    // Output total collected and total due
    echo "<p>Total Fee Collected: $" . $total_collected . "</p>";
    echo "<p>Total Due: $" . $total_due . "</p>";

    // Output pie chart
    echo '<canvas id="feePieChart" width="400" height="400"></canvas>';
  ?>
  <script>
    var ctx = document.getElementById('feePieChart').getContext('2d');
    var feePieChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ['Collected', 'Due'],
        datasets: [{
          label: 'Fee Collection',
          data: [<?php echo $collected_percentage; ?>, <?php echo $due_percentage; ?>],
          backgroundColor: [
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 99, 132, 0.6)'
          ],
          borderColor: [
            'rgba(54, 162, 235, 1)',
            'rgba(255, 99, 132, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: 'Fee Collection Status'
        }
      }
    });
  </script>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<div class="foot"><footer>
<h2> Finance Report</h2>
</footer> </div>


<style> .foot{text-align: center; border: 1px solid black;}</style>
</html>
