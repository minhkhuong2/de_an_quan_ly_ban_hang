<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $receipt
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

    .btn-success {
        background: #108043;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #receipt_print_area,
        #receipt_print_area * {
            visibility: visible;
        }

        #receipt_print_area {
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
    <div class="v3-title"><a href="index.php?action=receipt_list" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Chi tiết phiếu thu: <?php echo $receipt['receipt_code']; ?></div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" style="color:#d82c0d; border-color:#fca5a5;" onclick="processSingleDelete(<?php echo $receipt['id']; ?>)">🗑️ Xóa phiếu thu</button>
        <button class="btn-outline" onclick="window.print()">🖨️ In chứng từ thu</button>
        <button class="btn-success" onclick="document.getElementById('frm_update_receipt').submit()">💾 Lưu cập nhật</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="no-print" style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size:14px;">✅ Đã cập nhật thông tin phiếu thu thành công!</div>
<?php endif; ?>

<div id="receipt_print_area">
    <h2 class="no-print" style="display:none; text-align:center; margin-bottom:20px;" id="print_receipt_title">CHỨNG TỪ PHIẾU THU TIỀN<br><small style="font-size:14px;">Số hóa đơn: <?php echo $receipt['receipt_code']; ?></small></h2>

    <form id="frm_update_receipt" action="index.php?action=update_receipt" method="POST">
        <input type="hidden" name="id" value="<?php echo $receipt['id']; ?>">

        <div class="grid-2">
            <div class="v3-card">
                <div class="card-header no-print">1. Dữ liệu dòng tiền gốc (Cố định)</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Đối tượng nộp tiền thực tế</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($receipt['payer_name']); ?> (<?php echo $receipt['payer_group']; ?>)" disabled>
                    </div>
                    <div class="form-group">
                        <label>Tổng giá trị quỹ ghi nhận</label>
                        <input type="text" class="form-control" value="<?php echo number_format($receipt['amount'], 0, ',', '.'); ?> ₫" disabled style="color:#108043; font-weight:bold; font-size:16px;">
                    </div>
                    <div class="form-group">
                        <label>Hình thức nhận hạch toán</label>
                        <input type="text" class="form-control" value="<?php echo $receipt['payment_method'] === 'cash' ? '💵 Tiền mặt vào quỹ chi nhánh' : '🏦 Quỹ tiền gửi số tài khoản: ' . htmlspecialchars($receipt['account_number']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Thời gian hệ thống ghi nhận</label>
                        <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i:s', strtotime($receipt['created_at'])); ?>" disabled>
                    </div>
                </div>
            </div>

            <div class="v3-card">
                <div class="card-header no-print">2. Nội dung thông tin bổ sung</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Lý do ghi nhận thu quỹ</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($receipt['receipt_reason']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Mã chứng từ tham chiếu (UNC / Số hóa đơn ngân hàng)</label>
                        <input type="text" name="reference_code" class="form-control" value="<?php echo htmlspecialchars($receipt['reference_code']); ?>" placeholder="Cập nhật đối soát bổ sung...">
                    </div>
                    <div class="form-group">
                        <label>Mô tả chi tiết nội dung / Diễn giải <span>*</span></label>
                        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($receipt['description']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function processSingleDelete(id) {
        if (confirm("⚠️ CẢNH BÁO LƯU Ý: Thao tác xóa phiếu thu không thể khôi phục!\n\nNếu đây là phiếu thu nợ từ khách hàng, số nợ của khách hàng sẽ tự động tăng trở lại mức cũ. Bạn vẫn muốn xóa?")) {
            fetch('index.php?action=api_delete_receipt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id
                })
            }).then(res => res.json()).then(res => {
                alert(res.msg);
                window.location.href = 'index.php?action=receipt_list';
            });
        }
    }

    window.onbeforeprint = function() {
        document.getElementById('print_receipt_title').style.display = 'block';
    };
    window.onafterprint = function() {
        document.getElementById('print_receipt_title').style.display = 'none';
    };
</script>

<?php require_once __DIR__ . '/../layout/header.php'; ?>
