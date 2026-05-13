<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .sapo-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .sapo-header-bar h2 {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sapo-btn-group button {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid transparent;
        font-size: 14px;
    }

    .btn-cancel {
        background: #fff;
        border-color: #c4cdd5 !important;
        color: #212b36;
        margin-right: 10px;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
    }

    .sapo-grid {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .sapo-col-left {
        flex: 0 0 68%;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sapo-col-right {
        flex: 0 0 calc(32% - 20px);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .sapo-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
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
        color: #212b36;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .upload-box {
        border: 2px dashed #c4cdd5;
        border-radius: 6px;
        padding: 30px;
        text-align: center;
        color: #637381;
        background: #fafbfc;
    }

    .link-blue {
        color: #0088ff;
        text-decoration: none;
        font-size: 14px;
    }
</style>

<form action="" method="POST">
    <div class="sapo-header-bar">
        <h2>
            <a href="index.php?action=product_list" style="color:#637381; text-decoration:none;">←</a>
            Chỉnh sửa sản phẩm: <?php echo htmlspecialchars($product['product_name'] ?? ''); ?>
        </h2>
        <div class="sapo-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Hủy</button>
            <button type="submit" class="btn-save">Lưu thay đổi</button>
        </div>
    </div>

    <?php if (!empty($message)) echo $message; ?>
    <?php if (isset($_GET['success'])): ?>
        <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">
            ✅ Sản phẩm "<strong><?php echo htmlspecialchars($product['product_name'] ?? ''); ?></strong>" đã được tạo thành công!
        </div>
    <?php endif; ?>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tự sản phẩm</div>
                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>" required>
                </div>
                <div class="row-flex">
                    <div class="form-group">
                        <label>Mã SKU</label>
                        <input type="text" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Mã vạch/ Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($product['barcode'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Đơn vị tính</label>
                    <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($product['unit'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin giá</div>
                <div class="row-flex">
                    <div class="form-group">
                        <label>Giá bán</label>
                        <input type="number" name="base_price" class="form-control" value="<?php echo htmlspecialchars($product['base_price'] ?? 0); ?>">
                    </div>
                    <div class="form-group">
                        <label>Giá so sánh</label>
                        <input type="number" name="compare_price" class="form-control" value="<?php echo htmlspecialchars($product['compare_price'] ?? 0); ?>">
                    </div>
                </div>
                <div class="form-group" style="width: 48%;">
                    <label>Giá vốn</label>
                    <input type="number" name="cost_price" class="form-control" value="<?php echo htmlspecialchars($product['cost_price'] ?? 0); ?>">
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="tax" name="apply_tax" <?php echo (isset($product['apply_tax']) && $product['apply_tax'] == 1) ? 'checked' : ''; ?>>
                    <label for="tax" style="margin:0;">Áp dụng thuế</label>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin kho</div>
                <div class="checkbox-group">
                    <input type="checkbox" checked disabled>
                    <label style="margin:0; font-weight:bold; color: #0088ff;">Quản lý kho chi tiết theo mã định danh (IMEI/Serial)</label>
                </div>
                <table style="width: 100%; border-collapse: collapse; margin-top:15px;">
                    <tr style="background: #fafbfc; border: 1px solid #dfe3e8;">
                        <th style="padding: 10px; text-align: left;">Kho lưu trữ</th>
                        <th style="padding: 10px; text-align: left;">Tồn kho</th>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dfe3e8;">Cửa hàng chính</td>
                        <td style="padding: 10px; border: 1px solid #dfe3e8;"><strong>0</strong> (Tính theo IMEI)</td>
                    </tr>
                </table>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Tối ưu SEO</div>
                <p style="font-size: 14px; color: #637381;">Thiết lập các thẻ tiêu đề và mô tả để sản phẩm dễ dàng được tìm thấy trên Google.</p>
                <a href="#" class="link-blue">Tùy chỉnh SEO</a>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">Ảnh sản phẩm</div>
                <div class="upload-box">
                    <div style="font-size: 24px;">+</div>
                    <a href="#" class="link-blue">Thêm ảnh từ thiết bị</a>
                </div>
            </div>

            <div class="sapo-card">
                <div class="sapo-card-title">Phân loại</div>
                <div class="form-group">
                    <label>Danh mục</label>
                    <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Nhãn hiệu</label>
                    <input type="text" name="brand" class="form-control" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Tag</label>
                    <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($product['tags'] ?? ''); ?>">
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
