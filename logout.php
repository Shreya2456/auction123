<?php
// Include config file
require_once "includes/config.php";
require_once "includes/functions.php";

// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
$_SESSION["flash_message"] = "You have been logged out successfully.";
redirect("login.php");
?>
