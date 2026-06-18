<?php

/** @var array $transfers */
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
    }

    .table-fund {
        width: 100%;
        border-collapse: collapse;
    }

    .table-fund th {
        background: #f4f6f8;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        text-align: left;
        padding: 12px 20px;
        border-bottom: 1px solid #dfe3e8;
    }

    .table-fund td {
        padding: 14px 20px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .table-fund tbody tr:hover {
        background: #fafbfc;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-danger-bulk {
        background: #fff1f0;
        color: #d82c0d;
        border: 1px solid #ffa39e;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
        display: none;
    }

    .btn-danger-bulk:hover {
        background: #d82c0d;
        color: #fff;
    }

    input[type="checkbox"] {
        width: 17px;
        height: 18px;
        cursor: pointer;
        vertical-align: middle;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Sổ quỹ - Danh sách phiếu chuyển quỹ nội bộ</div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <button type="button" id="btn_bulk_delete" class="btn-danger-bulk" onclick="processBulkDeleteFundTransfers()">🗑️ Xóa phiếu đã chọn (<span id="selected_count">0</span>)</button>
        <a href="index.php?action=create_fund_transfer" class="btn-primary">+ Tạo phiếu chuyển quỹ</a>
    </div>
</div>

<div class="v3-card">
    <table class="table-fund">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;"><input type="checkbox" id="chk_all_funds" onchange="toggleSelectAllFundRows(this)"></th>
                <th style="width: 15%;">Mã phiếu</th>
                <th style="width: 20%;">Ngày giao dịch</th>
                <th style="width: 25%;">Hình thức chuyển</th>
                <th style="width: 20%; text-align: right;">Giá trị chuyển quỹ</th>
                <th style="width: 15%; padding-left: 30px;">Tham chiếu</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transfers)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#8c98a4; padding:30px;">Chưa ghi nhận hoạt động chuyển quỹ nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transfers as $t): ?>
                    <tr id="fund_row_<?php echo $t['id']; ?>">
                        <td style="text-align: center;" onclick="event.stopPropagation();">
                            <input type="checkbox" class="chk-fund-item" value="<?php echo $t['id']; ?>" onchange="updateBulkDeleteButtonStatus()">
                        </td>
                        <td style="font-weight: 600; color: #0088ff; cursor: pointer;" onclick="window.location.href='index.php?action=fund_transfer_detail&id=<?php echo $t['id']; ?>'">
                            <?php echo $t['transfer_code']; ?>
                        </td>
                        <td><?php echo $t['transaction_date'] ? date('d/m/Y H:i', strtotime($t['transaction_date'])) : '<i>Chưa vào sổ quỹ</i>'; ?></td>
                        <td>
                            <?php echo $t['from_type'] === 'cash' ? '💵 Tiền mặt' : '🏦 Ngân hàng'; ?> ➔
                            <?php echo $t['to_type'] === 'cash' ? '💵 Tiền mặt' : '🏦 Ngân hàng'; ?>
                        </td>
                        <td style="text-align: right; font-weight: bold; color: #212b36;">
                            <?php echo number_format($t['amount'], 0, ',', '.'); ?> ₫
                        </td>
                        <td style="color: #637381; padding-left: 30px;"><?php echo htmlspecialchars($t['reference_code'] ?: '---'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Logic chọn tất cả checkbox đầu dòng
    function toggleSelectAllFundRows(masterCheckbox) {
        let items = document.querySelectorAll('.chk-fund-item');
        items.forEach(cb => cb.checked = masterCheckbox.checked);
        updateBulkDeleteButtonStatus();
    }

    // Logic đếm số lượng phiếu được chọn để ẩn/hiện nút xóa hàng loạt
    function updateBulkDeleteButtonStatus() {
        let checkedItems = document.querySelectorAll('.chk-fund-item:checked');
        let btn = document.getElementById('btn_bulk_delete');
        let txtCount = document.getElementById('selected_count');

        if (checkedItems.length > 0) {
            txtCount.innerText = checkedItems.length;
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
            document.getElementById('chk_all_funds').checked = false;
        }
    }

    // Gửi lệnh AJAX xóa hàng loạt lên Server PHP (Mục 2.2)
    function processBulkDeleteFundTransfers() {
        let checkedItems = document.querySelectorAll('.chk-fund-item:checked');
        let idsArray = Array.from(checkedItems).map(cb => parseInt(cb.value));

        let confirmMsg = `⚠️ CẢNH BÁO: HÀNH ĐỘNG KHÔNG THỂ KHÔI PHỤC!\n\nBạn có chắc chắn muốn xóa hàng loạt ${idsArray.length} phiếu chuyển quỹ nội bộ đã chọn?\n(Các số liệu báo cáo tài chính liên quan sẽ bị điều chỉnh vĩnh viễn)`;

        if (confirm(confirmMsg)) {
            fetch('index.php?action=api_delete_bulk_fund', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: idsArray
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert(res.msg);
                        // Xóa mượt các dòng tr trên DOM mà không cần reload trang
                        idsArray.forEach(id => {
                            let row = document.getElementById('fund_row_' + id);
                            if (row) row.remove();
                        });
                        document.getElementById('btn_bulk_delete').style.display = 'none';
                        document.getElementById('chk_all_funds').checked = false;
                    } else {
                        alert("Lỗi: " + res.msg);
                    }
                });
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
