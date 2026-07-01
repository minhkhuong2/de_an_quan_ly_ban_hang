<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="header-container" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Danh sách Biên bản bàn giao</h2>
    <a href="index.php?action=create_handover" class="btn btn-primary" style="background:#0088ff; color:#fff; padding:8px 15px; border-radius:4px; text-decoration:none; font-weight:600;">+ Tạo biên bản bàn giao</a>
</div>

<div class="v3-card" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #dfe3e8; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <table class="table" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Mã biên bản</th>
                <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Ngày tạo</th>
                <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Chi nhánh</th>
                <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Đối tác vận chuyển</th>
                <th style="text-align:center; padding:10px; border-bottom:1px solid #dfe3e8;">Số kiện hàng</th>
                <th style="text-align:center; padding:10px; border-bottom:1px solid #dfe3e8;">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($records)): ?>
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#637381;">Chưa có biên bản bàn giao nào.</td>
            </tr>
            <?php else: ?>
                <?php foreach($records as $rec): ?>
                <tr>
                    <td style="padding:15px 10px; border-bottom:1px solid #dfe3e8;">
                        <a href="index.php?action=handover_detail&id=<?php echo $rec['id']; ?>" style="color:#0088ff; font-weight:600; text-decoration:none;">
                            <?php echo htmlspecialchars($rec['record_code']); ?>
                        </a>
                    </td>
                    <td style="padding:15px 10px; border-bottom:1px solid #dfe3e8; color:#637381;">
                        <?php echo date('d/m/Y H:i', strtotime($rec['created_at'])); ?>
                    </td>
                    <td style="padding:15px 10px; border-bottom:1px solid #dfe3e8; color:#212b36;">
                        <?php echo htmlspecialchars($rec['branch_name'] ?? '---'); ?>
                    </td>
                    <td style="padding:15px 10px; border-bottom:1px solid #dfe3e8; color:#212b36;">
                        <?php echo htmlspecialchars($rec['partner_name'] ?? '---'); ?>
                    </td>
                    <td style="padding:15px 10px; border-bottom:1px solid #dfe3e8; text-align:center; color:#212b36; font-weight:500;">
                        <?php echo $rec['total_packages']; ?>
                    </td>
                    <td style="padding:15px 10px; border-bottom:1px solid #dfe3e8; text-align:center;">
                        <?php if($rec['status'] == 'completed'): ?>
                            <span style="background:#eafff0; color:#108043; border:1px solid #8ce09f; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:500;">Đã bàn giao</span>
                        <?php else: ?>
                            <span style="background:#fff8ea; color:#8a6100; border:1px solid #ffea8a; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:500;">Chờ bàn giao</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
