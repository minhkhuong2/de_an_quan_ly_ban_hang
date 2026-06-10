<?php

/** @var array $promotions */
require_once __DIR__ . '/../layout/header.php';
$safe_promos = is_array($promotions ?? null) ? $promotions : [];
$current_status = $_GET['status'] ?? '';
?>

<style>
    /* RESET BỐ CỤC CHUẨN V3 */
    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* TABS CHUẨN V3 */
    .v3-tabs {
        display: flex;
        padding: 0 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
        gap: 30px;
    }

    .v3-tab {
        padding: 15px 0;
        font-size: 14px;
        font-weight: 500;
        color: #637381;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        transition: 0.2s;
    }

    .v3-tab.active {
        color: #0088ff;
        border-bottom-color: #0088ff;
        font-weight: 600;
    }

    .v3-tab:hover:not(.active) {
        color: #212b36;
    }

    /* THANH TÌM KIẾM VÀ NÚT TẠO (V3) */
    .v3-filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
    }

    .v3-filter-left {
        display: flex;
        gap: 10px;
        flex: 1;
        align-items: center;
    }

    .v3-search-box {
        position: relative;
        width: 350px;
    }

    .v3-search-box svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8c98a4;
    }

    .v3-search-box input {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        transition: 0.2s;
    }

    .v3-search-box input:focus {
        border-color: #0088ff;
        box-shadow: 0 0 0 1px #0088ff;
    }

    .btn-outline {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        color: #212b36;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-outline:hover {
        background: #f4f6f8;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary:hover {
        background: #0070cc;
    }

    /* KHU VỰC BỘ LỌC ẨN (Sổ xuống khi bấm Bộ lọc khác) */
    .v3-advanced-filter {
        display: none;
        padding: 15px 20px;
        background: #fafbfc;
        border-bottom: 1px solid #dfe3e8;
        gap: 15px;
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        outline: none;
        background: #fff;
    }

    /* BẢNG DỮ LIỆU V3 */
    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v3-table th {
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 600;
        color: #637381;
        border-bottom: 1px solid #dfe3e8;
        white-space: nowrap;
    }

    .v3-table td {
        padding: 15px 20px;
        font-size: 14px;
        color: #212b36;
        border-bottom: 1px solid #f4f6f8;
        vertical-align: top;
    }

    .v3-table tr:hover td {
        background: #fafbfc;
    }

    /* TRẠNG THÁI (DOT) */
    .status-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 400;
    }

    .status-dot::before {
        content: "";
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-active {
        color: #008a00;
    }

    .status-active::before {
        background: #008a00;
    }

    .status-inactive {
        color: #637381;
    }

    .status-inactive::before {
        background: #c4cdd5;
    }

    .status-stopped {
        color: #d82c0d;
    }

    .status-stopped::before {
        background: #d82c0d;
    }

    /* THANH THAO TÁC HÀNG LOẠT */
    .bulk-action-bar {
        display: none;
        background: #e6f7ff;
        padding: 10px 20px;
        border-bottom: 1px solid #91d5ff;
        align-items: center;
        justify-content: space-between;
    }

    /* MODAL TẠO MỚI */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(33, 43, 54, 0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        width: 750px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #212b36;
    }

    .close-btn {
        cursor: pointer;
        font-size: 24px;
        color: #637381;
        border: none;
        background: transparent;
        line-height: 1;
    }

    .modal-body {
        padding: 0;
        background: #f4f6f8;
    }

    .promo-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        padding: 20px;
    }

    .promo-card {
        background: #fff;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        padding: 15px;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        gap: 15px;
        align-items: flex-start;
        transition: 0.2s;
    }

    .promo-card:hover {
        border-color: #0088ff;
        box-shadow: 0 0 0 1px #0088ff;
    }

    .promo-icon {
        font-size: 24px;
        background: #f4f6f8;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .promo-info h4 {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #212b36;
        font-weight: 600;
    }

    .promo-info p {
        margin: 0;
        font-size: 12px;
        color: #637381;
        line-height: 1.4;
    }
