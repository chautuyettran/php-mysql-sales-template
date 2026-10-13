<?php
$pageTitle = 'Kiểm tra Upload File';
require_once '/var/www/src/includes/header.php';
require_once '/var/www/src/includes/navbar.php';
?>

<div class="container mt-4">

    <h2>Kiểm tra Upload File</h2>

    <form method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label for="productImage" class="form-label">
                Chọn ảnh
            </label>

            <input
                type="file"
                class="form-control"
                id="productImage"
                name="product_image"
                accept="image/jpeg,image/png,image/webp"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Gửi file
        </button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

        <hr>
        <h4>Dữ liệu nhận được trong $_FILES</h4>
        <pre><?php print_r($_FILES); ?></pre>

        <?php
            if (
                isset($_FILES['product_image'])
                && $_FILES['product_image']['error'] === UPLOAD_ERR_OK
            ) {

                $file = $_FILES['product_image'];

                $maxSize = 2 * 1024 * 1024;
                if ($file['size'] > $maxSize) {
                    echo '<div class="alert alert-danger mt-3">File ảnh không được vượt quá 2 MB.</div>';
                    exit;
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file['tmp_name']);

                $allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!in_array($mimeType, $allowedTypes, true)) {
                    echo '<div class="alert alert-danger mt-3">Chỉ cho phép file JPG, JPEG, PNG hoặc WebP.</div>';
                    exit;
                }

                echo '<p>MIME type: ' . htmlspecialchars($mimeType) . '</p>';

                $extensionMap = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp'
                ];
                $extension = $extensionMap[$mimeType]; 

                $newFileName = 
                    'product-' 
                    . bin2hex(random_bytes(8)) 
                    . '.' 
                    . $extension; 

                $destination = '/var/www/html/uploads/products/' . $newFileName; // Đổi đường dẫn đích[cite: 3]

                if (move_uploaded_file(
                    $file['tmp_name'],
                    $destination
                )) {
                    echo '<div class="alert alert-success mt-3">';
                    echo 'Upload file thành công với tên mới: <strong>' . htmlspecialchars($newFileName) . '</strong>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-danger mt-3">';
                    echo 'Không thể lưu file.';
                    echo '</div>';
                }
            }
        ?>
    <?php endif; ?>

</div>

<?php
require_once '/var/www/src/includes/footer.php';
?>