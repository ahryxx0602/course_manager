<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}
$data = [
    'title' => 'Danh sách khóa học'
];
layout("header", $data);
layout("sidebar");

// manager_course/?module=courses&action=list&category=1&keyword=PHP&page=1

$filter = filterData();

$chuoiWHERE = '';
$category = 0;
$keyword = '';
if (isGET()) {
    if (isset($filter['category'])) {
        $category = (int)$filter['category'];
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
        $chuoiWHERE .= " (c.name LIKE '%$keyword%' OR c.slug LIKE '%$keyword%') ";
    }
    if (!empty($category)) {
        if (strpos($chuoiWHERE, 'WHERE') === false) {
            $chuoiWHERE .= " WHERE ";
        } else {
            $chuoiWHERE .= " AND ";
        }
        $chuoiWHERE .= " c.category_id = " . $category;
    }
}

// Xử lí phân trang 
$maxData = getRows("SELECT id FROM courses");
$perPage = 6;
$maxPages = ceil($maxData / $perPage);
$page = isset($filter["page"]) ? (int)$filter["page"] : 1;

if ($page > $maxPages || $page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $perPage;

$getDetailCourses = getAll("
    SELECT c.id, c.name, c.slug, c.price, c.create_at, cat.name AS category_name
    FROM courses c
    INNER JOIN course_category cat 
    ON c.category_id = cat.id $chuoiWHERE
    ORDER BY c.create_at DESC
    LIMIT $offset, $perPage
");

$getCategories = getAll("SELECT * FROM course_category");

//Xử lí query string
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('&page=' . $page, '', $queryString);
}

if ($category > 0 || !empty($keyword)) {
    $maxData2 = getRows("
    SELECT id
    FROM courses c $chuoiWHERE
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
        <a href="?module=courses&action=add" class="btn btn-primary mb-3">
            <i class="fa-solid fa-plus me-1"></i> Thêm mới khóa học
        </a>

        <form action="" method="get" class="mb-3">
            <input type="hidden" name="module" value="courses" />
            <input type="hidden" name="action" value="list" />
            <div class=" row g-2">
                <div class="col-md-3">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option selected disabled>Chọn danh mục khóa học</option>
                        <?php foreach ($getCategories as $item): ?>
                            <option value="<?php echo $item['id']; ?>"
                                <?php echo ($category == $item['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($item['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-7">
                    <input value="<?php echo (!empty($keyword)) ? $keyword : false ?>"
                        name="keyword" class="form-control" type="text"
                        placeholder="Nhập thông tin tìm kiếm..." />
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
                    <th scope="col">Tên khóa học</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Giá</th>
                    <th scope="col">Ngày tạo</th>
                    <th scope="col">Danh mục</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($getDetailCourses as $key => $item): ?>
                    <tr>
                        <th scope="row"><?php echo $key + 1; ?></th>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['slug']); ?></td>
                        <td><?php echo number_format($item['price']); ?> đ</td>
                        <td><?php echo $item['create_at']; ?></td>
                        <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                        <td>
                            <a href="?module=courses&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-warning me-3">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Sửa
                            </a>
                            <a href="?module=courses&action=delete&id=<?php echo $item['id']; ?>"
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

                    <!-- Trước -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo $queryString ?>&page=<?php echo $page - 1; ?>">
                                Trước
                            </a>
                        </li>
                    <?php endif ?>

                    <?php
                    $start = $page - 1;
                    if ($start < 1) $start = 1;
                    $end = $page + 1;
                    if ($end > $maxPages) $end = $maxPages;
                    ?>

                    <?php if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo $queryString ?>&page=1">...</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo $queryString ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $maxPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo $queryString ?>&page=<?php echo $maxPages; ?>">...</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($page < $maxPages): ?>
                        <li class="page-item">
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