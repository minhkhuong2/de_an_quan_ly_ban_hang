<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
$expense = $expense ?? [
    'id' => 0,
    'expense_code' => '---',
    'recipient_name' => '',
    'amount' => 0,
    'payment_method' => 'cash',
    'bank_name' => '',
    'transaction_date' => date('Y-m-d H:i:s'),
    'expense_reason' => '',
    'reference_code' => '',
    'description' => ''
];
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
        background: #fff;
    }

    .form-control:disabled {
        background: #f4f6f8;
        color: #919eab;
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
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    /* CSS ẢO THUẬT: CẤU HÌNH IN PHIẾU CHUẨN MÁY IN POS */
    @media print {
        body * {
            visibility: hidden;
        }

        #print_area,
        #print_area * {
            visibility: visible;
        }

        #print_area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: #fff;
            padding: 20px;
        }

        .no-print {
            display: none !important;
        }

        .v3-card {
            border: none;
            box-shadow: none;
        }

        .form-control {
            border: none;
            padding: 0;
            background: none;
            font-weight: bold;
            color: #000;
        }

        .form-group label {
            color: #666;
            font-size: 12px;
        }
    }
</style>

<div class="v3-header no-print">
    <div class="v3-title"><a href="index.php?action=expense_list" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Chi tiết phiếu chi: <?php echo $expense['expense_code']; ?></div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" style="color:#d82c0d; border-color:#fca5a5;" onclick="processSingleDelete(<?php echo $expense['id']; ?>)">🗑️ Xóa phiếu</button>
        <button class="btn-outline" onclick="window.print()">🖨️ In phiếu chi</button>
        <button class="btn-primary" onclick="document.getElementById('frm_update').submit()">💾 Lưu cập nhật</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="no-print" style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size:14px;">✅ Cập nhật thông tin phiếu chi thành công!</div>
<?php endif; ?>

<div id="print_area">
    <h2 class="no-print" style="display:none; text-align:center; margin-bottom:20px;" id="print_title">PHIẾU CHI TIỀN<br><small style="font-size:14px;">Mã phiếu: <?php echo $expense['expense_code']; ?></small></h2>

    <form id="frm_update" action="index.php?action=update_expense" method="POST">
        <input type="hidden" name="id" value="<?php echo $expense['id']; ?>">

        <div class="grid-2">
            <div class="v3-card">
                <div class="card-header no-print">1. Thông tin giao dịch (Không thể sửa)</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tên người nhận tiền</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($expense['recipient_name']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Số tiền đã chi</label>
                        <input type="text" class="form-control" value="<?php echo number_format($expense['amount'], 0, ',', '.'); ?> ₫" disabled style="color:#d82c0d; font-weight:bold; font-size:16px;">
                    </div>
                    <div class="form-group">
                        <label>Phương thức thanh toán / Tài khoản</label>
                        <input type="text" class="form-control" value="<?php echo $expense['payment_method'] == 'cash' ? 'Tiền mặt' : 'Chuyển khoản: ' . htmlspecialchars($expense['bank_name']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Ngày chi tiền (Vào sổ quỹ)</label>
                        <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($expense['transaction_date'])); ?>" disabled>
                    </div>
                </div>
            </div>

            <div class="v3-card">
                <div class="card-header no-print">2. Thông tin có thể cập nhật</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Lý do chi phí</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($expense['expense_reason']); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Tham chiếu đối soát <span>*</span></label>
                        <input type="text" name="reference_code" class="form-control" value="<?php echo htmlspecialchars($expense['reference_code']); ?>" placeholder="Cập nhật mã UNC, Hóa đơn...">
                    </div>
                    <div class="form-group">
                        <label>Diễn giải nội dung <span>*</span></label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($expense['description']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // JS Xóa phiếu chi đơn lẻ
    function processSingleDelete(id) {
        if (confirm("⚠️ CẢNH BÁO: Xóa phiếu chi sẽ hoàn tác dòng tiền và trừ ngược lại công nợ khách hàng (nếu có).\nBạn có thực sự muốn xóa?")) {
            fetch('index.php?action=api_delete_expense', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                })
            }).then(res => res.json()).then(res => {
                alert(res.msg);
                window.location.href = 'index.php?action=expense_list';
            });
        }
    }

    // Thủ thuật JS để hiển thị Tiêu đề khi ấn nút In
    window.onbeforeprint = function() {
        document.getElementById('print_title').style.display = 'block';
    };
    window.onafterprint = function() {
        document.getElementById('print_title').style.display = 'none';
    };
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
