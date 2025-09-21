<?php
if (!defined('_ROOT_PATH')) {
  die('Truy cập không hợp lệ!');
}
$data = [
  'title' => 'Quên mật khẩu'
];
layout("header-auth", $data);
if ((isPOST())) {
  $filter = filterData();
  $errors = [];

  //Validate email
  if (empty(trim($filter['email']))) {
    $errors['email']['require'] = "Email bắt buộc phải nhập.";
  } else {
    // ĐÚng định dang ?, email exist?
    if (!validateEmail(trim($filter['email']))) {
      $errors['email']['length'] = "Email không đúng định dạng.";
    }
  }

  if (empty($errors)) {
    // Xử lí và gửi email
    if (!empty($filter['email'])) {
      $email = $filter['email'];
      $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'");
      if (!empty($checkEmail)) {
        //Update forget_token vào bảng users
        $forgot_token = sha1(uniqid() . time());
        $data = [
          'forget_token' => $forgot_token,
        ];
        $condition = $checkEmail['id'];
        $updateStatus = updateData('users', $data, $condition);

        if ($updateStatus) {
          $activateUrl = _HOST_URL . '/?module=auth&action=reset&token=' . urlencode($forgot_token);
          $emailTo = $email;
          $subject = 'Reset mật khẩu tài khoản trên hệ thống Ahryxx Course';
          $content = "
                    Xin chào,<br><br>
                    Bạn đang yêu cầu reset lại mật khẩu trên <b>Hệ thống Quản lý Khóa học Ahryxx Course</b>.<br><br>
                    Để thay đổi mật khẩu Vui lòng click vào đường link bên dưới<br>
                    <a href='$activateUrl'>$activateUrl</a><br><br>
                    Cảm ơn bạn!
                    ";



          senMail($emailTo, $subject, $content);

          setSessionFlash('msg', 'Gửi yêu cầu thành công vui lòng kiểm tra email.');
          setSessionFlash('msg_type', 'success');
        } else {
          setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại.');
          setSessionFlash('msg_type', 'danger');
        }
      }
    }
  } else {
    setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào !!');
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
<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
          class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1 auth-box">
        <?php if (!empty($msg) && !empty($msg_type)) {
          getMessage($msg, $msg_type);
        } ?>
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="d-flex flex-column align-items-center justify-content-center my-4">
            <h2 class="fw-normal mb-5 me-3">Quên mật khẩu</h2>
          </div>

          <div class="divider d-flex align-items-center my-4">
            <p class="text-center fw-bold mx-3 mb-0">Bring tech to your life</p>
          </div>

          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <input name='email' type="email"
              value="<?php if (!empty($oldData)) {
                        echo oldData($oldData, 'email');
                      } ?>"
              class="form-control form-control-lg"
              placeholder="Nhập địa chỉ email" />
            <?php if (!empty($errorsArr)) {
              echo formError($errorsArr, 'email');
            } ?>
          </div>
          <div class="text-center text-lg-start mt-4 pt-2">
            <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
          </div>

        </form>
      </div>
    </div>
  </div>
  <?php
  layout("footer-auth");
  ?>