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

    .sapo-filter-bar button {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
    }

    .sapo-table {
        width: 100%;
        border-collapse: collapse;
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
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh mục sản phẩm</h2>
    <a href="index.php?action=add_category" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm danh mục</a>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; min-height: 400px;">
    <div style="padding: 10px 20px; border-bottom: 1px solid #dfe3e8; color: #0088ff; font-weight: 500; font-size: 14px;">Tất cả danh mục</div>
    <div class="sapo-filter-bar">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" placeholder="Tìm kiếm danh mục..." style="padding-left: 35px; width: 100%;">
        </div>
        <button>Y Bộ lọc khác</button>
    </div>

    <?php if (!empty($categories)): ?>
        <table class="sapo-table">
            <thead>
                <tr id="normal-header">
                    <th style="width: 40px;"><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th>Hình ảnh</th>
                    <th>Tên danh mục</th>
                    <th>Trạng thái</th>
                </tr>
                <tr id="action-header" style="display: none; background: #e6f7ff; border-top: 1px solid #91d5ff; border-bottom: 1px solid #91d5ff;">
                    <th style="width: 40px;"><input type="checkbox" checked onclick="toggleAll(this)"></th>
                    <th colspan="3" style="color: #212b36;">
                        Đã chọn <strong id="selected-count">1</strong> danh mục trên trang này
                        <a href="#" id="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa?');" style="margin-left: 20px; color: #ff4d4f; text-decoration: none;">Xóa danh mục</a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $row): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" value="<?php echo $row['id']; ?>" onclick="toggleRow(this)"></td>
                        <td>
                            <div style="width:40px; height:40px; background:#fafbfc; border: 1px solid #dfe3e8; border-radius:4px; text-align:center; line-height:40px;">🖼️</div>
                        </td>
                        <td><a href="index.php?action=edit_category&id=<?php echo $row['id']; ?>" style="color: #0088ff; font-weight: 500; text-decoration: none;"><?php echo htmlspecialchars($row['category_name']); ?></a></td>
                        <td><span style="background: <?php echo ($row['status'] == 'Hiển thị') ? '#eafff0; color: #108043;' : '#f4f6f8; color: #637381;'; ?> padding: 4px 8px; border-radius: 4px; font-size: 13px;"><?php echo htmlspecialchars($row['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 15px;">📂</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Chưa có danh mục nào</h3>
            <p style="color: #637381; font-size: 14px; margin-bottom: 20px;">Quản lý các sản phẩm theo từng danh mục giúp việc kinh doanh dễ dàng hơn.</p>
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
        if (checked.length > 0) {
            document.getElementById('normal-header').style.display = 'none';
            document.getElementById('action-header').style.display = 'table-row';
            document.getElementById('selected-count').innerText = checked.length;
            document.getElementById('btn-delete').href = 'index.php?action=delete_category&id=' + checked[0].value;
        } else {
            document.getElementById('normal-header').style.display = 'table-row';
            document.getElementById('action-header').style.display = 'none';
        }
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
