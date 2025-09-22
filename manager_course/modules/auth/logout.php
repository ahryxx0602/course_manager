<?php
if (!defined('_ROOT_PATH')) {
              die('Truy cập không hợp lệ!');
}
$data = [
              'title' => 'Đăng nhập hệ thống'
];



if (isLogin()) {
              $token = getSession('token_login');
              $removeToken = deleteData('token_login', "token= '$token'");
              if ($removeToken) {
                            removeSession('token_login');
                            redirect('?module=auth&action=login');
              } else {
                            setSessionFlash('msg', "Lỗi hệ thống, xin vui lòng thử lại");
                            setSessionFlash("msg_type", "danger");
              }
} else {
              setSessionFlash('msg', "Lỗi hệ thống, xin vui lòng thử lại");
              setSessionFlash("msg_type", "danger");
}
