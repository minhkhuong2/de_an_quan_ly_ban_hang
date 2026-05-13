<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .sapo-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #212b36;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .btn-cancel {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 8px 16px;
        border-radius: 4px;
        color: #212b36;
        cursor: pointer;
        text-decoration: none;
        margin-right: 10px;
    }

    .btn-draft {
        background: #fff;
        border: 1px solid #0088ff;
        color: #0088ff;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    .btn-apply {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
    }

    .alert-info {
        background: #e6f7ff;
        border-left: 4px solid #1890ff;
        padding: 10px 15px;
        color: #212b36;
        font-size: 14px;
        margin-top: 15px;
    }
</style>

<form action="index.php?action=add_price" method="POST">
    <div class="sapo-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_price" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Tạo bảng giá mới</h2>
        <div>
            <a href="index.php?action=product_price" class="btn-cancel">Hủy</a>
            <button type="submit" name="btn_draft" class="btn-draft">Lưu nháp</button>
            <button type="submit" name="btn_apply" class="btn-apply">Lưu & Áp dụng</button>
        </div>
    </div>

    <div class="sapo-card" style="max-width: 800px; margin: 0 auto;">
        <h3 style="font-size: 16px; margin-bottom: 20px; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px;">1. Thông tin bảng giá</h3>

        <div class="form-group">
            <label>Tên bảng giá <span style="color:red;">*</span></label>
            <input type="text" name="price_name" class="form-control" placeholder="Ví dụ: Bảng giá khách VIP, Bảng giá sỉ..." required>
        </div>

        <div class="form-group">
            <label>Điều chỉnh giá <span style="color:red;">*</span></label>
            <div style="display: flex; gap: 15px;">
                <select name="adjust_type" class="form-control" style="width: 200px;">
                    <option value="Tăng giá">Tăng giá (%)</option>
                    <option value="Giảm giá">Giảm giá (%)</option>
                </select>
                <input type="number" name="adjust_value" class="form-control" placeholder="Nhập số % (Ví dụ: 5)" required>
            </div>
            <div class="alert-info">
                <strong>Lưu ý:</strong> Giá chỉ nhập số nguyên. Ví dụ giá bán là 100.000đ, chọn Giảm giá 5% thì giá sẽ là 95.000đ.
            </div>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 20px;">
            <input type="checkbox" name="auto_add" id="auto_add" style="width: 16px; height: 16px; cursor: pointer;">
            <label for="auto_add" style="margin: 0; cursor: pointer;">Tự động thêm sản phẩm mới vào bảng giá này</label>
        </div>

        <h3 style="font-size: 16px; margin-bottom: 20px; border-bottom: 1px solid #dfe3e8; padding-bottom: 10px; margin-top: 30px;">2. Chọn chi nhánh áp dụng</h3>
        <div class="form-group">
            <label>Chi nhánh áp dụng</label>
            <select name="branch" class="form-control">
                <option value="Tất cả chi nhánh">Tất cả chi nhánh (Quản lý chung)</option>
                <option value="Cửa hàng chính">Cửa hàng chính</option>
                <option value="Kho tổng">Kho tổng</option>
            </select>
        </div>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
