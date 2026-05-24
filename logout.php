<!-- destroys the session and logs off the user -->

<?php
session_start();
session_destroy();
header("location:/makazi254/login.php");   // redirects the user to the login page
exit;
?>