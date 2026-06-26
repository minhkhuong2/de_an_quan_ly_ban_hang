<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 24px;
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
        font-size: 16px;
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
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

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    /* Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        flex-shrink: 0;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #c4cdd5;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #0088ff;
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    .toggle-row {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #dfe3e8;
    }

    .toggle-row:last-child {
        border-bottom: none;
    }

    .toggle-title {
        font-weight: 600;
        color: #212b36;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .toggle-desc {
        font-size: 13px;
        color: #637381;
        line-height: 1.4;
        margin: 0;
    }

    /* Input % */
    .input-group {
        display: flex;
        align-items: center;
    }

    .input-group input {
        border-radius: 4px 0 0 4px;
        border-right: none;
        width: 120px;
        text-align: right;
    }

    .input-group-text {
        padding: 10px 15px;
        background: #f4f6f8;
        border: 1px solid #c4cdd5;
        border-radius: 0 4px 4px 0;
        color: #637381;
        font-weight: 600;
    }

    /* Lá»›p phá»§ lÃ m má» khi táº¯t quáº£n lÃ½ thuáº¿ */
    #tax_settings_wrapper {
        transition: 0.3s;
    }

    .disabled-overlay {
        opacity: 0.5;
        pointer-events: none;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=settings_hub" style="text-decoration:none; color:#637381; margin-right:10px;">â†</a> Cáº¥u hÃ¬nh Thuáº¿ (VAT)</div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn-outline" style="color: #108043; border-color: #8ce09f; background: #eafff0;" onclick="applyPreset('hkd')">ðŸŽ¯ Cáº¥u hÃ¬nh Há»™ Kinh Doanh</button>
        <button type="button" class="btn-outline" style="color: #0056b3; border-color: #b3d4ff; background: #e5f0ff;" onclick="applyPreset('dn')">ðŸ¢ Cáº¥u hÃ¬nh Doanh Nghiá»‡p</button>
        <button type="button" class="btn-primary" onclick="document.getElementById('frm_tax').submit()">ðŸ’¾ LÆ°u cáº¥u hÃ¬nh</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">âœ… Cáº­p nháº­t cáº¥u hÃ¬nh thuáº¿ thÃ nh cÃ´ng!</div>
<?php endif; ?>

<form id="frm_tax" action="index.php?action=update_tax_settings" method="POST">

    <div class="v3-card">
        <div class="card-body">
            <div class="toggle-row" style="border:none; padding:0;">
                <label class="switch" style="margin-top: 2px;">
                    <input type="checkbox" name="is_tax_enabled" id="is_tax_enabled" value="1" <?php echo ($tax['is_tax_enabled'] ?? 0) == 1 ? 'checked' : ''; ?> onchange="toggleMasterTax()">
                    <span class="slider"></span>
                </label>
                <div>
                    <div class="toggle-title" style="font-size: 16px;">Quáº£n lÃ½ thÃ´ng tin Thuáº¿ cho cá»­a hÃ ng</div>
                    <p class="toggle-desc">Khi báº­t, há»‡ thá»‘ng sáº½ kÃ­ch hoáº¡t tÃ­nh nÄƒng thuáº¿. CÃ¡c Ä‘Æ¡n hÃ ng, Ä‘Æ¡n nháº­p hÃ ng sáº½ tá»± Ä‘á»™ng háº¡ch toÃ¡n thuáº¿ suáº¥t dá»±a theo cáº¥u hÃ¬nh bÃªn dÆ°á»›i.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="tax_settings_wrapper" class="<?php echo ($tax['is_tax_enabled'] ?? 0) == 0 ? 'disabled-overlay' : ''; ?>">

        <div class="v3-card">
            <div class="card-header">1. Cáº¥u hÃ¬nh chung</div>
            <div class="card-body" style="padding-top: 0;">

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="default_tax_sales" id="default_tax_sales" value="1" <?php echo ($tax['default_tax_sales'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">Máº·c Ä‘á»‹nh tÃ­nh thuáº¿ khi bÃ¡n hÃ ng</div>
                        <p class="toggle-desc">CÃ¡c giao dá»‹ch bÃ¡n hÃ ng má»›i (ÄÆ¡n hÃ ng Web, POS) sáº½ tá»± Ä‘á»™ng Ä‘Æ°á»£c Ã¡p dá»¥ng má»©c thuáº¿ bÃ¡n hÃ ng.</p>
                    </div>
                </div>

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="default_tax_purchases" id="default_tax_purchases" value="1" <?php echo ($tax['default_tax_purchases'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">Máº·c Ä‘á»‹nh tÃ­nh thuáº¿ khi nháº­p hÃ ng</div>
                        <p class="toggle-desc">CÃ¡c giao dá»‹ch Äáº·t hÃ ng nháº­p, Nháº­p hÃ ng sáº½ Ä‘Æ°á»£c Ã¡p dá»¥ng thuáº¿. Há»¯u Ã­ch cho doanh nghiá»‡p cáº§n quáº£n lÃ½ VAT Ä‘áº§u vÃ o.</p>
                    </div>
                </div>

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="price_includes_tax" id="price_includes_tax" value="1" <?php echo ($tax['price_includes_tax'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">GiÃ¡ Ä‘Ã£ bao gá»“m thuáº¿</div>
                        <p class="toggle-desc">
                            <b style="color:#0088ff;">Báº­t:</b> GiÃ¡ sáº£n pháº©m lÃ  giÃ¡ cuá»‘i cÃ¹ng Ä‘Ã£ cÃ³ thuáº¿ (Há»‡ thá»‘ng tá»± bÃ³c tÃ¡ch thuáº¿).<br>
                            <b style="color:#d82c0d;">Táº¯t:</b> GiÃ¡ sáº£n pháº©m lÃ  giÃ¡ chÆ°a thuáº¿ (Há»‡ thá»‘ng sáº½ cá»™ng thÃªm tiá»n thuáº¿ vÃ o tá»•ng bill).
                        </p>
                    </div>
                </div>

                <div class="toggle-row">
                    <label class="switch"><input type="checkbox" name="tax_on_shipping" id="tax_on_shipping" value="1" <?php echo ($tax['tax_on_shipping'] ?? 0) == 1 ? 'checked' : ''; ?>><span class="slider"></span></label>
                    <div>
                        <div class="toggle-title">Ghi nháº­n thuáº¿ lÃªn phÃ­ váº­n chuyá»ƒn</div>
                        <p class="toggle-desc">PhÃ­ giao hÃ ng thu cá»§a khÃ¡ch cÅ©ng sáº½ bá»‹ Ã¡p má»©c thuáº¿ váº­n chuyá»ƒn tÆ°Æ¡ng á»©ng.</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2. Má»©c Thuáº¿ suáº¥t chung (%)</div>
            <div class="card-body">
                <p style="font-size: 13px; color: #637381; margin-bottom: 20px;">Há»‡ thá»‘ng há»— trá»£ nháº­p sá»‘ tháº­p phÃ¢n tá»‘i Ä‘a 2 chá»¯ sá»‘ (vÃ­ dá»¥: 1.5, 8.00, 10.00) Ä‘á»ƒ tÃ­nh toÃ¡n chuáº©n xÃ¡c tá»«ng Ä‘á»“ng láº».</p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <div class="form-group">
                            <label class="toggle-title">Thuáº¿ nháº­p hÃ ng chung <span>*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="general_purchase_tax_rate" id="general_purchase_tax_rate" class="form-control" value="<?php echo number_format($tax['general_purchase_tax_rate'] ?? 0, 2, '.', ''); ?>" required>
                                <div class="input-group-text">%</div>
                            </div>
                            <p class="toggle-desc" style="margin-top: 5px;">Ãp dá»¥ng cho cÃ¡c Ä‘Æ¡n nháº­p kho tá»« NhÃ  cung cáº¥p.</p>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label class="toggle-title">Thuáº¿ bÃ¡n hÃ ng chung <span>*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="general_sales_tax_rate" id="general_sales_tax_rate" class="form-control" value="<?php echo number_format($tax['general_sales_tax_rate'] ?? 0, 2, '.', ''); ?>" required>
                                <div class="input-group-text">%</div>
                            </div>
                            <p class="toggle-desc" style="margin-top: 5px;">Ãp dá»¥ng cho Ä‘Æ¡n xuáº¥t bÃ¡n ra cho KhÃ¡ch hÃ ng.</p>
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px dashed #dfe3e8; margin: 20px 0;">

                <div class="form-group" style="width: 50%;">
                    <label class="toggle-title">Thuáº¿ váº­n chuyá»ƒn</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="shipping_tax_rate" id="shipping_tax_rate" class="form-control" value="<?php echo number_format($tax['shipping_tax_rate'] ?? 0, 2, '.', ''); ?>">
                        <div class="input-group-text">%</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    // Logic má»/sÃ¡ng khá»‘i cÃ i Ä‘áº·t khi báº­t táº¯t Quáº£n lÃ½ thuáº¿
    function toggleMasterTax() {
        let isEnabled = document.getElementById('is_tax_enabled').checked;
        let wrapper = document.getElementById('tax_settings_wrapper');
        if (isEnabled) {
            wrapper.classList.remove('disabled-overlay');
        } else {
            wrapper.classList.add('disabled-overlay');
        }
    }

    // NÃºt tháº§n thÃ¡nh: Tá»± Ä‘á»™ng Ä‘iá»n cáº¥u hÃ¬nh chuáº©n Há»‡ thá»‘ng
    function applyPreset(type) {
        // Äáº£m báº£o Ä‘Ã£ báº­t Quáº£n lÃ½ thuáº¿
        document.getElementById('is_tax_enabled').checked = true;
        toggleMasterTax();

        if (type === 'hkd') {
            if (!confirm('Ãp dá»¥ng Cáº¥u hÃ¬nh Há»˜ KINH DOANH?\n\n- Thuáº¿ nháº­p: 0%\n- Thuáº¿ bÃ¡n: 1.5%\n- GiÃ¡ Ä‘Ã£ bao gá»“m thuáº¿: Báº­t')) return;

            document.getElementById('default_tax_sales').checked = true;
            document.getElementById('default_tax_purchases').checked = false;
            document.getElementById('price_includes_tax').checked = true;

            document.getElementById('general_purchase_tax_rate').value = '0.00';
            document.getElementById('general_sales_tax_rate').value = '1.50';

        } else if (type === 'dn') {
            if (!confirm('Ãp dá»¥ng Cáº¥u hÃ¬nh DOANH NGHIá»†P (Kháº¥u trá»« VAT)?\n\n- Thuáº¿ nháº­p: 10%\n- Thuáº¿ bÃ¡n: 10%\n- GiÃ¡ Ä‘Ã£ bao gá»“m thuáº¿: Táº¯t (Cá»™ng thÃªm VAT vÃ o bill)')) return;

            document.getElementById('default_tax_sales').checked = true;
            document.getElementById('default_tax_purchases').checked = true;
            document.getElementById('price_includes_tax').checked = false;

            document.getElementById('general_purchase_tax_rate').value = '10.00';
            document.getElementById('general_sales_tax_rate').value = '10.00';
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

