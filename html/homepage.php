<?php
    require 'check_authentication.php';
    check_auth();
?>

<!-- Include Navigation Bar -->
<?php 
    require 'navbar.php'; 
?>

<?php
    # Create new connection, specifying the database we care about
    $config = parse_ini_file('../../mysql.ini');
    $dbname = 'upward_outfitters';
    $conn = new mysqli(
            $config['mysqli.default_host'],
            $config['mysqli.default_user'],
            $config['mysqli.default_pw'],
            $dbname);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upward Outfitters</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        h1, h2, p {
            margin: 0;
            padding: 0;
        }

        /* Main Content */
        .container {
            text-align: center;
            padding: 50px 20px;
            margin: 50px auto;
            max-width: 800px;
        }

        h1 {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 1.5em;
            color: #555;
            margin-bottom: 20px;
        }

        p {
            font-size: 1em;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Button Styles */
        .button {
            display: inline-block;
            background-color: #757575;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #FF9800;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php show_navbar($conn); ?>

    <!-- Main Content -->
    <div class="container">
        <h1>Upward Outfitters</h1>
        <h2>Taking your outdoor experiences Upward.</h2>
        <p>
            We at Upward Outfitters specialize in rock climbing gear, apparel, and camping equipment.
            From our humble beginnings in a garage, we are now the go-to source for climbing enthusiasts,
            offering the latest gear and exceptional customer service. Start your adventure with us today!
        </p>

        <a href="catalog.php" class="button">View Products</a>
    </div>

</body>
</html>
