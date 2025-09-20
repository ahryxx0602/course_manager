<?php
if(!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
    $data = [
      'title' => 'Đăng ký tài khoản'
    ];
    layout("header-auth", $data);
    $msg = '';
    $msg_type = '';
    $errorsArr = [];
    $oldData = [];

    if(isPOST()){
      $filter = filterData();
      $errors = [];

      //Validate fullName
      if(empty(trim($filter['fullName']))){
        $errors['fullName']['require'] = "Họ tên bắt buộc phải nhập.";
      } else {
        if(strlen(trim($filter['fullName'])) < 5){
          $errors['fullName']['length'] = "Họ tên bắt buộc phải trên 5 kí tự.";
        }
      }

      // Validate email
      if(empty(trim($filter['email']))){
        $errors['email']['require'] = "Email bắt buộc phải nhập.";
      } else {
        // ĐÚng định dang ?, email exist?
        if(!validateEmail(trim($filter['email']))){
          $errors['email']['length'] = "Email không đúng định dạng.";
        } else {
          $email = $filter['email'];

          $checkEmail = getRows("SELECT * FROM users WHERE email = '$email'");
          if($checkEmail > 0){
            $errors['email']['check'] = "Email đã có trong hệ thống.";
          }
        }
      }

      //Validate phone
      if(empty($filter['phone'])){
        $errors['phone']['require'] = "Số điện thoại bắt buộc phải nhập.";
      } else {
        if(!isPhone($filter['phone'])){
          $errors['phone']['isPhone'] = "Số điện thoại không đúng định dạng.";
        }
      }

      //Validate Password
      if(empty($filter['password'])){
        $errors['password']['require'] = "Mật khẩu bắt buộc phải nhập.";
      } else {
        if(strlen(trim($filter['password']))<6){
          $errors['password']['length'] = "Mật khẩu phải lớn hơn 6 ký tự.";
        }
      }

      // if(!empty($filter['password'])){
      //     $password = trim($filter['password']);
      //     if(strlen($password) <br 6){
      //         $errors['password']['length'] = "Mật khẩu phải lớn hơn 6 ký tự.";
      //     } else {
      //         if(!preg_match('/[A-Z]/', $password)){
      //             $errors['password']['uppercase'] = "Mật khẩu phải có ít nhất 1 chữ in hoa.";
      //         }
      //         if(!preg_match('/[0-9]/', $password)){
      //             $errors['password']['number'] = "Mật khẩu phải có ít nhất 1 chữ số.";
      //         }
      //         if(!preg_match('/[\W]/', $password)){
      //             // \W: ký tự không phải chữ và số
      //             $errors['password']['special'] = "Mật khẩu phải có ít nhất 1 ký tự đặc biệt.";
      //         }
      //     }
      // }

      //validate confirm password
      if(empty($filter['confirmPassword'])){
        $errors['confirmPassword']['require'] = "Vui lòng nhập lại mật khẩu.";
      } else {
        if(trim($filter['password']) !== trim($filter['confirmPassword'])){
          $errors['confirmPassword']['length'] = "Mật khẩu nhập lại không khớp.";
        }
      }

      if(empty($errors)){
        // Không lỗi table: users
        $active_token = sha1(uniqid().time());
        $data = [
          'fullName' => $filter['fullName'],
          'email' => $filter['email'],
          'phone' => $filter['phone'],
          'password' => $filter['password'],
          'active_token' => $active_token,
          'group_id' => 1,
          'created_at' => date('Y:m:d H:i:s'),
        ];

        $InsertStatus = insertData('users', $data);

        if(($InsertStatus)){
          // Gửi email
          $activateUrl = _HOST_URL . '/?module=auth&action=active&token=' . urlencode($active_token);
          $emailTo = $filter['email'];
          $subject = '🎉 Kích hoạt tài khoản Hệ thống quản lý khóa học';
          $content = "
                    Xin chào,<br><br>
                    Chúc mừng bạn đã đăng ký tài khoản tại <b>Hệ thống Quản lý Khóa học</b>.<br><br>
                    Vui lòng kích hoạt tài khoản bằng liên kết sau:<br>
                    <a href='$activateUrl'>$activateUrl</a><br><br>
                    Nếu bạn không thực hiện đăng ký, hãy bỏ qua email này.<br><br>
                    Cảm ơn bạn!
                    ";



          senMail($emailTo, $subject, $content);

          setSessionFlash('msg', 'Đăng ký thành công, vui lòng kích hoạt tài khoản.');
          setSessionFlash('msg_type', 'success');
        } else {
          setSessionFlash('msg', 'Đăng ký không thành công, xin vui lòng thử lại.');
          setSessionFlash('msg_type', 'danger');
        }
      } else {
        setSessionFlash('msg', 'Dữ liệu không hợp lệ, hãy kiểm tra lại !!');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
      }
      $msg = getSessionFlash('msg');
      $msg_type = getSessionFlash('msg_type');
      $oldData = getSessionFlash('oldData');
      $errorsArr = getSessionFlash('errors');
    }
?>

<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
          class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1 auth-box">
        <?php
        getMessage($msg, $msg_type);
        ?>
        <form method="POST" action="" enctype="multipart/form-data ">
          <div class="d-flex flex-column align-items-center justify-content-center my-4">
            <h2 class="fw-normal mb-2 mt-3 me-3">Đăng nhập hệ thống</h2>
          </div>
            <!-- Fullname input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <input name='fullName' type="text" value="<?php echo oldData($oldData, 'fullName') ?>" class="form-control form-control-lg"
                placeholder="Nhập tên của bạn" />
                <?= formError($errorsArr, 'fullName'); ?>
            </div>
          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <input name='email' type="email" value="<?php echo oldData($oldData, 'email') ?>" class="form-control form-control-lg"
              placeholder="Nhập địa chỉ email" />
              <?= formError($errorsArr, 'email'); ?>
          </div>


            <!-- Phone input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <input name='phone' type="text" value="<?php echo oldData($oldData, 'phone') ?>" class="form-control form-control-lg"
              placeholder="Nhập số điện thoại" />
              <?= formError($errorsArr, 'phone'); ?>
            </div>

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <input name='password' type="password" class="form-control form-control-lg"
              placeholder="Nhập mật khẩu" />
              <?= formError($errorsArr, 'password'); ?>
          </div>
            <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-4">
                <input name='confirmPassword' type="password" class="form-control form-control-lg"
              placeholder="Nhập lại mật khẩu" />
              <?= formError($errorsArr, 'password'); ?>
            </div>

          <div class="text-center text-lg-start mt-4 pt-2">
            <button  type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
              style="padding-left: 2.5rem; padding-right: 2.5rem;">Đăng ký</button>
            <p class="small fw-bold mt-2 pt-1 mb-0">Bạn đã có tài khoản<a href="<?php echo _HOST_URL; ?>?module=auth&action=login"
                class="link-danger"> Đăng nhập</a></p>
          </div>

        </form>
      </div>
    </div>
  </div>
<?php
    layout("footer-auth");
?>