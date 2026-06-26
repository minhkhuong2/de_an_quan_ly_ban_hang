<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $orders
 * @var array $counts
 * @var string $tab
 * @var string $keyword
 * @var string $channel_filter
 */
?>

<style>
    .op-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .op-title {
        font-size: 24px;
        font-weight: bold;
        color: #212b36;
    }

    .op-tabs {
        display: flex;
        background: #fff;
        padding: 0 20px;
        border-radius: 8px 8px 0 0;
        border: 1px solid #dfe3e8;
        border-bottom: none;
    }

    .op-tab {
        padding: 15px 20px;
        color: #637381;
        text-decoration: none;
        font-weight: 600;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }

    .op-tab:hover {
        color: #0088ff;
    }

    .op-tab.active {
        color: #0088ff;
        border-bottom: 3px solid #0088ff;
    }

    .op-badge {
        background: #f4f6f8;
        color: #212b36;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 5px;
    }

    .op-tab.active .op-badge {
        background: #e5f0ff;
        color: #0088ff;
    }

    .op-toolbar {
        background: #fff;
        padding: 15px 20px;
        border: 1px solid #dfe3e8;
        border-top: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .op-search {
        flex: 1;
        position: relative;
    }

    .op-search input {
        width: 100%;
        padding: 8px 12px 8px 35px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
    }

    .op-search i {
        position: absolute;
        left: 12px;
        top: 10px;
        color: #919eab;
    }

    .op-filter select {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        background: #fff;
    }

    .op-table-container {
        background: #fff;
        border: 1px solid #dfe3e8;
        border-top: none;
        border-radius: 0 0 8px 8px;
        padding-bottom: 20px;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v3-table th {
        background: #f4f6f8;
        padding: 12px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 13px;
        color: #637381;
    }

    .v3-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 15px;
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

    .bulk-actions {
        display: none;
        background: #e5f0ff;
        padding: 10px 20px;
        border: 1px solid #b3d4ff;
        border-bottom: none;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="op-header">
    <div class="op-title">📦 Xử lý đơn hàng</div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">
        ✅ <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<!-- TABS -->
<div class="op-tabs">
    <a href="index.php?action=order_processing&tab=pending_confirm&channel=<?php echo $channel_filter; ?>" class="op-tab <?php echo $tab == 'pending_confirm' ? 'active' : ''; ?>">
        Chờ xác nhận <span class="op-badge"><?php echo $counts['pending_confirm']; ?></span>
    </a>
    <a href="index.php?action=order_processing&tab=pending_process&channel=<?php echo $channel_filter; ?>" class="op-tab <?php echo $tab == 'pending_process' ? 'active' : ''; ?>">
        Chờ xử lý <span class="op-badge"><?php echo $counts['pending_process']; ?></span>
    </a>
    <a href="index.php?action=order_processing&tab=packing&channel=<?php echo $channel_filter; ?>" class="op-tab <?php echo $tab == 'packing' ? 'active' : ''; ?>">
        In & Đóng gói <span class="op-badge"><?php echo $counts['packing']; ?></span>
    </a>
    <a href="index.php?action=order_processing&tab=handover&channel=<?php echo $channel_filter; ?>" class="op-tab <?php echo $tab == 'handover' ? 'active' : ''; ?>">
        Bàn giao <span class="op-badge"><?php echo $counts['handover']; ?></span>
    </a>
    <a href="index.php?action=order_processing&tab=all&channel=<?php echo $channel_filter; ?>" class="op-tab <?php echo $tab == 'all' ? 'active' : ''; ?>">
        Tất cả kiện hàng <span class="op-badge"><?php echo $counts['all']; ?></span>
    </a>
</div>

<!-- TOOLBAR (FILTER) -->
<form method="GET" action="index.php" id="filterForm">
    <input type="hidden" name="action" value="order_processing">
    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
    
    <div class="op-toolbar">
        <div class="op-search">
            <i class="fa-solid fa-search"></i>
            <input type="text" name="keyword" placeholder="Tìm theo mã đơn, sđt, tên khách hàng..." value="<?php echo htmlspecialchars($keyword); ?>">
        </div>
        <div class="op-filter" style="display:flex; align-items:center; gap: 10px;">
            <label style="font-weight:600; font-size:13px;">Chế độ chuyên Sàn:</label>
            <label class="switch" style="position:relative; display:inline-block; width:40px; height:20px;">
                <input type="checkbox" id="ecommerce_toggle" <?php echo $channel_filter == 'ecommerce' ? 'checked' : ''; ?> onchange="toggleEcommerceMode()" style="opacity:0; width:0; height:0;">
                <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:<?php echo $channel_filter == 'ecommerce' ? '#0088ff' : '#c4cdd5'; ?>; transition:.4s; border-radius:20px;">
                    <span style="position:absolute; content:''; height:16px; width:16px; left:<?php echo $channel_filter == 'ecommerce' ? '22px' : '2px'; ?>; bottom:2px; background-color:white; transition:.4s; border-radius:50%;"></span>
                </span>
            </label>
            
            <select name="channel" id="channelSelect" onchange="this.form.submit()" style="<?php echo $channel_filter == 'ecommerce' ? 'display:none;' : ''; ?>">
                <option value="all" <?php echo $channel_filter == 'all' ? 'selected' : ''; ?>>Tất cả kênh</option>
                <option value="other" <?php echo $channel_filter == 'other' ? 'selected' : ''; ?>>Ngoài Sàn (Web, POS, FB)</option>
                <option value="shopee" <?php echo $channel_filter == 'shopee' ? 'selected' : ''; ?>>Shopee</option>
                <option value="lazada" <?php echo $channel_filter == 'lazada' ? 'selected' : ''; ?>>Lazada</option>
                <option value="tiktok" <?php echo $channel_filter == 'tiktok' ? 'selected' : ''; ?>>TikTok Shop</option>
                <option value="pos" <?php echo $channel_filter == 'pos' ? 'selected' : ''; ?>>Bán tại quầy (POS)</option>
            </select>
            <button type="submit" class="btn-outline">Lọc</button>
        </div>
    </div>
</form>

<!-- BULK ACTIONS -->
<div class="bulk-actions" id="bulkActionsPanel">
    <span style="font-size:14px; color:#212b36;">Đã chọn <strong id="selectedCount">0</strong> đơn hàng</span>
    
    <form id="bulkForm" method="POST" style="display:flex; gap:10px;">
        <input type="hidden" name="order_ids" id="bulkOrderIds">
        
        <?php if ($tab == 'pending_confirm'): ?>
            <button type="button" class="btn-primary" onclick="submitBulk('order_processing_confirm')">Xác nhận hàng loạt</button>
        <?php elseif ($tab == 'pending_process'): ?>
            <button type="button" class="btn-primary" onclick="submitBulk('order_processing_pack')">Yêu cầu đóng gói</button>
        <?php elseif ($tab == 'packing'): ?>
            <button type="button" class="btn-outline" style="color:#0088ff; border-color:#0088ff;" onclick="printDocs('shipping')"><i class="fa-solid fa-print"></i> In phiếu giao hàng</button>
            <button type="button" class="btn-primary" onclick="submitBulk('order_processing_packed')">Xác nhận đã đóng gói</button>
        <?php elseif ($tab == 'handover'): ?>
            <button type="button" class="btn-primary" onclick="submitBulk('order_processing_handover')">Bàn giao cho ĐVVC</button>
        <?php endif; ?>
    </form>
</div>

<!-- DATA TABLE -->
<div class="op-table-container">
    <table class="v3-table">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th>
                <th>Mã đơn</th>
                <th>Kênh bán</th>
                <th>Khách hàng</th>
                <th>Trạng thái</th>
                <th style="text-align: right;">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="6" style="text-align:center; padding: 30px; color: #637381;">Không có dữ liệu trong mục này.</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" value="<?php echo $o['id']; ?>" onclick="updateBulkPanel()"></td>
                        <td>
                            <a href="index.php?action=view_order&id=<?php echo $o['id']; ?>" style="color:#0088ff; font-weight:bold; text-decoration:none;">
                                <?php echo htmlspecialchars($o['order_code']); ?>
                            </a>
                            <div style="font-size: 12px; color: #637381; margin-top: 3px;"><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></div>
                        </td>
                        <td>
                            <?php 
                                $src = strtolower($o['sales_channel'] ?? 'pos');
                                if (in_array($src, ['shopee', 'lazada', 'tiktok'])) {
                                    echo '<span style="color:#e67e22; font-weight:600;"><i class="fa-solid fa-store"></i> Sàn TMĐT</span>';
                                } else {
                                    echo '<span style="color:#108043; font-weight:600;"><i class="fa-solid fa-desktop"></i> Ngoài Sàn</span>';
                                }
                                echo '<div style="font-size:12px; color:#637381;">'.strtoupper($src).'</div>';
                            ?>
                        </td>
                        <td>
                            <b><?php echo htmlspecialchars($o['customer_name'] ?? 'Khách lẻ'); ?></b>
                            <div style="font-size:12px; color:#637381;"><?php echo htmlspecialchars($o['customer_phone'] ?? ''); ?></div>
                        </td>
                        <td>
                            <?php if ($o['order_status'] === 'pending'): ?>
                                <span style="color:#8a6100; background:#fff8ea; padding:2px 6px; border-radius:4px; font-size:12px;">Chờ xác nhận</span>
                            <?php elseif ($o['order_status'] === 'confirmed' && $o['shipping_status'] === 'pending'): ?>
                                <span style="color:#0088ff; background:#e5f0ff; padding:2px 6px; border-radius:4px; font-size:12px;">Chờ nhặt hàng</span>
                            <?php elseif ($o['shipping_status'] === 'packing'): ?>
                                <span style="color:#d82c0d; background:#ffe4e4; padding:2px 6px; border-radius:4px; font-size:12px;">Đang đóng gói</span>
                            <?php elseif ($o['shipping_status'] === 'packed'): ?>
                                <span style="color:#108043; background:#eafff0; padding:2px 6px; border-radius:4px; font-size:12px;">Đã đóng gói</span>
                            <?php elseif ($o['shipping_status'] === 'delivering'): ?>
                                <span style="color:#637381; background:#f4f6f8; padding:2px 6px; border-radius:4px; font-size:12px;">Đã bàn giao</span>
                            <?php else: ?>
                                <span><?php echo $o['shipping_status']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            <?php echo number_format($o['grand_total'] ?? 0, 0, '', '.'); ?> ₫
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleEcommerceMode() {
        const toggle = document.getElementById('ecommerce_toggle');
        const channelSelect = document.getElementById('channelSelect');
        
        if (toggle.checked) {
            channelSelect.value = 'ecommerce';
        } else {
            channelSelect.value = 'all';
        }
        document.getElementById('filterForm').submit();
    }

    function toggleAll(source) {
        checkboxes = document.querySelectorAll('.row-checkbox');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
        updateBulkPanel();
    }

    function updateBulkPanel() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const panel = document.getElementById('bulkActionsPanel');
        const countSpan = document.getElementById('selectedCount');
        
        if (checkboxes.length > 0) {
            panel.style.display = 'flex';
            countSpan.innerText = checkboxes.length;
        } else {
            panel.style.display = 'none';
        }
    }

    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        let ids = [];
        checkboxes.forEach((cb) => { ids.push(cb.value); });
        return ids.join(',');
    }

    function submitBulk(actionType) {
        if(!confirm("Bạn có chắc chắn muốn thực hiện thao tác này cho các đơn đã chọn?")) return;
        
        const form = document.getElementById('bulkForm');
        document.getElementById('bulkOrderIds').value = getSelectedIds();
        form.action = 'index.php?action=' + actionType;
        form.submit();
    }

    function printDocs(type) {
        const ids = getSelectedIds();
        if (ids === '') {
            alert('Vui lòng chọn ít nhất 1 đơn hàng để in.');
            return;
        }
        window.open(`index.php?action=order_processing_print&type=${type}&ids=${ids}`, '_blank');
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
