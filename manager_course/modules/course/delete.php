<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

$getData = filterData('get');
if (!empty($getData['id'])) {
    $course_id = (int)$getData['id'];
    $checkCourse = getRows("SELECT id FROM courses WHERE id = $course_id");

    if ($checkCourse > 0) {
        // Xóa khóa học
        $deleteStatus = deleteData("courses", "id = $course_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa khóa học thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
        } else {
            setSessionFlash('msg', 'Xóa khóa học thất bại');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Khóa học không tồn tại');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=course&action=list');
    }
} else {
    setSessionFlash('msg', 'Thiếu tham số ID khóa học');
    setSessionFlash('msg_type', 'danger');
    redirect('?module=course&action=list');
}
