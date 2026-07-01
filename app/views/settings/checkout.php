<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .v3-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .v3-title { font-size: 22px; font-weight: bold; color: #212b36; }
    .v3-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #dfe3e8; margin-bottom: 20px; }
    .card-header { padding: 15px 20px; border-bottom: 1px solid #dfe3e8; background: #fafbfc; font-weight: 600; color: #212b36; }
    .card-body { padding: 20px; }
    
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-weight: 600; color: #212b36; margin-bottom: 5px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none; }
    
    .btn-primary { background: #0088ff; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; }
    
    .alert-success { background: #eafff0; color: #108043; border: 1px solid #8ce09f; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
</style>

<div class="v3-header">
    <div class="v3-title">Cấu hình Trang thanh toán</div>
</div>

<?php if (!empty($success_message)): ?>
    <div class="alert-success">✅ <?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<div class="v3-card" style="max-width: 600px;">
    <div class="card-header">Xử lý Đơn hàng chưa hoàn tất</div>
    <div class="card-body">
        <p style="color: #637381; margin-bottom: 20px;">
            Gửi email tự động cho khách hàng để nhắc họ về giỏ hàng đã bỏ quên, giúp tăng tỷ lệ chuyển đổi.
        </p>

        <form method="POST" action="index.php?action=checkout_settings">
            <div class="form-group">
                <label class="form-label">Gửi email nhắc hoàn thành đơn hàng sau:</label>
                <select name="auto_email_reminder" class="form-control">
                    <option value="never" <?php echo ($checkout_settings['auto_email_reminder'] == 'never') ? 'selected' : ''; ?>>Không bao giờ</option>
                    <option value="1h" <?php echo ($checkout_settings['auto_email_reminder'] == '1h') ? 'selected' : ''; ?>>Sau 1 giờ (Đề nghị)</option>
                    <option value="6h" <?php echo ($checkout_settings['auto_email_reminder'] == '6h') ? 'selected' : ''; ?>>Sau 6 giờ</option>
                    <option value="10h" <?php echo ($checkout_settings['auto_email_reminder'] == '10h') ? 'selected' : ''; ?>>Sau 10 giờ (Đề nghị)</option>
                    <option value="24h" <?php echo ($checkout_settings['auto_email_reminder'] == '24h') ? 'selected' : ''; ?>>Sau 24 giờ</option>
                </select>
                <small style="color: #637381; display: block; margin-top: 5px;">* Tính năng tự động gửi email sẽ yêu cầu cấu hình cron job trên server để hoạt động thực tế.</small>
            </div>
            
            <div style="text-align: right; margin-top: 20px; border-top: 1px solid #dfe3e8; padding-top: 15px;">
                <button type="submit" class="btn-primary">💾 Lưu cấu hình</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
