<?php
if(!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

function layout($layoutName){
    if(file_exists(_PATH_URL_TEMPLATES . '/layouts/'.$layoutName. '.php')){
        require_once _PATH_URL_TEMPLATES . '/layouts/'.$layoutName. '.php';
    }
}