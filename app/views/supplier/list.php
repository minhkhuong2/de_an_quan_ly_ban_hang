<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .sapo-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách Nhà cung cấp</h2>
    <a href="index.php?action=add_supplier" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm nhà cung cấp</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Thêm mới nhà cung cấp thành công!</div><?php endif; ?>

<?php if (!empty($suppliers)): ?>
    <table class="sapo-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">ID</th>
                <th>Tên nhà cung cấp</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Địa chỉ</th>
                <th style="text-align: right;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $row): ?>
                <tr>
                    <td style="text-align: center; color: #637381;">#<?php echo $row['id']; ?></td>
                    <td style="color: #0088ff; font-weight: 500;"><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td style="color: #637381;"><?php echo htmlspecialchars($row['address']); ?></td>
                    <td style="text-align: right;">
                        <a href="index.php?action=delete_supplier&id=<?php echo $row['id']; ?>" onclick="return confirm('Xóa nhà cung cấp này?');" class="btn-action-del">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px;">
        <div style="font-size: 60px; margin-bottom: 15px;">🏢</div>
        <h3 style="font-size: 18px; color: #212b36;">Chưa có nhà cung cấp nào</h3>
        <p style="color: #637381; font-size: 14px; margin-bottom: 20px;">Quản lý nhà cung cấp giúp bạn tạo đơn nhập hàng nhanh chóng và chính xác.</p>
        <a href="index.php?action=add_supplier" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none;">+ Thêm ngay</a>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
