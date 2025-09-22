<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
$data = [
    'title' => 'Danh sách người dùng'
];
layout("header", $data);
layout("sidebar");
//manager_course/?module=users&action=list&group=1&keyword=Name Cấu trúc URL Tìm kiếm

$filter = filterData();

$chuoiWHERE = '';
$group = 0;
$keyword = '';
if (isGET()) {
    if (isset($filter['group'])) {
        $group = $filter['group'];
    }
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }
    if (!empty($keyword)) {
        if (strpos($chuoiWHERE, 'WHERE') == false) {
            $chuoiWHERE .= " WHERE ";
        } else {
            $chuoiWHERE .= " AND";
        }
        $chuoiWHERE .= " fullName LIKE '%$keyword%' OR email LIKE '%$keyword%' ";
    }
    if (!empty($group)) {
        if (strpos($chuoiWHERE, 'WHERE') == false) {
            $chuoiWHERE .= " WHERE ";
        } else {
            $chuoiWHERE .= " AND ";
        }
        $chuoiWHERE .= "group_id = $group";
    }
}


$getDetailUsers = getAll("
    SELECT  u.fullName, u.email, u.created_at, u.group_id, g.name AS group_name,
    u.id, u.group_id
    FROM users u
    INNER JOIN `groups` g 
    ON u.group_id = g.id $chuoiWHERE
    ORDER BY u.fullName ASC
");

$getGroup = getAll("SELECT * FROM `groups`");
?>

<div class="container mt-3">
    <div class="container-fluid">
        <a href="?module=users&action=add" class="btn btn-primary mb-3">
            <i class="fa-solid fa-user-plus me-1"></i>Thêm mới người dùng
        </a>
        <form action="" method="get" class="mb-3">
            <input type="hidden" name="module" value="users" />
            <input type="hidden" name="action" value="list" />
            <!--
            <input type="hidden" name="module" value="users" />
            <input type="hidden" name="action" value="list" />
            Để url có dang là manager_course/?module=users&action=list&group=1&keyword=Name 
            để phục vụ tìm kiếm
            -->
            <div class=" row g-2">
                <div class="col-md-3">
                    <select name="group" id="" class="form-select">
                        <option selected disabled>Chọn nhóm người dùng</option>
                        <?php foreach ($getGroup as $item): ?>
                            <option value="<?php echo $item['id']; ?>"
                                <?php echo ($group == $item['id']) ? 'selected' : ''; ?>>
                                <!-- Nếu biến $group (giá trị nhóm hiện tại) == id của nhóm trong vòng lặp 
                                thì thêm thuộc tính selected => option này sẽ được chọn mặc định -->
                                <?php echo htmlspecialchars($item['name']); ?>
                                <!-- Hiển thị tên nhóm ra giao diện. Dùng htmlspecialchars để tránh lỗi XSS -->
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>
                <div class="col-md-7">
                    <input value="<?php echo (!empty($keyword)) ? $keyword : false ?>" name="keyword" class="form-control" type="text" placeholder="Nhập thông tin tìm kiếm..." />
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