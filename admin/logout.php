<?php
// Start session before any output
session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect to login page
header('Location: /admin/user-login.php');
exit();
?>