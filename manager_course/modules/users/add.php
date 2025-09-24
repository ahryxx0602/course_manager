<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

$data = [
    'title' => 'Thêm mới người dùng'
];
layout("header", $data);
layout("sidebar");

if (isPOST()) {
    $filter = filterData();
    $errors = [];

    //Validate fullName
    if (empty(trim($filter['fullName']))) {
        $errors['fullName']['require'] = "Họ tên bắt buộc phải nhập.";
    } else {
        if (strlen(trim($filter['fullName'])) < 5) {
            $errors['fullName']['length'] = "Họ tên bắt buộc phải trên 5 kí tự.";
        }
    }

    // Validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['require'] = "Email bắt buộc phải nhập.";
    } else {
        // ĐÚng định dang ?, email exist?
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['length'] = "Email không đúng định dạng.";
        } else {
            $email = $filter['email'];

            $checkEmail = getRows("SELECT * FROM users WHERE email = '$email'");
            if ($checkEmail > 0) {
                $errors['email']['check'] = "Email đã có trong hệ thống.";
            }
        }
    }

    //Validate phone
    if (empty($filter['phone'])) {
        $errors['phone']['require'] = "Số điện thoại bắt buộc phải nhập.";
    } else {
        if (!isPhone($filter['phone'])) {
            $errors['phone']['isPhone'] = "Số điện thoại không đúng định dạng.";
        }
    }

    //Validate Password
    if (empty($filter['password'])) {
        $errors['password']['require'] = "Mật khẩu bắt buộc phải nhập.";
    } else {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = "Mật khẩu phải lớn hơn 6 ký tự.";
        }
    }
    if (empty($errors)) {
        // Chuẩn hóa dữ liệu đầu vào
        $fullName = trim($filter['fullName']);
        $email    = trim(mb_strtolower($filter['email']));
        $phone    = trim($filter['phone']);
        $address  = (!empty($filter['address']) ? $filter['address'] : null);

        // Lấy group_id và status từ form (fallback an toàn)
        $groupId  = isset($filter['group_id']) ? (int)$filter['group_id'] : 1;
        $status   = isset($filter['status']) ? (int)$filter['status'] : 0;

        // Tạo active_token nếu tài khoản CHƯA kích hoạt
        $active_token = ($status === 0)
            ? sha1(uniqid() . time())
            : "";

        $now = date('Y-m-d H:i:s');

        $data = [
            'fullName'     => $fullName,
            'email'        => $email,
            'phone'        => $phone,
            'avatar'       => '/templates/assets/image/defaultAvatar.jpg',
            'password'     => password_hash(trim($filter['password']), PASSWORD_DEFAULT),
            'address'      => $address,
            'status'       => $status,
            'active_token' => $active_token,
            'group_id'     => $groupId,
            'created_at'   => $now,
            'updated_at'   => $now,
        ];
        $insertStatus =  insertData('users', $data);
        if ($insertStatus) {
            setSessionFlash('msg', 'Thêm người dùng thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Dữ liệu không hợp lệ, hãy kiểm tra lại !!');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Thêm người dùng thất bại');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorsArr = getSessionFlash('errors');
?>
<div class="container add-user">
    <h2>Thêm mới người dùng</h2>
    <hr />
    <?php if (!empty($msg) && !empty($msg_type)) {
        getMessage($msg, $msg_type);
    } ?>
    <form action="" method="post">
        <div class="row">
            <div class="col-6 mb-3">
                <label for="fullName">Họ và tên</label>
                <input
                    id="fullName"
                    name="fullName"
                    type="text"
                    class="form-control"
                    placeholder="Họ tên"
                    value="<?php if (!empty($oldData)) {
                                echo oldData($oldData, 'fullName');
                            } ?>" />
                <?php if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'fullName');
                } ?>
            </div>
            <div class="col-6 mb-3">
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="text"
                    class="form-control"
                    placeholder="Email"
                    value="<?php if (!empty($oldData)) {
                                echo oldData($oldData, 'email');
                            } ?>" />
                <?php if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'email');
                } ?>
            </div>
            <div class="col-6 mb-3">
                <label for="phone">Số điện thoại</label>
                <input
                    id="phone"
                    name="phone"
                    type="text"
                    class="form-control"
                    placeholder="Số điện thoại"
                    value="<?php if (!empty($oldData)) {
                                echo oldData($oldData, 'phone');
                            } ?>" />
                <?php if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'phone');
                } ?>
            </div>
            <div class="col-6 mb-3">
                <label for="password">Mật khẩu</label>
                <input
                    id="password"
                    name="password"
                    type="text"
                    class="form-control"
                    placeholder="Mật khẩu"
                    value="<?php if (!empty($oldData)) {
                                echo oldData($oldData, 'password');
                            } ?>" />
                <?php if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'password');
                } ?>
            </div>
            <div class="col-6 mb-3">
                <label for="address">Địa chỉ</label>
                <input
                    id="address"
                    name="address"
                    type="text"
                    class="form-control"
                    placeholder="Địa chỉ"
                    value="<?php if (!empty($oldData)) {
                                echo oldData($oldData, 'address');
                            } ?>" />
                <?php if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'address');
                } ?>
            </div>
            <div class="col-3 mb-3">
                <label for="">Phân cấp người dùng</label>
                <select name="group_id" id="group" class="form-select form-control">
                    <?php
                    $getGroup = getAll("SELECT * FROM `groups`");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-3 mb-3">
                <label for="status">Trạng thái tài khoản</label>
                <select name="status" id="group" class="form-select form-control">
                    <option value="0">Chưa kích hoạt</option>
                    <option value="1">Đã kích hoạt</option>
                </select>
            </div>
        </div>
        <div class="d-flex mt-5 mb-3">
            <button type="submit" class="btn btn-success">Xác nhận</button>
        </div>
    </form>
</div>
<?php
layout("footer");
?>