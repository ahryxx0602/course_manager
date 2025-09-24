<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
$data = [
    'title' => 'Danh sách người dùng'
];
layout("header", $data);
layout("sidebar");
//manager_course/?module=users&action=list&group=1&keyword=Name&page=1 Cấu trúc URL Tìm kiếm

//Phân trang: Trước ... 5,6,7, ... Sau

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
        if (strpos($chuoiWHERE, 'WHERE') === false) {
            $chuoiWHERE .= " WHERE ";
        } else {
            $chuoiWHERE .= " AND";
        }
        $chuoiWHERE .= " (u.fullName LIKE '%$keyword%' OR u.email LIKE '%$keyword%') ";
    }
    if (!empty($group)) {
        if (strpos($chuoiWHERE, 'WHERE') == false) {
            $chuoiWHERE .= " WHERE ";
        } else {
            $chuoiWHERE .= " AND ";
        }
        $chuoiWHERE .= " u.group_id = " . (int)$group;
    }
}

// Xử lí phân trang 
$maxData = getRows("SELECT id FROM users");
$perPage = 6; //Số dòng dữ liệu trênnn 1 trang
$maxPages = ceil($maxData / $perPage); // Tính max page
$offset = 0;
$page = 1;
//GEt page
if (isset($filter["page"])) {
    $page = $filter["page"];
}

if ($page > $maxPages || $page < 1) {
    $page = 1;
}

//if (isset($page)) {
$offset = ($page - 1) * $perPage;
//}
$getDetailUsers = getAll("
    SELECT  u.fullName, u.email, u.created_at, u.group_id, g.name AS group_name,
    u.id, u.group_id
    FROM users u
    INNER JOIN `groups` g 
    ON u.group_id = g.id $chuoiWHERE
    ORDER BY u.fullName ASC
    LIMIT $offset, $perPage
");

$getGroup = getAll("SELECT * FROM `groups`");

//Xử lí queryy
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('&page=' . $page, '', $queryString);
}

if ($group > 0 || !empty($keyword)) {
    $maxData2 = getRows("
    SELECT id
    FROM users u $chuoiWHERE
    ");
    $maxPages = ceil($maxData2 / $perPage);
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>

<div class="container mt-3">
    <div class="container-fluid">
        <?php if (!empty($msg) && !empty($msg_type)) {
            getMessage($msg, $msg_type);
        } ?>
        <hr />
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
                    <select name="group" id="" class="form-select" onchange="this.form.submit()">
                        <option selected disabled>Chọn nhóm người dùng</option>
                        <?php foreach ($getGroup as $item): ?>
                            <option value=" <?php echo $item['id']; ?>"
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
                        <td><?php echo $item['group_name']; ?></td>
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

                    <!-- Xử lí nút "Trước" -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <!-- Khi trang hiện tại > 1 thì cho phép lùi 1 trang -->
                            <a class="page-link" href="?<?php echo $queryString ?>&page=<?php echo $page - 1; ?>">
                                Trước
                            </a>
                        </li>
                    <?php endif ?>


                    <!-- Tính vị trí trang bắt đầu (start = page - 1) -->
                    <?php
                    $start = $page - 1;
                    if ($start < 1) {
                        $start = 1; // Không cho nhỏ hơn 1
                    }
                    ?>

                    <!-- Nếu start > 1 thì hiển thị dấu "..." để nhảy về cụm trang trước -->
                    <?php if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?<?php echo $queryString ?>&page=<?php echo 1; ?>">
                                ...
                            </a>
                        </li>
                    <?php endif; ?>


                    <!-- Tính vị trí trang kết thúc (end = page + 1) -->
                    <?php
                    $end = $page + 1;
                    if ($end > $maxPages) {
                        $end = $maxPages; // Không vượt quá tổng số trang
                    }
                    ?>


                    <!-- Vòng lặp hiển thị các số trang từ $start đến $end -->
                    <?php for ($i = $start; $i <= $end; $i++) : ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <!-- Nếu $i == $page thì thêm class active -->
                            <a class="page-link" href="?<?php echo $queryString ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>


                    <!-- Nếu $end < $maxPages thì hiện dấu "..." để nhảy tới cụm sau -->
                    <?php if ($end <= $maxPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo $queryString ?>&page=<?php echo $maxPages; ?>">
                                ...
                            </a>
                        </li>
                    <?php endif; ?>


                    <!-- Xử lí nút "Sau" -->
                    <?php if ($page < $maxPages): ?>
                        <li class="page-item">
                            <!-- Khi chưa ở trang cuối thì cho phép nhảy sang trang tiếp -->
                            <a class="page-link" href="?<?php echo $queryString ?>&page=<?php echo $page + 1; ?>">
                                Sau
                            </a>
                        </li>
                    <?php endif ?>

                </ul>
            </nav>
        </div>
    </div>
</div>


<?php
layout("footer");
?>