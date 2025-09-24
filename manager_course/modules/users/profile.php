<?php
if (!defined('_ROOT_PATH')) {
              die('Truy cập không hợp lệ!');
}

$data = [
              'title' => 'Chỉnh sửa người dùng'
];
layout("header", $data);
layout("sidebar");
$upload = upload_image('avatar');
$token = getSession('token_login');
if (!empty($token)) {
              $checkTokenLogin = getOne("SELECT * FROM token_login WHERE token ='$token'");
              if (!empty($checkTokenLogin)) {
                            $user_id = $checkTokenLogin['user_id'];
                            $detailUser = getOne("SELECT * FROM users WHERE id = '$user_id'");
              }
}

$uploadDir = _PATH_URL . '/uploads/courses/';
if (!is_dir($uploadDir)) {
              mkdir($uploadDir, 0755, true); // Tạo folder đệ quy nếu chưa có
}
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


              if ($filter['email'] != $detailUser['email']) {
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
              if (!empty(trim($filter['password']))) {
                            $errors['password']['require'] = "Mật khẩu bắt buộc phải nhập.";
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
                            $avatar = $detailUser['avatar'];

                            if ($upload === false) {
                                          $errors['avatar']['upload'] = 'Upload ảnh thất bại hoặc file không hợp lệ.';
                            } elseif ($upload) {
                                          $avatar = $upload;
                            }

                            // Tạo active_token nếu tài khoản CHƯA kích hoạt
                            $now = date('Y-m-d H:i:s');

                            $dataUpdate = [
                                          'fullName'     => $fullName,
                                          'email'        => $email,
                                          'phone'        => $phone,
                                          'address'      => $address,
                                          'status'       => $status,
                                          'active_token' => $active_token,
                                          'group_id'     => $groupId,
                                          'updated_at'   => $now,
                                          'avatar'       => $avatar
                            ];
                            if (!empty($filter['password'])) {
                                          $dataUpdate['password'] = password_hash(trim($filter['password']), PASSWORD_DEFAULT);
                            }
                            $condition = "id=" . $user_id;
                            $updateStatus =  updateData('users', $dataUpdate, $condition);
                            if ($updateStatus) {
                                          setSessionFlash('msg', 'Cập nhật dùng thành công');
                                          setSessionFlash('msg_type', 'success');
                                          redirect('?module=users&action=list');
                            } else {
                                          setSessionFlash('msg', 'Dữ liệu không hợp lệ, hãy kiểm tra lại !!');
                                          setSessionFlash('msg_type', 'danger');
                            }
              } else {
                            setSessionFlash('msg', 'Cập nhật dùng thất bại');
                            setSessionFlash('msg_type', 'danger');
                            setSessionFlash('oldData', $filter);
                            setSessionFlash('errors', $errors);
              }
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');

$oldData = getSessionFlash('oldData');
if (!empty($detailUser)) {
              $oldData = $detailUser;
}
$errorsArr = getSessionFlash('errors');

?>
<div class="container add-user">
              <h2>Thông tin người dùng</h2>
              <hr />
              <?php if (!empty($msg) && !empty($msg_type)) {
                            getMessage($msg, $msg_type);
              } ?>
              <form action="" method="post" enctype="multipart/form-data">
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
                                                                      type="password"
                                                                      class="form-control"
                                                                      placeholder="Mật khẩu" />
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
                                          <div class="col-6 mb-3">
                                                        <label for="avatar">Ảnh đại diện</label>
                                                        <input id="avatar" name="avatar" type="file" class="form-control" accept="image/*"
                                                                      onchange="previewImage(event)" />
                                                        <img id="preview"
                                                                      src="<?php echo !empty(oldData($oldData, 'avatar')) ? _HOST_URL . oldData($oldData, 'avatar') : ''; ?>"
                                                                      alt="Preview ảnh"
                                                                      class="img-avatar mt-2"
                                                                      width="200"
                                                                      style="display: <?php echo empty(oldData($oldData, 'avatar')) ? 'none' : 'block'; ?>;" />
                                                        <?php echo formError($errorsArr, 'avatar'); ?>
                                          </div>

                            </div>
                            <div class="d-flex mt-5 mb-3">
                                          <button type="submit" class="btn btn-success me-2">Xác nhận</button>
                                          <a type="button" href="?module=users&action=list" class="btn btn-primary">Quay lại</a>
                            </div>
              </form>
</div>
<?php
layout("footer");
?>