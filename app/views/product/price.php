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
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách Bảng giá</h2>
    <a href="index.php?action=add_price" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm bảng giá</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Bảng giá đã được tạo thành công!</div>
<?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; min-height: 400px;">
    <div class="sapo-filter-bar">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">🔍</span>
            <input type="text" placeholder="Tìm kiếm bảng giá..." style="padding-left: 35px; width: 100%;">
        </div>
    </div>

    <?php if (!empty($prices)): ?>
        <table class="sapo-table">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox"></th>
                    <th>Tên bảng giá</th>
                    <th>Chi nhánh áp dụng</th>
                    <th>Quy tắc giá</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prices as $row): ?>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="#" style="color: #0088ff; font-weight: 500; text-decoration: none;"><?php echo htmlspecialchars($row['price_name']); ?></a></td>
                        <td><?php echo htmlspecialchars($row['branch']); ?></td>
                        <td><?php echo $row['adjust_type'] . ' ' . $row['adjust_value'] . '%'; ?></td>
                        <td>
                            <span style="background: <?php echo ($row['status'] == 'Đang áp dụng') ? '#eafff0; color: #108043;' : '#fff8ea; color: #b7791f;'; ?> padding: 4px 8px; border-radius: 4px; font-size: 13px;">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?action=product_price&delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bảng giá này?');" style="color: #ff4d4f; text-decoration: none;">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 15px;">🏷️</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Chưa có bảng giá nào</h3>
            <p style="color: #637381; font-size: 14px; margin-bottom: 20px;">Tạo bảng giá riêng biệt cho từng nhóm khách hàng hoặc chi nhánh.</p>
            <a href="index.php?action=add_price" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Tạo bảng giá ngay</a>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
