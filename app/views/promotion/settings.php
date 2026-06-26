<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $settings */ ?>

<style>
    .akc-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 800px;
        margin: 0 auto 20px auto;
    }

    .akc-card-title {
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
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Cấu hình Khuyến mại</h2>
</div>

<form action="index.php?action=promo_settings" method="POST">

    <?php if (isset($_GET['success'])): ?>
        <div style="max-width: 800px; margin: 0 auto 20px auto; background:#eafff0; color:#108043; padding:15px; border-radius:6px; border:1px solid #33d067; font-weight:500;">
            ✅ Đã lưu cấu hình thuật toán Khuyến mại thành công!
        </div>
    <?php endif; ?>

    <div class="akc-card">
        <div class="akc-card-title">1. Trạng thái Khuyến mại chung</div>
        <label class="switch-box">
            <input type="checkbox" name="promo_global_status" value="1" <?php echo ($settings['promo_global_status'] ?? '1') == '1' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">Áp dụng chương trình khuyến mại cho cửa hàng</strong>
                <p class="desc-text">Mặc định hệ thống sẽ bật. Nếu bỏ tích, toàn bộ các chương trình khuyến mại sẽ bị đóng băng (không áp dụng) trong lúc tạo đơn hàng.</p>
            </div>
        </label>
    </div>

    <div class="akc-card">
        <div class="akc-card-title">2. Cơ chế cộng dồn Khuyến mại</div>

        <label class="switch-box">
            <input type="radio" name="promo_stacking" value="single" onchange="toggleCalcMethod()" <?php echo ($settings['promo_stacking'] ?? 'single') == 'single' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">Áp dụng 1 chương trình / Mã giảm giá</strong>
                <p class="desc-text">Mỗi đơn hàng chỉ được chọn duy nhất một chương trình khuyến mại có lợi nhất.</p>
            </div>
        </label>

        <div style="height: 1px; background: #dfe3e8; margin: 15px 0;"></div>

        <label class="switch-box">
            <input type="radio" name="promo_stacking" value="multiple" onchange="toggleCalcMethod()" <?php echo ($settings['promo_stacking'] ?? '') == 'multiple' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">Cho phép áp dụng nhiều chương trình cùng lúc</strong>
                <p class="desc-text">Cho phép khách hàng "săn sale" cộng dồn nhiều mã (Ví dụ: Vừa giảm giá đơn hàng, vừa áp mã Freeship).</p>
            </div>
        </label>

        <div id="calc_method_box" style="margin-left: 32px; padding: 15px; border-left: 2px solid #0088ff; display: none;">
            <p style="font-weight: 500; font-size: 13px; color: #0088ff; margin-bottom: 10px;">Vui lòng chọn thuật toán tính toán khi cộng dồn:</p>

            <label class="switch-box" style="margin-bottom: 15px;">
                <input type="radio" name="promo_calc_method" value="original" <?php echo ($settings['promo_calc_method'] ?? 'original') == 'original' ? 'checked' : ''; ?>>
                <div>
                    <strong style="font-size: 14px; color: #212b36;">Tính trên giá gốc của đơn hàng (Cộng song song)</strong>
                    <div class="math-example">
                        Đơn 100k. Có 2 mã: Giảm 5% và Giảm 10%.<br>
                        Mã 1 = 5% * 100k = 5k | Mã 2 = 10% * 100k = 10k<br>
                        => Tổng giảm = 15.000đ
                    </div>
                </div>
            </label>

            <label class="switch-box">
                <input type="radio" name="promo_calc_method" value="sequential" <?php echo ($settings['promo_calc_method'] ?? '') == 'sequential' ? 'checked' : ''; ?>>
                <div>
                    <strong style="font-size: 14px; color: #212b36;">Áp dụng lần lượt (Lũy kế)</strong>
                    <div class="math-example">
                        Đơn 100k. Có 2 mã: Giảm 5% và Giảm 10%.<br>
                        Mã 1 = 5% * 100k = 5k. (Đơn còn 95k)<br>
                        Mã 2 = 10% * 95.000 = 9.500đ<br>
                        => Tổng giảm = 14.500đ <span style="color:#108043;">(Có lợi cho người bán hơn)</span>
                    </div>
                </div>
            </label>
        </div>

        <div style="background: #fff8ea; padding: 12px; border-radius: 6px; border: 1px solid #ffea8a; margin-top: 15px; font-size: 13px; color: #8a6100;">
            💡 <b>Quy tắc ưu tiên:</b> Hệ thống luôn ưu tiên tính các Khuyến mại dành riêng cho sản phẩm trước, sau đó mới tính đến Khuyến mại trên tổng hóa đơn.
        </div>
    </div>

    <div class="akc-card">
        <div class="akc-card-title">3. Quản lý Mã giảm giá (Coupon)</div>
        <label class="switch-box">
            <input type="checkbox" name="promo_coupon_enabled" value="1" <?php echo ($settings['promo_coupon_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
            <div>
                <strong style="font-size: 14px; color: #212b36;">Phát hành mã giảm giá cho cửa hàng</strong>
                <p class="desc-text">Cho phép tạo các chương trình yêu cầu khách/thu ngân phải nhập đúng đoạn mã (VD: SALE2024) thì mới được giảm tiền.</p>
            </div>
        </label>
    </div>

    <div style="max-width: 800px; margin: 0 auto; text-align: right;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:12px 30px; border-radius:4px; font-weight:bold; cursor:pointer; font-size: 15px;">💾 Lưu Cấu hình</button>
    </div>
</form>

<script>
    function toggleCalcMethod() {
        let isMultiple = document.querySelector('input[name="promo_stacking"][value="multiple"]').checked;
        document.getElementById('calc_method_box').style.display = isMultiple ? 'block' : 'none';
    }

    // Chạy khi vừa load trang
    window.addEventListener('DOMContentLoaded', toggleCalcMethod);
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
