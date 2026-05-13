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

    .sapo-table th,
    .sapo-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
    }

    .sapo-table th {
        color: #637381;
        font-weight: 500;
        font-size: 14px;
    }

    .sapo-table input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0088ff;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách sản phẩm</h2>
    <div>
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; margin-right: 10px; cursor: pointer;">↑ Xuất file</button>
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; margin-right: 10px; cursor: pointer;">↓ Nhập file</button>
        <a href="index.php?action=add_product" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm sản phẩm ▼</a>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; overflow: hidden; min-height: 400px;">
    <div style="padding: 10px 20px; border-bottom: 1px solid #dfe3e8; color: #0088ff; font-weight: 500; font-size: 14px;">Tất cả</div>
    <div class="sapo-filter-bar">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" placeholder="Tìm kiếm theo mã sản phẩm, tên sản phẩm, barcode" style="padding-left: 35px; width: 100%;">
        </div>
        <select>
            <option>Kênh bán hàng ▼</option>
        </select>
        <select>
            <option>Loại sản phẩm ▼</option>
        </select>
        <select>
            <option>Tag ▼</option>
        </select>
        <button>Y Bộ lọc khác</button>
        <button style="background: #fafbfc; color: #c4cdd5;">Lưu bộ lọc</button>
    </div>

    <?php if (!empty($products)): ?>
        <table class="sapo-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr id="normal-header">
                    <th style="width: 40px;"><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th>Sản phẩm</th>
                    <th>Có thể bán</th>
                    <th>Loại</th>
                    <th>Nhãn hiệu</th>
                    <th>Ngày khởi tạo</th>
                </tr>
                <tr id="action-header" style="display: none; background: #e6f7ff; border-top: 1px solid #91d5ff; border-bottom: 1px solid #91d5ff;">
                    <th style="width: 40px;"><input type="checkbox" checked onclick="toggleAll(this)"></th>
                    <th colspan="5" style="color: #212b36;">
                        Đã chọn <strong id="selected-count">1</strong> sản phẩm trên trang này
                        <a href="#" id="btn-edit" style="margin-left: 20px; color: #0088ff; text-decoration: none;">Sửa sản phẩm</a>
                        <a href="#" id="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" style="margin-left: 15px; color: #ff4d4f; text-decoration: none;">Xóa sản phẩm</a>
                        <span style="margin-left: 15px; cursor: pointer;">Thao tác khác ▼</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $row): ?>
                    <tr class="product-row">
                        <td><input type="checkbox" class="row-checkbox" value="<?php echo $row['id']; ?>" onclick="toggleRow(this)"></td>
                        <td style="display: flex; align-items: center; gap: 10px;">
                            <div style="width:40px; height:40px; background:#fafbfc; border: 1px solid #dfe3e8; border-radius:4px; text-align:center; line-height:40px;">🖼️</div>
                            <a href="index.php?action=edit_product&id=<?php echo $row['id']; ?>" style="color: #0088ff; font-weight: 500; text-decoration: none;"><?php echo htmlspecialchars($row['product_name']); ?></a>
                        </td>
                        <td><?php echo isset($row['ton_kho']) ? $row['ton_kho'] : '0'; ?></td>
                        <td><?php echo !empty($row['category']) ? htmlspecialchars($row['category']) : '---'; ?></td>
                        <td><?php echo !empty($row['brand']) ? htmlspecialchars($row['brand']) : '---'; ?></td>
                        <td>11/05/2026</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: #637381; font-size: 14px;">
            <span>Từ 1 đến <?php echo count($products); ?> trên tổng <?php echo count($products); ?></span>
            <div style="display: flex; gap: 15px; align-items: center;">
                <span>Hiển thị <select style="padding: 4px; border: 1px solid #c4cdd5; border-radius: 4px;">
                        <option>20</option>
                    </select> Kết quả</span>
                <div style="display: flex; gap: 5px;">
                    <button style="border: none; background: transparent; cursor: pointer; color: #c4cdd5;">&lt;</button>
                    <button style="border: none; background: #0088ff; color: #fff; width: 24px; height: 24px; border-radius: 50%;">1</button>
                    <button style="border: none; background: transparent; cursor: pointer;">&gt;</button>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div style="position: relative; padding-bottom: 50px;">
            <div style="opacity: 0.3; pointer-events: none; user-select: none;">
                <table class="sapo-table" style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <?php for ($i = 0; $i < 4; $i++): ?>
                            <tr>
                                <td style="padding: 15px;">
                                    <div style="width: 20px; height: 20px; background: #f4f6f8; border-radius: 4px;"></div>
                                </td>
                                <td>
                                    <div style="width: 200px; height: 16px; background: #f4f6f8; border-radius: 8px;"></div>
                                </td>
                                <td>
                                    <div style="width: 80px; height: 16px; background: #f4f6f8; border-radius: 8px;"></div>
                                </td>
                                <td>
                                    <div style="width: 100px; height: 16px; background: #f4f6f8; border-radius: 8px;"></div>
                                </td>
                                <td>
                                    <div style="width: 120px; height: 16px; background: #f4f6f8; border-radius: 8px;"></div>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div style="position: absolute; top: 40%; left: 50%; transform: translate(-50%, -30%); text-align: center; width: 100%; z-index: 10;">
                <div style="font-size: 60px; margin-bottom: 15px;">🛍️</div>
                <h3 style="font-size: 18px; color: #212b36; margin-bottom: 10px; font-weight: bold;">Cửa hàng của bạn chưa có sản phẩm nào</h3>
                <p style="color: #637381; font-size: 14px; margin-bottom: 20px;">Thêm mới hoặc nhập danh sách sản phẩm của bạn.<br>Bạn có thể tải file mẫu <a href="#" style="color:#0088ff; text-decoration:none;">tại đây</a></p>
                <div style="display: flex; justify-content: center; gap: 10px;">
                    <button style="background: #fff; border: 1px solid #0088ff; color: #0088ff; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 14px;">📥 Nhập file sản phẩm</button>
                    <a href="index.php?action=add_product" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500; font-size: 14px;">+ Thêm sản phẩm</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleRow(checkbox) {
        let row = checkbox.closest('tr');
        row.style.background = checkbox.checked ? '#f4f6f8' : 'transparent';
        updateActionBar();
    }

    function toggleAll(masterCheckbox) {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => {
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

            let firstId = checked[0].value;
            document.getElementById('btn-edit').href = 'index.php?action=edit_product&id=' + firstId;
            document.getElementById('btn-delete').href = 'index.php?action=delete_product&id=' + firstId;
        } else {
            normalHeader.style.display = 'table-row';
            actionHeader.style.display = 'none';
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
