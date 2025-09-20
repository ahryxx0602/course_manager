<?php
//Kiểm tra xem active_token ở url có giống active_token trong csdl hay không
// Update trường status trong bảng users -> 1 (đã kích hoạt) + xóa active_token trong csdl
if (!defined('_ROOT_PATH')) {
  die('Truy cập không hợp lệ!');
}
$data = [
  'title' => 'Xác thực hệ thống'
];
layout("header-auth", $data);
$filterData = filterData('get');

//Đường link hợp lệ
if (!empty($filterData['token'])) :
  $token = $filterData['token'];
  $checkToken = getOne("SELECT * FROM users WHERE active_token = '$token'");
?>

  <section class="vh-100">
    <div class="container-fluid h-custom">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-md-9 col-lg-6 col-xl-5">
          <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
            class="img-fluid" alt="Sample image">
        </div>

        <?php if ((!empty($checkToken))) :
          // Update dữ liệu sau khi xác thực
          $data = [
            'status' => 1,
            'active_token' => null,
            'updated_at' => date('Y;m:d H:i:s')
          ];
          $condition = "id =" . $checkToken['id'];
          updateData('users', $data, $condition);
        ?>
          <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
            <div class="d-flex flex-column align-items-center justify-content-center my-4">
              <h2 class="fw-normal mb-5 me-3">Kích hoạt tài khoản thành công</h2>
            </div>
            <div class="text-center text-lg-start mt-4 pt-2">
              <a style="font-size: 20px; text-decoration: dodgerblue !important; color: dodgerblue !important;"
                href="<?php echo _HOST_URL; ?>?module=auth&action=login"
                class="link-danger"> Đăng nhập ngay</a>
            </div>
            </form>
          </div>
        <?php else : ?>
          <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
            <div class="d-flex flex-column align-items-center justify-content-center my-4">
              <h2 class="fw-normal mb-5 me-3">Kích hoạt tài khoản không thành công, đường link đã hết hạn hoặc không tồn tại</h2>
            </div>
            <div class="text-center text-lg-start mt-4 pt-2">
              <a style="font-size: 20px; text-decoration: dodgerblue !important; color: dodgerblue !important;"
                href="<?php echo _HOST_URL; ?>?module=auth&action=login"
                class="link-danger"> Quay lại</a>
            </div>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>

  <?php endif;
layout("footer-auth");
  ?>

  <!-- if($a>10){
  todo1;
} else if{
  todo2;
} else{
  todo3;
}

tương đương

if($a>10):
  todo1;
else if :
  todo2;
else:
  todo3;
endif; -->