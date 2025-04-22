<?php
// logout.php - User logout script
require_once "config.php";

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
setFlashMessage("success", "You have been logged out successfully.");
redirectTo("login.php");
?>