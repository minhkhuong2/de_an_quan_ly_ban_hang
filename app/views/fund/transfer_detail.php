<?php

/** @var array $transfer */
/** @var array $branches */
/** @var array $bank_accounts */
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
        font-size: 15px;
    }

    .card-body {
        padding: 20px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 13.5px;
        margin-bottom: 6px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
        background: #fff;
    }

    .form-control:focus {
        border-color: #0088ff;
    }

    .form-control:disabled {
        background: #f4f6f8;
        color: #919eab;
        cursor: not-allowed;
        border-style: dashed;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=fund_transfers" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Chi tiết phiếu chuyển quỹ: <?php echo htmlspecialchars($transfer['transfer_code']); ?></div>

    <div style="display: flex; gap: 10px;">
        <a href="index.php?action=fund_transfers" class="btn-outline">Quay lại</a>
        <button type="button" class="btn-outline" style="color: #d82c0d; border-color: #fca5a5;" onclick="processSingleDeleteFundTransfer(<?php echo $transfer['id']; ?>)">🗑️ Xóa phiếu</button>
        <button type="button" class="btn-primary" onclick="document.getElementById('frm_update_fund').submit()">💾 Lưu cập nhật phiếu</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size:14px;">✅ Cập nhật thông tin chứng từ chứng minh dòng tiền thành công!</div>
<?php endif; ?>

<form id="frm_update_fund" action="index.php?action=update_fund_transfer" method="POST">
    <input type="hidden" name="id" value="<?php echo $transfer['id']; ?>">

    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">2.1. Thông tin chung cố định (Không thể sửa)</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Mã phiếu chi định danh</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($transfer['transfer_code']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Hình thức nguồn chuyển tiền từ</label>
                    <input type="text" class="form-control" value="<?php echo $transfer['from_type'] === 'cash' ? '💵 Tiền mặt chi nhánh nộp' : '🏦 Sổ tiền gửi ngân hàng chuyển'; ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Số tiền đã chuyển quỹ nội bộ</label>
                    <input type="text" class="form-control" style="font-size:16px; font-weight:bold; color:#108043;" value="<?php echo number_format($transfer['amount'], 0, ',', '.'); ?> ₫" disabled>
                </div>
                <div class="form-group">
                    <label>Ngày ghi nhận tạo phiếu</label>
                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i:s', strtotime($transfer['created_at'])); ?>" disabled>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2.2. Thông tin bổ sung & Cập nhật (Quy tắc Sapo)</div>
            <div class="card-body">

                <div class="form-group">
                    <label>Diễn giải nội dung phiếu chi chuyển <span>*</span></label>
                    <textarea name="description" class="form-control" rows="2" required placeholder="Nhập lý do..."><?php echo htmlspecialchars($transfer['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Thông tin tham chiếu giao dịch (Mã UNC, Mã ngân hàng...)</label>
                    <input type="text" name="reference_code" class="form-control" value="<?php echo htmlspecialchars($transfer['reference_code']); ?>" placeholder="Nhập mã đối soát...">
                </div>

                <div class="form-group">
                    <label>Nơi nhận quỹ tiền (Chi nhánh / Ngân hàng đích)</label>
                    <?php if ($transfer['to_id'] > 0): ?>
                        <input type="text" class="form-control" value="<?php echo $transfer['to_type'] === 'cash' ? '💵 Tiền mặt' : '🏦 Ngân hàng'; ?> (Đã chốt sổ quỹ)" disabled>
                    <?php else: ?>
                        <select name="to_id_update" class="form-control" style="border-color: #0088ff; background: #f4f9ff;">
                            <option value="">-- Chọn đích đến để bổ sung vào sổ quỹ --</option>
                            <?php if ($transfer['to_type'] === 'cash'): ?>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($bank_accounts as $bank): ?>
                                    <option value="<?php echo $bank['id']; ?>"><?php echo htmlspecialchars($bank['bank_name']) . ' (' . htmlspecialchars($bank['account_number']) . ')'; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p style="font-size:12px; color:#0088ff; margin-top:4px;">✨ Sapo khuyên dùng: Bạn được bổ sung thông tin do lúc tạo đang để trống.</p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Ngày phát sinh giao dịch nhận tiền (Ngày vào sổ thực tế)</label>
                    <?php if (!empty($transfer['transaction_date'])): ?>
                        <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($transfer['transaction_date'])); ?>" disabled>
                    <?php else: ?>
                        <input type="datetime-local" name="transaction_date_update" class="form-control" style="border-color: #0088ff; background: #f4f9ff;">
                        <p style="font-size:12px; color:#0088ff; margin-top:4px;">✨ Thao tác: Chọn ngày giờ thực tế tiền tinh tinh vào tài khoản.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    // AJAX xử lý xóa đơn lẻ từ trang chi tiết
    function processSingleDeleteFundTransfer(fundId) {
        let confirmMsg = "⚠️ CẢNH BÁO LƯU Ý: Thao tác xóa phiếu chuyển quỹ nội bộ không thể khôi phục và sẽ làm ảnh hưởng đến báo cáo tài chính!\n\nBạn có thực sự muốn xóa phiếu này không?";

        if (confirm(confirmMsg)) {
            fetch('index.php?action=api_delete_fund', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: fundId
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert(res.msg);
                        // Chuyển hướng về trang danh sách sau khi xóa thành công
                        window.location.href = 'index.php?action=fund_transfers&success=deleted_single';
                    } else {
                        alert("Lỗi từ hệ thống: " + res.msg);
                    }
                });
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
