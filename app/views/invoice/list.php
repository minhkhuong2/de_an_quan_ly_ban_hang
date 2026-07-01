<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .v3-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .v3-title { font-size: 22px; font-weight: bold; color: #212b36; }
    
    .filter-bar { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; align-items: center; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #dfe3e8; }
    .form-control { padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none; }
    .btn-primary { background: #0088ff; color: #fff; padding: 8px 15px; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .btn-outline { background: #fff; color: #212b36; padding: 8px 15px; border: 1px solid #c4cdd5; border-radius: 4px; font-weight: 500; cursor: pointer; }

    .v3-card { background: #fff; border-radius: 8px; border: 1px solid #dfe3e8; }
    .table-list { width: 100%; border-collapse: collapse; }
    .table-list th { background: #f4f6f8; padding: 12px 15px; text-align: left; color: #637381; font-size: 13px; border-bottom: 1px solid #dfe3e8; white-space: nowrap; }
    .table-list td { padding: 15px; border-bottom: 1px solid #dfe3e8; font-size: 14px; color: #212b36; }

    .badge { padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
    .badge-issued { background: #eafff0; color: #108043; border: 1px solid #8ce09f; }
    .badge-pending { background: #fff8ea; color: #8a6100; border: 1px solid #ffea8a; }
</style>

<div class="v3-header">
    <div class="v3-title">🧾 Danh sách Hóa đơn điện tử</div>
    <div>
        <a href="index.php?action=order_list" class="btn-outline">← Quay lại Đơn hàng</a>
    </div>
</div>

<form method="GET" action="index.php" class="filter-bar">
    <input type="hidden" name="action" value="invoice_list">
    
    <input type="text" name="keyword" class="form-control" placeholder="Tìm số HĐ, mã đơn, MST, tên Cty..." value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>" style="width: 350px;">
    
    <button type="submit" class="btn-primary">Tìm kiếm</button>
    <a href="index.php?action=invoice_list" class="btn-outline">Xóa bộ lọc</a>
</form>

<div class="v3-card" style="overflow-x: auto;">
    <table class="table-list">
        <thead>
            <tr>
                <th>Số hóa đơn</th>
                <th>Ký hiệu</th>
                <th>Mã đơn hàng</th>
                <th>Ngày phát hành</th>
                <th>Thông tin người mua</th>
                <th>Mã số thuế</th>
                <th>Mã CQT</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 30px; color: #637381;">Không tìm thấy hóa đơn nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td style="font-weight: bold; color: #0088ff;"><?php echo htmlspecialchars($inv['invoice_number'] ?? '---'); ?></td>
                        <td><?php echo htmlspecialchars($inv['invoice_symbol'] ?? '---'); ?></td>
                        <td>
                            <a href="index.php?action=view_order&id=<?php echo $inv['id']; ?>" style="color: #0088ff; text-decoration: none; font-weight: 600;">
                                <?php echo htmlspecialchars($inv['order_code']); ?>
                            </a>
                        </td>
                        <td><?php echo !empty($inv['invoice_date']) ? date('d/m/Y H:i', strtotime($inv['invoice_date'])) : '---'; ?></td>
                        <td>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($inv['invoice_company_name'] ?: ($inv['invoice_buyer_name'] ?: 'Khách lẻ')); ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($inv['invoice_tax_code'] ?? '---'); ?></td>
                        <td style="font-size: 13px; color: #637381; font-family: monospace;"><?php echo htmlspecialchars($inv['invoice_cqt_code'] ?? '---'); ?></td>
                        <td style="font-weight: bold; text-align: right; color: #d82c0d;">
                            <?php echo number_format($inv['grand_total'], 0, ',', '.'); ?>đ
                        </td>
                        <td>
                            <?php if ($inv['invoice_status'] == 'issued'): ?>
                                <span class="badge badge-issued">Đã phát hành</span>
                            <?php elseif ($inv['invoice_status'] == 'pending_issue'): ?>
                                <span class="badge badge-pending">Chờ phát hành</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
