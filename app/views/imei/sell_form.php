<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2 style="margin-bottom: 20px;">Xác nhận Xuất Kho (Bán Hàng)</h2>

<div class="card" style="max-width: 500px;">
    <p style="margin-bottom: 15px; color: #666;">Vui lòng nhập thông tin khách hàng để lưu lịch sử bảo hành điện tử.</p>

    <form action="index.php?action=sell" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

        <div class="form-group">
            <label>Tên khách hàng:</label>
            <input type="text" name="customer_name" required placeholder="VD: Nguyễn Văn A">
        </div>

        <div class="form-group">
            <label>Số điện thoại:</label>
            <input type="text" name="customer_phone" required placeholder="VD: 0987654321">
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-warning">Xác nhận Bán & Lưu Hóa Đơn</button>
            <a href="index.php?action=list" style="margin-left: 15px; color: #666; text-decoration: none;">Hủy bỏ</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
