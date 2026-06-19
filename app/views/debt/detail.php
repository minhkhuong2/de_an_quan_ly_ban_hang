<?php

/** @var array $customer */
/** @var array $debt_history */
require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 22px;
        font-weight: bold;
        color: #212b36;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
    }

    .info-bar {
        display: flex;
        gap: 40px;
        padding: 20px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .info-box h3 {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #637381;
    }

    .info-box p {
        margin: 0;
        font-size: 22px;
        font-weight: bold;
        color: #d82c0d;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
    }

    .v3-table th {
        background: #f4f6f8;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        text-align: left;
        padding: 12px 15px;
        border-bottom: 1px solid #dfe3e8;
    }

    .v3-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        width: 450px;
        padding: 25px;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=debt_app_list" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Chi tiết công nợ khách hàng</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline">📥 Xuất file</button>
        <button class="btn-primary" onclick="document.getElementById('adjust_modal').style.display='flex'">⚙️ Điều chỉnh Công nợ</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Xác nhận hoàn tất cập nhật điều chỉnh số dư công nợ mục tiêu thành công!</div>
<?php endif; ?>

<div class="info-bar">
    <div class="info-box">
        <h3>Khách hàng</h3>
        <span style="font-size:16px; font-weight:bold; color:#212b36;"><?php echo htmlspecialchars(trim($customer['last_name'] . ' ' . $customer['first_name'])); ?></span><br>
        <small style="color:#637381;">SĐT: <?php echo htmlspecialchars($customer['phone'] ?: '---'); ?></small>
    </div>
    <div class="info-box" style="border-left: 1px solid #dfe3e8; padding-left: 40px;">
        <h3>Công nợ hiện tại</h3>
        <p><?php echo number_format($customer['debt'], 0, ',', '.'); ?> ₫</p>
    </div>
</div>

<div class="v3-card">
    <div class="card-header">Danh sách giao dịch phát sinh công nợ</div>
    <table class="v3-table">
        <thead>
            <tr>
                <th>Mã chứng từ</th>
                <th>Loại giao dịch</th>
                <th>Lý do / Diễn giải</th>
                <th>Thời gian giao dịch</th>
                <th style="text-align: right;">Giá trị công nợ thay đổi</th>
                <th style="text-align: right;">Công nợ sau giao dịch</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($debt_history)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#8c98a4; padding:30px;">Chưa ghi nhận chứng từ giao dịch phát sinh nợ nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($debt_history as $hist): ?>
                    <?php
                    $type_text = 'Đơn hàng';
                    if ($hist['transaction_type'] == 'receipt') $type_text = 'Phiếu thu nợ';
                    if ($hist['transaction_type'] == 'payment') $type_text = 'Phiếu chi';
                    if ($hist['transaction_type'] == 'adjustment') $type_text = 'Điều chỉnh công nợ';

                    // Hiển thị giá trị thay đổi (+/-)
                    $change_text = '';
                    $change_style = '';
                    if ($hist['debt_increase'] > 0) {
                        $change_text = '+' . number_format($hist['debt_increase'], 0, ',', '.');
                        $change_style = 'color: #d82c0d; font-weight:600;';
                    } else {
                        $change_text = '-' . number_format($hist['debt_decrease'], 0, ',', '.');
                        $change_style = 'color: #108043; font-weight:600;';
                    }
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: #0088ff;"><?php echo htmlspecialchars($hist['reference_code']); ?></td>
                        <td><span style="background:#f4f6f8; padding:3px 8px; border-radius:4px; font-size:12px; font-weight:600;"><?php echo $type_text; ?></span></td>
                        <td><?php echo htmlspecialchars($hist['description']); ?></td>
                        <td style="color:#637381;"><?php echo date('d/m/Y H:i', strtotime($hist['created_at'])); ?></td>
                        <td style="text-align: right; <?php echo $change_style; ?>"><?php echo $change_text; ?> ₫</td>
                        <td style="text-align: right; font-weight: bold; color:#212b36;"><?php echo number_format($hist['balance'], 0, ',', '.'); ?> ₫</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="adjust_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color:#212b36;">Điều chỉnh công nợ khách hàng</h3>

        <form action="index.php?action=debt_app_adjust" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">

            <div class="form-group" style="margin-top:15px;">
                <label>Số nợ hiện tại cửa hàng ghi nhận:</label>
                <input type="text" class="form-control" value="<?php echo number_format($customer['debt'], 0, ',', '.'); ?> ₫" disabled style="background:#f4f6f8; font-weight:600;">
            </div>

            <div class="form-group">
                <label>Nợ sau khi chỉnh sửa (Số nợ mới) <span>*</span></label>
                <input type="number" name="target_debt" class="form-control" style="font-size:16px; font-weight:bold; color:#0088ff;" required placeholder="VD: Nhập 0 nếu muốn xóa hết nợ">
                <p style="font-size:12px; color:#637381; margin-top:4px;">✨ <i>Hệ thống tự động tính toán hạch toán tăng/giảm dựa vào số này.</i></p>
            </div>

            <div class="form-group">
                <label>Ghi chú / Lý do điều chỉnh <span>*</span></label>
                <textarea name="description" class="form-control" rows="3" required placeholder="Nhập lý do điều chỉnh tài chính..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('adjust_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary">Xác nhận hoàn tất</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
