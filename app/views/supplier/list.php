<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $suppliers */ ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách nhà cung cấp</h2>
    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">↑ Xuất file</button>
        <a href="index.php?action=add_supplier" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm nhà cung cấp</a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #8ce09f;">✅ Thêm mới nhà cung cấp thành công!</div><?php endif; ?>
<?php if (isset($_GET['success_edit'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #8ce09f;">✅ Cập nhật thông tin nhà cung cấp thành công!</div><?php endif; ?>
<?php if (isset($_GET['success_delete'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;">🗑️ Đã xóa nhà cung cấp thành công!</div><?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0; min-height: 500px;">

    <form action="index.php" method="GET" style="display: flex; flex-wrap: wrap; gap: 15px; padding: 15px; border-bottom: 1px solid #dfe3e8; background: #fafbfc; align-items: center; border-radius: 8px 8px 0 0;">
        <input type="hidden" name="action" value="supplier_list">

        <div style="position: relative; flex: 1; min-width: 250px; max-width: 350px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">🔍</span>
            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm theo tên, mã NCC, SĐT..." style="width: 100%; padding: 8px 12px 8px 35px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none; font-size: 14px; box-sizing: border-box;">
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <label style="font-size: 13px; color: #637381; font-weight: 500;">Từ ngày:</label>
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>" style="padding: 7px; border: 1px solid #c4cdd5; border-radius: 4px; font-size: 13px; outline: none; color: #212b36;">
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <label style="font-size: 13px; color: #637381; font-weight: 500;">Đến ngày:</label>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>" style="padding: 7px; border: 1px solid #c4cdd5; border-radius: 4px; font-size: 13px; outline: none; color: #212b36;">
        </div>

        <button type="submit" style="padding: 8px 20px; background: #0088ff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold;">Lọc</button>

        <?php if (!empty($_GET['search']) || !empty($_GET['start_date']) || !empty($_GET['end_date'])): ?>
            <a href="index.php?action=supplier_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; font-weight: 500;">❌ Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($suppliers)): ?>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #fff; color: #637381; font-weight: 600; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 12px 15px;">Mã NCC</th>
                    <th style="padding: 12px 15px;">Tên nhà cung cấp</th>
                    <th style="padding: 12px 15px;">Số điện thoại</th>
                    <th style="padding: 12px 15px;">Ngày tạo</th>
                    <th style="padding: 12px 15px; text-align: right;">Công nợ</th>
                    <th style="padding: 12px 15px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $s): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8; transition: 0.2s;">
                        <td style="padding: 12px 15px; color:#0088ff; font-weight: bold;"><?php echo htmlspecialchars($s['supplier_code']); ?></td>
                        <td style="padding: 12px 15px; font-weight: 500; color: #212b36;">
                            <?php echo htmlspecialchars($s['supplier_name']); ?>
                            <div style="font-size: 12px; color: #637381; font-weight: normal; margin-top: 4px;">Email: <?php echo htmlspecialchars($s['email'] ?? '---'); ?></div>
                        </td>
                        <td style="padding: 12px 15px; color: #212b36;"><?php echo htmlspecialchars($s['phone']); ?></td>
                        <td style="padding: 12px 15px; color: #637381;"><?php echo date('d/m/Y', strtotime($s['created_at'])); ?></td>
                        <td style="padding: 12px 15px; text-align: right; font-weight: bold; color: <?php echo $s['debt'] > 0 ? '#cf1322' : '#108043'; ?>;">
                            <?php echo number_format($s['debt'], 0, ',', '.'); ?> ₫
                        </td>
                        <td style="padding: 12px 15px; text-align: center;">
                            <a href="index.php?action=edit_supplier&id=<?php echo $s['id']; ?>" style="text-decoration: none; font-size: 16px; margin-right: 12px;" title="Sửa thông tin">✏️</a>
                            <a href="index.php?action=delete_supplier&id=<?php echo $s['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa Nhà cung cấp này không?');" style="text-decoration: none; font-size: 16px;" title="Xóa nhà cung cấp">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiển thị 1 - <?php echo count($suppliers); ?> trên tổng <?php echo count($suppliers); ?> nhà cung cấp
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">🏢</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Chưa có nhà cung cấp nào</h3>
            <p style="color: #637381; margin-top: 10px;">Vui lòng thêm mới hoặc thử lại với bộ lọc khác.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
