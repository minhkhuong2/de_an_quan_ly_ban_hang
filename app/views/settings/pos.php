<?php require_once __DIR__ . '/../layout/header.php'; ?>

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

    .v3-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        font-weight: 600;
        color: #212b36;
        font-size: 16px;
        background: #fafbfc;
        border-radius: 8px 8px 0 0;
    }

    .v3-card-body {
        padding: 20px;
    }

    /* Thiáº¿t káº¿ form hÃ ng ngang */
    .setting-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 15px 0;
        border-bottom: 1px dashed #dfe3e8;
    }

    .setting-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .setting-info {
        flex: 1;
        padding-right: 40px;
    }

    .setting-title {
        font-weight: 600;
        color: #212b36;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .setting-desc {
        color: #637381;
        font-size: 13px;
        line-height: 1.5;
    }

    .setting-action {
        flex: 0 0 auto;
    }

    /* NÃšT Gáº T (TOGGLE SWITCH) CHUáº¨N IOS/Há»‡ thá»‘ng */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
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
        transition: .3s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    input:checked+.slider {
        background-color: #0088ff;
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    /* Form control cÆ¡ báº£n */
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        outline: none;
    }

    .form-control:focus {
        border-color: #0088ff;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Cáº¥u hÃ¬nh kÃªnh bÃ¡n táº¡i quáº§y (POS)</div>
    <button type="submit" form="posSettingsForm" class="btn-primary">ðŸ’¾ LÆ°u cáº¥u hÃ¬nh</button>
</div>

<form id="posSettingsForm" action="index.php?action=save_pos_settings" method="POST">

    <div class="v3-card">
        <div class="v3-card-header">1. Cáº¥u hÃ¬nh bÃ¡n hÃ ng chung</div>
        <div class="v3-card-body">

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Kiá»ƒu thanh toÃ¡n</div>
                    <div class="setting-desc">Chá»n thanh toÃ¡n 1 bÆ°á»›c (Nhanh) hoáº·c 2 bÆ°á»›c (Chi tiáº¿t nhiá»u hÃ¬nh thá»©c)</div>
                </div>
                <div class="setting-action" style="width: 200px;">
                    <select name="pos_payment_steps" class="form-control">
                        <option value="1" <?php echo ($settings_db['pos_payment_steps'] == '1') ? 'selected' : ''; ?>>Thanh toÃ¡n 1 bÆ°á»›c</option>
                        <option value="2" <?php echo ($settings_db['pos_payment_steps'] == '2') ? 'selected' : ''; ?>>Thanh toÃ¡n 2 bÆ°á»›c</option>
                    </select>
                </div>
            </div>

            <?php
            // Máº£ng chá»©a cÃ¡c Toggles á»Ÿ Khá»‘i 1
            $block1_toggles = [
                'pos_allow_negative_stock' => ['Cho phÃ©p bÃ¡n Ã¢m', 'Há»‡ thá»‘ng sáº½ ghi nháº­n tá»“n Ã¢m náº¿u bÃ¡n hÃ ng khi háº¿t tá»“n kho.'],
                'pos_suggest_amount' => ['Gá»£i Ã½ tiá»n thanh toÃ¡n', 'Há»‡ thá»‘ng gá»£i Ã½ sá»‘ tiá»n khÃ¡ch Ä‘Æ°a dá»±a trÃªn tá»•ng tiá»n cáº§n tráº£.'],
                'pos_allow_price_edit' => ['Äiá»u chá»‰nh giÃ¡', 'Cho phÃ©p nhÃ¢n viÃªn thay Ä‘á»•i giÃ¡ bÃ¡n hoáº·c thÃªm khuyáº¿n máº¡i tay.'],
                'pos_auto_promotions' => ['Ãp dá»¥ng khuyáº¿n máº¡i tá»± Ä‘á»™ng', 'Há»‡ thá»‘ng tá»± Ä‘á»™ng quÃ©t vÃ  trá»« tiá»n cÃ¡c chÆ°Æ¡ng trÃ¬nh KM há»£p lá»‡.'],
                'pos_use_promo_code' => ['Sá»­ dá»¥ng mÃ£ khuyáº¿n máº¡i (Coupon)', 'Hiá»ƒn thá»‹ Ã´ nháº­p mÃ£ giáº£m giÃ¡ trÃªn mÃ n hÃ¬nh POS.'],
                'pos_shift_management' => ['Quáº£n lÃ½ ca lÃ m viá»‡c', 'Theo dÃµi thÃ´ng tin, doanh thu cá»§a tá»«ng ca lÃ m viá»‡c.'],
                'pos_Há»‡ thá»‘ng_qr' => ['Káº¿t ná»‘i hiá»ƒn thá»‹ Há»‡ thá»‘ng QR', 'Hiá»ƒn thá»‹ mÃ£ QR VietQR Ä‘á»ƒ khÃ¡ch quÃ©t thanh toÃ¡n nhanh.']
            ];

            foreach ($block1_toggles as $key => $info):
                $checked = ($settings_db[$key] == '1') ? 'checked' : '';
            ?>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-title"><?php echo $info[0]; ?></div>
                        <div class="setting-desc"><?php echo $info[1]; ?></div>
                    </div>
                    <div class="setting-action">
                        <label class="switch">
                            <input type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo $checked; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <div class="v3-card">
        <div class="v3-card-header">2. MÃ¡y in & Máº«u in hÃ³a Ä‘Æ¡n</div>
        <div class="v3-card-body">

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Lá»±a chá»n khá»• in máº·c Ä‘á»‹nh</div>
                    <div class="setting-desc">TÃ¹y chá»n khá»• giáº¥y phÃ¹ há»£p vá»›i mÃ¡y in táº¡i cá»­a hÃ ng.</div>
                </div>
                <div class="setting-action" style="width: 200px;">
                    <select name="pos_print_size" class="form-control">
                        <option value="80mm" <?php echo ($settings_db['pos_print_size'] == '80mm') ? 'selected' : ''; ?>>Khá»• 80mm (MÃ¡y in nhiá»‡t)</option>
                        <option value="58mm" <?php echo ($settings_db['pos_print_size'] == '58mm') ? 'selected' : ''; ?>>Khá»• 57/58mm (MÃ¡y in mini)</option>
                        <option value="A4" <?php echo ($settings_db['pos_print_size'] == 'A4') ? 'selected' : ''; ?>>Khá»• A4 / A5 (MÃ¡y in vÄƒn phÃ²ng)</option>
                    </select>
                </div>
            </div>

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Sá»‘ báº£n in (LiÃªn)</div>
                    <div class="setting-desc">Sá»‘ lÆ°á»£ng hÃ³a Ä‘Æ¡n tá»± Ä‘á»™ng in ra má»—i khi thanh toÃ¡n.</div>
                </div>
                <div class="setting-action" style="width: 100px;">
                    <input type="number" name="pos_print_copies" class="form-control" value="<?php echo $settings_db['pos_print_copies'] ?? '1'; ?>" min="1" max="5">
                </div>
            </div>

            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Tá»± Ä‘á»™ng in hÃ³a Ä‘Æ¡n khi thanh toÃ¡n</div>
                    <div class="setting-desc">Máº·c Ä‘á»‹nh hiá»ƒn thá»‹ cá»­a sá»• in ngay khi áº¥n nÃºt thanh toÃ¡n thÃ nh cÃ´ng.</div>
                </div>
                <div class="setting-action">
                    <label class="switch">
                        <input type="checkbox" name="pos_auto_print" value="1" <?php echo ($settings_db['pos_auto_print'] == '1') ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

        </div>
    </div>

    <div class="v3-card">
        <div class="v3-card-header">3. BÃ¡n hÃ ng Offline</div>
        <div class="v3-card-body">
            <div class="setting-row">
                <div class="setting-info">
                    <div class="setting-title">Sá»­ dá»¥ng cháº¿ Ä‘á»™ bÃ¡n hÃ ng Offline</div>
                    <div class="setting-desc">Cho phÃ©p bÃ¡n hÃ ng bÃ¬nh thÆ°á»ng ngay cáº£ khi máº¥t káº¿t ná»‘i máº¡ng Internet. Dá»¯ liá»‡u sáº½ Ä‘á»“ng bá»™ khi cÃ³ máº¡ng trá»Ÿ láº¡i.</div>
                    <button type="button" class="btn-primary" style="background: #fff; color: #212b36; border: 1px solid #c4cdd5; margin-top: 10px; font-size: 13px; padding: 6px 12px;">ðŸ”„ Äá»“ng bá»™ láº¡i dá»¯ liá»‡u vá» mÃ¡y</button>
                </div>
                <div class="setting-action">
                    <label class="switch">
                        <input type="checkbox" name="pos_offline_mode" value="1" <?php echo ($settings_db['pos_offline_mode'] == '1') ? 'checked' : ''; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

