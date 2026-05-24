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

    .sapo-filter-bar button,
    .sapo-filter-bar select {
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
        background: #fff;
        border-radius: 0 0 8px 8px;
    }

    .sapo-table th,
    .sapo-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
        font-size: 14px;
    }

    .sapo-table th {
        background: #fafbfc;
        color: #637381;
        font-weight: 500;
        border-bottom: 1px solid #dfe3e8;
    }

    .btn-action-del {
        color: #ff4d4f;
        text-decoration: none;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid transparent;
    }

    .btn-action-del:hover {
        background: #fff1f0;
        border-color: #ffa39e;
    }

    .badge-status {
        background: #eafff0;
        color: #108043;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách nhà cung cấp</h2>
    <a href="index.php?action=add_supplier" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm nhà cung cấp</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật nhà cung cấp thành công!</div><?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0;">
    <div class="sapo-filter-bar" style="border-radius: 8px 8px 0 0;">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" placeholder="Tìm kiếm theo Tên, Mã hoặc Số điện thoại NCC..." style="padding-left: 35px; width: 100%; box-sizing: border-box;">
        </div>
        <select>
            <option>Ngày tạo ▼</option>
        </select>
        <select>
            <option>Nhân viên phụ trách ▼</option>
        </select>
        <select>
            <option>Tag ▼</option>
        </select>
        <button style="color: #0088ff; font-weight: 500; background: #e6f7ff; border-color: transparent;">Lưu bộ lọc</button>
    </div>

    <?php if (!empty($suppliers)): ?>
        <table class="sapo-table">
            <thead>
                <tr>
                    <th style="width: 100px;">Mã NCC</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Trạng thái</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $row): ?>
                    <tr>
                        <td style="color: #637381; font-weight: 500;"><?php echo htmlspecialchars($row['supplier_code'] ?? 'SUP---'); ?></td>

                        <td style="font-weight: 500;">
                            <a href="index.php?action=edit_supplier&id=<?php echo $row['id']; ?>" style="color: #0088ff; text-decoration: none;">
                                <?php echo htmlspecialchars($row['supplier_name']); ?>
                            </a>
                        </td>

                        <td><span class="badge-status"><?php echo htmlspecialchars($row['status'] ?? 'Đang giao dịch'); ?></span></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td style="text-align: right;">
                            <a href="index.php?action=edit_supplier&id=<?php echo $row['id']; ?>" style="color: #0088ff; text-decoration: none; margin-right: 15px; font-weight: 500;">Sửa</a>
                            <a href="index.php?action=delete_supplier&id=<?php echo $row['id']; ?>" onclick="return confirm('Xóa nhà cung cấp này?');" class="btn-action-del">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 15px;">🏢</div>
            <h3 style="font-size: 18px; color: #212b36;">Chưa có nhà cung cấp nào</h3>
            <p style="color: #637381; font-size: 14px; margin-bottom: 20px;">Quản lý nhà cung cấp giúp bạn tạo đơn nhập hàng nhanh chóng và chính xác.</p>
            <a href="index.php?action=add_supplier" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none;">+ Thêm ngay</a>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
