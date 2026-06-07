<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $staffs */ ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Quản lý nhân viên</h2>
    <a href="index.php?action=add_staff" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Thêm mới nhân viên</a>
</div>

<?php if (isset($_GET['success_add']) && isset($_GET['invite_link'])): ?>
    <div style="background:#e6f7ff; border:1px solid #91d5ff; padding:15px; border-radius:6px; margin-bottom:20px;">
        <h4 style="color:#0050b3; margin-top:0;">📧 Hệ thống đã gửi email mời nhân viên!</h4>
        <p style="margin-bottom: 5px;">(Mô phỏng Email) Bạn hãy gửi link này cho nhân viên để họ thiết lập mật khẩu:</p>
        <a href="<?php echo urldecode($_GET['invite_link']); ?>" target="_blank" style="font-weight:bold; color:#cf1322; text-decoration:underline;">Bấm vào đây để xác nhận tham gia cửa hàng</a>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success_delete'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;">🗑️ Đã xóa nhân viên!</div><?php endif; ?>
<?php if (isset($_GET['success_activate'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Nhân viên đã kích hoạt tài khoản thành công!</div><?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #fafbfc; color: #637381; border-bottom: 1px solid #dfe3e8; font-size: 14px;">
                <th style="padding: 15px;">Họ tên nhân viên</th>
                <th style="padding: 15px;">Email</th>
                <th style="padding: 15px;">Số điện thoại</th>
                <th style="padding: 15px;">Phân quyền</th>
                <th style="padding: 15px;">Trạng thái</th>
                <th style="padding: 15px; text-align:center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staffs as $s): ?>
                <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px;">
                    <td style="padding: 15px; font-weight: 500; color: #0088ff;"><?php echo htmlspecialchars(trim($s['last_name'] . ' ' . $s['first_name'])); ?></td>
                    <td style="padding: 15px;"><?php echo htmlspecialchars($s['email']); ?></td>
                    <td style="padding: 15px;"><?php echo htmlspecialchars($s['phone']); ?></td>
                    <td style="padding: 15px;"><span style="background:#f4f6f8; padding:4px 8px; border-radius:4px;"><?php echo htmlspecialchars($s['role']); ?></span></td>
                    <td style="padding: 15px;">
                        <?php if ($s['status'] == 'Đang kích hoạt'): ?>
                            <span style="color:#108043; background:#eafff0; padding:4px 8px; border-radius:4px; font-weight:500;">● Đang kích hoạt</span>
                        <?php else: ?>
                            <span style="color:#b7791f; background:#fff8ea; padding:4px 8px; border-radius:4px; font-weight:500;">○ Chờ xác nhận</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="index.php?action=edit_staff&id=<?php echo $s['id']; ?>" style="text-decoration:none; margin-right:10px;">✏️ Sửa</a>
                        <?php if ($s['role'] != 'Admin'): // Không cho tự xóa Admin 
                        ?>
                            <a href="index.php?action=delete_staff&id=<?php echo $s['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');" style="color:#cf1322; text-decoration:none;">🗑️ Xóa</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
