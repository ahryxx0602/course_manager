<?php
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set default timezone to Asia/Ho_Chi_Minh
session_start(); // Start a new session or resume the existing session
ob_start(); // Start output buffering

require_once '../config.php'; // Include the configuration file

require_once './modules/auth/login.php'; // Include the login module