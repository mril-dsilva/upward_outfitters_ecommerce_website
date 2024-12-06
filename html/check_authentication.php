<?php
    function check_auth(){
        session_start();

        if(!array_key_exists('username', $_SESSION)) {
            header("Location: index.php", true, 303);
            exit();
        }
    }
?>