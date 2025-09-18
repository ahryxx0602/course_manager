<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
$pageTitle = $pageTitle ?? 'Đăng nhập';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title> <?php echo $data['title']; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Font (Source Sans 3) -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" />
  <!-- Bootstrap 5 -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
  <!-- AdminLTE (nếu dùng) -->
  <link rel="stylesheet" href="<?= _HOST_URL_TEMPLATES ?>assets/css/adminlte.css" />
  <!-- Trang riêng -->
  <link rel="stylesheet" href="<?= _HOST_URL_TEMPLATES ?>assets/css/login.css" />
  <style>
    body { font-family: "Source Sans 3", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif; }
  </style>
</head>
