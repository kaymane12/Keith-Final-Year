<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Include config.php to establish a database connection
    require('config.php');
    
    // Get the star rating from the form submission
    $rating = (int)$_POST['rating'];
    $userId = $_SESSION['id']; // Assuming user ID is stored in session

    // Validate the rating and userId
    if ($rating >= 1 && $rating <= 5 && is_numeric($userId)) {
        // Insert the star rating into the database
        $stmt = $mysqli->prepare("INSERT INTO user_ratings (user_id, rating) VALUES (?, ?)");
        $stmt->bind_param('ii', $userId, $rating);
        
        if ($stmt->execute()) {
            echo "Thank you for your rating.";
        } else {
            echo "Failed to record rating: " . $stmt->error;
        }
        
        // Close the statement and database connection
        $stmt->close();
        $mysqli->close();
    } else {
        echo "Invalid input.";
    }

    // Log the user out
    unset($_SESSION['id']);
    session_destroy();
    
    // Redirect the user to the login page
    header('Location: index.php');
    exit();
}

// Display the star rating form
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    <title>Rate Your Experience</title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <link rel="stylesheet" href="css/fileinput.min.css">
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Styling for star rating */
        .star-rating {
            direction: rtl;
            display: inline-block;
            margin: 20px 0;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 2.5em;
            color: #d3d3d3;
            cursor: pointer;
            padding: 0;
            margin: 0;
        }

        .star-rating input:checked ~ label {
            color: #ffc107;
        }

        .star-rating input:hover ~ label,
        .star-rating input:hover ~ input ~ label {
            color: #ffc107;
        }

        /* Additional styling */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/sidebar.php'); ?>
        <div class="content-wrapper">
            <h2>Please rate your experience</h2>
            <form method="POST" action="rating.php" onsubmit="return validateRating();">
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5">
                    <label for="star5">&#9733;</label>
                    
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4">&#9733;</label>
                    
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3">&#9733;</label>
                    
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2">&#9733;</label>
                    
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1">&#9733;</label>
                </div>
                <button type="submit">Submit Rating</button>
            </form>
        </div>
    </div>

    <script src="js/jquery-1.11.3-jquery.min.js"></script>
    <script src="js/validation.min.js"></script>

    <script>
        // Validate that a rating is selected before form submission
        function validateRating() {
            const ratingInputs = document.getElementsByName('rating');
            for (let input of ratingInputs) {
                if (input.checked) {
                    return true;
                }
            }
            alert("Please select a rating before submitting.");
            return false;
        }
    </script>
</body>
</html>
