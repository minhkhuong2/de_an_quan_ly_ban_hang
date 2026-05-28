<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $products */
/** @var array $categories */ ?>
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
        min-width: 200px;
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

    .sapo-dropdown-content a,
    .sapo-dropdown-content label {
        color: #212b36;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
        font-weight: 400;
        font-size: 14px;
        cursor: pointer;
    }

    .sapo-dropdown-content a:hover,
    .sapo-dropdown-content label:hover {
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

    .badge-type {
        background: #f4f6f8;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        color: #637381;
        border: 1px solid #c4cdd5;
        margin-top: 4px;
        display: inline-block;
    }

    /* CSS cho form Sửa tồn kho nhanh */
    .stock-form-input {
        width: 100%;
        padding: 6px;
        box-sizing: border-box;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
    }

    .stock-form-input:focus {
        border-color: #0088ff;
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
                <a href="index.php?action=add_conversion">Thêm phiên bản quy đổi</a>
                <a href="index.php?action=add_combo">Thêm sản phẩm Combo</a>
                <div style="height: 1px; background: #dfe3e8; margin: 5px 0;"></div>
                <a href="#">Sản phẩm Lô - HSD</a>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Thao tác thành công!</div><?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; overflow: visible; min-height: 400px;">

    <form action="index.php" method="GET" class="sapo-filter-bar">
        <input type="hidden" name="action" value="product_list">

        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm kiếm theo mã SKU, tên sản phẩm, barcode..." style="padding-left: 35px; width: 100%;">
        </div>

        <select name="type" onchange="this.form.submit()">
            <option value="">-- Hình thức SP --</option>
            <option value="Thường" <?php echo (($_GET['type'] ?? '') == 'Thường') ? 'selected' : ''; ?>>Sản phẩm thường</option>
            <option value="Combo" <?php echo (($_GET['type'] ?? '') == 'Combo') ? 'selected' : ''; ?>>Sản phẩm Combo</option>
            <option value="Quy đổi" <?php echo (($_GET['type'] ?? '') == 'Quy đổi') ? 'selected' : ''; ?>>Sản phẩm quy đổi</option>
        </select>

        <select name="category" onchange="this.form.submit()">
            <option value="">-- Danh mục --</option>
            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category_name']); ?>" <?php echo (($_GET['category'] ?? '') == $cat['category_name']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>

        <button type="submit" style="background: #e6f7ff; color: #0088ff; border-color: #91d5ff;">Lọc kết quả</button>
        <?php if (!empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['category'])): ?>
            <a href="index.php?action=product_list" style="text-decoration:none; padding: 8px 12px; color: #ff4d4f; border: 1px solid #ffa39e; border-radius: 4px;">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

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
                        Đã chọn <strong id="selected-count">1</strong> sản phẩm

                        <div class="sapo-dropdown" style="margin-left: 20px;">
                            <button class="action-btn">Chọn thao tác ▼</button>
                            <div class="sapo-dropdown-content">
                                <a>✏️ Sửa sản phẩm hàng loạt</a>
                                <div style="height: 1px; background: #dfe3e8; margin: 5px 0;"></div>
                                <a href="#" id="btn-delete" onclick="return confirm('Sản phẩm đã xóa không thể khôi phục. Bạn có chắc chắn muốn xóa?');" style="color: #ff4d4f;">🗑️ Xóa sản phẩm</a>
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
                            <a href="index.php?action=edit_product&id=<?php echo $row['id']; ?>" style="color: #0088ff; font-weight: 500; text-decoration: none; font-size: 15px;">
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </a><br>
                            <span style="color: #637381; font-size: 12px;"><?php echo !empty($row['sku']) ? htmlspecialchars($row['sku']) : '---'; ?></span>

                            <?php if (!empty($row['parent_id'])): ?>
                                <br><span class="badge-type">📦 Sản phẩm quy đổi</span>
                            <?php elseif (isset($row['product_type']) && $row['product_type'] == 'Combo'): ?>
                                <br><span class="badge-type">🎁 Sản phẩm Combo</span>
                            <?php endif; ?>
                        </td>

                        <td class="col-num" style="color: <?php echo (isset($row['co_the_ban']) && $row['co_the_ban'] > 0) ? '#108043' : '#212b36'; ?>; font-weight: 500;">
                            <?php echo isset($row['co_the_ban']) ? $row['co_the_ban'] : '0'; ?>
                        </td>

                        <td class="col-num" style="position: relative; color: <?php echo (isset($row['ton_kho']) && $row['ton_kho'] > 0) ? '#108043' : '#212b36'; ?>; font-weight: 500;">

                            <div id="stock-view-<?php echo $row['id']; ?>" style="display: flex; justify-content: flex-end; align-items: center; gap: 8px;">
                                <span><?php echo isset($row['ton_kho']) ? $row['ton_kho'] : '0'; ?></span>
                                <?php if (empty($row['parent_id']) && ($row['product_type'] ?? '') != 'Combo'): ?>
                                    <a href="javascript:void(0)" onclick="openStockPopup(<?php echo $row['id']; ?>, <?php echo $row['ton_kho'] ?? 0; ?>)" style="color: #0088ff; text-decoration: none; font-size: 14px;" title="Cập nhật tồn kho">✏️</a>
                                <?php endif; ?>
                            </div>

                            <div id="stock-popup-<?php echo $row['id']; ?>" style="display: none; position: absolute; right: 10px; top: 45px; background: #fff; border: 1px solid #dfe3e8; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 6px; padding: 15px; width: 240px; z-index: 100; text-align: left;">
                                <div style="font-weight: bold; margin-bottom: 12px; color: #212b36; font-size: 14px; border-bottom: 1px solid #f4f6f8; padding-bottom: 8px;">Chỉnh sửa tồn kho</div>
                                <form action="index.php?action=quick_update_stock" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                    <div style="margin-bottom: 12px;">
                                        <label style="font-size: 12px; color: #637381; display: block; margin-bottom: 4px; font-weight: 500;">Tồn kho mới</label>
                                        <input type="number" name="new_stock" id="new_stock_<?php echo $row['id']; ?>" class="stock-form-input" value="<?php echo $row['ton_kho'] ?? 0; ?>" oninput="calcAdj(<?php echo $row['id']; ?>, <?php echo $row['ton_kho'] ?? 0; ?>)">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="font-size: 12px; color: #637381; display: block; margin-bottom: 4px; font-weight: 500;">Điều chỉnh (+/-)</label>
                                        <input type="number" id="adj_<?php echo $row['id']; ?>" class="stock-form-input" value="0" oninput="calcNew(<?php echo $row['id']; ?>, <?php echo $row['ton_kho'] ?? 0; ?>)">
                                    </div>
                                    <div style="display: flex; gap: 8px; justify-content: flex-end; padding-top: 5px; border-top: 1px solid #f4f6f8;">
                                        <button type="button" onclick="closeStockPopup(<?php echo $row['id']; ?>)" style="background: #fff; border: 1px solid #c4cdd5; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">Hủy</button>
                                        <button type="submit" style="background: #0088ff; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">Lưu</button>
                                    </div>
                                </form>
                            </div>
                        </td>

                        <td class="col-text" style="color: #0088ff; font-weight: 500;">
                            <?php echo htmlspecialchars($row['smart_categories'] ?? '---'); ?>
                        </td>

                        <td class="col-text"><?php echo !empty($row['brand']) ? htmlspecialchars($row['brand']) : '---'; ?></td>
                        <td class="col-text" style="color: #637381;"><?php echo date('d/m/Y', strtotime($row['created_at'] ?? date('Y-m-d'))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            <span>Hiển thị 1 - <?php echo count($products); ?> trên tổng <?php echo count($products); ?> sản phẩm</span>
        </div>

    <?php else: ?>
        <?php
        $is_filtering = !empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['category']);
        ?>

        <?php if ($is_filtering): ?>
            <div style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🔍</div>
                <h3 style="font-size: 20px; color: #212b36; font-weight: bold;">Không tìm thấy sản phẩm nào</h3>
                <p style="color: #637381; margin-bottom: 25px;">Thử thay đổi từ khóa tìm kiếm hoặc xóa các bộ lọc hiện tại.</p>
                <a href="index.php?action=product_list" style="background: #fff; border: 1px solid #c4cdd5; color: #212b36; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">Xóa bộ lọc</a>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 80px; margin-bottom: 20px;">🛍️</div>
                <h3 style="font-size: 20px; color: #212b36; font-weight: bold;">Cửa hàng của bạn chưa có sản phẩm nào</h3>
                <p style="color: #637381; margin-bottom: 25px;">Thêm mới hoặc nhập danh sách sản phẩm để bắt đầu bán hàng.</p>
                <a href="index.php?action=add_product" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm sản phẩm ngay</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    // JS HIỆU ỨNG CHECKBOX & CHỌN HÀNG LOẠT
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

    // JS XỬ LÝ POPUP TỒN KHO THÔNG MINH
    let currentPopupId = null;

    function openStockPopup(id, currentStock) {
        // Đóng popup đang mở (nếu có)
        if (currentPopupId && currentPopupId !== id) {
            closeStockPopup(currentPopupId);
        }

        const popup = document.getElementById('stock-popup-' + id);
        const view = document.getElementById('stock-view-' + id);

        if (popup.style.display === 'none' || popup.style.display === '') {
            popup.style.display = 'block';
            view.style.display = 'none';
            // Đặt lại giá trị ban đầu mỗi khi mở
            document.getElementById('new_stock_' + id).value = currentStock;
            document.getElementById('adj_' + id).value = 0;
            currentPopupId = id;
        } else {
            closeStockPopup(id);
        }
    }

    function closeStockPopup(id) {
        document.getElementById('stock-popup-' + id).style.display = 'none';
        document.getElementById('stock-view-' + id).style.display = 'flex';
        if (currentPopupId === id) currentPopupId = null;
    }

    // Tự động tính Điều chỉnh khi nhập Tồn kho mới
    function calcAdj(id, originalStock) {
        let newStock = parseInt(document.getElementById('new_stock_' + id).value) || 0;
        document.getElementById('adj_' + id).value = newStock - originalStock;
    }

    // Tự động tính Tồn kho mới khi nhập Điều chỉnh (+/-)
    function calcNew(id, originalStock) {
        let adjustment = parseInt(document.getElementById('adj_' + id).value) || 0;
        document.getElementById('new_stock_' + id).value = originalStock + adjustment;
    }

    // Đóng popup khi click ra ngoài vùng popup
    document.addEventListener('click', function(event) {
        if (currentPopupId) {
            const popup = document.getElementById('stock-popup-' + currentPopupId);
            const view = document.getElementById('stock-view-' + currentPopupId);

            // Nếu click không nằm trong popup và không nằm trong nút mở popup
            if (!popup.contains(event.target) && !view.contains(event.target)) {
                closeStockPopup(currentPopupId);
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
