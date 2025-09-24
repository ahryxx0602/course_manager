<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

$data = ['title' => 'Thêm mới khóa học'];
layout("header", $data);
layout("sidebar");

$upload = upload_image('thumbnail');
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

    // Slug
    $slug = trim($filter['slug'] ?? '');
    if ($slug === '' && !empty($filter['name'])) {
        $slug = toSlug($filter['name']);
    }
    if ($slug === '') {
        $errors['slug']['require'] = "Slug bắt buộc phải nhập.";
    } else {
        $exists = getRows("SELECT id FROM courses WHERE slug = '$slug'");
        if ($exists > 0) {
            $errors['slug']['unique'] = "Slug đã tồn tại, vui lòng chọn slug khác.";
        }
    }

    // Category
    $category_id = (int)($filter['category_id'] ?? 0);
    if ($category_id <= 0) {
        $errors['category_id']['require'] = "Vui lòng chọn danh mục khóa học.";
    } else {
        $cat = getRows("SELECT id FROM course_category WHERE id = $category_id");
        if ($cat <= 0) {
            $errors['category_id']['exist'] = "Danh mục không tồn tại.";
        }
    }

    // Price
    $price = $filter['price'] ?? '';
    if ($price === '' || $price === null) {
        $errors['price']['require'] = "Giá khóa học bắt buộc phải nhập.";
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $errors['price']['number'] = "Giá phải là số không âm.";
    } else {
        $price = (float)$price;
    }

    // Description
    $description = trim($filter['description'] ?? '');

    // Thumb
    $thumbnailRel = '/uploads/courses/default-thumbnail.jpg';
    if ($upload === false) {
        $errors['thumbnail']['upload'] = 'Upload ảnh thất bại hoặc file không hợp lệ.';
    } elseif ($upload) {
        $thumbnailRel = $upload;
    }

    if (empty($errors)) {
        $now = date('Y-m-d H:i:s');
        $dataInsert = [
            'name'        => trim($filter['name']),
            'slug'        => $slug,
            'category_id' => $category_id,
            'description' => $description ?: null,
            'price'       => $price,
            'thumbnail'   => $thumbnailRel,  // dùng đúng biến đã upload/mặc định
            'create_at'   => $now,
            'update_at'   => $now,
        ];

        $ok = insertData('courses', $dataInsert);
        if ($ok) {
            setSessionFlash('msg', 'Thêm khóa học thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
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
                <div class="mt-2">
                    <?php
                    $oldThumbRel = !empty($oldData['thumbnail']) ? $oldData['thumbnail'] : '';
                    $oldThumbAbs = $oldThumbRel ? (_HOST_URL . $oldThumbRel) : '';
                    ?>
                    <img id="preview"
                        src="<?php echo $oldThumbAbs; ?>"
                        alt="Preview"
                        style="max-width:200px;max-height:200px;display:<?php echo $oldThumbAbs ? 'block' : 'none'; ?>;" />
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
            <a href="?module=course&action=list" class="btn btn-primary">Quay lại</a>
        </div>
    </form>
</div>
<script>
    function createSlug(s) {
        return s.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // bỏ dấu
            .replace(/đ/g, 'd') // đ -> d
            .replace(/[^a-z0-9\s-]/g, '') // bỏ ký tự lạ
            .trim().replace(/\s+/g, '-') // khoảng trắng -> -
            .replace(/-+/g, '-'); // gộp dấu -
    }

    const nameEl = document.getElementById('name');
    const slugEl = document.getElementById('slug');

    if (nameEl && slugEl) {
        nameEl.addEventListener('input', function() {
            slugEl.value = createSlug(this.value);
        });
    }
</script>

<?php layout("footer"); ?>