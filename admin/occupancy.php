<?php
include("includes/config.php");

// Query to get occupancy status of rooms and calculate remaining capacity
$query = "SELECT rooms.room_no, rooms.seater, COUNT(registration.roomno) AS occupied_count,
                 CASE 
                    WHEN COUNT(registration.roomno) = rooms.seater THEN 'Fully Occupied'
                    ELSE CONCAT(rooms.seater - COUNT(registration.roomno), ' remaining') 
                 END AS remaining_capacity
          FROM rooms
          LEFT JOIN registration ON rooms.room_no = registration.roomno
          GROUP BY rooms.room_no, rooms.seater";

// Execute the query
$result = $mysqli->query($query);

// Initialize counters for occupancy and vacancy
$occupiedCount = 0;
$vacantCount = 0;

// Store data in an array and calculate counts
$rooms = [];
while ($row = $result->fetch_assoc()) {
    // Add row data to rooms array
    $rooms[] = $row;
    
    // Update counters
    if ($row['remaining_capacity'] === 'Fully Occupied') {
        $occupiedCount++;
    } else {
        $vacantCount++;
    }
}

// Prepare an INSERT query with ON DUPLICATE KEY UPDATE clause
$insertQuery = "INSERT INTO occupancy_reports (room_number, room_type, occupancy_status, occupied_count, remaining_capacity)
               VALUES (?, ?, ?, ?, ?)
               ON DUPLICATE KEY UPDATE
               room_type = VALUES(room_type),
               occupancy_status = VALUES(occupancy_status),
               occupied_count = VALUES(occupied_count),
               remaining_capacity = VALUES(remaining_capacity)";

// Prepare a statement for execution
$stmt = $mysqli->prepare($insertQuery);

// Loop through each room and bind the parameters
foreach ($rooms as $room) {
    // Convert values to variables and bind them
    $room_number = $room['room_no'];
    $room_type = $room['seater'];
    $occupancy_status = $room['remaining_capacity'] === 'Fully Occupied' ? 'Fully Occupied' : 'Vacant';
    $occupied_count = $room['occupied_count'];
    $remaining_capacity = $room['remaining_capacity'];

    // Bind the parameters to the query
    $stmt->bind_param("sissi", 
        $room_number, 
        $room_type, 
        $occupancy_status, 
        $occupied_count, 
        $remaining_capacity
    );
    
    // Execute the statement
    $stmt->execute();
}

// Close the statement
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

        /* Styles for vacant and occupied rows */
        .tr-vacant {
            background-color: #ccffcc; /* Light green for vacant rooms */
        }

        .tr-occupied {
            background-color: #ffcccc; /* Light red for occupied rooms */
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/sidebar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="container">
                        <div class="table-container">
                            <h2>Occupancy Report</h2>
                            <table>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Room Type</th>
                                    <th>Occupancy Status</th>
                                    <th>Occupied</th>
                                    <th>Remaining Capacity</th>
                                </tr>
                                <?php foreach ($rooms as $room): ?>
                                    <!-- Determine CSS class based on occupancy status -->
                                    <tr class="<?= $room['remaining_capacity'] === 'Fully Occupied' ? 'tr-occupied' : 'tr-vacant' ?>">
                                        <td><?= htmlspecialchars($room['room_no']) ?></td>
                                        <td><?= htmlspecialchars($room['seater']) ?></td>
                                        <td><?= htmlspecialchars($room['remaining_capacity'] === 'Fully Occupied' ? 'Fully Occupied' : 'Vacant') ?></td>
                                        <td><?= htmlspecialchars($room['occupied_count']) ?></td> <!-- New column showing the number of people occupied in each room -->
                                        <td><?= htmlspecialchars($room['remaining_capacity']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <div class="chart-container">
                            <h2>Occupancy Chart</h2>
                            <canvas id="occupancyChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <script>
                        const ctx = document.getElementById('occupancyChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Occupied', 'Vacant'],
                                datasets: [{
                                    label: 'Occupancy Status',
                                    data: [<?= $occupiedCount ?>, <?= $vacantCount ?>],
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
                                <li>Total fully occupied rooms: <?= $occupiedCount ?></li>
                                <li>Total vacant rooms: <?= $vacantCount ?></li>
                            </ul>
                            This suggests a <?= round(($occupiedCount / ($occupiedCount + $vacantCount)) * 100, 2) ?>% occupancy rate.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="foot">
        <footer>
            <h2>Room Occupancy Report</h2>
        </footer>
        
<style>
    .foot {
        text-align: center;
        border: 1px solid black;
    }</style>
    </div>
</body>
</html>