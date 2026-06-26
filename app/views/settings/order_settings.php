<?php

/** @var array $settings */
/** @var array $advanced */
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

    /* Box quy trÃ¬nh xá»­ lÃ½ */
    .workflow-box {
        border: 1px solid #c4cdd5;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .workflow-box:hover {
        border-color: #0088ff;
        background: #f4f9ff;
    }

    .workflow-box.active {
        border-color: #0088ff;
        background: #e5f0ff;
        box-shadow: 0 0 0 1px #0088ff;
    }

    .wf-title {
        font-weight: 600;
        color: #212b36;
        margin-bottom: 5px;
        font-size: 15px;
    }

    .wf-desc {
        font-size: 13px;
        color: #637381;
        line-height: 1.5;
    }

    .setting-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #dfe3e8;
    }

    .setting-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .sr-content h4 {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #212b36;
    }

    .sr-content p {
        margin: 0;
        font-size: 13px;
        color: #637381;
    }

    input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin-top: 2px;
        cursor: pointer;
    }

    input[type="radio"] {
        width: 18px;
        height: 18px;
        margin-top: 2px;
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
        font-size: 14px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Cáº¥u hÃ¬nh quy trÃ¬nh xá»­ lÃ½ Ä‘Æ¡n hÃ ng</div>
    <button class="btn-primary" onclick="document.getElementById('frm_settings').submit()">ðŸ’¾ LÆ°u thiáº¿t láº­p</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">âœ… Cáº­p nháº­t cáº¥u hÃ¬nh xá»­ lÃ½ Ä‘Æ¡n hÃ ng thÃ nh cÃ´ng!</div>
<?php endif; ?>

<form id="frm_settings" action="index.php?action=save_order_settings" method="POST">

    <div class="v3-card">
        <div class="card-header">1. Quy trÃ¬nh xá»­ lÃ½ Ä‘Æ¡n hÃ ng (ÄÃ³ng gÃ³i & Giao váº­n)</div>
        <div class="card-body">
            <?php $wf = $settings['order_workflow'] ?? 'basic'; ?>

            <label class="workflow-box <?php echo $wf == 'basic' ? 'active' : ''; ?>" onclick="selectWorkflow(this)">
                <input type="radio" name="order_workflow" value="basic" <?php echo $wf == 'basic' ? 'checked' : ''; ?>>
                <div>
                    <div class="wf-title">Quy trÃ¬nh CÆ¡ báº£n</div>
                    <div class="wf-desc">DÃ nh cho cá»­a hÃ ng muá»‘n xá»­ lÃ½ Ä‘Æ¡n nhanh chÃ³ng vá»›i cÃ¡c bÆ°á»›c Ä‘Æ¡n giáº£n nháº¥t. Bá» qua cÃ¡c khÃ¢u quÃ©t mÃ£ váº¡ch vÃ  phÃ¢n lÃ n.</div>
                </div>
            </label>

            <label class="workflow-box <?php echo $wf == 'standard' ? 'active' : ''; ?>" onclick="selectWorkflow(this)">
                <input type="radio" name="order_workflow" value="standard" <?php echo $wf == 'standard' ? 'checked' : ''; ?>>
                <div>
                    <div class="wf-title">Quy trÃ¬nh TiÃªu chuáº©n (Retail Pro / )</div>
                    <div class="wf-desc">Quáº£n lÃ½ cháº·t cháº½ tá»«ng khÃ¢u Ä‘Ã³ng gÃ³i, giao hÃ ng Ä‘á»ƒ Ä‘áº£m báº£o tÃ­nh chÃ­nh xÃ¡c vÃ  giáº£m sai sÃ³t. YÃªu cáº§u xÃ¡c nháº­n xuáº¥t kho.</div>
                </div>
            </label>

            <label class="workflow-box <?php echo $wf == 'advanced' ? 'active' : ''; ?>" onclick="selectWorkflow(this, true)">
                <input type="radio" name="order_workflow" value="advanced" <?php echo $wf == 'advanced' ? 'checked' : ''; ?>>
                <div>
                    <div class="wf-title">Quy trÃ¬nh NÃ¢ng cao (Wave Picking - Enterprise)</div>
                    <div class="wf-desc">Khuyáº¿n nghá»‹ tá»« 1000 Ä‘Æ¡n/ngÃ y. Tá»‘i Æ°u hÃ³a toÃ n bá»™ quy trÃ¬nh báº±ng cÃ¡ch tá»± Ä‘á»™ng gom nhÃ³m Ä‘Æ¡n hÃ ng thÃ´ng minh.</div>
                </div>
            </label>

            <div id="advanced_block" style="display: <?php echo $wf == 'advanced' ? 'block' : 'none'; ?>; background: #f4f6f8; padding: 20px; border-radius: 6px; margin-top: 15px; border: 1px dashed #c4cdd5;">
                <h4 style="margin-top:0; margin-bottom:15px; color:#212b36;">CÃ i Ä‘áº·t luá»“ng nháº·t hÃ ng & Ä‘Ã³ng gÃ³i (YÃªu cáº§u dÃ¹ng mÃ¡y quÃ©t mÃ£ váº¡ch)</h4>

                <div class="setting-row" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                    <input type="checkbox" name="scan_shelf" id="scan_shelf" <?php echo ($advanced['scan_shelf'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="scan_shelf">
                        <h4>YÃªu cáº§u quÃ©t mÃ£ vá»‹ trÃ­ ká»‡ khi láº¥y hÃ ng</h4>
                        <p>Báº¯t buá»™c nhÃ¢n viÃªn quÃ©t mÃ£ váº¡ch trÃªn ká»‡ trÆ°á»›c khi láº¥y sáº£n pháº©m.</p>
                    </label>
                </div>
                <div class="setting-row" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                    <input type="checkbox" name="scan_item_pick" id="scan_item_pick" <?php echo ($advanced['scan_item_pick'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="scan_item_pick">
                        <h4>YÃªu cáº§u quÃ©t mÃ£ sáº£n pháº©m khi nháº·t hÃ ng</h4>
                        <p>XÃ¡c nháº­n Ä‘Ã£ láº¥y Ä‘Ãºng sáº£n pháº©m vÃ  Ä‘á»§ sá»‘ lÆ°á»£ng vÃ o giá» hÃ ng.</p>
                    </label>
                </div>
                <div class="setting-row" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                    <input type="checkbox" name="scan_item_pack" id="scan_item_pack" <?php echo ($advanced['scan_item_pack'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="scan_item_pack">
                        <h4>YÃªu cáº§u quÃ©t mÃ£ sáº£n pháº©m khi Ä‘Ã³ng gÃ³i</h4>
                        <p>BÆ°á»›c kiá»ƒm tra cuá»‘i cÃ¹ng trÆ°á»›c khi Ä‘Ã³ng há»™p giao Ä‘i.</p>
                    </label>
                </div>
                <div class="setting-row" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                    <input type="checkbox" name="strict_wave" id="strict_wave" <?php echo ($advanced['strict_wave'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="strict_wave">
                        <h4>ÄÃ³ng gÃ³i tuáº§n tá»± nghiÃªm ngáº·t theo tá»«ng "Wave"</h4>
                        <p>Náº¿u báº­t, nhÃ¢n viÃªn pháº£i hoÃ n thÃ nh táº¥t cáº£ Ä‘Æ¡n trong 1 Ä‘á»£t gom má»›i Ä‘Æ°á»£c sang Ä‘á»£t khÃ¡c.</p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="v3-card">
        <div class="card-header">2. Cáº¥u hÃ¬nh váº­n hÃ nh Ä‘Æ¡n hÃ ng</div>
        <div class="card-body">
            <div class="setting-row">
                <input type="checkbox" name="allow_negative_sale_warning" id="allow_negative" <?php echo ($settings['allow_negative_sale_warning'] ?? '1') == '1' ? 'checked' : ''; ?>>
                <label class="sr-content" for="allow_negative">
                    <h4>Hiá»ƒn thá»‹ cá»­a sá»• cáº£nh bÃ¡o "Cho phÃ©p bÃ¡n Ã¢m" táº¡i mÃ n hÃ¬nh táº¡o Ä‘Æ¡n</h4>
                    <p>Náº¿u báº­t, há»‡ thá»‘ng sáº½ hiá»‡n popup há»i Ã½ kiáº¿n báº¡n cÃ³ muá»‘n tiáº¿p tá»¥c bÃ¡n khi sá»‘ lÆ°á»£ng mua vÆ°á»£t quÃ¡ tá»“n kho hay khÃ´ng.</p>
                </label>
            </div>
            <div class="setting-row" style="border:none;">
                <input type="checkbox" name="auto_archive_order" id="auto_archive" <?php echo ($settings['auto_archive_order'] ?? '0') == '1' ? 'checked' : ''; ?>>
                <label class="sr-content" for="auto_archive">
                    <h4>Tá»± Ä‘á»™ng lÆ°u trá»¯ Ä‘Æ¡n hÃ ng</h4>
                    <p>Há»‡ thá»‘ng tá»± Ä‘á»™ng chuyá»ƒn cÃ¡c Ä‘Æ¡n Ä‘Ã£ hoÃ n táº¥t (ÄÃ£ thanh toÃ¡n & ÄÃ£ giao hÃ ng) vÃ o má»¥c LÆ°u trá»¯ cho gá»n danh sÃ¡ch.</p>
                </label>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:20px;">
        <div class="v3-card" style="flex:1;">
            <div class="card-header">3. TÃ¹y chá»‰nh nguá»“n Ä‘Æ¡n</div>
            <div class="card-body">
                <p style="font-size:13px; color:#637381; margin-bottom:15px;">Quáº£n lÃ½ danh sÃ¡ch cÃ¡c nguá»“n Ä‘Æ¡n hÃ ng (FB, Shopee, CTV...) hiá»ƒn thá»‹ trÃªn mÃ n hÃ¬nh táº¡o Ä‘Æ¡n.</p>
                <a href="index.php?action=order_sources" style="color:#0088ff; text-decoration:none; font-weight:600;">Quáº£n lÃ½ nguá»“n Ä‘Æ¡n hÃ ng âž”</a>
            </div>
        </div>

        <div class="v3-card" style="flex:1;">
            <div class="card-header">4. Xá»­ lÃ½ dá»¯ liá»‡u khi xÃ³a Ä‘Æ¡n hÃ ng</div>
            <div class="card-body">
                <div class="setting-row" style="margin-bottom:10px; padding-bottom:10px;">
                    <input type="checkbox" checked disabled>
                    <label class="sr-content">
                        <h4 style="color:#8c98a4;">Tá»± Ä‘á»™ng xÃ³a doanh thu vÃ  cÃ´ng ná»£ (Báº¯t buá»™c)</h4>
                    </label>
                </div>
                <div class="setting-row" style="border:none;">
                    <input type="checkbox" name="auto_delete_transaction" id="auto_del_txn" <?php echo ($settings['auto_delete_transaction'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <label class="sr-content" for="auto_del_txn">
                        <h4>Tá»± Ä‘á»™ng xÃ³a giao dá»‹ch sá»• quá»¹, phiáº¿u thu/chi</h4>
                        <p style="color:#e67e22;">Cáº£nh bÃ¡o: HÃ nh Ä‘á»™ng xÃ³a lÃ  khÃ´ng thá»ƒ khÃ´i phá»¥c!</p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="v3-card">
        <div class="card-header">5. Gá»­i Email nháº¯c nhá»Ÿ thanh toÃ¡n</div>
        <div class="card-body">
            <h4 style="margin:0 0 10px 0; font-size:14px;">Thá»i gian gá»­i nháº¯c nhá»Ÿ Ä‘á»‘i vá»›i Ä‘Æ¡n chÆ°a hoÃ n táº¥t:</h4>
            <?php $hours = $settings['reminder_email_hours'] ?? '1'; ?>
            <select name="reminder_email_hours" class="form-control" style="width: 300px; padding: 10px;">
                <option value="0" <?php echo $hours == '0' ? 'selected' : ''; ?>>KhÃ´ng bao giá»</option>
                <option value="1" <?php echo $hours == '1' ? 'selected' : ''; ?>>Sau 1 giá» (Khuyáº¿n dÃ¹ng)</option>
                <option value="6" <?php echo $hours == '6' ? 'selected' : ''; ?>>Sau 6 giá»</option>
                <option value="10" <?php echo $hours == '10' ? 'selected' : ''; ?>>Sau 10 giá» (Khuyáº¿n dÃ¹ng)</option>
                <option value="24" <?php echo $hours == '24' ? 'selected' : ''; ?>>Sau 24 giá»</option>
            </select>
        </div>
    </div>
</form>

<script>
    function selectWorkflow(element, isAdvanced = false) {
        document.querySelectorAll('.workflow-box').forEach(b => b.classList.remove('active'));
        element.classList.add('active');
        element.querySelector('input').checked = true;

        let block = document.getElementById('advanced_block');
        if (isAdvanced) {
            block.style.display = 'block';
        } else {
            block.style.display = 'none';
            // Táº¯t háº¿t checkbox bÃªn trong náº¿u khÃ´ng dÃ¹ng NÃ¢ng cao
            block.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

