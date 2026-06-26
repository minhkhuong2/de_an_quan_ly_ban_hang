<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $returns */ ?>

<style>
    .Há»‡ thá»‘ng-filter-bar {
        display: flex;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
        align-items: center;
    }

    .Há»‡ thá»‘ng-filter-bar input.search-input {
        flex: 1;
        padding: 8px 12px 8px 35px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .Há»‡ thá»‘ng-filter-bar select {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
    }

    .filter-btn {
        padding: 8px 16px;
        background: #f4f6f8;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    .filter-btn:hover {
        background: #dfe3e8;
    }

    .ret-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .ret-table th {
        padding: 12px 15px;
        background: #fafbfc;
        color: #637381;
        font-weight: 600;
        border-bottom: 1px solid #dfe3e8;
    }

    .ret-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f4f6f8;
        color: #212b36;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sÃ¡ch Ä‘Æ¡n tráº£ hÃ ng nháº­p</h2>

    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">â†‘ Xuáº¥t file</button>
        <a href="index.php?action=add_direct_return" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Tráº£ hÃ ng khÃ´ng theo Ä‘Æ¡n</a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #8ce09f;">âœ… Táº¡o phiáº¿u tráº£ hÃ ng thÃ nh cÃ´ng! Tá»“n kho Ä‘Ã£ Ä‘Æ°á»£c trá»« tÆ°Æ¡ng á»©ng.</div><?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0; min-height: 500px;">

    <form action="index.php" method="GET" class="Há»‡ thá»‘ng-filter-bar">
        <input type="hidden" name="action" value="purchase_return_list">

        <div style="position: relative; flex: 1; max-width: 400px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">ðŸ”</span>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="TÃ¬m theo mÃ£ Ä‘Æ¡n tráº£, Ä‘Æ¡n nháº­p, NCC...">
        </div>

        <select name="status">
            <option value="">-- Tráº¡ng thÃ¡i hoÃ n tiá»n --</option>
            <option value="ChÆ°a hoÃ n tiá»n" <?php echo (($_GET['status'] ?? '') == 'ChÆ°a hoÃ n tiá»n') ? 'selected' : ''; ?>>ChÆ°a hoÃ n tiá»n</option>
            <option value="HoÃ n má»™t pháº§n" <?php echo (($_GET['status'] ?? '') == 'HoÃ n má»™t pháº§n') ? 'selected' : ''; ?>>HoÃ n má»™t pháº§n</option>
            <option value="ÄÃ£ hoÃ n tiá»n" <?php echo (($_GET['status'] ?? '') == 'ÄÃ£ hoÃ n tiá»n') ? 'selected' : ''; ?>>ÄÃ£ hoÃ n tiá»n</option>
        </select>

        <button type="submit" class="filter-btn">Lá»c</button>

        <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
            <a href="index.php?action=purchase_return_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; margin-left: 10px;">XÃ³a bá»™ lá»c</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($returns)): ?>
        <table class="ret-table">
            <thead>
                <tr>
                    <th>MÃ£ Ä‘Æ¡n tráº£</th>
                    <th>ÄÆ¡n nháº­p gá»‘c</th>
                    <th>NgÃ y táº¡o</th>
                    <th>NhÃ  cung cáº¥p</th>
                    <th style="text-align: center;">Sá»‘ lÆ°á»£ng tráº£</th>
                    <th style="text-align: right;">GiÃ¡ trá»‹ hoÃ n</th>
                    <th style="text-align: center;">Tráº¡ng thÃ¡i tiá»n</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($returns as $r): ?>
                    <tr style="cursor: pointer;" onclick="window.location.href='#'">
                        <td style="color:#0088ff; font-weight: bold;">#RET<?php echo $r['id']; ?></td>
                        <td style="padding: 12px;">
                            <?php if ($r['order_id'] > 0): ?>
                                <a href="index.php?action=view_purchase&id=<?php echo $r['order_id']; ?>" style="color:#637381; text-decoration: none;">#PN<?php echo $r['order_id']; ?></a>
                            <?php else: ?>
                                <span style="color: #c4cdd5;">---</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></td>
                        <td style="font-weight: 500;"><?php echo htmlspecialchars($r['supplier_name']); ?></td>
                        <td style="text-align: center; font-weight: bold;"><?php echo $r['total_qty']; ?></td>
                        <td style="text-align: right; font-weight: bold; color: #212b36;"><?php echo number_format($r['total_amount'], 0, ',', '.'); ?> â‚«</td>
                        <td style="text-align: center;">
                            <?php if ($r['refund_status'] == 'ÄÃ£ hoÃ n tiá»n'): ?>
                                <span style="background:#eafff0; color:#108043; padding:4px 8px; border-radius:4px; font-size:12px; font-weight: 600;">ÄÃ£ hoÃ n tiá»n</span>
                            <?php elseif ($r['refund_status'] == 'HoÃ n má»™t pháº§n'): ?>
                                <span style="background:#fff7e6; color:#fa8c16; padding:4px 8px; border-radius:4px; font-size:12px; font-weight: 600;">HoÃ n 1 pháº§n</span>
                            <?php else: ?>
                                <span style="background:#fff1f0; color:#cf1322; padding:4px 8px; border-radius:4px; font-size:12px; font-weight: 600;">ChÆ°a hoÃ n tiá»n</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiá»ƒn thá»‹ 1 - <?php echo count($returns); ?> trÃªn tá»•ng <?php echo count($returns); ?> Ä‘Æ¡n tráº£ hÃ ng
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">ðŸ“¤</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">ChÆ°a cÃ³ Ä‘Æ¡n tráº£ hÃ ng nháº­p nÃ o</h3>
            <p style="color: #637381;">Báº¡n cÃ³ thá»ƒ táº¡o Ä‘Æ¡n tráº£ hÃ ng tá»« mÃ n hÃ¬nh Chi tiáº¿t Ä‘Æ¡n Ä‘áº·t hÃ ng nháº­p.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

