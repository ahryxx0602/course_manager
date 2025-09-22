<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
$data = [
    'title' => 'Danh sách người dùng'
];
layout("header", $data);
layout("sidebar");

$getDetailUsers = getAll("
    SELECT u.id, u.fullName, u.email, u.created_at, u.group_id, g.name AS group_name
    FROM users u
    INNER JOIN `groups` g 
    ON u.group_id = g.id
    ORDER BY u.fullName ASC
");
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
                <?php
                foreach ($getDetailUsers as $key => $item):
                ?>
                    <tr>
                        <th scope="row"><?php echo $key + 1; ?></th>
                        <td><?php echo $item['fullName']; ?></td>
                        <td><?php echo $item['email']; ?></td>
                        <td><?php echo $item['created_at']; ?></td>
                        <td><?php echo $item['group_id']; ?></td>
                        <td>
                            <a href="?module=users&action=permission&id=<?php echo $item['id']; ?>" class="btn btn-success">
                                <i class="fa-solid fa-user-shield me-1"></i> Phân quyền
                            </a>
                        </td>
                        <td>
                            <a href="?module=users&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-warning me-3">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Sửa
                            </a>
                            <a href="?module=users&action=delete&id=<?php echo $item['id']; ?>"
                                onclick="return confirm('Có chắc chắn muốn xóa không ?')"
                                class="btn btn-danger">
                                <i class="fa-solid fa-trash me-1"></i> Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
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
</div>


<?php
layout("footer");
?>