<?php
    // Show all errors from the PHP interpreter.
    ini_set('display_errors', 1);    
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Show all errors from the MySQLi Extension.
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  

    $sql_location = './sql/';

    session_start();

    # Create new connection, specifying the database we care about
    $config = parse_ini_file('../../mysql.ini');
    $dbname = 'upward_outfitters';
    $conn = new mysqli(
            $config['mysqli.default_host'],
            $config['mysqli.default_user'],
            $config['mysqli.default_pw'],
            $dbname);
    
    # If user is already logged in, redirect them to the homepage
    if(array_key_exists('username', $_SESSION)) {
        header("Location: homepage.php", true, 303);
        exit();
    }

    if(array_key_exists('login', $_POST)){
        login($conn, $_POST["l_username"], $_POST["l_password"]);
    }

    if(array_key_exists('register', $_POST)){
        register($conn);
    }
?>

<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <h1>Login</h1>
        <form method="POST">
            <label for="l_username">Username</label>
            <input type="text" name="l_username" required/>

            <label for="l_password">Password</label>
            <input type="password" name="l_password" required/>

            <input type="submit" name="login" value="Submit" />
        </form>
        <h1>Register</h1>
        <form method="POST">
            <label for="r_username">Username</label>
            <input type="text" name="r_username" required/>

            <label for="r_password">Password</label>
            <input type="password" name="r_password" required/>

            <label for="r_confirm_password">Confirm Password</label>
            <input type="password" name="r_confirm_password" required/>

            <input type="submit" name="register" value="Submit" />
        </form>
    </body>
</html>

<?php
function login($conn, $param_username, $param_password){
    global $sql_location;

    $login_stmt = file_get_contents($sql_location . "user_retrieve.sql");
    $login_stmt = $conn -> prepare($login_stmt);

    // Make sure delete statement was prepared properly
    if (!$login_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    // Bind id parameter to delete statement
    $login_stmt -> bind_param('ss', $username, $password);

    $username = $param_username;
    $password = $param_password;

    $login_stmt -> execute();

    $login_result = $login_stmt -> get_result();

    $login_queries = $login_result -> fetch_all();

    $sel_username = $login_queries[0][0];
    $sel_role = $login_queries[0][1];
    $sel_partner_id = $login_queries[0][2];

    # Redirect here if the select didn't return anything.
    if ($sel_username == null){
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }

    $_SESSION["username"] = $sel_username; 
    $_SESSION["user_role"] = $sel_role;
    $_SESSION["partner_id"] = $partner_id;

    header("Location: homepage.php", true, 303);
    exit();
}

function register($conn){
    global $sql_location;

    // Make sure the password and the confirmed password are the same. If not,
    // just redirect back here
    if ($_POST["r_password"] != $_POST["r_confirm_password"]){
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }


    $register_stmt = file_get_contents($sql_location . "user_create.sql");
    $register_stmt = $conn -> prepare($register_stmt);

    // Make sure register statement was prepared properly
    if (!$register_stmt) {
        echo "Couldn't prepare the statement";
        exit;
    }

    $register_stmt -> bind_param('ss', $username, $password);

    $username = $_POST["r_username"];
    $password = $_POST["r_password"];

    $register_stmt -> execute();

    login($conn, $_POST["r_username"], $_POST["r_password"]);
}
?>