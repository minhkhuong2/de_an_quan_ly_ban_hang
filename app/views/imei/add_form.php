<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2 style="margin-bottom: 20px;">Nhập kho thiết bị (Mã định danh)</h2>

<div class="card" style="max-width: 600px;">
    <?php if (!empty($message)) echo $message; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Sản phẩm (Tên máy):</label>
            <select name="product_id" required>
                <option value="">-- Chọn điện thoại --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo $p['product_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Mã IMEI:</label>
            <input type="text" name="imei_code" placeholder="Nhập mã IMEI (Có thể bỏ trống)">
        </div>

        <div class="form-group">
            <label>Số Serial (Bắt buộc):</label>
            <input type="text" name="serial_number" required placeholder="Nhập mã Serial duy nhất">
        </div>

        <button type="submit" class="btn btn-success" style="margin-top: 10px;">Lưu vào kho</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
