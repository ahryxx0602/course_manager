<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

$data = ['title' => 'Thêm mới khóa học'];
layout("header", $data);
layout("sidebar");

$thumbnail = '/uploads/courses/default-thumbnail.jpg'; // default

if (!empty($_FILES['thumbnail']['name'])) {
    $uploadDir = '/uploads/courses/';
    $uploadPath = _PATH_URL . $uploadDir; // define _PATH_URL là thư mục gốc

    $fileName = time() . '-' . basename($_FILES['thumbnail']['name']);
    $targetFile = $uploadPath . $fileName;

    $isImage = getimagesize($_FILES['thumbnail']['tmp_name']);
    if ($isImage !== false) {
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile)) {
            $thumbnail = $uploadDir . $fileName;
        } else {
            $errors['thumbnail']['upload'] = "Không thể tải ảnh lên.";
        }
    } else {
        $errors['thumbnail']['invalid'] = "File không phải là ảnh hợp lệ.";
    }
}
function toSlug($str)
{
    // chuẩn hoá rất cơ bản, đủ dùng
    $str = trim(mb_strtolower($str, 'UTF-8'));
    $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
    $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
    $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
    $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
    $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
    $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
    $str = preg_replace('/đ/u', 'd', $str);
    $str = preg_replace('/[^a-z0-9]+/u', '-', $str);
    $str = preg_replace('/-+/u', '-', $str);
    return trim($str, '-');
}

if (isPOST()) {
    $filter = filterData();
    $errors = [];

    // Validate name
    if (empty(trim($filter['name'] ?? ''))) {
        $errors['name']['require'] = "Tên khóa học bắt buộc phải nhập.";
    } elseif (mb_strlen(trim($filter['name'])) < 3) {
        $errors['name']['length'] = "Tên khóa học phải từ 3 ký tự.";
    }

    // Validate slug (tự sinh nếu trống)
    $slug = trim($filter['slug'] ?? '');
    if (empty($slug) && !empty($filter['name'])) {
        $slug = toSlug($filter['name']);
    }
    if (empty($slug)) {
        $errors['slug']['require'] = "Slug bắt buộc phải nhập.";
    }

    // Validate category_id
    $category_id = (int)($filter['category_id'] ?? 0);
    if ($category_id <= 0) {
        $errors['category_id']['require'] = "Vui lòng chọn danh mục khóa học.";
    } else {
        // kiểm tra category có tồn tại
        $catRows = getRows("SELECT id FROM course_category WHERE id = $category_id");
        if ($catRows <= 0) {
            $errors['category_id']['exist'] = "Danh mục không tồn tại.";
        }
    }

    // Validate price
    $price = $filter['price'] ?? '';
    if ($price === '' || $price === null) {
        $errors['price']['require'] = "Giá khóa học bắt buộc phải nhập.";
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $errors['price']['number'] = "Giá phải là số không âm.";
    } else {
        $price = (float)$price;
    }

    // Description (optional)
    $description = trim($filter['description'] ?? '');

    // Thumbnail (optional) – có thể để mặc định
    $thumbnail = trim($filter['thumbnail'] ?? '');
    if (empty($thumbnail)) {
        $thumbnail = '/uploads/courses/default-thumbnail.jpg';
    }

    // Check slug unique (tuỳ DB đã index unique hay chưa)
    if (empty($errors['slug'])) {
        $checkSlug = getRows("SELECT id FROM courses WHERE slug = '$slug'");
        if ($checkSlug > 0) {
            $errors['slug']['unique'] = "Slug đã tồn tại, vui lòng chọn slug khác.";
        }
    }

    if (empty($errors)) {
        $now = date('Y-m-d H:i:s');
        $dataInsert = [
            'name'       => trim($filter['name']),
            'slug'       => $slug,
            'category_id' => $category_id,
            'description' => $description ?: null,
            'price'      => $price,
            'thumbnail'  => $thumbnail,
            'create_at'  => $now,
            'update_at'  => $now,
        ];

        $insertStatus = insertData('courses', $dataInsert);
        if ($insertStatus) {
            setSessionFlash('msg', 'Thêm khóa học thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=courses&action=list');
        } else {
            setSessionFlash('msg', 'Dữ liệu không hợp lệ, hãy kiểm tra lại !!');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Thêm khóa học thất bại');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorsArr = getSessionFlash('errors');

// Lấy danh mục
$categories = getAll("SELECT id, name FROM course_category ORDER BY name ASC");
?>
<div class="container add-course">
    <h2>Thêm mới khóa học</h2>
    <hr />
    <?php if (!empty($msg) && !empty($msg_type)) {
        getMessage($msg, $msg_type);
    } ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 mb-3">
                <label for="name">Tên khóa học</label>
                <input id="name" name="name" type="text" class="form-control"
                    placeholder="VD: PHP Cơ bản"
                    value="<?php echo !empty($oldData) ? oldData($oldData, 'name') : ''; ?>" />
                <?php echo !empty($errorsArr) ? formError($errorsArr, 'name') : ''; ?>
            </div>

            <div class="col-6 mb-3">
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" class="form-control"
                    placeholder="vd: php-co-ban"
                    value="<?php echo !empty($oldData) ? oldData($oldData, 'slug') : ''; ?>" />
                <?php echo !empty($errorsArr) ? formError($errorsArr, 'slug') : ''; ?>
            </div>

            <div class="col-6 mb-3">
                <label for="category_id">Danh mục</label>
                <select name="category_id" id="category_id" class="form-select form-control">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php
                            $oldCat = !empty($oldData) ? (int)oldData($oldData, 'category_id') : 0;
                            echo ($oldCat === (int)$cat['id']) ? 'selected' : '';
                            ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo !empty($errorsArr) ? formError($errorsArr, 'category_id') : ''; ?>
            </div>

            <div class="col-6 mb-3">
                <label for="price">Giá (VNĐ)</label>
                <input id="price" name="price" type="number" min="0" step="1000" class="form-control"
                    placeholder="vd: 399000"
                    value="<?php echo !empty($oldData) ? oldData($oldData, 'price') : ''; ?>" />
                <?php echo !empty($errorsArr) ? formError($errorsArr, 'price') : ''; ?>
            </div>

            <div class="col-12 mb-3">
                <label for="thumbnail">Ảnh đại diện (upload)</label>
                <input id="thumbnail" name="thumbnail" type="file" class="form-control" accept="image/*"
                    onchange="previewImage(event)" />
                <?php echo formError($errorsArr, 'thumbnail'); ?>

                <div class="mt-2">
                    <img id="preview" src="<?php echo !empty($oldData['thumbnail']) ? $oldData['thumbnail'] : ''; ?>"
                        alt="Preview" style="max-width: 200px; max-height: 200px; display: <?php echo !empty($oldData['thumbnail']) ? 'block' : 'none'; ?>;" />
                </div>
            </div>

            <div class="col-12 mb-3">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="4" class="form-control"
                    placeholder="Mô tả ngắn về khóa học..."><?php
                                                            echo !empty($oldData) ? oldData($oldData, 'description') : '';
                                                            ?></textarea>
                <?php echo !empty($errorsArr) ? formError($errorsArr, 'description') : ''; ?>
            </div>
        </div>

        <div class="d-flex mt-4 mb-3">
            <button type="submit" class="btn btn-success me-2">Xác nhận</button>
            <a href="?module=courses&action=list" class="btn btn-primary">Quay lại</a>
        </div>
    </form>
</div>

<?php layout("footer"); ?>