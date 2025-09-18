<?php
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set default timezone to Asia/Ho_Chi_Minh
session_start(); // Start a new session or resume the existing session
ob_start(); // Start output buffering

require_once './config.php'; // Include the configuration file
require_once './includes/connect.php'; // Include the database connection file
require_once './includes/database.php'; // Include the database functions file
require_once './includes/session.php';
//Nhúng trước file function
//Email
require_once './includes/mailer/Exception.php';
require_once './includes/mailer/PHPMailer.php';
require_once './includes/mailer/SMTP.php';


require_once './includes/functions.php';


senMail('phvanthanh06@gmail.com', 'TEST-MAIL','Hello from server.');

// setSessionFlash('Ahryxx', 'php');
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

