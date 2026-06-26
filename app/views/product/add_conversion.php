<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $baseProducts Khai báo ẩn để mách cho VS Code biết biến này tồn tại */
?>
<style>
    .sapo-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-cancel {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 8px 16px;
        border-radius: 4px;
        color: #212b36;
        text-decoration: none;
        font-weight: 500;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        margin-left: 10px;
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
        border-bottom: 1px solid #f4f6f8;
        padding-bottom: 10px;
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
        box-sizing: border-box;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }
</style>

<form action="index.php?action=add_conversion" method="POST">
    <div class="sapo-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_list" style="color:#637381; text-decoration:none; margin-right: 10px;">←</a> Thêm phiên bản quy đổi</h2>
        <div>
            <a href="index.php?action=product_list" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-save">Thêm phiên bản</button>
        </div>
    </div>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin quy đổi</div>

                <div class="form-group">
                    <label>Chọn phiên bản gốc (Sản phẩm mẹ) <span style="color:red;">*</span></label>
                    <select name="parent_id" id="parent_id" class="form-control" required onchange="calculatePrice()">
                        <option value="">-- Tìm kiếm tên hoặc mã SKU sản phẩm gốc --</option>
                        <?php foreach ($baseProducts as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['base_price']; ?>" data-unit="<?php echo $p['unit']; ?>">
                                <?php echo htmlspecialchars($p['product_name']); ?> (ĐVT: <?php echo htmlspecialchars($p['unit'] ?? 'Chưa rõ'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row-flex">
                    <div class="form-group">
                        <label>Đơn vị quy đổi <span style="color:red;">*</span></label>
                        <input type="text" name="unit" class="form-control" placeholder="VD: Lốc, Thùng, Hộp..." required>
                    </div>
                    <div class="form-group">
                        <label>Số lượng quy đổi <span style="color:red;">*</span></label>
                        <input type="number" name="conversion_qty" id="conversion_qty" class="form-control" placeholder="VD: 6, 12, 24" min="2" required oninput="calculatePrice()">
                        <span style="font-size: 12px; color: #637381; margin-top: 5px; display: block;">Lưu ý: Số lượng phải là số nguyên > 1</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div class="sapo-card-title">Thông tin bán hàng</div>

                <div class="form-group">
                    <label>Mã SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="VD: THUNG-IP15">
                </div>

                <div class="form-group">
                    <label>Mã vạch / Barcode</label>
                    <input type="text" name="barcode" class="form-control" placeholder="Quét mã vạch (nếu có)">
                </div>

                <div class="form-group">
                    <label>Giá bán gợi ý (₫) <span style="color:red;">*</span></label>
                    <input type="number" name="base_price" id="base_price" class="form-control" value="0" required>
                    <span style="font-size: 12px; color: #637381; margin-top: 5px; display: block;">Hệ thống gợi ý: Giá SP gốc x Số lượng quy đổi</span>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Phép thuật: Tự động tính giá bán gợi ý giống y hệt tính năng của Sapo
    function calculatePrice() {
        let selectBox = document.getElementById("parent_id");
        let qtyInput = document.getElementById("conversion_qty").value;
        let priceInput = document.getElementById("base_price");

        if (selectBox.selectedIndex > 0 && qtyInput >= 1) {
            let basePrice = selectBox.options[selectBox.selectedIndex].getAttribute('data-price');
            // Tính giá = Giá sản phẩm gốc * Số lượng quy đổi
            let suggestedPrice = parseFloat(basePrice) * parseFloat(qtyInput);
            priceInput.value = suggestedPrice;
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
