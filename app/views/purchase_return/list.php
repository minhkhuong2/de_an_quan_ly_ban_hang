<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $returns */ ?>

<style>
    .akc-filter-bar {
        display: flex;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
        align-items: center;
    }

    .akc-filter-bar input.search-input {
        flex: 1;
        padding: 8px 12px 8px 35px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .akc-filter-bar select {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
    }

    .filter-btn {
        padding: 8px 16px;
        background: #f4f6f8;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    .filter-btn:hover {
        background: #dfe3e8;
    }

    .ret-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .ret-table th {
        padding: 12px 15px;
        background: #fafbfc;
        color: #637381;
        font-weight: 600;
        border-bottom: 1px solid #dfe3e8;
    }

    .ret-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f4f6f8;
        color: #212b36;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách đơn trả hàng nhập</h2>

    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">↑ Xuất file</button>
        <a href="index.php?action=add_direct_return" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Trả hàng không theo đơn</a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #8ce09f;">✅ Tạo phiếu trả hàng thành công! Tồn kho đã được trừ tương ứng.</div><?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0; min-height: 500px;">

    <form action="index.php" method="GET" class="akc-filter-bar">
        <input type="hidden" name="action" value="purchase_return_list">

        <div style="position: relative; flex: 1; max-width: 400px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">🔍</span>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm theo mã đơn trả, đơn nhập, NCC...">
        </div>

        <select name="status">
            <option value="">-- Trạng thái hoàn tiền --</option>
            <option value="Chưa hoàn tiền" <?php echo (($_GET['status'] ?? '') == 'Chưa hoàn tiền') ? 'selected' : ''; ?>>Chưa hoàn tiền</option>
            <option value="Hoàn một phần" <?php echo (($_GET['status'] ?? '') == 'Hoàn một phần') ? 'selected' : ''; ?>>Hoàn một phần</option>
            <option value="Đã hoàn tiền" <?php echo (($_GET['status'] ?? '') == 'Đã hoàn tiền') ? 'selected' : ''; ?>>Đã hoàn tiền</option>
        </select>

        <button type="submit" class="filter-btn">Lọc</button>

        <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
            <a href="index.php?action=purchase_return_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; margin-left: 10px;">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($returns)): ?>
        <table class="ret-table">
            <thead>
                <tr>
                    <th>Mã đơn trả</th>
                    <th>Đơn nhập gốc</th>
                    <th>Ngày tạo</th>
                    <th>Nhà cung cấp</th>
                    <th style="text-align: center;">Số lượng trả</th>
                    <th style="text-align: right;">Giá trị hoàn</th>
                    <th style="text-align: center;">Trạng thái tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($returns as $r): ?>
                    <tr style="cursor: pointer;" onclick="window.location.href='#'">
                        <td style="color:#0088ff; font-weight: bold;">#RET<?php echo $r['id']; ?></td>
                        <td style="padding: 12px;">
                            <?php if ($r['order_id'] > 0): ?>
                                <a href="index.php?action=view_purchase&id=<?php echo $r['order_id']; ?>" style="color:#637381; text-decoration: none;">#PN<?php echo $r['order_id']; ?></a>
                            <?php else: ?>
                                <span style="color: #c4cdd5;">---</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></td>
                        <td style="font-weight: 500;"><?php echo htmlspecialchars($r['supplier_name']); ?></td>
                        <td style="text-align: center; font-weight: bold;"><?php echo $r['total_qty']; ?></td>
                        <td style="text-align: right; font-weight: bold; color: #212b36;"><?php echo number_format($r['total_amount'], 0, ',', '.'); ?> ₫</td>
                        <td style="text-align: center;">
                            <?php if ($r['refund_status'] == 'Đã hoàn tiền'): ?>
                                <span style="background:#eafff0; color:#108043; padding:4px 8px; border-radius:4px; font-size:12px; font-weight: 600;">Đã hoàn tiền</span>
                            <?php elseif ($r['refund_status'] == 'Hoàn một phần'): ?>
                                <span style="background:#fff7e6; color:#fa8c16; padding:4px 8px; border-radius:4px; font-size:12px; font-weight: 600;">Hoàn 1 phần</span>
                            <?php else: ?>
                                <span style="background:#fff1f0; color:#cf1322; padding:4px 8px; border-radius:4px; font-size:12px; font-weight: 600;">Chưa hoàn tiền</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiển thị 1 - <?php echo count($returns); ?> trên tổng <?php echo count($returns); ?> đơn trả hàng
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">📤</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Chưa có đơn trả hàng nhập nào</h3>
            <p style="color: #637381;">Bạn có thể tạo đơn trả hàng từ màn hình Chi tiết đơn đặt hàng nhập.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
