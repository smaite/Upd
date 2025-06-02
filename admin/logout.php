<?php
// Start session
session_start();

// Include required files
require_once '../config.php';
require_once '../functions.php';

// Log out the user
logout();

// Redirect to the login page
header('Location: login.php');
exit; 