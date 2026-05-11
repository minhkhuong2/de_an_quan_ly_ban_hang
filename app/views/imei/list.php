<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Quản lý kho IMEI / Serial</h2>
    <a href="index.php?action=add" class="btn btn-success">+ Nhập kho mới</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã Máy</th>
                <th>Mã IMEI</th>
                <th>Số Serial</th>
                <th>Trạng thái</th>
                <th>Ngày nhập</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo $row['imei_code']; ?></td>
                    <td><strong><?php echo $row['serial_number']; ?></strong></td>
                    <td>
                        <span style="font-weight: bold; color: <?php echo ($row['status'] == 'Trong kho') ? '#52c41a' : (($row['status'] == 'Đã bán') ? '#1890ff' : '#ff4d4f'); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td><?php echo $row['import_date']; ?></td>
                    <td>
                        <?php if ($row['status'] == 'Trong kho'): ?>
                            <a href="index.php?action=sell&id=<?php echo $row['id']; ?>" class="btn btn-warning" onclick="return confirm('Xác nhận xuất kho (Bán) máy này?');">Xuất kho</a>
                        <?php elseif ($row['status'] == 'Đã bán'): ?>
                            <a href="index.php?action=warranty&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Khách mang máy đến bảo hành?');">Nhận bảo hành</a>
                        <?php elseif ($row['status'] == 'Đang bảo hành'): ?>
                            <a href="index.php?action=returnItem&id=<?php echo $row['id']; ?>" class="btn" style="background:#13c2c2;" onclick="return confirm('Đã sửa xong, trả máy lại cho khách?');">Trả máy</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
