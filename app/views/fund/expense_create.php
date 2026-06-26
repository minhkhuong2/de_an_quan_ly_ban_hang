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
    <div class="v3-title">ðŸ“ Táº¡o phiáº¿u chi quá»¹ thá»§ cÃ´ng</div>
    <button type="button" class="btn-danger" onclick="document.getElementById('frm_expense_manual').submit()">ðŸ“¤ LÆ°u & HoÃ n táº¥t chi tiá»n</button>
</div>

<form id="frm_expense_manual" action="index.php?action=store_expense" method="POST">
    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">1. ThÃ´ng tin háº¡ch toÃ¡n dÃ²ng tiá»n ra</div>
            <div class="card-body">

                <div class="form-group">
                    <label>Loáº¡i quá»¹ chi tiá»n <span>*</span></label>
                    <div class="radio-group">
                        <label style="cursor:pointer;"><input type="radio" name="payment_method" value="cash" checked onchange="toggleFundType()"> ðŸ’µ Tiá»n máº·t (Quá»¹ chi nhÃ¡nh)</label>
                        <label style="cursor:pointer;"><input type="radio" name="payment_method" value="bank" onchange="toggleFundType()"> ðŸ¦ TÃ i khoáº£n ngÃ¢n hÃ ng</label>
                    </div>
                </div>

                <div id="bank_select_block" class="form-group" style="display:none;">
                    <label>Sá»• tÃ i khoáº£n ngÃ¢n hÃ ng chi tiá»n <span>*</span></label>
                    <select name="bank_account_id" class="form-control">
                        <?php foreach ($bank_accounts as $ba): ?>
                            <option value="<?php echo $ba['id']; ?>"><?php echo htmlspecialchars($ba['bank_name'] . ' (' . $ba['account_number'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>NhÃ³m Ä‘á»‘i tÆ°á»£ng nháº­n tiá»n <span>*</span></label>
                    <select name="recipient_group" id="recipient_group" class="form-control" onchange="onRecipientGroupChange()">
                        <option value="customer">KhÃ¡ch hÃ ng</option>
                        <option value="supplier">NhÃ  cung cáº¥p</option>
                        <option value="employee">NhÃ¢n viÃªn há»‡ thá»‘ng</option>
                        <option value="other">Äá»‘i tÆ°á»£ng khÃ¡c (Chi ngoÃ i váº­n hÃ nh)</option>
                    </select>
                </div>

                <div class="form-group" id="target_select_block">
                    <label>Chá»n Ä‘á»‘i tÆ°á»£ng nháº­n tÆ°Æ¡ng á»©ng <span>*</span></label>
                    <select name="recipient_id" id="recipient_id" class="form-control" onchange="syncRecipientName()"></select>
                </div>

                <div class="form-group">
                    <label>TÃªn ngÆ°á»i nháº­n tiá»n thá»±c táº¿ <span>*</span></label>
                    <input type="text" name="recipient_name" id="recipient_name" class="form-control" required placeholder="Nháº­p há» tÃªn ngÆ°á»i nháº­n...">
                </div>

                <div class="form-group">
                    <label>GiÃ¡ trá»‹ chi tiá»n (VNÄ) <span>*</span></label>
                    <input type="number" name="amount" class="form-control" style="font-size:18px; font-weight:bold; color:#d82c0d;" required min="1" placeholder="Nháº­p sá»‘ tiá»n...">
                </div>

            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. LÃ½ do chi & Chá»©ng tá»« Ä‘i kÃ¨m</div>
            <div class="card-body">

                <div class="form-group">
                    <label>LÃ½ do xuáº¥t quá»¹ chi <span>*</span></label>
                    <select name="expense_reason" id="expense_reason" class="form-control" onchange="onReasonChange()">
                    </select>
                </div>

                <div class="form-group">
                    <label>NhÃ³m chi phÃ­ háº¡ch toÃ¡n bÃ¡o cÃ¡o</label>
                    <input type="text" name="expense_category" id="expense_category" class="form-control" readonly style="background:#f4f6f8; font-weight:600; color:#e67e22;">
                </div>

                <div class="form-group">
                    <label>Diá»…n giáº£i ná»™i dung chi chi tiáº¿t <span>*</span></label>
                    <textarea name="description" class="form-control" rows="2" required placeholder="VÃ­ dá»¥: Chi tráº£ tiá»n lÆ°Æ¡ng thÃ¡ng 6, chi tiá»n Ä‘iá»‡n nÆ°á»›c..."></textarea>
                </div>

                <div class="form-group">
                    <label>Chi nhÃ¡nh xuáº¥t quá»¹ <span>*</span></label>
                    <select name="branch_id" class="form-control">
                        <?php foreach ($branches as $br): ?>
                            <option value="<?php echo $br['id']; ?>"><?php echo htmlspecialchars($br['branch_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>NgÃ y chi tiá»n thá»±c táº¿ <span>*</span></label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control" required>
                </div>

                <div class="grid-2" style="gap:10px;">
                    <div class="form-group">
                        <label>MÃ£ phiáº¿u chi</label>
                        <input type="text" name="expense_code" class="form-control" placeholder="Há»‡ thá»‘ng tá»± sinh">
                    </div>
                    <div class="form-group">
                        <label>MÃ£ tham chiáº¿u (náº¿u cÃ³)</label>
                        <input type="text" name="reference_code" class="form-control" placeholder="MÃ£ UNC, hÃ³a Ä‘Æ¡n...">
                    </div>
                </div>

                <div id="debt_impact_block" class="debt-box">
                    <input type="checkbox" name="is_debt_affected" id="is_debt_affected" value="1" style="width:18px; height:18px; cursor:pointer;">
                    <div>
                        <label for="is_debt_affected" style="font-weight:bold; color:#8a6100; cursor:pointer;">Cáº­p nháº­t háº¡ch toÃ¡n vÃ o cÃ´ng ná»£ Ä‘á»‘i tÆ°á»£ng</label>
                        <p style="margin:4px 0 0 0; font-size:12px; color:#8a6100;">TÃ­ch chá»n náº¿u muá»‘n sá»‘ tiá»n chi nÃ y tÃ­nh tÄƒng ná»£ pháº£i thu cá»§a khÃ¡ch hÃ ng.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    // Data náº¡p Ä‘á»™ng tá»« PHP sang JS Ä‘á»ƒ váº½ giao diá»‡n tháº§n tá»‘c
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

    // Quy táº¯c LÃ½ do chi & NhÃ³m chi phÃ­ khá»›p 100% tÃ i liá»‡u Há»‡ thá»‘ng
    const reasonConfig = {
        customer: [{
                reason: 'HoÃ n tiá»n tráº£ hÃ ng',
                cat: 'Chi phÃ­ hoÃ n hÃ ng'
            },
            {
                reason: 'Chi há»— trá»£ / Khuyáº¿n máº¡i',
                cat: 'Chi phÃ­ Marketing'
            }
        ],
        supplier: [{
                reason: 'Thanh toÃ¡n tiá»n hÃ ng nháº­p',
                cat: 'GiÃ¡ vá»‘n hÃ ng bÃ¡n'
            },
            {
                reason: 'Chi pháº¡t há»£p Ä‘á»“ng NCC',
                cat: 'Chi phÃ­ phÃ¡t sinh'
            }
        ],
        employee: [{
                reason: 'Chi tráº£ tiá»n lÆ°Æ¡ng nhÃ¢n viÃªn',
                cat: 'Chi phÃ­ nhÃ¢n sá»±'
            },
            {
                reason: 'Chi tiá»n táº¡m á»©ng nhÃ¢n viÃªn',
                cat: 'Chi phÃ­ váº­n hÃ nh'
            }
        ],
        other: [{
                reason: 'Thanh toÃ¡n hÃ³a Ä‘Æ¡n Ä‘iá»‡n nÆ°á»›c',
                cat: 'Chi phÃ­ tiá»‡n Ã­ch vÄƒn phÃ²ng'
            },
            {
                reason: 'Chi tráº£ lÃ£i vay ngÃ¢n hÃ ng',
                cat: 'Chi phÃ­ tÃ i chÃ­nh'
            },
            {
                reason: 'Chi phÃ­ khÃ¡c',
                cat: 'Chi phÃ­ ngoÃ i váº­n hÃ nh'
            }
        ]
    };

    // Tá»± Ä‘á»™ng Ä‘iá»n ngÃ y giá» hiá»‡n táº¡i vÃ o form
    window.onload = function() {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('transaction_date').value = now.toISOString().slice(0, 16);
        onRecipientGroupChange(); // Cháº¡y khá»Ÿi táº¡o dropdown láº§n Ä‘áº§u
    };

    function toggleFundType() {
        let isBank = document.querySelector('input[name="payment_method"]:checked').value === 'bank';
        document.getElementById('bank_select_block').style.display = isBank ? 'block' : 'none';
    }

    // Sá»± kiá»‡n thay Ä‘á»•i nhÃ³m Ä‘á»‘i tÆ°á»£ng nháº­n -> Äá»•i list TÃªn + list LÃ½ do chi
    function onRecipientGroupChange() {
        let group = document.getElementById('recipient_group').value;
        let idSelect = document.getElementById('recipient_id');
        let block = document.getElementById('target_select_block');
        let debtBlock = document.getElementById('debt_impact_block');

        // 1. Thay Ä‘á»•i danh sÃ¡ch ngÆ°á»i nháº­n
        if (group === 'other') {
            block.style.display = 'none';
            idSelect.innerHTML = '';
            document.getElementById('recipient_name').value = '';
            document.getElementById('recipient_name').readOnly = false;
            debtBlock.style.display = 'none'; // Chá»‰ khÃ¡ch hÃ ng má»›i hiá»‡n áº£nh hÆ°á»Ÿng cÃ´ng ná»£
        } else {
            block.style.display = 'block';
            let list = dataSources[group];
            idSelect.innerHTML = list.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
            document.getElementById('recipient_name').readOnly = true;
            syncRecipientName();

            // Hiá»‡n checkbox cÃ´ng ná»£ náº¿u Ä‘á»‘i tÆ°á»£ng lÃ  khÃ¡ch hÃ ng
            debtBlock.style.display = (group === 'customer') ? 'flex' : 'none';
        }

        // 2. Thay Ä‘á»•i danh sÃ¡ch LÃ½ do chi
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

