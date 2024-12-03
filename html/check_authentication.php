<?php
    session_start();

    if(!array_key_exists('username', $_SESSION)) {
        header("Location: login.php", true, 303);
        exit();
    }
?>