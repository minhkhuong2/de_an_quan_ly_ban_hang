<?php

/** @var array $customers */
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

    .radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
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
</style>

<div class="v3-header">
    <div class="v3-title">Tạo phiếu thu công nợ khách hàng</div>
    <button type="button" class="btn-primary" onclick="document.getElementById('frm_receipt').submit()">💾 Hoàn tất tạo phiếu</button>
</div>

<form id="frm_receipt" action="index.php?action=store_receipt" method="POST">
    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">1. Thông tin phiếu thu</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Hình thức thanh toán <span>*</span></label>
                    <div class="radio-group">
                        <label><input type="radio" name="payment_method" value="cash" checked onchange="toggleBank()"> Tiền mặt</label>
                        <label><input type="radio" name="payment_method" value="bank" onchange="toggleBank()"> Chuyển khoản</label>
                    </div>
                </div>

                <div id="bank_block" class="form-group" style="display:none;">
                    <label>Tài khoản nhận tiền <span>*</span></label>
                    <select name="bank_account_id" class="form-control">
                        <?php foreach ($bank_accounts as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo $b['bank_name'] . ' - ' . $b['account_number']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Đối tượng nộp (Khách hàng đang có nợ) <span>*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control" onchange="updateMaxAmount()" required>
                        <option value="">-- Chọn khách hàng --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?php echo $c['id']; ?>" data-debt="<?php echo $c['debt']; ?>">
                                <?php echo htmlspecialchars($c['last_name'] . ' ' . $c['first_name'] . ' (' . $c['phone'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Số tiền thu (VNĐ) <span>*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control" style="font-size:18px; font-weight:bold; color:#108043;" required min="1">
                    <div id="debt_hint" style="font-size:13px; color:#d82c0d; margin-top:5px; display:none;">
                        * Dư nợ hiện tại: <b id="max_debt_text">0</b> đ (Không được thu vượt mức này)
                    </div>
                </div>

                <div class="form-group">
                    <label>Thứ tự thanh toán đơn hàng (Thuật toán rải tiền) <span>*</span></label>
                    <select name="payment_strategy" class="form-control">
                        <option value="oldest_first">Đơn hàng mua trước thanh toán trước (Mặc định)</option>
                        <option value="newest_first">Đơn hàng mua sau thanh toán trước (Ưu tiên đơn mới)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lý do / Diễn giải <span>*</span></label>
                    <textarea name="description" class="form-control" rows="2" required>Thu tiền bán hàng (Thanh toán công nợ)</textarea>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. Thông tin bổ sung</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Chi nhánh ghi nhận <span>*</span></label>
                    <select name="branch_id" class="form-control">
                        <?php foreach ($branches as $br): ?>
                            <option value="<?php echo $br['id']; ?>"><?php echo $br['branch_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ngày nhận tiền</label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Mã phiếu thu</label>
                    <input type="text" name="receipt_code" class="form-control" placeholder="Tự động sinh nếu để trống">
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    window.onload = function() {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('transaction_date').value = now.toISOString().slice(0, 16);
    };

    function toggleBank() {
        let val = document.querySelector('input[name="payment_method"]:checked').value;
        document.getElementById('bank_block').style.display = (val === 'bank') ? 'block' : 'none';
    }

    function updateMaxAmount() {
        let select = document.getElementById('customer_id');
        let option = select.options[select.selectedIndex];
        let debtHint = document.getElementById('debt_hint');
        let amountInput = document.getElementById('amount');

        if (select.value) {
            let maxDebt = parseFloat(option.getAttribute('data-debt'));
            document.getElementById('max_debt_text').innerText = new Intl.NumberFormat('vi-VN').format(maxDebt);
            debtHint.style.display = 'block';
            amountInput.max = maxDebt; // Set HTML validation
        } else {
            debtHint.style.display = 'none';
            amountInput.max = "";
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
