<?php
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set default timezone to Asia/Ho_Chi_Minh
session_start(); // Start a new session or resume the existing session
ob_start(); // Start output buffering

require_once './config.php'; // Include the configuration file


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
                            echo "Ket noi thanh cong";
                            require_once $path;
              } else {
                            require_once './modules/errors/404.php';
              }
} else {
              require_once './modules/errors/505.php';
}