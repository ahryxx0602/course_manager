<?php
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set default timezone to Asia/Ho_Chi_Minh
session_start(); // Start a new session or resume the existing session
ob_start(); // Start output buffering

require_once './config.php'; // Include the configuration file
require_once './includes/connect.php'; // Include the database connection file
require_once './includes/database.php'; // Include the database functions file
require_once './includes/session.php';


// setSessionFlash('Ahryxx', 'php');
$rel = getSessionFlash('Ahryxx');
echo $rel;
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
die();
$module = _MODULE_;
$action = _ACTION_;
if(!empty($_GET['module'])) {
              $module = $_GET['module'];
}
if(!empty($_GET['action'])) {
              $action = $_GET['action'];
}

$path = './modules/' . $module . '/' . $action . '.php';
if(!empty($path)) {
              if(file_exists($path)) {
                            require_once $path;
              } else {
                            require_once './modules/errors/404.php';
              }
} else {
              require_once './modules/errors/505.php';
}

