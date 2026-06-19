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
        color: #d82c0d;
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

    .btn-danger {
        background: #d82c0d;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .debt-checkbox-box {
        background: #fff8ea;
        border: 1px solid #ffea8a;
        padding: 15px;
        border-radius: 6px;
        margin-top: 10px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .debt-checkbox-box input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        margin-top: 2px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Tạo phiếu chi tiền cho khách hàng</div>
    <button type="button" class="btn-danger" onclick="document.getElementById('frm_expense').submit()">📤 Hoàn tất chi tiền</button>
</div>

<form id="frm_expense" action="index.php?action=store_expense" method="POST">
    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">1. Thông tin phiếu chi (Xuất tiền)</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Hình thức chi tiền <span>*</span></label>
                    <div class="radio-group">
                        <label><input type="radio" name="payment_method" value="cash" checked onchange="toggleBank()"> Tiền mặt</label>
                        <label><input type="radio" name="payment_method" value="bank" onchange="toggleBank()"> Chuyển khoản ngân hàng</label>
                    </div>
                </div>

                <div id="bank_block" class="form-group" style="display:none;">
                    <label>Tài khoản xuất tiền <span>*</span></label>
                    <select name="bank_account_id" class="form-control">
                        <?php foreach ($bank_accounts as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo $b['bank_name'] . ' - ' . $b['account_number']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Đối tượng nhận tiền <span>*</span></label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">-- Chọn khách hàng --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['last_name'] . ' ' . $c['first_name'] . ' (' . $c['phone'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Số tiền chi (VNĐ) <span>*</span></label>
                    <input type="number" name="amount" class="form-control" style="font-size:18px; font-weight:bold; color:#d82c0d;" required min="1" placeholder="0">
                </div>

                <div class="debt-checkbox-box">
                    <input type="checkbox" name="is_debt_affected" id="chk_debt" value="1">
                    <div>
                        <label for="chk_debt" style="margin: 0; font-size: 15px; color: #8a6100;"><b>Hạch toán tăng công nợ khách hàng</b></label>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #8a6100;">Nếu tích chọn, số tiền này sẽ được cộng thêm vào công nợ (khách hàng sẽ nợ cửa hàng khoản tiền này).</p>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Lý do chi <span>*</span></label>
                    <textarea name="description" class="form-control" rows="2" required>Chi trả khách hàng...</textarea>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. Thông tin bổ sung</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Chi nhánh xuất tiền <span>*</span></label>
                    <select name="branch_id" class="form-control">
                        <?php foreach ($branches as $br): ?>
                            <option value="<?php echo $br['id']; ?>"><?php echo $br['branch_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ngày chi tiền</label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Mã phiếu chi</label>
                    <input type="text" name="expense_code" class="form-control" placeholder="Tự động sinh nếu để trống">
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
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
