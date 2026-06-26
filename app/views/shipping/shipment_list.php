<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var string $keyword
 * @var string $status_filter
 * @var string $recon_filter
 * @var array $branches
 * @var array $shipments
 */
?>

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

    .v3-filter-bar {
        background: #fff;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        border: 1px solid #dfe3e8;
        border-bottom: none;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .v3-form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    /* Thanh Thao tÃ¡c hÃ ng loáº¡t (áº¨n máº·c Ä‘á»‹nh) */
    .bulk-action-bar {
        background: #e5f0ff;
        padding: 12px 15px;
        border: 1px solid #b3d4ff;
        border-bottom: none;
        display: none;
        align-items: center;
        gap: 15px;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        background: #fff;
        border: 1px solid #dfe3e8;
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
        text-decoration: none;
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

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-delivering {
        background: #e5f0ff;
        color: #0088ff;
    }

    .badge-delivered {
        background: #eafff0;
        color: #108043;
    }

    .badge-returning {
        background: #fff8ea;
        color: #8a6100;
    }

    /* Modals */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        width: 500px;
        padding: 25px;
        border-radius: 8px;
    }
</style>

<div class="v3-header">
    <div class="v3-title">ðŸ“¦ Quáº£n lÃ½ danh sÃ¡ch Váº­n Ä‘Æ¡n</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="alert('TÃ­nh nÄƒng Xuáº¥t Excel Ä‘ang cáº­p nháº­t!')">ðŸ“¥ Xuáº¥t file</button>
        <a href="index.php?action=order_list" class="btn-primary">+ Táº¡o Ä‘Æ¡n hÃ ng má»›i</a>
    </div>
</div>

<?php if (isset($_GET['success_status'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #33d067;">âœ… Cáº­p nháº­t tráº¡ng thÃ¡i váº­n Ä‘Æ¡n thÃ nh cÃ´ng!</div>
<?php endif; ?>
<?php if (isset($_GET['success_recon'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:15px; border:1px solid #33d067;">ðŸ’° Äá»‘i soÃ¡t thÃ nh cÃ´ng! Há»‡ thá»‘ng Ä‘Ã£ tá»± Ä‘á»™ng táº¡o Phiáº¿u Thu tiá»n COD vÃ o Sá»• quá»¹.</div>
<?php endif; ?>

<form class="v3-filter-bar" method="GET" action="index.php">
    <input type="hidden" name="action" value="shipment_list">

    <input type="text" name="keyword" class="v3-form-control" placeholder="TÃ¬m MÃ£ Váº­n Ä‘Æ¡n, TÃªn KH..." value="<?php echo htmlspecialchars($keyword); ?>" style="width: 250px;">

    <select name="status" class="v3-form-control">
        <option value="all">Táº¥t cáº£ Tráº¡ng thÃ¡i Giao hÃ ng</option>
        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Chá» láº¥y hÃ ng</option>
        <option value="delivering" <?php echo $status_filter == 'delivering' ? 'selected' : ''; ?>>Äang giao hÃ ng</option>
        <option value="delivered" <?php echo $status_filter == 'delivered' ? 'selected' : ''; ?>>ÄÃ£ giao thÃ nh cÃ´ng</option>
        <option value="returning" <?php echo $status_filter == 'returning' ? 'selected' : ''; ?>>Äang hoÃ n hÃ ng</option>
    </select>

    <select name="recon_status" class="v3-form-control">
        <option value="all">Tráº¡ng thÃ¡i Äá»‘i soÃ¡t</option>
        <option value="unreconciled" <?php echo $recon_filter == 'unreconciled' ? 'selected' : ''; ?>>ðŸ”´ ChÆ°a Ä‘á»‘i soÃ¡t (Äang ná»£ COD)</option>
        <option value="reconciled" <?php echo $recon_filter == 'reconciled' ? 'selected' : ''; ?>>âœ… ÄÃ£ Ä‘á»‘i soÃ¡t (ÄÃ£ nháº­n tiá»n)</option>
    </select>

    <button type="submit" class="btn-primary">Lá»c Váº­n Ä‘Æ¡n</button>
</form>

<div id="bulk_bar" class="bulk-action-bar">
    <span style="font-weight:600; color:#0056b3;">ÄÃ£ chá»n <span id="selected_count">0</span> váº­n Ä‘Æ¡n:</span>
    <button class="btn-outline" onclick="openStatusModal()">ðŸ”„ Äá»•i tráº¡ng thÃ¡i</button>
    <button class="btn-primary" style="background:#108043;" onclick="openReconModal()">ðŸ’° Thá»±c hiá»‡n Äá»‘i soÃ¡t (Thu tiá»n)</button>
    <button class="btn-outline" onclick="alert('TÃ­nh nÄƒng In Phiáº¿u bÃ n giao Ä‘ang cáº­p nháº­t')">ðŸ–¨ï¸ In Phiáº¿u bÃ n giao</button>
</div>

<table class="v3-table">
    <thead>
        <tr>
            <th style="width:40px; text-align:center;"><input type="checkbox" id="check_all" onclick="toggleCheckAll()"></th>
            <th>MÃ£ Váº­n ÄÆ¡n</th>
            <th>MÃ£ ÄÆ¡n Há»‡ thá»‘ng / KhÃ¡ch hÃ ng</th>
            <th>Äá»‘i tÃ¡c</th>
            <th>Tráº¡ng thÃ¡i Giao / Äá»‘i soÃ¡t</th>
            <th style="text-align:right;">Tiá»n COD</th>
            <th style="text-align:right;">PhÃ­ Ship</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($shipments)): ?>
            <tr>
                <td colspan="7" style="text-align:center; padding:30px; color:#637381;">KhÃ´ng tÃ¬m tháº¥y váº­n Ä‘Æ¡n nÃ o phÃ¹ há»£p.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($shipments as $s): ?>
                <tr>
                    <td style="text-align:center;"><input type="checkbox" class="row-checkbox" value="<?php echo $s['id']; ?>" onclick="updateBulkBar()"></td>
                    <td><b style="color:#0088ff;"><?php echo htmlspecialchars($s['tracking_code']); ?></b></td>
                    <td>
                        <a href="index.php?action=view_order&id=<?php echo $s['order_id']; ?>" style="font-weight:bold; color:#212b36; text-decoration:none;">ðŸ“¦ <?php echo $s['order_code']; ?></a><br>
                        <small style="color:#637381;">ðŸ‘¤ <?php echo htmlspecialchars($s['customer_name']); ?></small>
                    </td>
                    <td><b style="text-transform:uppercase; color:#e67e22;"><?php echo $s['partner_code']; ?></b></td>
                    <td>
                        <?php
                        if ($s['status'] == 'delivered') echo '<span class="badge badge-delivered">ÄÃ£ giao</span>';
                        elseif ($s['status'] == 'delivering') echo '<span class="badge badge-delivering">Äang giao</span>';
                        elseif ($s['status'] == 'returning') echo '<span class="badge badge-returning">Äang hoÃ n</span>';
                        else echo '<span class="badge" style="background:#f4f6f8; border:1px solid #c4cdd5;">' . $s['status'] . '</span>';
                        ?>
                        <br>
                        <?php echo $s['recon_status'] == 'reconciled' ? '<span style="font-size:12px; color:#108043;">âœ… ÄÃ£ Ä.SoÃ¡t</span>' : '<span style="font-size:12px; color:#d82c0d;">ðŸ”´ ChÆ°a Ä.SoÃ¡t</span>'; ?>
                    </td>
                    <td style="text-align:right; font-weight:bold; color:#0088ff;"><?php echo number_format($s['cod_amount'], 0, '', '.'); ?> Ä‘</td>
                    <td style="text-align:right; color:#d82c0d;">- <?php echo number_format($s['shipping_fee'], 0, '', '.'); ?> Ä‘</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div id="status_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0;">ðŸ”„ Cáº­p nháº­t tráº¡ng thÃ¡i thá»§ cÃ´ng</h3>
        <p style="font-size:13px; color:#637381;">Ãp dá»¥ng cho cÃ¡c váº­n Ä‘Æ¡n Shipper ngoÃ i hoáº·c há»‡ thá»‘ng chÆ°a ká»‹p Ä‘á»“ng bá»™.</p>
        <form action="index.php?action=update_shipment_status" method="POST">
            <input type="hidden" name="shipment_ids" id="status_shipment_ids">
            <div class="form-group" style="margin-top:15px;">
                <label>Tráº¡ng thÃ¡i má»›i</label>
                <select name="new_status" class="v3-form-control">
                    <option value="delivering">ðŸšš Äang giao hÃ ng</option>
                    <option value="delivered">âœ… ÄÃ£ giao thÃ nh cÃ´ng</option>
                    <option value="returning">ðŸ”„ Äang hoÃ n hÃ ng</option>
                    <option value="returned">ðŸ”™ ÄÃ£ hoÃ n kho</option>
                </select>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('status_modal').style.display='none'">Há»§y</button>
                <button type="submit" class="btn-primary">LÆ°u Cáº­p Nháº­t</button>
            </div>
        </form>
    </div>
</div>

<div id="recon_modal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0; color:#108043;">ðŸ’° Ghi nháº­n Äá»‘i soÃ¡t (Thu tiá»n COD)</h3>
        <p style="font-size:13px; color:#d82c0d; background:#ffe4e4; padding:10px; border-radius:4px;">âš ï¸ LÆ°u Ã½: Chá»‰ chá»n cÃ¡c Ä‘Æ¡n CÃ™NG Má»˜T HÃƒNG Váº¬N CHUYá»‚N vÃ  ÄÃƒ GIAO/HOÃ€N Ä‘á»ƒ trÃ¡nh lá»—i káº¿ toÃ¡n!</p>
        <form action="index.php?action=reconcile_shipments" method="POST">
            <input type="hidden" name="recon_shipment_ids" id="recon_shipment_ids">

            <div class="form-group" style="margin-top:15px;">
                <label>Chi nhÃ¡nh ghi nháº­n Tiá»n vÃ o sá»• quá»¹ <span>*</span></label>
                <select name="recon_branch_id" class="v3-form-control" required>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Ghi chÃº Ä‘á»‘i soÃ¡t</label>
                <input type="text" name="recon_note" class="v3-form-control" placeholder="VD: Äá»‘i soÃ¡t GHN tuáº§n 1 thÃ¡ng 6">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('recon_modal').style.display='none'">Há»§y</button>
                <button type="submit" class="btn-primary" style="background:#108043;">XÃ¡c nháº­n Táº¡o Phiáº¿u Äá»‘i SoÃ¡t</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Logic Checkbox HÃ ng loáº¡t
    function toggleCheckAll() {
        let isChecked = document.getElementById('check_all').checked;
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = isChecked);
        updateBulkBar();
    }

    function updateBulkBar() {
        let checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        let bulkBar = document.getElementById('bulk_bar');
        document.getElementById('selected_count').innerText = checkedCount;

        if (checkedCount > 0) {
            bulkBar.style.display = 'flex';
        } else {
            bulkBar.style.display = 'none';
            document.getElementById('check_all').checked = false;
        }
    }

    // Má»Ÿ Modal Tráº¡ng thÃ¡i
    function openStatusModal() {
        let ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        document.getElementById('status_shipment_ids').value = ids;
        document.getElementById('status_modal').style.display = 'flex';
    }

    // Má»Ÿ Modal Äá»‘i soÃ¡t
    function openReconModal() {
        let ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value).join(',');
        document.getElementById('recon_shipment_ids').value = ids;
        document.getElementById('recon_modal').style.display = 'flex';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

