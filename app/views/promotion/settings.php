<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $settings */ ?>

<style>
    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 800px;
        margin: 0 auto 20px auto;
    }

    .Há»‡ thá»‘ng-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #212b36;
        border-bottom: 1px solid #dfe3e8;
        padding-bottom: 10px;
    }

    .switch-box {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
    }

    .switch-box input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        accent-color: #0088ff;
        cursor: pointer;
    }

    .switch-box input[type="radio"] {
        width: 16px;
        height: 16px;
        margin-top: 3px;
        accent-color: #0088ff;
        cursor: pointer;
    }

    .desc-text {
        font-size: 13px;
        color: #637381;
        margin: 5px 0 0 0;
        line-height: 1.5;
    }

    .math-example {
        background: #f4f6f8;
        border: 1px dashed #c4cdd5;
        padding: 12px;
        border-radius: 6px;
        margin-top: 10px;
        font-family: monospace;
        font-size: 13px;
        color: #212b36;
    }
</style>

<div style="max-width: 800px; margin: 0 auto 20px auto;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Cáº¥u hÃ¬nh Khuyáº¿n máº¡i</h2>
</div>

<form action="index.php?action=promo_settings" method="POST">

    <?php if (isset($_GET['success'])): ?>
        <div style="max-width: 800px; margin: 0 auto 20px auto; background:#eafff0; color:#108043; padding:15px; border-radius:6px; border:1px solid #33d067; font-weight:500;">
            âœ… ÄÃ£ lÆ°u cáº¥u hÃ¬nh thuáº­t toÃ¡n Khuyáº¿n máº¡i thÃ nh cÃ´ng!
        </div>
    <?php endif; ?>

    <div class="Há»‡ thá»‘ng-card">
        <div class="Há»‡ thá»‘ng-card-title">1. Tráº¡ng thÃ¡i Khuyáº¿n máº¡i chung</div>
        <label class="switch-box">
            <input type="checkbox" name="promo_global_status" value="1" <?php echo ($settings['promo_global_status'] ?? '1') == '1' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">Ãp dá»¥ng chÆ°Æ¡ng trÃ¬nh khuyáº¿n máº¡i cho cá»­a hÃ ng</strong>
                <p class="desc-text">Máº·c Ä‘á»‹nh há»‡ thá»‘ng sáº½ báº­t. Náº¿u bá» tÃ­ch, toÃ n bá»™ cÃ¡c chÆ°Æ¡ng trÃ¬nh khuyáº¿n máº¡i sáº½ bá»‹ Ä‘Ã³ng bÄƒng (khÃ´ng Ã¡p dá»¥ng) trong lÃºc táº¡o Ä‘Æ¡n hÃ ng.</p>
            </div>
        </label>
    </div>

    <div class="Há»‡ thá»‘ng-card">
        <div class="Há»‡ thá»‘ng-card-title">2. CÆ¡ cháº¿ cá»™ng dá»“n Khuyáº¿n máº¡i</div>

        <label class="switch-box">
            <input type="radio" name="promo_stacking" value="single" onchange="toggleCalcMethod()" <?php echo ($settings['promo_stacking'] ?? 'single') == 'single' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">Ãp dá»¥ng 1 chÆ°Æ¡ng trÃ¬nh / MÃ£ giáº£m giÃ¡</strong>
                <p class="desc-text">Má»—i Ä‘Æ¡n hÃ ng chá»‰ Ä‘Æ°á»£c chá»n duy nháº¥t má»™t chÆ°Æ¡ng trÃ¬nh khuyáº¿n máº¡i cÃ³ lá»£i nháº¥t.</p>
            </div>
        </label>

        <div style="height: 1px; background: #dfe3e8; margin: 15px 0;"></div>

        <label class="switch-box">
            <input type="radio" name="promo_stacking" value="multiple" onchange="toggleCalcMethod()" <?php echo ($settings['promo_stacking'] ?? '') == 'multiple' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">Cho phÃ©p Ã¡p dá»¥ng nhiá»u chÆ°Æ¡ng trÃ¬nh cÃ¹ng lÃºc</strong>
                <p class="desc-text">Cho phÃ©p khÃ¡ch hÃ ng "sÄƒn sale" cá»™ng dá»“n nhiá»u mÃ£ (VÃ­ dá»¥: Vá»«a giáº£m giÃ¡ Ä‘Æ¡n hÃ ng, vá»«a Ã¡p mÃ£ Freeship).</p>
            </div>
        </label>

        <div id="calc_method_box" style="margin-left: 32px; padding: 15px; border-left: 2px solid #0088ff; display: none;">
            <p style="font-weight: 500; font-size: 13px; color: #0088ff; margin-bottom: 10px;">Vui lÃ²ng chá»n thuáº­t toÃ¡n tÃ­nh toÃ¡n khi cá»™ng dá»“n:</p>

            <label class="switch-box" style="margin-bottom: 15px;">
                <input type="radio" name="promo_calc_method" value="original" <?php echo ($settings['promo_calc_method'] ?? 'original') == 'original' ? 'checked' : ''; ?>>
                <div>
                    <strong style="font-size: 14px; color: #212b36;">TÃ­nh trÃªn giÃ¡ gá»‘c cá»§a Ä‘Æ¡n hÃ ng (Cá»™ng song song)</strong>
                    <div class="math-example">
                        ÄÆ¡n 100k. CÃ³ 2 mÃ£: Giáº£m 5% vÃ  Giáº£m 10%.<br>
                        MÃ£ 1 = 5% * 100k = 5k | MÃ£ 2 = 10% * 100k = 10k<br>
                        => Tá»•ng giáº£m = 15.000Ä‘
                    </div>
                </div>
            </label>

            <label class="switch-box">
                <input type="radio" name="promo_calc_method" value="sequential" <?php echo ($settings['promo_calc_method'] ?? '') == 'sequential' ? 'checked' : ''; ?>>
                <div>
                    <strong style="font-size: 14px; color: #212b36;">Ãp dá»¥ng láº§n lÆ°á»£t (LÅ©y káº¿)</strong>
                    <div class="math-example">
                        ÄÆ¡n 100k. CÃ³ 2 mÃ£: Giáº£m 5% vÃ  Giáº£m 10%.<br>
                        MÃ£ 1 = 5% * 100k = 5k. (ÄÆ¡n cÃ²n 95k)<br>
                        MÃ£ 2 = 10% * 95.000 = 9.500Ä‘<br>
                        => Tá»•ng giáº£m = 14.500Ä‘ <span style="color:#108043;">(CÃ³ lá»£i cho ngÆ°á»i bÃ¡n hÆ¡n)</span>
                    </div>
                </div>
            </label>
        </div>

        <div style="background: #fff8ea; padding: 12px; border-radius: 6px; border: 1px solid #ffea8a; margin-top: 15px; font-size: 13px; color: #8a6100;">
            ðŸ’¡ <b>Quy táº¯c Æ°u tiÃªn:</b> Há»‡ thá»‘ng luÃ´n Æ°u tiÃªn tÃ­nh cÃ¡c Khuyáº¿n máº¡i dÃ nh riÃªng cho sáº£n pháº©m trÆ°á»›c, sau Ä‘Ã³ má»›i tÃ­nh Ä‘áº¿n Khuyáº¿n máº¡i trÃªn tá»•ng hÃ³a Ä‘Æ¡n.
        </div>
    </div>

    <div class="Há»‡ thá»‘ng-card">
        <div class="Há»‡ thá»‘ng-card-title">3. Quáº£n lÃ½ MÃ£ giáº£m giÃ¡ (Coupon)</div>
        <label class="switch-box">
            <input type="checkbox" name="promo_coupon_enabled" value="1" <?php echo ($settings['promo_coupon_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">PhÃ¡t hÃ nh mÃ£ giáº£m giÃ¡ cho cá»­a hÃ ng</strong>
                <p class="desc-text">Cho phÃ©p táº¡o cÃ¡c chÆ°Æ¡ng trÃ¬nh yÃªu cáº§u khÃ¡ch/thu ngÃ¢n pháº£i nháº­p Ä‘Ãºng Ä‘oáº¡n mÃ£ (VD: SALE2024) thÃ¬ má»›i Ä‘Æ°á»£c giáº£m tiá»n.</p>
            </div>
        </label>
    </div>

    <div style="max-width: 800px; margin: 0 auto; text-align: right;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:12px 30px; border-radius:4px; font-weight:bold; cursor:pointer; font-size: 15px;">ðŸ’¾ LÆ°u Cáº¥u hÃ¬nh</button>
    </div>
</form>

<script>
    function toggleCalcMethod() {
        let isMultiple = document.querySelector('input[name="promo_stacking"][value="multiple"]').checked;
        document.getElementById('calc_method_box').style.display = isMultiple ? 'block' : 'none';
    }

    // Cháº¡y khi vá»«a load trang
    window.addEventListener('DOMContentLoaded', toggleCalcMethod);
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

