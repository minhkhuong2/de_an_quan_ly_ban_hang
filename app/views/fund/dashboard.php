<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var float|int $opening_balance
 * @var float|int $total_in
 * @var float|int $total_out
 * @var float|int $closing_balance
 * @var string $fund_type
 * @var string $start_date
 * @var string $end_date
 * @var array $transactions
 */
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

    /* 4 Thẻ thống kê */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dfe3e8;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .summary-title {
        font-size: 14px;
        color: #637381;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .summary-value {
        font-size: 24px;
        font-weight: bold;
    }

    .text-blue {
        color: #0088ff;
    }

    .text-green {
        color: #108043;
    }

    .text-red {
        color: #d82c0d;
    }

    .text-dark {
        color: #212b36;
    }

    /* Bảng dữ liệu */
    .v3-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #dfe3e8;
    }

    .v3-filter-bar {
        padding: 15px 20px;
        display: flex;
        gap: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        align-items: center;
    }

    .v3-form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v3-table th {
        background: #f4f6f8;
        padding: 12px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 13px;
        color: #637381;
    }

    .v3-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
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
</style>

<div class="v3-header">
    <div class="v3-title">📊 Tổng quan Sổ quỹ</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="document.getElementById('export_modal').style.display='flex'">📥 Xuất file Sổ quỹ</button>
        <a href="index.php?action=create_receipt" class="btn-primary" style="background:#108043;">+ Tạo Phiếu thu</a>
        <a href="index.php?action=create_expense" class="btn-primary" style="background:#d82c0d;">- Tạo Phiếu chi</a>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-title">Số dư quỹ đầu kỳ</div>
        <div class="summary-value text-blue"><?php echo number_format($opening_balance, 0, ',', '.'); ?> ₫</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Tổng thu trong kỳ (+)</div>
        <div class="summary-value text-green"><?php echo number_format($total_in, 0, ',', '.'); ?> ₫</div>
    </div>
    <div class="summary-card">
        <div class="summary-title">Tổng chi trong kỳ (-)</div>
        <div class="summary-value text-red"><?php echo number_format($total_out, 0, ',', '.'); ?> ₫</div>
    </div>
    <div class="summary-card" style="background:#f4f9ff; border-color:#b3d4ff;">
        <div class="summary-title text-blue">TỒN QUỸ CUỐI KỲ (=)</div>
        <div class="summary-value text-blue"><?php echo number_format($closing_balance, 0, ',', '.'); ?> ₫</div>
    </div>
</div>

<div class="v3-card">
    <form method="GET" action="index.php" class="v3-filter-bar">
        <input type="hidden" name="action" value="fund_dashboard">

        <span style="font-size:14px; font-weight:600; color:#637381;">Loại quỹ:</span>
        <select name="fund_type" class="v3-form-control">
            <option value="all" <?php echo $fund_type == 'all' ? 'selected' : ''; ?>>Tất cả Tổng quỹ</option>
            <option value="cash" <?php echo $fund_type == 'cash' ? 'selected' : ''; ?>>💵 Quỹ tiền mặt</option>
            <option value="bank" <?php echo $fund_type == 'bank' ? 'selected' : ''; ?>>🏦 Quỹ tiền gửi NH</option>
        </select>

        <span style="font-size:14px; font-weight:600; color:#637381; margin-left:15px;">Thời gian:</span>
        <input type="datetime-local" name="start_date" class="v3-form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($start_date)); ?>">
        <span>➔</span>
        <input type="datetime-local" name="end_date" class="v3-form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($end_date)); ?>">

        <button type="submit" class="btn-primary">Lọc báo cáo</button>
    </form>

    <table class="v3-table">
        <thead>
            <tr>
                <th>Ngày ghi sổ</th>
                <th>Mã chứng từ</th>
                <th>Loại / Hình thức</th>
                <th>Đối tượng / Lý do</th>
                <th style="text-align:right;">Thu vào (+)</th>
                <th style="text-align:right;">Chi ra (-)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:30px; color:#637381;">Không có giao dịch nào trong kỳ này.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td style="color:#637381;"><?php echo date('d/m/Y H:i', strtotime($t['transaction_date'])); ?></td>
                        <td>
                            <?php if ($t['doc_type'] == 'receipt'): ?>
                                <a href="index.php?action=receipt_detail&id=<?php echo $t['doc_code']; ?>" style="color:#108043; font-weight:bold; text-decoration:none;">📥 <?php echo $t['doc_code']; ?></a>
                            <?php else: ?>
                                <a href="index.php?action=expense_detail&id=<?php echo $t['doc_code']; ?>" style="color:#d82c0d; font-weight:bold; text-decoration:none;">📤 <?php echo $t['doc_code']; ?></a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="font-size:12px; font-weight:600; padding:2px 6px; border-radius:4px; background:#f4f6f8;"><?php echo $t['payment_method'] == 'cash' ? 'Tiền mặt' : 'Chuyển khoản'; ?></span>
                        </td>
                        <td>
                            <b><?php echo htmlspecialchars($t['partner']); ?></b><br>
                            <small style="color:#637381;"><?php echo htmlspecialchars($t['reason']); ?></small>
                        </td>
                        <td style="text-align:right; color:#108043; font-weight:bold;">
                            <?php echo $t['total_in'] > 0 ? '+ ' . number_format($t['total_in'], 0, ',', '.') : '---'; ?>
                        </td>
                        <td style="text-align:right; color:#d82c0d; font-weight:bold;">
                            <?php echo $t['total_out'] > 0 ? '- ' . number_format($t['total_out'], 0, ',', '.') : '---'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="export_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">📥 Xuất file báo cáo Sổ quỹ</h3>
        <form action="index.php?action=export_cashbook" method="POST">

            <div class="form-group" style="margin-top:15px;">
                <label>Chọn biểu mẫu / Loại quỹ cần xuất:</label>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <label style="cursor:pointer;"><input type="radio" name="export_type" value="all" checked> Xuất Tổng quỹ (Bao gồm tất cả phiếu Thu/Chi)</label>
                    <label style="cursor:pointer;"><input type="radio" name="export_type" value="cash"> Sổ quỹ tiền mặt (Mẫu S6 - HKD theo TT88)</label>
                    <label style="cursor:pointer;"><input type="radio" name="export_type" value="bank"> Sổ quỹ tiền gửi (Mẫu S7 - HKD theo TT88)</label>
                </div>
            </div>

            <div style="background:#f4f9ff; padding:12px; border-radius:4px; font-size:13px; color:#0056b3; margin-bottom:20px;">
                💡 File được tải xuống trực tiếp dưới định dạng .CSV, có thể mở bằng Microsoft Excel hoặc Google Sheets.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('export_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary" onclick="document.getElementById('export_modal').style.display='none'">Xác nhận Tải File</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
