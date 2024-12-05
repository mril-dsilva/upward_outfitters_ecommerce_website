 <!-- Include Navigation Bar function php-->
 <?php 
require 'navbar.php'; 
?>
<?php
    // Show all errors from the PHP interpreter.
    ini_set('display_errors', 1);    
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upward Outfitters</title>
    <style>
        body {
            text-align: center;
            font-family: sans-serif;
        }
        .container {
            margin: 50px auto;
            width: 80%;
            max-width: 800px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            border: 1px solid black;
            cursor: pointer;
        }

    </style>
</head>
<body>

    <?php  show_navbar($conn);   
    ?>

    <div class="container">
        
        <h1>Upward Outfitters</h1>

        <h2>Taking your outdoor experiences Upward.</h2>

        <p>
            We at Upward Outfitters specialize in rock climbing gear, apparel, and camping equipment.
            From our humble beginnings in a garage, we are now the go-to source for climbing enthusiasts,
            offering the latest gear and exceptional customer service. Start your adventure with us today!
        </p>

        <a href="catalog.php" class="button">View Products</a> <!-- links to product catalog -->
        
    </div>
</body>
</html>
