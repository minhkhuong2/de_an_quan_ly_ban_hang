<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $customers
 * @var array $suppliers
 * @var array $employees
 * @var array $branches
 * @var array $bank_accounts
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
        background: #eafff0;
        font-weight: 600;
        color: #108043;
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
    }

    .form-control:focus {
        border-color: #108043;
    }

    .radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }

    .btn-success {
        background: #108043;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .fifo-box {
        background: #eafff0;
        border: 1px solid #8ce09f;
        padding: 15px;
        border-radius: 6px;
        margin-top: 15px;
        display: none;
    }
</style>

<div class="v3-header">
    <div class="v3-title">📝 Tạo phiếu thu quỹ thủ công</div>
    <button type="button" class="btn-success" onclick="document.getElementById('frm_receipt_manual').submit()">📥 Lưu & Hoàn tất thu tiền</button>
</div>

<form id="frm_receipt_manual" action="index.php?action=store_receipt" method="POST">
    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">1. Thông tin hạch toán dòng tiền vào</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Loại quỹ nhận tiền <span>*</span></label>
                    <div class="radio-group">
                        <label><input type="radio" name="payment_method" value="cash" checked onchange="toggleFundType()"> 💵 Tiền mặt (Quỹ chi nhánh)</label>
                        <label><input type="radio" name="payment_method" value="bank" onchange="toggleFundType()"> 🏦 Tài khoản ngân hàng</label>
                    </div>
                </div>

                <div id="bank_select_block" class="form-group" style="display:none;">
                    <label>Sổ tài khoản ngân hàng nhận tiền <span>*</span></label>
                    <select name="bank_account_id" class="form-control">
                        <?php foreach ($bank_accounts as $ba): ?>
                            <option value="<?php echo $ba['id']; ?>"><?php echo htmlspecialchars($ba['bank_name'] . ' (' . $ba['account_number'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhóm đối tượng nộp <span>*</span></label>
                    <select name="payer_group" id="payer_group" class="form-control" onchange="onPayerGroupChange()">
                        <option value="customer">Khách hàng</option>
                        <option value="supplier">Nhà cung cấp</option>
                        <option value="employee">Nhân viên hệ thống</option>
                        <option value="other">Đối tượng khác</option>
                    </select>
                </div>

                <div class="form-group" id="target_select_block">
                    <label>Chọn đối tượng nộp tương ứng <span>*</span></label>
                    <select name="payer_id" id="payer_id" class="form-control" onchange="syncPayerName()"></select>
                </div>

                <div class="form-group">
                    <label>Đối tượng nộp (Tên người nộp tiền) <span>*</span></label>
                    <input type="text" name="payer_name" id="payer_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Giá trị thu (VNĐ) <span>*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control" style="font-size:18px; font-weight:bold; color:#108043;" required min="1" placeholder="Nhập số tiền...">
                    <div id="debt_hint" style="font-size:13px; color:#d82c0d; margin-top:5px; display:none;">
                        * Khách đang nợ: <b id="max_debt_text">0</b> đ (Thuật toán sẽ tự rải trừ nợ)
                    </div>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. Lý do thu & Thông tin bổ sung</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Lý do thu <span>*</span></label>
                    <select name="receipt_reason" id="receipt_reason" class="form-control">
                    </select>
                </div>

                <div id="fifo_block" class="fifo-box">
                    <label style="color:#108043; margin-bottom:5px;"><i class="fa-solid fa-wand-magic-sparkles"></i> Phân bổ trừ nợ tự động (FIFO)</label>
                    <select name="payment_strategy" class="form-control" style="border-color:#8ce09f; background:#fff;">
                        <option value="oldest_first">Đơn hàng mua trước thanh toán trước (Mặc định)</option>
                        <option value="newest_first">Đơn hàng mua sau thanh toán trước</option>
                    </select>
                    <p style="font-size:12px; color:#108043; margin-top:5px; margin-bottom:0;">Hệ thống sẽ dùng số tiền thu được để "rải" thanh toán tự động cho các hóa đơn đang nợ của khách hàng này.</p>
                </div>

                <div class="form-group" style="margin-top:15px;">
                    <label>Diễn giải nội dung <span>*</span></label>
                    <textarea name="description" class="form-control" rows="2" required placeholder="Ghi chú chi tiết khoản thu..."></textarea>
                </div>

                <div class="form-group">
                    <label>Chi nhánh nhận <span>*</span></label>
                    <select name="branch_id" class="form-control">
                        <?php foreach ($branches as $br): ?>
                            <option value="<?php echo $br['id']; ?>"><?php echo htmlspecialchars($br['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid-2" style="gap:10px;">
                    <div class="form-group">
                        <label>Ngày nhận tiền <span>*</span></label>
                        <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Mã phiếu thu</label>
                        <input type="text" name="receipt_code" class="form-control" placeholder="Tự sinh mã">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const dataSources = {
        customer: <?php echo json_encode(array_map(function ($c) {
                        return ['id' => $c['id'], 'name' => $c['last_name'] . ' ' . $c['first_name'], 'debt' => $c['debt']];
                    }, $customers)); ?>,
        supplier: <?php echo json_encode(array_map(function ($s) {
                        return ['id' => $s['id'], 'name' => $s['supplier_name']];
                    }, $suppliers)); ?>,
        employee: <?php echo json_encode(array_map(function ($e) {
                        return ['id' => $e['id'], 'name' => $e['full_name']];
                    }, $employees)); ?>,
        other: []
    };

    const reasonConfig = {
        customer: ['Thu tiền bán hàng (Thanh toán công nợ)', 'Khách hàng hoàn ứng', 'Thu nhập khác từ khách'],
        supplier: ['Nhà cung cấp hoàn tiền', 'Thu chiết khấu thương mại'],
        employee: ['Nhân viên hoàn ứng', 'Thu bồi thường từ nhân viên'],
        other: ['Thu lãi tiền gửi ngân hàng', 'Thu thanh lý tài sản', 'Khác']
    };

    window.onload = function() {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('transaction_date').value = now.toISOString().slice(0, 16);
        onPayerGroupChange();
    };

    function toggleFundType() {
        let isBank = document.querySelector('input[name="payment_method"]:checked').value === 'bank';
        document.getElementById('bank_select_block').style.display = isBank ? 'block' : 'none';
    }

    function onPayerGroupChange() {
        let group = document.getElementById('payer_group').value;
        let idSelect = document.getElementById('payer_id');
        let block = document.getElementById('target_select_block');
        let fifoBlock = document.getElementById('fifo_block');
        let debtHint = document.getElementById('debt_hint');

        if (group === 'other') {
            block.style.display = 'none';
            idSelect.innerHTML = '';
            document.getElementById('payer_name').value = '';
            document.getElementById('payer_name').readOnly = false;
            fifoBlock.style.display = 'none';
            debtHint.style.display = 'none';
        } else {
            block.style.display = 'block';
            let list = dataSources[group];
            idSelect.innerHTML = list.map(item => `<option value="${item.id}" data-debt="${item.debt || 0}">${item.name}</option>`).join('');
            document.getElementById('payer_name').readOnly = true;
            syncPayerName();

            fifoBlock.style.display = (group === 'customer') ? 'block' : 'none';
        }

        let reasonSelect = document.getElementById('receipt_reason');
        reasonSelect.innerHTML = reasonConfig[group].map(r => `<option value="${r}">${r}</option>`).join('');
    }

    function syncPayerName() {
        let select = document.getElementById('payer_id');
        if (select.options.length > 0) {
            let opt = select.options[select.selectedIndex];
            document.getElementById('payer_name').value = opt.text;

            if (document.getElementById('payer_group').value === 'customer') {
                let debt = parseFloat(opt.getAttribute('data-debt'));
                document.getElementById('max_debt_text').innerText = new Intl.NumberFormat('vi-VN').format(debt);
                document.getElementById('debt_hint').style.display = debt > 0 ? 'block' : 'none';
                document.getElementById('amount').max = debt;
            } else {
                document.getElementById('debt_hint').style.display = 'none';
                document.getElementById('amount').max = "";
            }
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
