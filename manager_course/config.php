<?php
const _ROOT_PATH = true;
const _MODULE_ = 'dashboard';
const _ACTION_ = 'index';
// Cấu hình kết nối cơ sở dữ liệu
const _HOST = 'localhost';
const _DB = 'course_manager';
const _USER = 'root';
const _PASS = '';
const _DRIVER = 'mysql';


// debug error
const _DEBUG = true;

// thiết lập host
define('_HOST_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/manager_course/');

define('_HOST_URL_TEMPLATES', _HOST_URL . 'templates/');

// Thiết lập PATH
define('_PATH_URL', __DIR__);
define('_PATH_URL_TEMPLATES', _PATH_URL . '/templates/');
