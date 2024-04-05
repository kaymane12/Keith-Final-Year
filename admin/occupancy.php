<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
    <title>Occupancy Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .table-container {
            flex: 1;
            margin-right: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .chart-container {
            flex: 1;
        }
    </style>
</head>
<body>
   
    <?php include('includes/header.php');?>

	<div class="ts-main-content">
			<?php include('includes/sidebar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
                <?php
    include("includes/config.php");

    // Assuming $mysqli is your database connection

    // Query to get occupancy status of rooms
    $query = "SELECT roomno, rooms.seater, CASE WHEN registration.id IS NOT NULL THEN 'Occupied' ELSE 'Vacant' END AS occupancy_status
              FROM rooms
              LEFT JOIN registration ON registration.roomno = registration.roomno";

    $result = $mysqli->query($query);

    // Fetching data for chart
    $occupiedCount = 0;
    $vacantCount = 0;
    while ($row = $result->fetch_assoc()) {
        if ($row['occupancy_status'] == 'Occupied') {
            $occupiedCount++;
        } else {
            $vacantCount++;
        }
    }

    // Close connection
    $mysqli->close();
    ?>
    <div class="container">
        <div class="table-container">
            
            <table>
                <tr>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Occupancy Status</th>
                </tr>
                <?php
                include("includes/config.php");

                // Assuming $mysqli is your database connection

                // Query to get occupancy status of rooms
                $query = "SELECT roomno, rooms.seater, CASE WHEN registration.id IS NOT NULL THEN 'Occupied' ELSE 'Vacant' END AS occupancy_status
                          FROM rooms
                          LEFT JOIN registration ON registration.roomno = registration.roomno";

                $result = $mysqli->query($query);

                // Displaying occupancy report
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['roomno'] . "</td>";
                    echo "<td>" . $row['seater'] . "</td>";
                    echo "<td>" . $row['occupancy_status'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <div class="chart-container">
            <h2>Occupancy Chart</h2>
            <canvas id="occupancyChart" width="400" height="200"></canvas>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('occupancyChart').getContext('2d');
        var occupancyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Occupied', 'Vacant'],
                datasets: [{
                    label: 'Occupancy Status',
                    data: [<?php echo $occupiedCount; ?>, <?php echo $vacantCount; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <div>
        <h2>Summary Conclusion</h2>
        <p>
            The occupancy report indicates the status of each room in the facility. From the provided data:
            <ul>
                <li>Total occupied rooms: <?php echo $occupiedCount; ?></li>
                <li>Total vacant rooms: <?php echo $vacantCount; ?></li>
            </ul>
            This suggests a <?php echo round(($occupiedCount / ($occupiedCount + $vacantCount)) * 100, 2); ?>% occupancy rate.
        </p>
    </div>
    </div>

			

    </div>
</div>
</div>
</body>
<div class="foot"><footer>
<h2> Room Occupancy Report</h2>
</footer> </div>


<style> .foot{text-align: center; border: 1px solid black;}</style>
</html>
