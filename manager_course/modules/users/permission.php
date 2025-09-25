<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

$data = ['title' => 'Phân quyền người dùng'];
layout("header", $data);
layout("sidebar");

/** Lấy id user từ GET và kiểm tra tồn tại */
$filterGet = filterData('get');
if (empty($filterGet['id'])) {
    setSessionFlash('msg', 'Người dùng không tồn tại');
    setSessionFlash('msg_type', 'danger');
    redirect('?module=users&action=list');
    exit;
}
$idUser = (int)$filterGet['id'];

$checkID = getOne("SELECT * FROM users WHERE id = $idUser");
if (empty($checkID)) {
    setSessionFlash('msg', 'Người dùng không tồn tại');
    setSessionFlash('msg_type', 'danger');
    redirect('?module=users&action=list');
    exit;
}

/** Quyền hiện tại để tick sẵn */
$permissionOld = [];
if (!empty($checkID['permission'])) {
    $decoded = json_decode($checkID['permission'], true);
    if (is_array($decoded)) {
        $permissionOld = array_map('intval', $decoded);
    }
}

/** Submit phân quyền */
if (isPOST()) {
    $filter = filterData(); // POST
    $permArr = !empty($filter['permission']) ? $filter['permission'] : [];
    $permArr = array_map('intval', (array)$permArr);

    $dataUpdate = [
        'permission' => json_encode($permArr, JSON_UNESCAPED_UNICODE),
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    $condition = "id = $idUser";
    $ok = updateData('users', $dataUpdate, $condition);

    if ($ok) {
        setSessionFlash('msg', 'Cập nhật phân quyền thành công');
        setSessionFlash('msg_type', 'success');
        redirect('?module=users&action=list');
        exit;
    } else {
        setSessionFlash('msg', 'Dữ liệu không hợp lệ, hãy kiểm tra lại !!');
        setSessionFlash('msg_type', 'danger');
    }
}
?>
<div class="container">
    <form method="post">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="width:80px" class="text-center">STT</th>
                    <th>Khóa học</th>
                    <th style="width:140px" class="text-center">Phân quyền</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $getDetailCourse = getAll("SELECT id, name FROM courses");
                $count = 1;
                foreach ($getDetailCourse as $item):
                    $courseId = (int)$item['id'];
                    $checked = in_array($courseId, $permissionOld, true) ? 'checked' : '';
                ?>
                    <tr>
                        <td class="text-center"><?= $count++ ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td class="text-center">
                            <input
                                type="checkbox"
                                name="permission[]"
                                value="<?= $courseId ?>"
                                <?= $checked ?> />
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <button class="btn btn-success" type="submit">Xác nhận</button>
        <a class="btn btn-secondary" href="?module=users&action=list">Hủy</a>
    </form>
</div>

<?php layout("footer"); ?>