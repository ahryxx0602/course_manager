<?php
if (!defined('_ROOT_PATH')) {
  die('Truy cập không hợp lệ!');
}
$data = [
  'title' => 'Đăng nhập hệ thống'
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

  //validate password
  if (empty($filter['password'])) {
    $errors['password']['require'] = "Mật khẩu bắt buộc phải nhập.";
  } else {
    if (strlen(trim($filter['password'])) < 6) {
      $errors['password']['length'] = "Mật khẩu phải lớn hơn 6 ký tự.";
    }
  }

  if (empty($errors)) {
    //Kiểm tra dữ liệu
    $email = $filter['email'];
    $password = $filter['password'];

    //Kiểm tra email
    $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'");

    if (!empty($checkEmail)) {
      $checkStatus = password_verify($password, $checkEmail["password"]);
      if ($checkStatus) {
        // Tài khoản chỉ login 1 nơi
        $user_id = $checkEmail['id'];
        $checkAlready = getRows("SELECT * FROM token_login WHERE user_id = $user_id");

        if ($checkAlready > 0) {
          setSessionFlash('msg', "Tài khoản đang đăng nhập ở một nơi khác, vui lòng thử lại sau.");
          setSessionFlash('msg_type', "danger");
          redirect("?module=auth&action=login");
        } else {
          // Tạo token insert vào token_login
          $token = sha1(uniqid() . time());

          // Gán token lên session 
          setSessionFlash("token_login", $token);
          $data = [
            'token' => $token,
            'create_at' => date('Y-m-d H:i:s'),
            'user_id' => $checkEmail['id'],
          ];

          $insertToken = insertData('token_login', $data);
          if ($insertToken) {
            setSessionFlash('msg', "Đăng nhập thành công.");
            setSessionFlash('msg_type', "success");
            redirect(("/"));
          } else {
            setSessionFlash('msg', "Đăng nhập không thành công.");
            setSessionFlash('msg_type', "danger");
          }
        }
      } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào !!');
        setSessionFlash('msg_type', 'danger');
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
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp" class="img-fluid"
          alt="Sample image">
      </div>
      <div style="margin-bottom: 130px;" class="col-md-8 col-lg-6 col-xl-4 offset-xl-1 auth-box">
        <?php if (!empty($msg) && !empty($msg_type)) {
          getMessage($msg, $msg_type);
        } ?>
        <form method="POST" action="" enctype="multipart/form-data ">
          <div class="d-flex flex-column align-items-center justify-content-center my-4">
            <h2 class="fw-normal mb-5 me-3">Đăng nhập hệ thống</h2>
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

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <input name='password' type="password"
              class="form-control form-control-lg"
              placeholder="Nhập mật khẩu" />
            <?php if (!empty($errorsArr)) {
              echo formError($errorsArr, 'password');
            } ?>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="<?php echo _HOST_URL; ?>?module=auth&action=forgot" class="text-body">Quên mật khẩu</a>
          </div>

          <div class="text-center text-lg-start mt-4 pt-2">
            <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">Đăng nhập</button>
            <p class="small fw-bold mt-2 pt-1 mb-0">Bạn chưa có tài khoản<a
                href="<?php echo _HOST_URL; ?>?module=auth&action=register" class="link-danger"> Đăng ký nhanh</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
  layout("footer-auth");
  ?>