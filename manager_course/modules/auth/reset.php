<?php
if (!defined('_ROOT_PATH')) {
  die('Truy cập không hợp lệ!');
}
$data = [
  'title' => 'Đặt lại mật khẩu'
];
layout("header-auth", $data);
$filterGet = filterData('get');

if (!empty($filterGet['token'])) {
  $tokenReset = $filterGet['token'];
}
if (!empty($tokenReset)) {
  //Check token có chính xác không
  $checkToken = getOne("SELECT * FROM users WHERE forget_token = '$tokenReset'");
  if (!empty($checkToken)) {
    if ((isPOST())) {
      $filter = filterData();
      $errors = [];

      //validate password
      if (empty($filter['password'])) {
        $errors['password']['require'] = "Mật khẩu bắt buộc phải nhập.";
      } else {
        if (strlen(trim($filter['password'])) < 6) {
          $errors['password']['length'] = "Mật khẩu phải lớn hơn 6 ký tự.";
        }
      }

      //validate confirm password
      if (empty($filter['confirmPassword'])) {
        $errors['confirmPassword']['require'] = "Vui lòng nhập lại mật khẩu.";
      } else {
        if (trim($filter['password']) !== trim($filter['confirmPassword'])) {
          $errors['confirmPassword']['length'] = "Mật khẩu nhập lại không khớp.";
        }
      }

      if (empty($errors)) {
        $password = password_hash($filter['password'], PASSWORD_DEFAULT);
        $condition = "id=" . $checkToken['id'];
        $data = [
          'password' => $password,
          'forget_token' => null,
          'updated_at' => date('Y-m-d H:i:s')
        ];
        $updateStatus = updateData('users', $data, $condition);

        if ($updateStatus) {
          // Gửi mail thành công
          $emailTo = $checkToken['email'];
          $subject = 'Đổi mật khẩu thành công!';
          $content = "
                    Xin chào,<br><br>
                    Chúc mừng bạn đã đổi mật khẩu thành công</b>.<br><br>
                    Nếu không phải bạn thao tác đổi mật khẩu hãy liên hệ với admin<br>
                    Cảm ơn bạn!
                    ";



          senMail($emailTo, $subject, $content);

          setSessionFlash('msg', 'Đổi mật khẩu thành công.');
          setSessionFlash('msg_type', 'success');
        } else {
          setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại. ');
          setSessionFlash('msg_type', 'danger');
        }
      } else {
        setSessionFlash('msg', 'Dữ liệu không hợp lệ, hãy kiểm tra lại !!');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
      }
    }
  } else {
    getMessage('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
  }
} else {
  getMessage('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
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
            <h2 class="fw-normal mb-5 me-3">Đặt lại mật khẩu</h2>
          </div>

          <div class="divider d-flex align-items-center my-4">
            <p class="text-center fw-bold mx-3 mb-0">Bring tech to your life</p>
          </div>

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <input name='password' type="password"
              class="form-control form-control-lg"
              placeholder="Nhập mật khẩu" />
            <?php if (!empty($errorsArr)) {
              echo formError($errorsArr, 'password');
            } ?>
          </div>
          <!-- Confirm Password input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <input name='confirmPassword' type="password" class="form-control form-control-lg"
              placeholder="Nhập lại mật khẩu" />
            <?php if (!empty($errorsArr)) {
              echo formError($errorsArr, 'confirmPassword');
            } ?>
          </div>
          <div class="text-center text-lg-start mt-4 pt-2">
            <button type="sumit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
            <p class="small fw-bold mt-2 pt-1 mb-0">Bạn chưa có tài khoản<a
                href="<?php echo _HOST_URL; ?>?module=auth&action=login" class="link-danger">Quay lại Đăng nhập</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
  layout("footer-auth");
  ?>