<?php 

    $sql_location = './sql/';

    session_start();

    $config = parse_ini_file('../../mysql.ini');
    $dbname = 'upward_outfitters';
    $conn = new mysqli(
            $config['mysqli.default_host'],
            $config['mysqli.default_user'],
            $config['mysqli.default_pw'],
            $dbname);
    
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
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #FFF9F0, #FFFAE5);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            .container {
                display: flex;
                flex-direction: column;
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                gap: 20px;
                text-align: center;
                width: 60%; /* Wider container */
                max-width: 900px; /* Limit max width */
            }

            .header-section h1 {
                font-size: 2em;
                color: #333;
                margin-bottom: 10px;
            }

            .header-section p {
                font-size: 1.2em;
                color: #757575;
                margin-bottom: 30px;
            }

            .form-container {
                display: flex;
                justify-content: space-between;
                gap: 40px;
            }

            .form-section {
                flex: 1;
                padding: 20px;
                text-align: left;
            }

            .separator {
                width: 1px;
                background-color: #ccc;
            }

            h1 {
                font-size: 1.5em;
                color: #333;
                margin-bottom: 15px;
            }

            form {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            label {
                font-size: 0.9em;
                color: #555;
            }

            input[type="text"], 
            input[type="password"] {
                padding: 10px;
                font-size: 1em;
                border: 1px solid #ccc;
                border-radius: 5px;
                width: 100%;
                box-sizing: border-box;
            }

            input[type="submit"] {
                background-color: #757575;
                color: white;
                padding: 10px;
                font-size: 1em;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.5s;
            }

            input[type="submit"]:hover {
                background-color: #FF9800;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header-section">
                <h1>Welcome to Upward Outfitters</h1>
                <p>Log-in or Register to continue.</p>
            </div>
            <div class="form-container">
                <div class="form-section">
                    <h1>Login</h1>
                    <form method="POST">
                        <label for="l_username">Username</label>
                        <input type="text" name="l_username" required/>

                        <label for="l_password">Password</label>
                        <input type="password" name="l_password" required/>

                        <input type="submit" name="login" value="Submit" />
                    </form>
                </div>
                <div class="separator"></div>
                <div class="form-section">
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
                </div>
            </div>
        </div>
    </body>
</html>

<?php
function login($conn, $param_username, $param_password){
    global $sql_location;

    $login_stmt = file_get_contents($sql_location . "user_retrieve.sql");
    $login_stmt = $conn -> prepare($login_stmt);

    if (!$login_stmt) {
        echo "Couldn't prepare the statement";
        exit();
    }

    $login_stmt -> bind_param('ss', $username, $password);

    $username = htmlspecialchars($param_username);
    $password = htmlspecialchars($param_password);

    $login_stmt -> execute();

    $login_result = $login_stmt -> get_result();

    $login_queries = $login_result -> fetch_all();

    $sel_username = $login_queries[0][0];
    $sel_role = $login_queries[0][1];
    $sel_partner_id = $login_queries[0][2];

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

    if ($_POST["r_password"] != $_POST["r_confirm_password"]){
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }

    $register_stmt = file_get_contents($sql_location . "user_create.sql");
    $register_stmt = $conn -> prepare($register_stmt);

    if (!$register_stmt) {
        echo "Couldn't prepare the statement";
        exit();
    }

    $register_stmt -> bind_param('ss', $username, $password);

    $username = htmlspecialchars($_POST["r_username"]);
    $password = htmlspecialchars($_POST["r_password"]);

    $register_stmt -> execute();

    $reg_result = $register_stmt -> get_result();

    $reg_queries = $reg_result -> fetch_all();

    $reg_success = $reg_queries[0][0];

    $reg_result -> close();
    $conn -> next_result();

    if($reg_success){
        login($conn, $_POST["r_username"], $_POST["r_password"]);
    }else{
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
}
?>

