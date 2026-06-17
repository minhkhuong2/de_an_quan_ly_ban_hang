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

    /* Box quy trình xử lý */
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
    <div class="v3-title">Cấu hình quy trình xử lý đơn hàng</div>
    <button class="btn-primary" onclick="document.getElementById('frm_settings').submit()">💾 Lưu thiết lập</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật cấu hình xử lý đơn hàng thành công!</div>
<?php endif; ?>

<form id="frm_settings" action="index.php?action=save_order_settings" method="POST">

    <div class="v3-card">
        <div class="card-header">1. Quy trình xử lý đơn hàng (Đóng gói & Giao vận)</div>
        <div class="card-body">
            <?php $wf = $settings['order_workflow'] ?? 'basic'; ?>

            <label class="workflow-box <?php echo $wf == 'basic' ? 'active' : ''; ?>" onclick="selectWorkflow(this)">
                <input type="radio" name="order_workflow" value="basic" <?php echo $wf == 'basic' ? 'checked' : ''; ?>>
                <div>
                    <div class="wf-title">Quy trình Cơ bản</div>
                    <div class="wf-desc">Dành cho cửa hàng muốn xử lý đơn nhanh chóng với các bước đơn giản nhất. Bỏ qua các khâu quét mã vạch và phân làn.</div>
                </div>
            </label>

            <label class="workflow-box <?php echo $wf == 'standard' ? 'active' : ''; ?>" onclick="selectWorkflow(this)">
                <input type="radio" name="order_workflow" value="standard" <?php echo $wf == 'standard' ? 'checked' : ''; ?>>
                <div>
                    <div class="wf-title">Quy trình Tiêu chuẩn (Retail Pro / OmniAI)</div>
                    <div class="wf-desc">Quản lý chặt chẽ từng khâu đóng gói, giao hàng để đảm bảo tính chính xác và giảm sai sót. Yêu cầu xác nhận xuất kho.</div>
                </div>
            </label>

            <label class="workflow-box <?php echo $wf == 'advanced' ? 'active' : ''; ?>" onclick="selectWorkflow(this, true)">
                <input type="radio" name="order_workflow" value="advanced" <?php echo $wf == 'advanced' ? 'checked' : ''; ?>>
                <div>
                    <div class="wf-title">Quy trình Nâng cao (Wave Picking - Enterprise)</div>
                    <div class="wf-desc">Khuyến nghị từ 1000 đơn/ngày. Tối ưu hóa toàn bộ quy trình bằng cách tự động gom nhóm đơn hàng thông minh.</div>
                </div>
            </label>

            <div id="advanced_block" style="display: <?php echo $wf == 'advanced' ? 'block' : 'none'; ?>; background: #f4f6f8; padding: 20px; border-radius: 6px; margin-top: 15px; border: 1px dashed #c4cdd5;">
                <h4 style="margin-top:0; margin-bottom:15px; color:#212b36;">Cài đặt luồng nhặt hàng & đóng gói (Yêu cầu dùng máy quét mã vạch)</h4>

                <div class="setting-row" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                    <input type="checkbox" name="scan_shelf" id="scan_shelf" <?php echo ($advanced['scan_shelf'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="scan_shelf">
                        <h4>Yêu cầu quét mã vị trí kệ khi lấy hàng</h4>
                        <p>Bắt buộc nhân viên quét mã vạch trên kệ trước khi lấy sản phẩm.</p>
                    </label>
                </div>
                <div class="setting-row" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                    <input type="checkbox" name="scan_item_pick" id="scan_item_pick" <?php echo ($advanced['scan_item_pick'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="scan_item_pick">
                        <h4>Yêu cầu quét mã sản phẩm khi nhặt hàng</h4>
                        <p>Xác nhận đã lấy đúng sản phẩm và đủ số lượng vào giỏ hàng.</p>
                    </label>
                </div>
                <div class="setting-row" style="border-bottom: none; margin-bottom: 10px; padding-bottom: 0;">
                    <input type="checkbox" name="scan_item_pack" id="scan_item_pack" <?php echo ($advanced['scan_item_pack'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="scan_item_pack">
                        <h4>Yêu cầu quét mã sản phẩm khi đóng gói</h4>
                        <p>Bước kiểm tra cuối cùng trước khi đóng hộp giao đi.</p>
                    </label>
                </div>
                <div class="setting-row" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                    <input type="checkbox" name="strict_wave" id="strict_wave" <?php echo ($advanced['strict_wave'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="sr-content" for="strict_wave">
                        <h4>Đóng gói tuần tự nghiêm ngặt theo từng "Wave"</h4>
                        <p>Nếu bật, nhân viên phải hoàn thành tất cả đơn trong 1 đợt gom mới được sang đợt khác.</p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="v3-card">
        <div class="card-header">2. Cấu hình vận hành đơn hàng</div>
        <div class="card-body">
            <div class="setting-row">
                <input type="checkbox" name="allow_negative_sale_warning" id="allow_negative" <?php echo ($settings['allow_negative_sale_warning'] ?? '1') == '1' ? 'checked' : ''; ?>>
                <label class="sr-content" for="allow_negative">
                    <h4>Hiển thị cửa sổ cảnh báo "Cho phép bán âm" tại màn hình tạo đơn</h4>
                    <p>Nếu bật, hệ thống sẽ hiện popup hỏi ý kiến bạn có muốn tiếp tục bán khi số lượng mua vượt quá tồn kho hay không.</p>
                </label>
            </div>
            <div class="setting-row" style="border:none;">
                <input type="checkbox" name="auto_archive_order" id="auto_archive" <?php echo ($settings['auto_archive_order'] ?? '0') == '1' ? 'checked' : ''; ?>>
                <label class="sr-content" for="auto_archive">
                    <h4>Tự động lưu trữ đơn hàng</h4>
                    <p>Hệ thống tự động chuyển các đơn đã hoàn tất (Đã thanh toán & Đã giao hàng) vào mục Lưu trữ cho gọn danh sách.</p>
                </label>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:20px;">
        <div class="v3-card" style="flex:1;">
            <div class="card-header">3. Tùy chỉnh nguồn đơn</div>
            <div class="card-body">
                <p style="font-size:13px; color:#637381; margin-bottom:15px;">Quản lý danh sách các nguồn đơn hàng (FB, Shopee, CTV...) hiển thị trên màn hình tạo đơn.</p>
                <a href="index.php?action=order_sources" style="color:#0088ff; text-decoration:none; font-weight:600;">Quản lý nguồn đơn hàng ➔</a>
            </div>
        </div>

        <div class="v3-card" style="flex:1;">
            <div class="card-header">4. Xử lý dữ liệu khi xóa đơn hàng</div>
            <div class="card-body">
                <div class="setting-row" style="margin-bottom:10px; padding-bottom:10px;">
                    <input type="checkbox" checked disabled>
                    <label class="sr-content">
                        <h4 style="color:#8c98a4;">Tự động xóa doanh thu và công nợ (Bắt buộc)</h4>
                    </label>
                </div>
                <div class="setting-row" style="border:none;">
                    <input type="checkbox" name="auto_delete_transaction" id="auto_del_txn" <?php echo ($settings['auto_delete_transaction'] ?? '1') == '1' ? 'checked' : ''; ?>>
                    <label class="sr-content" for="auto_del_txn">
                        <h4>Tự động xóa giao dịch sổ quỹ, phiếu thu/chi</h4>
                        <p style="color:#e67e22;">Cảnh báo: Hành động xóa là không thể khôi phục!</p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="v3-card">
        <div class="card-header">5. Gửi Email nhắc nhở thanh toán</div>
        <div class="card-body">
            <h4 style="margin:0 0 10px 0; font-size:14px;">Thời gian gửi nhắc nhở đối với đơn chưa hoàn tất:</h4>
            <?php $hours = $settings['reminder_email_hours'] ?? '1'; ?>
            <select name="reminder_email_hours" class="form-control" style="width: 300px; padding: 10px;">
                <option value="0" <?php echo $hours == '0' ? 'selected' : ''; ?>>Không bao giờ</option>
                <option value="1" <?php echo $hours == '1' ? 'selected' : ''; ?>>Sau 1 giờ (Khuyến dùng)</option>
                <option value="6" <?php echo $hours == '6' ? 'selected' : ''; ?>>Sau 6 giờ</option>
                <option value="10" <?php echo $hours == '10' ? 'selected' : ''; ?>>Sau 10 giờ (Khuyến dùng)</option>
                <option value="24" <?php echo $hours == '24' ? 'selected' : ''; ?>>Sau 24 giờ</option>
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
            // Tắt hết checkbox bên trong nếu không dùng Nâng cao
            block.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
