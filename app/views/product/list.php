<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .sapo-filter-bar {
        display: flex;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
    }

    .sapo-filter-bar input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .sapo-filter-bar select,
    .sapo-filter-bar button {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .sapo-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .sapo-table th,
    .sapo-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
        font-size: 14px;
        vertical-align: middle;
        word-wrap: break-word;
    }

    .sapo-table th {
        color: #637381;
        font-weight: 500;
        background: #fafbfc;
        border-bottom: 1px solid #dfe3e8;
    }

    .col-cb {
        width: 40px;
        text-align: center !important;
    }

    .col-img {
        width: 60px;
    }

    .col-name {
        width: auto;
    }

    .col-num {
        width: 100px;
        text-align: right !important;
    }

    .col-text {
        width: 130px;
    }

    .sapo-table input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0088ff;
    }

    .sapo-dropdown {
        position: relative;
        display: inline-block;
    }

    .sapo-dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 180px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
        border-radius: 4px;
        border: 1px solid #dfe3e8;
        top: 100%;
        left: 0;
        margin-top: 5px;
    }

    .sapo-dropdown-content::before {
        content: "";
        position: absolute;
        top: -10px;
        left: 0;
        width: 100%;
        height: 10px;
        background: transparent;
    }

    .sapo-dropdown:hover .sapo-dropdown-content {
        display: block;
    }

    .sapo-dropdown-content a {
        color: #212b36;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
        font-weight: 400;
        font-size: 14px;
        cursor: pointer;
    }

    .sapo-dropdown-content a:hover {
        background-color: #f4f6f8;
        color: #0088ff;
    }

    .dropdown-btn {
        background: #0088ff;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .action-btn {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        color: #212b36;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách sản phẩm</h2>
    <div style="display: flex; gap: 10px; align-items: center;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">↑ Xuất file</button>
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">↓ Nhập file</button>

        <div class="sapo-dropdown">
            <button class="dropdown-btn">+ Thêm sản phẩm ▼</button>
            <div class="sapo-dropdown-content" style="right: 0; left: auto;">
                <a href="index.php?action=add_product">Thêm sản phẩm thường</a>
                <a href="#">Sản phẩm Serial/IMEI</a>
                <a href="#">Sản phẩm Combo</a>
                <a href="#">Sản phẩm lô - HSD</a>
            </div>
        </div>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; overflow: visible; min-height: 400px;">
    <div style="padding: 10px 20px; border-bottom: 1px solid #dfe3e8; display: flex; gap: 20px; font-size: 14px;">
        <span style="color: #0088ff; font-weight: 500; border-bottom: 2px solid #0088ff; padding-bottom: 10px; margin-bottom: -11px;">Tất cả sản phẩm</span>
        <span style="color: #637381; cursor: pointer;">Đang giao dịch</span>
        <span style="color: #637381; cursor: pointer;">Ngừng giao dịch</span>
    </div>

    <div class="sapo-filter-bar">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" placeholder="Tìm kiếm theo mã sản phẩm, tên sản phẩm, barcode..." style="padding-left: 35px; width: 100%;">
        </div>
        <select>
            <option>Loại sản phẩm ▼</option>
        </select>
        <select>
            <option>Nhãn hiệu ▼</option>
        </select>
        <select>
            <option>Trạng thái ▼</option>
        </select>
        <button>Y Bộ lọc khác</button>
        <button style="color: #0088ff; font-weight: 500;">Lưu bộ lọc</button>
    </div>

    <?php if (!empty($products)): ?>
        <table class="sapo-table">
            <thead>
                <tr id="normal-header">
                    <th class="col-cb"><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th class="col-img">Ảnh</th>
                    <th class="col-name">Sản phẩm</th>
                    <th class="col-num">Có thể bán</th>
                    <th class="col-num">Tồn kho</th>
                    <th class="col-text">Danh mục</th>
                    <th class="col-text">Nhãn hiệu</th>
                    <th class="col-text">Ngày tạo</th>
                </tr>
                <tr id="action-header" style="display: none; background: #e6f7ff; border-top: 1px solid #91d5ff; border-bottom: 1px solid #91d5ff;">
                    <th class="col-cb"><input type="checkbox" checked onclick="toggleAll(this)"></th>
                    <th colspan="7" style="color: #212b36; font-weight: normal; overflow: visible;">
                        Đã chọn <strong id="selected-count">1</strong> sản phẩm trên trang này
                        <div class="sapo-dropdown" style="margin-left: 20px;">
                            <button class="action-btn">Chọn thao tác ▼</button>
                            <div class="sapo-dropdown-content">
                                <a>📦 Kiểm tra tồn kho</a>
                                <a>🖨️ In mã vạch</a>
                                <a>✅ Đang giao dịch</a>
                                <a>🚫 Ngừng giao dịch</a>
                                <div style="height: 1px; background: #dfe3e8; margin: 5px 0;"></div>
                                <a href="#" id="btn-delete" onclick="return confirm('Thao tác này sẽ xóa sản phẩm bạn đã chọn. Bạn có chắc chắn muốn xóa?');" style="color: #ff4d4f;">🗑️ Xóa sản phẩm</a>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $row): ?>
                    <tr class="product-row">
                        <td class="col-cb"><input type="checkbox" class="row-checkbox" value="<?php echo $row['id']; ?>" onclick="toggleRow(this)"></td>

                        <td class="col-img">
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" style="width:40px; height:40px; border-radius:4px; object-fit:cover; border:1px solid #dfe3e8;">
                            <?php else: ?>
                                <div style="width:40px; height:40px; background:#f4f6f8; border: 1px solid #dfe3e8; border-radius:4px; text-align:center; line-height:40px; font-size: 20px;">📱</div>
                            <?php endif; ?>
                        </td>

                        <td class="col-name">
                            <a href="index.php?action=edit_product&id=<?php echo $row['id']; ?>" style="color: #0088ff; font-weight: 500; text-decoration: none;">
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </a><br>
                            <span style="color: #637381; font-size: 12px;"><?php echo !empty($row['sku']) ? htmlspecialchars($row['sku']) : '---'; ?></span>
                        </td>

                        <td class="col-num" style="color: <?php echo (isset($row['co_the_ban']) && $row['co_the_ban'] > 0) ? '#108043' : '#212b36'; ?>; font-weight: 500;">
                            <?php echo isset($row['co_the_ban']) ? $row['co_the_ban'] : '0'; ?>
                        </td>

                        <td class="col-num" style="color: <?php echo (isset($row['ton_kho']) && $row['ton_kho'] > 0) ? '#108043' : '#212b36'; ?>; font-weight: 500;">
                            <?php echo isset($row['ton_kho']) ? $row['ton_kho'] : '0'; ?>
                        </td>

                        <td class="col-text"><?php echo !empty($row['category']) ? htmlspecialchars($row['category']) : '---'; ?></td>
                        <td class="col-text"><?php echo !empty($row['brand']) ? htmlspecialchars($row['brand']) : '---'; ?></td>
                        <td class="col-text" style="color: #637381;"><?php echo date('d/m/Y', strtotime($row['created_at'] ?? date('Y-m-d'))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            <span>Hiển thị kết quả từ 1 - <?php echo count($products); ?> trên tổng <?php echo count($products); ?></span>
            <div style="display: flex; gap: 15px; align-items: center;">
                <span>Hiển thị <select style="padding: 4px; border: 1px solid #c4cdd5; border-radius: 4px;">
                        <option>20</option>
                    </select> Kết quả</span>
            </div>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 80px 20px;">
            <div style="font-size: 80px; margin-bottom: 20px;">🛍️</div>
            <h3 style="font-size: 20px; color: #212b36; font-weight: bold;">Cửa hàng của bạn chưa có sản phẩm nào</h3>
            <p style="color: #637381; margin-bottom: 25px;">Thêm mới hoặc nhập danh sách sản phẩm của bạn.</p>
            <div style="display: flex; justify-content: center; gap: 15px;">
                <button style="background: #fff; border: 1px solid #0088ff; color: #0088ff; padding: 8px 16px; border-radius: 4px; font-weight: 500; cursor: pointer;">📥 Nhập file sản phẩm</button>
                <a href="index.php?action=add_product" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm sản phẩm</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleRow(checkbox) {
        checkbox.closest('tr').style.background = checkbox.checked ? '#f4f6f8' : 'transparent';
        updateActionBar();
    }

    function toggleAll(masterCheckbox) {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = masterCheckbox.checked;
            toggleRow(cb);
        });
    }

    function updateActionBar() {
        let checked = document.querySelectorAll('.row-checkbox:checked');
        let normalHeader = document.getElementById('normal-header');
        let actionHeader = document.getElementById('action-header');

        if (checked.length > 0) {
            normalHeader.style.display = 'none';
            actionHeader.style.display = 'table-row';
            document.getElementById('selected-count').innerText = checked.length;
            document.getElementById('btn-delete').href = 'index.php?action=delete_product&id=' + checked[0].value;
        } else {
            normalHeader.style.display = 'table-row';
            actionHeader.style.display = 'none';
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
