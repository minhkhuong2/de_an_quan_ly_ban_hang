<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $supplier */ ?>

<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #212b36;
        padding-bottom: 12px;
        border-bottom: 1px solid #dfe3e8;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: #0088ff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 136, 255, 0.2);
    }

    .row-2-cols {
        display: flex;
        gap: 20px;
    }

    .row-2-cols>div {
        flex: 1;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 22px; font-weight: bold; color: #212b36;">
        <a href="index.php?action=supplier_list" style="text-decoration:none; color:#637381; margin-right: 10px;">←</a>
        Cập nhật nhà cung cấp: <span style="color: #0088ff;"><?php echo htmlspecialchars($supplier['supplier_code']); ?></span>
    </h2>
</div>

<form action="index.php?action=edit_supplier&id=<?php echo $supplier['id']; ?>" method="POST">
    <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start;">

        <div style="flex: 1 1 65%; min-width: 600px;">
            <div class="sapo-card">
                <div class="sapo-card-title">📝 Thông tin chung</div>
                <div class="form-group">
                    <label>Tên nhà cung cấp <span style="color:red;">*</span></label>
                    <input type="text" name="supplier_name" class="form-control" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required>
                </div>

                <div class="row-2-cols">
                    <div class="form-group">
                        <label>Mã nhà cung cấp</label>
                        <input type="text" name="supplier_code" class="form-control" value="<?php echo htmlspecialchars($supplier['supplier_code']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($supplier['phone']); ?>">
                    </div>
                </div>

                <div class="row-2-cols">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($supplier['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Mã số thuế</label>
                        <input type="text" name="tax_code" class="form-control" value="<?php echo htmlspecialchars($supplier['tax_code']); ?>">
                    </div>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">📍 Thông tin địa chỉ</div>
                <div class="form-group">
                    <label>Địa chỉ cụ thể</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($supplier['address']); ?></textarea>
                </div>
            </div>
        </div>

        <div style="flex: 1 1 30%; min-width: 300px;">
            <div class="sapo-card">
                <div class="sapo-card-title">⚙️ Thông tin khác</div>
                <div class="form-group">
                    <label>Nhóm nhà cung cấp</label>
                    <select class="form-control">
                        <option>Bán buôn</option>
                        <option>Đại lý</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhân viên phụ trách</label>
                    <input type="text" name="employee" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? 'Admin'); ?>" readonly style="background:#f4f6f8; cursor:not-allowed; color:#0088ff; font-weight:bold;">
                </div>

                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea class="form-control" rows="4"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid #dfe3e8; padding-top: 20px; padding-bottom: 40px;">
        <button type="button" style="padding: 10px 20px; border-radius: 4px; border: 1px solid #c4cdd5; background: #fff; cursor: pointer; font-weight:500;" onclick="window.location.href='index.php?action=supplier_list'">Hủy bỏ</button>
        <button type="submit" style="padding: 10px 20px; border-radius: 4px; border: none; background: #0088ff; color: #fff; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,136,255,0.2);">💾 Lưu thay đổi</button>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
