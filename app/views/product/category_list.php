<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $categories */ ?>
<style>
    .akc-filter-bar {
        display: flex;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
    }

    .akc-filter-bar input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .akc-filter-bar select,
    .akc-filter-bar button {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .akc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .akc-table th,
    .akc-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
        font-size: 14px;
        vertical-align: middle;
    }

    .akc-table th {
        color: #637381;
        font-weight: 500;
        background: #fafbfc;
        border-bottom: 1px solid #dfe3e8;
    }

    .col-cb {
        width: 40px;
        text-align: center !important;
    }

    .akc-table input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0088ff;
    }

    .badge-type {
        background: #f4f6f8;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        color: #637381;
        border: 1px solid #c4cdd5;
        margin-left: 10px;
    }

    .badge-auto {
        background: #e6f7ff;
        color: #0088ff;
        border-color: #91d5ff;
    }

    .akc-dropdown {
        position: relative;
        display: inline-block;
    }

    .akc-dropdown-content {
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

    .akc-dropdown-content::before {
        content: "";
        position: absolute;
        top: -10px;
        left: 0;
        width: 100%;
        height: 10px;
        background: transparent;
    }

    .akc-dropdown:hover .akc-dropdown-content {
        display: block;
    }

    .akc-dropdown-content a {
        color: #212b36;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
        font-weight: 400;
        font-size: 14px;
        cursor: pointer;
    }

    .akc-dropdown-content a:hover {
        background-color: #f4f6f8;
        color: #0088ff;
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
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh mục sản phẩm</h2>
    <a href="index.php?action=add_category" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm danh mục</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Thao tác danh mục thành công!</div><?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; overflow: visible; min-height: 400px;">

    <form action="index.php" method="GET" class="akc-filter-bar">
        <input type="hidden" name="action" value="product_category">

        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm kiếm theo tên danh mục..." style="padding-left: 35px; width: 100%;">
        </div>

        <select name="type" onchange="this.form.submit()">
            <option value="">-- Loại danh mục --</option>
            <option value="manual" <?php echo (($_GET['type'] ?? '') == 'manual') ? 'selected' : ''; ?>>Danh mục Thủ công</option>
            <option value="auto" <?php echo (($_GET['type'] ?? '') == 'auto') ? 'selected' : ''; ?>>Danh mục Tự động</option>
        </select>

        <select>
            <option>Kênh bán hàng ▼</option>
        </select>
        <button type="submit" style="background: #e6f7ff; color: #0088ff; border-color: #91d5ff;">Lọc kết quả</button>
        <?php if (isset($_GET['search']) || isset($_GET['type'])): ?>
            <a href="index.php?action=product_category" style="text-decoration:none; padding: 8px 12px; color: #ff4d4f; border: 1px solid #ffa39e; border-radius: 4px;">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($categories)): ?>
        <table class="akc-table">
            <thead>
                <tr id="normal-header">
                    <th class="col-cb"><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th>Danh mục</th>
                    <th style="width: 150px; text-align: center;">Số lượng SP</th>
                    <th>Điều kiện áp dụng</th>
                </tr>

                <tr id="action-header" style="display: none; background: #e6f7ff; border-top: 1px solid #91d5ff; border-bottom: 1px solid #91d5ff;">
                    <th class="col-cb"><input type="checkbox" checked onclick="toggleAll(this)"></th>
                    <th colspan="3" style="color: #212b36; font-weight: normal; overflow: visible;">
                        Đã chọn <strong id="selected-count">1</strong> danh mục

                        <div class="akc-dropdown" style="margin-left: 20px;">
                            <button class="action-btn">Chọn thao tác ▼</button>
                            <div class="akc-dropdown-content">
                                <a>🌐 Hiển thị trên kênh</a>
                                <a>🚫 Ẩn trên kênh</a>
                                <div style="height: 1px; background: #dfe3e8; margin: 5px 0;"></div>
                                <a href="#" id="btn-delete" onclick="return confirm('Xóa danh mục sẽ không xóa sản phẩm bên trong. Bạn có chắc chắn muốn xóa?');" style="color: #ff4d4f;">🗑️ Xóa danh mục</a>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $row): ?>
                    <tr class="product-row">
                        <td class="col-cb"><input type="checkbox" class="row-checkbox" value="<?php echo $row['id']; ?>" onclick="toggleRow(this)"></td>

                        <td style="font-weight: 500;">
                            <a href="index.php?action=edit_category&id=<?php echo $row['id']; ?>" style="color: #0088ff; text-decoration: none; font-size: 15px;">
                                <?php echo htmlspecialchars($row['category_name']); ?>
                            </a>
                            <?php if (($row['selection_type'] ?? '') == 'auto'): ?>
                                <span class="badge-type badge-auto">⚡ Tự động</span>
                            <?php else: ?>
                                <span class="badge-type">Thủ công</span>
                            <?php endif; ?>
                        </td>

                        <td style="text-align: center; font-weight: bold; color: <?php echo $row['product_count'] > 0 ? '#108043' : '#212b36'; ?>;">
                            <?php echo $row['product_count']; ?>
                        </td>

                        <td style="color: #637381; font-size: 13px;">
                            <?php
                            if (($row['selection_type'] ?? '') == 'auto') {
                                echo "Sản phẩm thỏa mãn " . (($row['match_type'] ?? 'all') == 'all' ? 'Tất cả điều kiện' : 'Một số điều kiện');
                            } else {
                                echo "Thêm thủ công từng sản phẩm";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            <span>Hiển thị 1 - <?php echo count($categories); ?> danh mục</span>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 80px 20px;">
            <div style="font-size: 80px; margin-bottom: 20px;">📂</div>
            <h3 style="font-size: 20px; color: #212b36; font-weight: bold;">Chưa có danh mục sản phẩm nào</h3>
            <p style="color: #637381; margin-bottom: 25px;">Tạo danh mục để phân loại sản phẩm, giúp khách hàng dễ dàng tìm kiếm khi mua sắm.</p>
            <a href="index.php?action=add_category" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm danh mục</a>
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
            document.getElementById('btn-delete').href = 'index.php?action=delete_category&id=' + checked[0].value;
        } else {
            normalHeader.style.display = 'table-row';
            actionHeader.style.display = 'none';
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
