<?php
// Assuming $mysqli is your database connection
include("includes/config.php");

// Query to get notices report
$query = "SELECT timestamp, title
          FROM notices";

$result = $mysqli->query($query);

// Fetch data into arrays for Chart.js
$data = [];
$labels = [];
$titles = [];
while ($row = $result->fetch_assoc()) {
    $timestamp = $row['timestamp'];
    $title = $row['title'];
    
    if (!in_array($timestamp, $labels)) {
        $labels[] = $timestamp;
    }
    
    if (!in_array($title, $titles)) {
        $titles[] = $title;
    }
    
    // Assigning 1 for each occurrence of title at timestamp
    $data[$timestamp][$title] = 1;
}

// Free result set
$result->free();

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    <title>NoticeBoard Report</title>
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
<div class="ts-main-content">
    <?php include('includes/sidebar.php');?>
    <?php include('includes/header.php');?>
   
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                  
                    <canvas id="noticeChart" width="400" height="200"></canvas>

                    <script>
                        var ctx = document.getElementById('noticeChart').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode($labels); ?>,
                                datasets: [
                                    <?php foreach ($titles as $title): ?>
                                    {
                                        label: '<?php echo $title; ?>',
                                        data: [
                                            <?php foreach ($labels as $timestamp): ?>
                                                <?php echo isset($data[$timestamp][$title]) ? $data[$timestamp][$title] : 0; ?>,
                                            <?php endforeach; ?>
                                        ],
                                        backgroundColor: getRandomColor(),
                                        borderWidth: 1
                                    },
                                    <?php endforeach; ?>
                                ]
                            },
                            options: {
                                scales: {
                                    x: {
                                        stacked: true,
                                        title: {
                                            display: true,
                                            text: 'Timestamp'
                                        }
                                    },
                                    y: {
                                        stacked: false,
                                        title: {
                                            display: true,
                                            text: 'Title'
                                        }
                                    }
                                }
                            }
                        });

                        function getRandomColor() {
                            var letters = '0123456789ABCDEF';
                            var color = '#';
                            for (var i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<div class="foot"><footer>
<h1> NoticeBoard Report</h1>
</footer> </div>
<style> .foot{text-align: center; border: 2px solid black;}</style>
</html>
