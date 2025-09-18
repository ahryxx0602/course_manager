<?php
if(!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
    $data = [
      'title' => 'Đăng ký tài khoản'
    ];
    layout("header-auth", $data);
?>
<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
          class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <form>
          <div class="d-flex flex-column align-items-center justify-content-center my-4">
            <h2 class="fw-normal mb-5 me-3">Đăng nhập hệ thống</h2>
          </div>

          <div class="divider d-flex align-items-center my-4">
            <p class="text-center fw-bold mx-3 mb-0">Bring tech to your life</p>
          </div>

            <!-- Fullname input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" class="form-control form-control-lg"
                placeholder="Nhập tên của bạn" />
            </div>
          <!-- Email input -->
          <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" class="form-control form-control-lg"
              placeholder="Nhập địa chỉ email" />
          </div>


            <!-- Phone input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" class="form-control form-control-lg"
              placeholder="Nhập số điện thoại" />
            </div>

          <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-3">
            <input type="password" class="form-control form-control-lg"
              placeholder="Nhập mật khẩu" />
          </div>
            <!-- Password input -->
          <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" class="form-control form-control-lg"
              placeholder="Nhập lại mật khẩu" />
            </div>

          <div class="text-center text-lg-start mt-4 pt-2">
            <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
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