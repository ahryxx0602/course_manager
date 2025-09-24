<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

$getData = filterData('get');
if (!empty($getData['id'])) {
    $user_id = $getData['id'];
    $checkUser = getRows("SELECT * FROM users WHERE id = $user_id");
    if ($checkUser > 0) {
        //Xóa
        $checkToken = getRows("SELECT * FROM token_login WHERE user_id = $user_id");
        if ($checkToken > 0) {
            deleteData('token_login', "user_id = $user_id");
        }
        $deleteStatus = deleteData("users", "id = $user_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa người dùng thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Xóa người dùng thất bại');
            setSessionFlash('msg_type', 'danger');
        }
    }
}
