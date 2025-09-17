<?php
if(!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
try{
    if(class_exists('PDO')){
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", // Hỗ trợ tiếng việt
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Đẩy lỗi vào ngoại lệ
        );
        $dns = _DRIVER . ":host=" . _HOST . ";dbname=" . _DB;
        $conn = new PDO($dns, _USER, _PASSWORD, $options);
    }
}catch(PDOException $e){
    require_once './modules/errors/505.php';
    die();
}