</style>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size: 14px;">✅ Cập nhật chương trình thành công!</div><?php endif; ?>
<?php if (isset($_GET['success_bulk'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size: 14px;">⚡ Thao tác hàng loạt thành công!</div><?php endif; ?>

<div class="v3-card">
    <div class="v3-tabs">
        <a href="index.php?action=promo_list" class="v3-tab <?php echo empty($current_status) ? 'active' : ''; ?>">Tất cả</a>
        <a href="index.php?action=promo_list&status=Đang áp dụng" class="v3-tab <?php echo $current_status === 'Đang áp dụng' ? 'active' : ''; ?>">Đang áp dụng</a>
        <a href="index.php?action=promo_list&status=Chưa áp dụng" class="v3-tab <?php echo $current_status === 'Chưa áp dụng' ? 'active' : ''; ?>">Chưa áp dụng</a>
    </div>

    <form method="GET" action="index.php" id="filterForm">
        <input type="hidden" name="action" value="promo_list">
        <?php if ($current_status): ?><input type="hidden" name="status" value="<?php echo $current_status; ?>"><?php endif; ?>

        <div class="v3-filter-bar">
            <div class="v3-filter-left">
                <div class="v3-search-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="search" placeholder="Tìm kiếm mã/ chương trình khuyến mại" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <button type="button" class="btn-outline" onclick="toggleAdvancedFilter()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Bộ lọc khác
                </button>
            </div>
            <button type="button" class="btn-primary" onclick="document.getElementById('promoModal').style.display='flex'">
                Tạo khuyến mại
            </button>
        </div>

        <div class="v3-advanced-filter" id="advancedFilter">
            <select name="hinh_thuc" class="filter-select">
                <option value="">-- Hình thức --</option>
                <option value="coupon" <?php if (($_GET['hinh_thuc'] ?? '') == 'coupon') echo 'selected'; ?>>Mã Khuyến mại</option>
                <option value="auto" <?php if (($_GET['hinh_thuc'] ?? '') == 'auto') echo 'selected'; ?>>Chương trình tự động</option>
            </select>
            <select name="type" class="filter-select">
                <option value="">-- Loại khuyến mại --</option>
                <option value="discount_order" <?php if (($_GET['type'] ?? '') == 'discount_order') echo 'selected'; ?>>Giảm giá đơn hàng</option>
                <option value="discount_product" <?php if (($_GET['type'] ?? '') == 'discount_product') echo 'selected'; ?>>Giảm giá sản phẩm</option>
                <option value="gift_by_product" <?php if (($_GET['type'] ?? '') == 'gift_by_product') echo 'selected'; ?>>Mua X tặng Y</option>
                <option value="free_shipping" <?php if (($_GET['type'] ?? '') == 'free_shipping') echo 'selected'; ?>>Miễn phí vận chuyển</option>
            </select>
            <button type="submit" class="btn-outline" style="background: #0088ff; color: #fff; border: none;">Áp dụng lọc</button>
            <a href="index.php?action=promo_list" style="font-size: 13px; color: #0088ff; text-decoration: none; margin-left: 10px;">Xóa bộ lọc</a>
        </div>
    </form>

    <form method="POST" action="index.php?action=bulk_action_promo" id="bulkForm">
        <div class="bulk-action-bar" id="bulk_action_bar">
            <div><span style="font-size: 14px; font-weight: 500; color: #212b36;" id="selected_count">Đã chọn 0</span></div>
            <div>
                <button type="submit" name="action" value="Tiếp tục" class="btn-outline" style="padding: 6px 12px;">Tiếp tục</button>
                <button type="submit" name="action" value="Ngừng" class="btn-outline" style="padding: 6px 12px; margin-left: 8px;">Ngừng</button>
                <button type="submit" name="action" value="delete" class="btn-outline" style="padding: 6px 12px; margin-left: 8px; color: #d82c0d; border-color: #ffccc7;" onclick="return confirm('Xóa các mục đã chọn?');">Xóa</button>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="v3-table">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="check_all" style="width:16px; height:16px; cursor:pointer;"></th>
                        <th>Khuyến mại</th>
                        <th>Loại khuyến mại</th>
                        <th>Trạng thái</th>
                        <th>Đã dùng</th>
                        <th>Kết hợp với</th>
                        <th>Thời gian áp dụng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($safe_promos)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 60px; color: #637381; font-size: 14px;">Không tìm thấy khuyến mại nào.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($safe_promos as $p): ?>
                        <tr>
                            <td><input type="checkbox" name="promo_ids[]" value="<?php echo $p['id']; ?>" class="row-checkbox" style="width:16px; height:16px; cursor:pointer;"></td>
                            <td>
                                <a href="index.php?action=view_promo&id=<?php echo $p['id']; ?>" style="color: #0088ff; text-decoration: none; font-weight: 600; display: block; margin-bottom: 4px;"><?php echo htmlspecialchars($p['promo_name']); ?></a>
                                <?php if (!empty($p['promo_code'])): ?>
                                    <div style="font-size: 13px; color: #637381;">Mã KM: <?php echo htmlspecialchars($p['promo_code']); ?></div>
                                <?php else: ?>
                                    <div style="font-size: 13px; color: #637381;">Chương trình tự động</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                if ($p['promo_type'] == 'discount_order') echo 'Giảm giá đơn hàng';
                                elseif ($p['promo_type'] == 'discount_product') echo 'Giảm giá sản phẩm';
                                elseif ($p['promo_type'] == 'gift_by_order' || $p['promo_type'] == 'gift_by_product') echo 'Mua X tặng Y';
                                elseif ($p['promo_type'] == 'free_shipping') echo 'Miễn phí vận chuyển';
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($p['status'] == 'Đang áp dụng') echo '<span class="status-dot status-active">Đang áp dụng</span>';
                                elseif ($p['status'] == 'Chưa áp dụng') echo '<span class="status-dot status-inactive">Chưa áp dụng</span>';
                                else echo '<span class="status-dot status-stopped">Ngừng áp dụng</span>';
                                ?>
                            </td>
                            <td><?php echo $p['used_count'] ?? 0; ?> <?php echo empty($p['usage_limit']) ? '/ &infin;' : '/ ' . $p['usage_limit']; ?></td>
                            <td><span style="color: #637381;">-</span></td>
                            <td style="color: #637381;">
                                <div style="color: #212b36; margin-bottom: 4px;"><?php echo date('d/m/Y H:i', strtotime($p['start_date'])); ?></div>
                                <div>- <?php echo ($p['no_end_date']) ? 'Không giới hạn' : date('d/m/Y H:i', strtotime($p['end_date'])); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<div id="promoModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tạo khuyến mại</h3>
            <button type="button" class="close-btn" onclick="document.getElementById('promoModal').style.display='none'">&times;</button>
        </div>
        <div class="v3-tabs" style="border-bottom: 1px solid #dfe3e8; margin-bottom: 0;">
            <div class="v3-tab active" onclick="switchModalTab('coupon', this)" style="padding: 15px 20px;">Mã khuyến mại</div>
            <div class="v3-tab" onclick="switchModalTab('auto', this)" style="padding: 15px 20px;">Chương trình khuyến mại</div>
        </div>
        <div class="modal-body">
            <div id="tab_coupon" class="promo-grid">
                <a href="index.php?action=add_promo&mode=coupon&type=discount_order" class="promo-card">
                    <div class="promo-icon" style="color:#0088ff; background: #e6f7ff;">🏷️</div>
                    <div class="promo-info">
                        <h4>Giảm giá đơn hàng</h4>
                        <p>Khách nhập mã để được giảm giá trên tổng hóa đơn.</p>
                    </div>
                </a>
                <a href="index.php?action=add_promo&mode=coupon&type=discount_product" class="promo-card">
                    <div class="promo-icon" style="color:#fa8c16; background: #fff7e6;">📱</div>
                    <div class="promo-info">
                        <h4>Giảm giá sản phẩm</h4>
                        <p>Khách nhập mã để được giảm giá trực tiếp trên sản phẩm.</p>
                    </div>
                </a>
                <a href="index.php?action=add_promo&mode=coupon&type=gift_by_product" class="promo-card">
                    <div class="promo-icon" style="color:#108043; background: #eafff0;">🎁</div>
                    <div class="promo-info">
                        <h4>Mua X Tặng Y</h4>
                        <p>Khách nhập mã để nhận quà tặng khi mua đủ số lượng.</p>
                    </div>
                </a>
                <a href="index.php?action=add_promo&mode=coupon&type=free_shipping" class="promo-card">
                    <div class="promo-icon" style="color:#cf1322; background: #fff1f0;">🚚</div>
                    <div class="promo-info">
                        <h4>Miễn phí vận chuyển</h4>
                        <p>Cung cấp mã Freeship cho khách hàng.</p>
                    </div>
                </a>
            </div>
            <div id="tab_auto" class="promo-grid" style="display: none;">
                <a href="index.php?action=add_promo&mode=auto&type=discount_order" class="promo-card">
                    <div class="promo-icon" style="color:#0088ff; background: #e6f7ff;">⚡</div>
                    <div class="promo-info">
                        <h4>Giảm giá đơn hàng</h4>
                        <p>Tự động trừ tiền trên tổng đơn nếu đủ điều kiện.</p>
                    </div>
                </a>
                <a href="index.php?action=add_promo&mode=auto&type=discount_product" class="promo-card">
                    <div class="promo-icon" style="color:#fa8c16; background: #fff7e6;">⚡</div>
                    <div class="promo-info">
                        <h4>Giảm giá sản phẩm</h4>
                        <p>Tự động hiển thị giá đã giảm trên sản phẩm.</p>
                    </div>
                </a>
                <a href="index.php?action=add_promo&mode=auto&type=gift_by_product" class="promo-card">
                    <div class="promo-icon" style="color:#108043; background: #eafff0;">⚡</div>
                    <div class="promo-info">
                        <h4>Mua X Tặng Y</h4>
                        <p>Hệ thống tự động thêm quà tặng 0đ vào giỏ hàng.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Ẩn hiện Bộ lọc mở rộng
    function toggleAdvancedFilter() {
        let filterBox = document.getElementById('advancedFilter');
        filterBox.style.display = (filterBox.style.display === 'flex') ? 'none' : 'flex';
    }

    // Checkbox thao tác hàng loạt
    const checkAll = document.getElementById('check_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActionBar = document.getElementById('bulk_action_bar');
    const selectedCountText = document.getElementById('selected_count');

    function updateBulkAction() {
        let checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkActionBar.style.display = 'flex';
            selectedCountText.innerText = 'Đã chọn ' + checkedCount;
        } else {
            bulkActionBar.style.display = 'none';
        }
        checkAll.checked = (checkedCount === rowCheckboxes.length && rowCheckboxes.length > 0);
    }

    checkAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkAction();
    });
    rowCheckboxes.forEach(cb => cb.addEventListener('change', updateBulkAction));

    // Modal Popup chuyển Tab
    function switchModalTab(tabId, element) {
        let tabs = element.parentElement.querySelectorAll('.v3-tab');
        tabs.forEach(t => t.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('tab_coupon').style.display = (tabId === 'coupon') ? 'grid' : 'none';
        document.getElementById('tab_auto').style.display = (tabId === 'auto') ? 'grid' : 'none';
    }

    // Đóng Modal khi click ngoài
    window.onclick = function(event) {
        let modal = document.getElementById('promoModal');
        if (event.target === modal) modal.style.display = "none";
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
