<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

$data = [
    'title' => 'Chỉnh sửa khóa học'
];
layout("header", $data);
layout("sidebar");

$getData = filterData('get');

if (!empty($getData['id'])) {
    $course_id = $getData['id'];
    $detailCourse = getOne("SELECT * FROM courses WHERE id = $course_id");
} else {
    setSessionFlash('msg', 'Khóa học không tồn tại');
    setSessionFlash('msg_type', 'danger');
    redirect('?module=course&action=list');
}

$uploadDir = _PATH_URL . '/uploads/courses/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Tạo folder đệ quy nếu chưa có
}

if (isPOST()) {
    $filter = filterData();
    $errors = [];

    if (empty(trim($filter['name']))) {
        $errors['name']['require'] = "Tên khóa học bắt buộc phải nhập.";
    } elseif (mb_strlen(trim($filter['name'])) < 3) {
        $errors['name']['length'] = "Tên khóa học phải từ 3 ký tự.";
    }

    $slug = trim($filter['slug'] ?? '');
    if (empty($slug) && !empty($filter['name'])) {
        $slug = toSlug($filter['name']);
    }
    if (empty($slug)) {
        $errors['slug']['require'] = "Slug bắt buộc phải nhập.";
    }

    $category_id = (int)($filter['category_id'] ?? 0);
    if ($category_id <= 0) {
        $errors['category_id']['require'] = "Vui lòng chọn danh mục khóa học.";
    } else {
        $catRows = getRows("SELECT id FROM course_category WHERE id = $category_id");
        if ($catRows <= 0) {
            $errors['category_id']['exist'] = "Danh mục không tồn tại.";
        }
    }

    $price = $filter['price'] ?? '';
    if ($price === '' || $price === null) {
        $errors['price']['require'] = "Giá khóa học bắt buộc phải nhập.";
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $errors['price']['number'] = "Giá phải là số không âm.";
    } else {
        $price = (float)$price;
    }

    $description = trim($filter['description'] ?? '');

    $thumbnail = $detailCourse['thumbnail'];
    if (!empty($_FILES['thumbnail']['name'])) {
        $uploadDir = _PATH_URL . '/uploads/courses/';
        $fileName = time() . '_' . basename($_FILES['thumbnail']['name']);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile)) {
            $thumbnail = '/uploads/courses/' . $fileName;
        } else {
            $errors['thumbnail']['upload'] = "Upload ảnh thất bại.";
        }
    }

    if (empty($errors)) {
        $now = date('Y-m-d H:i:s');
        $dataUpdate = [
            'name' => trim($filter['name']),
            'slug' => $slug,
            'category_id' => $category_id,
            'description' => $description ?: null,
            'price' => $price,
            'thumbnail' => $thumbnail,
            'update_at' => $now,
        ];

        $condition = "id=" . $course_id;
        $updateStatus = updateData('courses', $dataUpdate, $condition);

        if ($updateStatus) {
            setSessionFlash('msg', 'Cập nhật khóa học thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
        } else {
            setSessionFlash('msg', 'Dữ liệu không hợp lệ, hãy kiểm tra lại !!');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Cập nhật khóa học thất bại');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
if (!empty($detailCourse)) {
    $oldData = $detailCourse;
}
$errorsArr = getSessionFlash('errors');
$categories = getAll("SELECT id, name FROM course_category ORDER BY name ASC");
?>
<div class="container add-course">
    <h2>Chỉnh sửa khóa học</h2>
    <hr />
    <?php if (!empty($msg) && !empty($msg_type)) getMessage($msg, $msg_type); ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 mb-3">
                <label for="name">Tên khóa học</label>
                <input id="name" name="name" type="text" class="form-control"
                    value="<?php echo oldData($oldData, 'name'); ?>" />
                <?php echo formError($errorsArr, 'name'); ?>
            </div>
            <div class="col-6 mb-3">
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" class="form-control"
                    value="<?php echo oldData($oldData, 'slug'); ?>" />
                <?php echo formError($errorsArr, 'slug'); ?>
            </div>
            <div class="col-6 mb-3">
                <label for="category_id">Danh mục</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (oldData($oldData, 'category_id') == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo formError($errorsArr, 'category_id'); ?>
            </div>
            <div class="col-6 mb-3">
                <label for="price">Giá (VNĐ)</label>
                <input id="price" name="price" type="number" min="0" step="1000" class="form-control"
                    value="<?php echo oldData($oldData, 'price'); ?>" />
                <?php echo formError($errorsArr, 'price'); ?>
            </div>
            <div class="col-6 mb-3">
                <label for="thumbnail">Ảnh đại diện</label>
                <input id="thumbnail" name="thumbnail" type="file" class="form-control" accept="image/*"
                    onchange="previewImage(event)" />
                <img id="preview"
                    src="<?php echo !empty(oldData($oldData, 'thumbnail')) ? _HOST_URL . oldData($oldData, 'thumbnail') : ''; ?>"
                    alt="Preview ảnh"
                    class="img-thumbnail mt-2"
                    width="200"
                    style="display: <?php echo empty(oldData($oldData, 'thumbnail')) ? 'none' : 'block'; ?>;" />
                <?php echo formError($errorsArr, 'thumbnail'); ?>
            </div>
            <div class="col-12 mb-3">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="4" class="form-control"><?php echo oldData($oldData, 'description'); ?></textarea>
                <?php echo formError($errorsArr, 'description'); ?>
            </div>
        </div>
        <div class="d-flex mt-4 mb-3">
            <button type="submit" class="btn btn-success me-2">Xác nhận</button>
            <a href="?module=course&action=list" class="btn btn-primary">Quay lại</a>
        </div>
    </form>
</div>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<?php layout("footer"); ?>