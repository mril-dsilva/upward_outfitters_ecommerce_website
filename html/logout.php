<?php
    session_start();

    // free all session variables
    session_unset();                
    
    // delete all data associated with a session,
    // invalidate session id
    session_destroy(); 
    
    // Redirect the client to this page, but using a get request this time.
    // Code 303 means "See other"
    header("Location: index.php", true, 303);
    exit();
?>