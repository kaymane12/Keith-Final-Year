<?php
include("includes/config.php");

// Query to get student details report
$query = "SELECT id, CONCAT(firstName, ' ', lastName) AS name, gender, course, roomno FROM registration";

$result = $mysqli->query($query);

// Check if the query executed successfully
if (!$result) {
    die("Error: " . $mysqli->error);
}
// Close connection
$mysqli->close();
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
    <title>Student Details Report</title>
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
        /* CSS styles */
        .container {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .chart-container {
            width: 45%;
            max-width: 400px; /* Set max width for chart containers */
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php include("includes/header.php");?>

<div class="ts-main-content">
    <?php include("includes/sidebar.php");?>

                <?php
                    include("includes/config.php");

                    // Query to get student details report
                    $query = "SELECT id, CONCAT(firstName, ' ', lastName) AS name, gender, course, roomno FROM registration";

                    $result = $mysqli->query($query);

                    // Initialize variables for counting
                    $maleCount = 0;
                    $femaleCount = 0;
                    $courseCounts = array();
                    $nameStartsWith = array();
                    $nameEndsWith = array();

                    while ($row = $result->fetch_assoc()) {
                        // Count gender
                        if ($row['gender'] == 'Male') {
                            $maleCount++;
                        } elseif ($row['gender'] == 'Female') {
                            $femaleCount++;
                        }

                        // Count course
                        if (isset($courseCounts[$row['course']])) {
                            $courseCounts[$row['course']]++;
                        } else {
                            $courseCounts[$row['course']] = 1;
                        }

                        // Count names starting and ending with specific letters
                        $firstChar = strtoupper(substr($row['name'], 0, 1));
                        $lastChar = strtoupper(substr($row['name'], -1));

                        if (isset($nameStartsWith[$firstChar])) {
                            $nameStartsWith[$firstChar]++;
                        } else {
                            $nameStartsWith[$firstChar] = 1;
                        }

                        if (isset($nameEndsWith[$lastChar])) {
                            $nameEndsWith[$lastChar]++;
                        } else {
                            $nameEndsWith[$lastChar] = 1;
                        }
                    }

                    // Close connection
                    $mysqli->close();
                ?>

                <!-- Student Details Table -->
                <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
              
                <table>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Course</th>
                        <th>Room Number</th>
                    </tr>
                    <?php
                        include("includes/config.php");

                        $result = $mysqli->query($query);

                        // Displaying student details report
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['gender'] . "</td>";
                            echo "<td>" . $row['course'] . "</td>";
                            echo "<td>" . $row['roomno'] . "</td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            

                <!-- Gender Chart -->
                <div class="container">
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>

                    <!-- Course Chart -->
                    <div class="chart-container">
                        <canvas id="courseChart"></canvas>
                    </div>
                </div>

                <!-- Names Starting Chart -->
                <div class="container">
                    <div class="chart-container">
                        <canvas id="namesStartingChart"></canvas>
                    </div>

                    <!-- Names Ending Chart -->
                    <div class="chart-container">
                        <canvas id="namesEndingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gender Chart -->
<script>
    var genderCtx = document.getElementById('genderChart').getContext('2d');
    var genderChart = new Chart(genderCtx, {
        type: 'bar',
        data: {
            labels: ['Male', 'Female'],
            datasets: [{
                data: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>],
                backgroundColor: ['#36a2eb', '#ff6384']
            }]
        },
        options: {
            responsive: true
        }
    });



    // Course Chart
    var courseCtx = document.getElementById('courseChart').getContext('2d');
    var courseChart = new Chart(courseCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($courseCounts)); ?>,
            datasets: [{
                label: 'Number of Students',
                data: <?php echo json_encode(array_values($courseCounts)); ?>,
                backgroundColor: '#4caf50'
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    // Names Starting Chart
    var namesStartingCtx = document.getElementById('namesStartingChart').getContext('2d');
    var namesStartingChart = new Chart(namesStartingCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($nameStartsWith)); ?>,
            datasets: [{
                label: 'Names Starting with Letter',
                data: <?php echo json_encode(array_values($nameStartsWith)); ?>,
                backgroundColor: '#ffc107'
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    // Names Ending Chart
    var namesEndingCtx = document.getElementById('namesEndingChart').getContext('2d');
    var namesEndingChart = new Chart(namesEndingCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($nameEndsWith)); ?>,
            datasets: [{
                label: 'Names Ending with Letter',
                data: <?php echo json_encode(array_values($nameEndsWith)); ?>,
                backgroundColor: '#9c27b0'
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>

<!-- Summary Conclusion -->
<div class="container">
    <div>
        <h2>Summary Conclusions</h2>
        <p>
            <strong>Gender Distribution:</strong> <?php echo $maleCount; ?> males and <?php echo $femaleCount; ?> females are enrolled.
            <br>
            <strong>Course Enrollment:</strong> <?php foreach ($courseCounts as $course => $count) { echo $course . ': ' . $count . ', '; } ?>
            <br>
            <strong>Names Starting:</strong> <?php foreach ($nameStartsWith as $char => $count) { echo $char . ': ' . $count . ', '; } ?>
            <br>
            <strong>Names Ending:</strong> <?php foreach ($nameEndsWith as $char => $count) { echo $char . ': ' . $count . ', '; } ?>
        </p>
    </div>
</div>
</body>
<div class="foot"><footer>
<h2> Student Report</h2>
</footer> </div>


<style> .foot{text-align: center; border: 1px solid black;}</style>
</html>
