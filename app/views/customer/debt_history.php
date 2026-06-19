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

    .card-body {
        padding: 20px;
    }

    .debt-overview {
        display: flex;
        gap: 30px;
        background: #fafbfc;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .debt-box {
        flex: 1;
    }

    .debt-label {
        font-size: 14px;
        color: #637381;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .debt-value {
        font-size: 24px;
        font-weight: bold;
    }

    .text-danger {
        color: #d82c0d;
    }

    /* Khách nợ (Dương) */
    .text-success {
        color: #108043;
    }

    /* Cửa hàng nợ (Âm) */

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

    /* Modal CSS */
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
        width: 500px;
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
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=customer_list" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Sổ chi tiết công nợ: <?php echo htmlspecialchars(trim($customer['last_name'] . ' ' . $customer['first_name'])); ?></div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="document.getElementById('export_debt_modal').style.display='flex'">📥 Xuất file</button>
        <button class="btn-primary" onclick="document.getElementById('adjust_modal').style.display='flex'">⚙️ Điều chỉnh công nợ</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật điều chỉnh công nợ thành công!</div>
<?php endif; ?>

<div class="debt-overview">
    <div class="debt-box">
        <div class="debt-label">Tổng công nợ hiện tại</div>
        <?php if ($customer['debt'] > 0): ?>
            <div class="debt-value text-danger"><?php echo number_format($customer['debt'], 0, ',', '.'); ?> ₫</div>
            <div style="font-size:13px; color:#d82c0d; margin-top:5px;">⚠️ Dư nợ dương: Khách hàng đang nợ cửa hàng.</div>
        <?php elseif ($customer['debt'] < 0): ?>
            <div class="debt-value text-success"><?php echo number_format(abs($customer['debt']), 0, ',', '.'); ?> ₫</div>
            <div style="font-size:13px; color:#108043; margin-top:5px;">💰 Dư nợ âm: Cửa hàng đang nợ khách hàng.</div>
        <?php else: ?>
            <div class="debt-value" style="color: #637381;">0 ₫</div>
            <div style="font-size:13px; color:#637381; margin-top:5px;">Khách hàng không có nợ.</div>
        <?php endif; ?>
    </div>
    <div class="debt-box" style="border-left: 1px solid #dfe3e8; padding-left: 30px;">
        <div class="debt-label">Mã khách hàng</div>
        <div style="font-size: 16px; font-weight: 600; color: #212b36; margin-top:5px;"><?php echo htmlspecialchars($customer['customer_code']); ?></div>
        <div style="font-size: 14px; color: #637381; margin-top:5px;">SĐT: <?php echo htmlspecialchars($customer['phone'] ?: '---'); ?></div>
    </div>
</div>

<div class="v3-card">
    <table class="v3-table">
        <thead>
            <tr>
                <th style="width: 15%;">Thời gian</th>
                <th style="width: 15%;">Mã chứng từ</th>
                <th style="width: 25%;">Diễn giải</th>
                <th style="width: 15%; text-align: right;">Ghi Tăng (+)</th>
                <th style="width: 15%; text-align: right;">Ghi Giảm (-)</th>
                <th style="width: 15%; text-align: right;">Dư Nợ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($debt_history)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#8c98a4; padding:30px;">Khách hàng chưa có giao dịch phát sinh công nợ.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($debt_history as $hist): ?>
                    <tr>
                        <td style="color:#637381;"><?php echo date('d/m/Y H:i', strtotime($hist['created_at'])); ?></td>
                        <td style="color:#0088ff; font-weight: 600;"><?php echo htmlspecialchars($hist['reference_code'] ?: '---'); ?></td>
                        <td><?php echo htmlspecialchars($hist['description']); ?></td>
                        <td style="text-align: right; color: #d82c0d; font-weight: 600;">
                            <?php echo $hist['debt_increase'] > 0 ? '+' . number_format($hist['debt_increase'], 0, ',', '.') : ''; ?>
                        </td>
                        <td style="text-align: right; color: #108043; font-weight: 600;">
                            <?php echo $hist['debt_decrease'] > 0 ? '-' . number_format($hist['debt_decrease'], 0, ',', '.') : ''; ?>
                        </td>
                        <td style="text-align: right; font-weight: bold; color:#212b36;">
                            <?php echo number_format($hist['balance'], 0, ',', '.'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="adjust_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Điều chỉnh công nợ</h3>
        <form action="index.php?action=adjust_customer_debt" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">

            <div class="form-group" style="margin-top: 15px;">
                <label>Loại điều chỉnh</label>
                <div style="display: flex; gap: 20px;">
                    <label style="cursor:pointer; display:flex; align-items:center; gap:5px;">
                        <input type="radio" name="adjust_type" value="increase" checked>
                        <span style="color:#d82c0d; font-weight:600;">Ghi TĂNG công nợ (Khách nợ thêm)</span>
                    </label>
                    <label style="cursor:pointer; display:flex; align-items:center; gap:5px;">
                        <input type="radio" name="adjust_type" value="decrease">
                        <span style="color:#108043; font-weight:600;">Ghi GIẢM công nợ (Cấn trừ nợ)</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Giá trị điều chỉnh (VNĐ) <span>*</span></label>
                <input type="number" name="amount" class="form-control" required min="1" placeholder="Nhập số tiền...">
            </div>

            <div class="form-group">
                <label>Lý do điều chỉnh <span>*</span></label>
                <textarea name="description" class="form-control" rows="3" required placeholder="VD: Bù trừ công nợ do sai sót hóa đơn tháng trước..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('adjust_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary">Lưu điều chỉnh</button>
            </div>
        </form>
    </div>
</div>
<div id="export_debt_modal" class="modal">
    <div class="modal-content" style="width: 450px;">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Xuất file công nợ khách hàng</h3>

        <div class="form-group">
            <label>Loại xuất file:</label>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label style="cursor: pointer;"><input type="radio" name="export_type" value="overview" checked> Tổng quan công nợ của khách hàng</label>
                <label style="cursor: pointer;"><input type="radio" name="export_type" value="detailed"> Chi tiết công nợ của khách hàng (Bao gồm lịch sử Hóa đơn/Phiếu)</label>
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label>Danh sách:</label>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label style="cursor: pointer;"><input type="radio" name="export_list" value="all" checked> Tất cả công nợ</label>
                <label style="cursor: pointer;"><input type="radio" name="export_list" value="filtered"> Tất cả công nợ theo bộ lọc</label>
            </div>
        </div>

        <div style="background: #f4f9ff; padding: 12px; border-radius: 4px; font-size: 13px; color: #0056b3; margin-bottom: 20px; line-height: 1.5;">
            📧 <b>Lưu ý:</b> File danh sách Excel sẽ được gửi tự động qua email của tài khoản đang thao tác.
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button class="btn-outline" onclick="document.getElementById('export_debt_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="processExportSingleDebt(<?php echo $customer['id']; ?>)">Xuất file</button>
        </div>
    </div>
</div>

<script>
    function processExportSingleDebt(customerId) {
        let type = document.querySelector('input[name="export_type"]:checked').value;
        let list = document.querySelector('input[name="export_list"]:checked').value;

        fetch('index.php?action=api_export_debt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    export_scope: 'single',
                    customer_id: customerId,
                    export_type: type,
                    list_filter: list
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg); // Hiển thị thông báo chuẩn Sapo
                document.getElementById('export_debt_modal').style.display = 'none';
            });
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
