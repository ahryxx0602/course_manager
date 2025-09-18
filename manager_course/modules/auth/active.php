<?php
if(!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
    layout("header-auth");
?>
<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
          class="img-fluid" alt="Sample image">
      </div>
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
    </div>
  </div>
<?php
    layout("footer-auth");
?>