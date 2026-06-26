<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $receipts */ ?>

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

    .filter-btn {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
    }

    .filter-btn:hover {
        background: #f4f6f8;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách đơn nhập hàng</h2>
    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">↑ Xuất file</button>
        <a href="index.php?action=direct_receive" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Tạo đơn nhập hàng</a>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; min-height: 500px;">

    <form action="index.php" method="GET" class="akc-filter-bar">
        <input type="hidden" name="action" value="po_receipt_list">

        <div style="position: relative; flex: 1; max-width: 400px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">🔍</span>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm kiếm theo mã đơn, nhà cung cấp...">
        </div>

        <button type="button" class="filter-btn">Trạng thái ▼</button>
        <button type="button" class="filter-btn">Bộ lọc khác ⚙️</button>

        <?php if (!empty($_GET['search'])): ?>
            <a href="index.php?action=po_receipt_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; margin-left: 10px;">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($receipts)): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Mã phiếu nhập</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Ngày nhập</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Nhà cung cấp</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Chi nhánh</th>
                    <th style="padding: 12px; text-align: center; color: #637381; font-weight: 500;">Số lượng</th>
                    <th style="padding: 12px; text-align: right; color: #637381; font-weight: 500;">Giá trị đơn</th>
                    <th style="padding: 12px; text-align: center; color: #637381; font-weight: 500;">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receipts as $row): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8;">
                        <td style="padding: 12px; font-weight: 500;">
                            <a href="index.php?action=view_purchase&id=<?php echo $row['id']; ?>" style="color: #0088ff; text-decoration: none;">
                                #PN<?php echo $row['id']; ?>
                            </a>
                        </td>
                        <td style="padding: 12px; color: #212b36;"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td style="padding: 12px; color: #212b36;"><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                        <td style="padding: 12px; color: #637381;"><?php echo htmlspecialchars($row['branch']); ?></td>
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #212b36;"><?php echo $row['total_qty']; ?></td>
                        <td style="padding: 12px; text-align: right; font-weight: 500; color: #212b36;"><?php echo number_format($row['total_amount'], 0, ',', '.'); ?>₫</td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: #eafff0; color: #108043; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; border: 1px solid #8ce09f;">Đã nhập kho</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiển thị 1 - <?php echo count($receipts); ?> trên tổng <?php echo count($receipts); ?> phiếu nhập
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">📦</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Chưa có phiếu nhập hàng nào</h3>
            <p style="color: #637381; margin-bottom: 20px;">Phiếu nhập hàng sẽ xuất hiện ở đây sau khi bạn xác nhận nhập kho từ Đơn đặt hàng hoặc Nhập hàng trực tiếp.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
