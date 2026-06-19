<?php

/** @var array $customers */
/** @var array $suppliers */
/** @var array $employees */
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

    .form-control:focus {
        border-color: #0088ff;
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
        padding: 10px 22px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .debt-box {
        background: #fff8ea;
        border: 1px solid #ffea8a;
        padding: 15px;
        border-radius: 6px;
        margin-top: 15px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">📝 Tạo phiếu chi quỹ thủ công</div>
    <button type="button" class="btn-danger" onclick="document.getElementById('frm_expense_manual').submit()">📤 Lưu & Hoàn tất chi tiền</button>
</div>

<form id="frm_expense_manual" action="index.php?action=store_expense" method="POST">
    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">1. Thông tin hạch toán dòng tiền ra</div>
            <div class="card-body">

                <div class="form-group">
                    <label>Loại quỹ chi tiền <span>*</span></label>
                    <div class="radio-group">
                        <label style="cursor:pointer;"><input type="radio" name="payment_method" value="cash" checked onchange="toggleFundType()"> 💵 Tiền mặt (Quỹ chi nhánh)</label>
                        <label style="cursor:pointer;"><input type="radio" name="payment_method" value="bank" onchange="toggleFundType()"> 🏦 Tài khoản ngân hàng</label>
                    </div>
                </div>

                <div id="bank_select_block" class="form-group" style="display:none;">
                    <label>Sổ tài khoản ngân hàng chi tiền <span>*</span></label>
                    <select name="bank_account_id" class="form-control">
                        <?php foreach ($bank_accounts as $ba): ?>
                            <option value="<?php echo $ba['id']; ?>"><?php echo htmlspecialchars($ba['bank_name'] . ' (' . $ba['account_number'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhóm đối tượng nhận tiền <span>*</span></label>
                    <select name="recipient_group" id="recipient_group" class="form-control" onchange="onRecipientGroupChange()">
                        <option value="customer">Khách hàng</option>
                        <option value="supplier">Nhà cung cấp</option>
                        <option value="employee">Nhân viên hệ thống</option>
                        <option value="other">Đối tượng khác (Chi ngoài vận hành)</option>
                    </select>
                </div>

                <div class="form-group" id="target_select_block">
                    <label>Chọn đối tượng nhận tương ứng <span>*</span></label>
                    <select name="recipient_id" id="recipient_id" class="form-control" onchange="syncRecipientName()"></select>
                </div>

                <div class="form-group">
                    <label>Tên người nhận tiền thực tế <span>*</span></label>
                    <input type="text" name="recipient_name" id="recipient_name" class="form-control" required placeholder="Nhập họ tên người nhận...">
                </div>

                <div class="form-group">
                    <label>Giá trị chi tiền (VNĐ) <span>*</span></label>
                    <input type="number" name="amount" class="form-control" style="font-size:18px; font-weight:bold; color:#d82c0d;" required min="1" placeholder="Nhập số tiền...">
                </div>

            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. Lý do chi & Chứng từ đi kèm</div>
            <div class="card-body">

                <div class="form-group">
                    <label>Lý do xuất quỹ chi <span>*</span></label>
                    <select name="expense_reason" id="expense_reason" class="form-control" onchange="onReasonChange()">
                    </select>
                </div>

                <div class="form-group">
                    <label>Nhóm chi phí hạch toán báo cáo</label>
                    <input type="text" name="expense_category" id="expense_category" class="form-control" readonly style="background:#f4f6f8; font-weight:600; color:#e67e22;">
                </div>

                <div class="form-group">
                    <label>Diễn giải nội dung chi chi tiết <span>*</span></label>
                    <textarea name="description" class="form-control" rows="2" required placeholder="Ví dụ: Chi trả tiền lương tháng 6, chi tiền điện nước..."></textarea>
                </div>

                <div class="form-group">
                    <label>Chi nhánh xuất quỹ <span>*</span></label>
                    <select name="branch_id" class="form-control">
                        <?php foreach ($branches as $br): ?>
                            <option value="<?php echo $br['id']; ?>"><?php echo htmlspecialchars($br['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ngày chi tiền thực tế <span>*</span></label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control" required>
                </div>

                <div class="grid-2" style="gap:10px;">
                    <div class="form-group">
                        <label>Mã phiếu chi</label>
                        <input type="text" name="expense_code" class="form-control" placeholder="Hệ thống tự sinh">
                    </div>
                    <div class="form-group">
                        <label>Mã tham chiếu (nếu có)</label>
                        <input type="text" name="reference_code" class="form-control" placeholder="Mã UNC, hóa đơn...">
                    </div>
                </div>

                <div id="debt_impact_block" class="debt-box">
                    <input type="checkbox" name="is_debt_affected" id="is_debt_affected" value="1" style="width:18px; height:18px; cursor:pointer;">
                    <div>
                        <label for="is_debt_affected" style="font-weight:bold; color:#8a6100; cursor:pointer;">Cập nhật hạch toán vào công nợ đối tượng</label>
                        <p style="margin:4px 0 0 0; font-size:12px; color:#8a6100;">Tích chọn nếu muốn số tiền chi này tính tăng nợ phải thu của khách hàng.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    // Data nạp động từ PHP sang JS để vẽ giao diện thần tốc
    const dataSources = {
        customer: <?php echo json_encode(array_map(function ($c) {
                        return ['id' => $c['id'], 'name' => $c['last_name'] . ' ' . $c['first_name']];
                    }, $customers)); ?>,
        supplier: <?php echo json_encode(array_map(function ($s) {
                        return ['id' => $s['id'], 'name' => $s['supplier_name']];
                    }, $suppliers)); ?>,
        employee: <?php echo json_encode(array_map(function ($e) {
                        return ['id' => $e['id'], 'name' => $e['full_name']];
                    }, $employees)); ?>,
        other: []
    };

    // Quy tắc Lý do chi & Nhóm chi phí khớp 100% tài liệu Sapo
    const reasonConfig = {
        customer: [{
                reason: 'Hoàn tiền trả hàng',
                cat: 'Chi phí hoàn hàng'
            },
            {
                reason: 'Chi hỗ trợ / Khuyến mại',
                cat: 'Chi phí Marketing'
            }
        ],
        supplier: [{
                reason: 'Thanh toán tiền hàng nhập',
                cat: 'Giá vốn hàng bán'
            },
            {
                reason: 'Chi phạt hợp đồng NCC',
                cat: 'Chi phí phát sinh'
            }
        ],
        employee: [{
                reason: 'Chi trả tiền lương nhân viên',
                cat: 'Chi phí nhân sự'
            },
            {
                reason: 'Chi tiền tạm ứng nhân viên',
                cat: 'Chi phí vận hành'
            }
        ],
        other: [{
                reason: 'Thanh toán hóa đơn điện nước',
                cat: 'Chi phí tiện ích văn phòng'
            },
            {
                reason: 'Chi trả lãi vay ngân hàng',
                cat: 'Chi phí tài chính'
            },
            {
                reason: 'Chi phí khác',
                cat: 'Chi phí ngoài vận hành'
            }
        ]
    };

    // Tự động điền ngày giờ hiện tại vào form
    window.onload = function() {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('transaction_date').value = now.toISOString().slice(0, 16);
        onRecipientGroupChange(); // Chạy khởi tạo dropdown lần đầu
    };

    function toggleFundType() {
        let isBank = document.querySelector('input[name="payment_method"]:checked').value === 'bank';
        document.getElementById('bank_select_block').style.display = isBank ? 'block' : 'none';
    }

    // Sự kiện thay đổi nhóm đối tượng nhận -> Đổi list Tên + list Lý do chi
    function onRecipientGroupChange() {
        let group = document.getElementById('recipient_group').value;
        let idSelect = document.getElementById('recipient_id');
        let block = document.getElementById('target_select_block');
        let debtBlock = document.getElementById('debt_impact_block');

        // 1. Thay đổi danh sách người nhận
        if (group === 'other') {
            block.style.display = 'none';
            idSelect.innerHTML = '';
            document.getElementById('recipient_name').value = '';
            document.getElementById('recipient_name').readOnly = false;
            debtBlock.style.display = 'none'; // Chỉ khách hàng mới hiện ảnh hưởng công nợ
        } else {
            block.style.display = 'block';
            let list = dataSources[group];
            idSelect.innerHTML = list.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
            document.getElementById('recipient_name').readOnly = true;
            syncRecipientName();

            // Hiện checkbox công nợ nếu đối tượng là khách hàng
            debtBlock.style.display = (group === 'customer') ? 'flex' : 'none';
        }

        // 2. Thay đổi danh sách Lý do chi
        let reasonSelect = document.getElementById('expense_reason');
        let reasons = reasonConfig[group];
        reasonSelect.innerHTML = reasons.map(r => `<option value="${r.reason}">${r.reason}</option>`).join('');
        onReasonChange();
    }

    function syncRecipientName() {
        let select = document.getElementById('recipient_id');
        if (select.options.length > 0) {
            document.getElementById('recipient_name').value = select.options[select.selectedIndex].text;
        }
    }

    function onReasonChange() {
        let group = document.getElementById('recipient_group').value;
        let reasonVal = document.getElementById('expense_reason').value;
        let matched = reasonConfig[group].find(r => r.reason === reasonVal);
        if (matched) {
            document.getElementById('expense_category').value = matched.cat;
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
