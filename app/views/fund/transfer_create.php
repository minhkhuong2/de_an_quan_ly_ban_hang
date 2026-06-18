<?php

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
        color: #212b36;
        font-size: 15px;
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 8px;
        color: #212b36;
    }

    .form-group label span {
        color: #d82c0d;
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

    .form-control:focus {
        border-color: #0088ff;
    }

    .radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
    }

    .radio-item input {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=fund_transfers" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Tạo phiếu chuyển quỹ nội bộ</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="window.location.href='index.php?action=fund_transfers'">Hủy</button>
        <button class="btn-primary" onclick="document.getElementById('frm_transfer').submit()">💾 Lưu phiếu chuyển quỹ</button>
    </div>
</div>

<form id="frm_transfer" action="index.php?action=store_fund_transfer" method="POST">

    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">1. Quỹ chuyển tiền (Nguồn)</div>
            <div class="card-body">
                <div class="radio-group">
                    <label class="radio-item">
                        <input type="radio" name="from_type" value="cash" checked onchange="toggleFromType()"> Tiền mặt
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="from_type" value="bank" onchange="toggleFromType()"> Tài khoản ngân hàng
                    </label>
                </div>

                <div id="from_branch_block" class="form-group">
                    <label>Chi nhánh nộp tiền <span>*</span></label>
                    <select name="from_branch_id" class="form-control">
                        <?php foreach ($branches as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="from_bank_block" class="form-group" style="display: none;">
                    <label>Tài khoản chuyển tiền <span>*</span></label>
                    <select name="from_bank_id" class="form-control">
                        <?php foreach ($bank_accounts as $bank): ?>
                            <option value="<?php echo $bank['id']; ?>"><?php echo $bank['bank_name'] . ' - ' . $bank['account_number'] . ' (' . $bank['account_name'] . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. Quỹ nhận tiền (Đích)</div>
            <div class="card-body">
                <div class="radio-group">
                    <label class="radio-item">
                        <input type="radio" name="to_type" value="bank" checked onchange="toggleToType()"> Tài khoản ngân hàng
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="to_type" value="cash" onchange="toggleToType()"> Tiền mặt
                    </label>
                </div>

                <div id="to_branch_block" class="form-group" style="display: none;">
                    <label>Chi nhánh nhận tiền <span>*</span></label>
                    <select name="to_branch_id" class="form-control">
                        <?php foreach ($branches as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="to_bank_block" class="form-group">
                    <label>Tài khoản nhận tiền <span>*</span></label>
                    <select name="to_bank_id" class="form-control">
                        <?php foreach ($bank_accounts as $bank): ?>
                            <option value="<?php echo $bank['id']; ?>"><?php echo $bank['bank_name'] . ' - ' . $bank['account_number'] . ' (' . $bank['account_name'] . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="v3-card">
        <div class="card-header">3. Thông tin bổ sung</div>
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label>Giá trị (VNĐ) <span>*</span></label>
                    <input type="number" name="amount" class="form-control" style="font-size: 18px; font-weight: bold; color: #0088ff;" placeholder="0" required min="1">
                </div>
                <div class="form-group">
                    <label>Mã phiếu chuyển quỹ</label>
                    <input type="text" name="transfer_code" class="form-control" placeholder="Tự động sinh nếu để trống">
                </div>
                <div class="form-group">
                    <label>Ngày nhận tiền (Ngày giao dịch) <span>*</span></label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tham chiếu</label>
                    <input type="text" name="reference_code" class="form-control" placeholder="Mã giao dịch ngân hàng, UNC...">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Diễn giải lý do chuyển quỹ <span>*</span></label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Nhập lý do chuyển quỹ..." required></textarea>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Set thời gian hiện tại cho ô Ngày nhận tiền
    window.addEventListener('DOMContentLoaded', () => {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('transaction_date').value = now.toISOString().slice(0, 16);
    });

    // JS Đổi giao diện Quỹ Nguồn
    function toggleFromType() {
        let val = document.querySelector('input[name="from_type"]:checked').value;
        document.getElementById('from_branch_block').style.display = (val === 'cash') ? 'block' : 'none';
        document.getElementById('from_bank_block').style.display = (val === 'bank') ? 'block' : 'none';
    }

    // JS Đổi giao diện Quỹ Đích
    function toggleToType() {
        let val = document.querySelector('input[name="to_type"]:checked').value;
        document.getElementById('to_branch_block').style.display = (val === 'cash') ? 'block' : 'none';
        document.getElementById('to_bank_block').style.display = (val === 'bank') ? 'block' : 'none';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
