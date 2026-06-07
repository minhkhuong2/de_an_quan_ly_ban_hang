<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array|null $branches */ $safe_branches = is_array($branches) ? $branches : []; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Quản lý chi nhánh</h2>
    <a href="index.php?action=add_branch" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight:500;">+ Thêm chi nhánh</a>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật thông tin chi nhánh thành công!</div><?php endif; ?>

<div style="background: #e6f7ff; color: #0050b3; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #91d5ff; font-size: 14px;">
    ℹ️ <b>Quy định hệ thống:</b> Vì lý do toàn vẹn dữ liệu kế toán và vận chuyển, bạn <b>không thể xóa</b> chi nhánh. Hãy chuyển sang trạng thái "Ngừng hoạt động" nếu không sử dụng nữa.
</div>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #fafbfc; color: #637381; border-bottom: 1px solid #dfe3e8; font-size: 14px;">
                <th style="padding: 15px;">Tên chi nhánh / Kho</th>
                <th style="padding: 15px;">Điện thoại</th>
                <th style="padding: 15px;">Địa chỉ</th>
                <th style="padding: 15px;">Trạng thái</th>
                <th style="padding: 15px; text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($safe_branches as $b): ?>
                <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px; <?php echo $b['status'] == 'Ngừng hoạt động' ? 'opacity:0.6;' : ''; ?>">
                    <td style="padding: 15px; font-weight: bold; color: #0088ff;">
                        🏢 <?php echo htmlspecialchars($b['branch_name']); ?>
                        <?php if ($b['is_default']): ?> <span style="font-size:11px; background:#ffea8a; color:#8a6100; padding:2px 6px; border-radius:4px; margin-left:5px;">Mặc định</span> <?php endif; ?>
                    </td>
                    <td style="padding: 15px;"><?php echo htmlspecialchars($b['phone'] ?? '---'); ?></td>
                    <td style="padding: 15px;"><?php echo htmlspecialchars($b['address'] ?? '---'); ?></td>
                    <td style="padding: 15px;">
                        <?php echo $b['status'] == 'Hoạt động' ? '<span style="color:#108043; font-weight:500;">● Đang hoạt động</span>' : '<span style="color:#cf1322; font-weight:500;">○ Ngừng hoạt động</span>'; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="index.php?action=edit_branch&id=<?php echo $b['id']; ?>" style="background:#fff; border:1px solid #c4cdd5; padding:6px 12px; border-radius:4px; text-decoration:none; color:#212b36; font-weight:500;">Cấu hình</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
