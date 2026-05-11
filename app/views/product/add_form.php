<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2 style="margin-bottom: 20px;">Thêm Dòng Điện Thoại Mới (Danh mục gốc)</h2>

<div class="card" style="max-width: 600px;">
    <?php if (!empty($message)) echo $message; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Tên điện thoại:</label>
            <input type="text" name="product_name" required placeholder="VD: Samsung Galaxy S24 Ultra">
        </div>

        <div class="form-group">
            <label>Hãng sản xuất:</label>
            <input type="text" name="brand" required placeholder="VD: Samsung, Apple, Xiaomi...">
        </div>

        <div class="form-group">
            <label>Giá bán dự kiến (VNĐ):</label>
            <input type="number" name="base_price" required placeholder="VD: 30000000">
        </div>

        <button type="submit" class="btn btn-success" style="margin-top: 10px;">Lưu Sản Phẩm</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
