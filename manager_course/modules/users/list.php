<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
$data = [
    'title' => 'Danh sách người dùng'
];
layout("header", $data);
layout("sidebar");
?>

<div class="container mt-3">
    <div class="container-fluid">
        <a href="?module=users&action=add" class="btn btn-primary mb-3">
            <i class="fa-solid fa-user-plus me-1"></i>Thêm mới người dùng
        </a>
        <form action="" method="get" class="mb-3">
            <div class=" row g-2">
                <div class="col-md-3">
                    <select name="" id="" class="form-select">
                        <option selected disabled>Chọn nhóm người dùng</option>
                        <option value="1">Quản trị viên</option>
                        <option value="2">Người dùng</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input class="form-control" type="text" placeholder="Nhập thông tin tìm kiếm..." />
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Họ tên</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ngày đăng ký</th>
                    <th scope="col">Nhóm</th>
                    <th scope="col">Phân quyền</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>1</td>
                    <td>2</td>
                    <td>@3</td>
                    <td>4</td>
                    <td>
                        <a href="#" class="btn btn-success">
                            <i class="fa-solid fa-user-shield me-1"></i> Phân quyền
                        </a>
                    </td>
                    <td>
                        <a href="#" class="btn btn-warning me-3">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Sửa
                        </a>
                        <a href="#" class="btn btn-danger">
                            <i class="fa-solid fa-trash me-1"></i> Xóa
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>
</div>

<?php
layout("footer");
?>