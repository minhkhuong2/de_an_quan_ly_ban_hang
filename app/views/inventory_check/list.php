<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $checks */ ?>

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

    .ic-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .ic-table th {
        padding: 12px 15px;
        background: #fafbfc;
        color: #637381;
        font-weight: 600;
        border-bottom: 1px solid #dfe3e8;
    }

    .ic-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f4f6f8;
        color: #212b36;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sÃ¡ch phiáº¿u kiá»ƒm kho</h2>
    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">â†‘ Xuáº¥t file</button>
        <a href="index.php?action=add_inventory_check" style="background:#0088ff; color:white; padding:8px 16px; border-radius:4px; text-decoration:none; font-weight:500;">+ Táº¡o phiáº¿u kiá»ƒm kho</a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">âœ… CÃ¢n báº±ng kho thÃ nh cÃ´ng! Sá»‘ lÆ°á»£ng Ä‘Ã£ Ä‘Æ°á»£c cáº­p nháº­t.</div>
<?php endif; ?>
<?php if (isset($_GET['success_edit'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">âœ… ÄÃ£ cáº­p nháº­t thÃ´ng tin phiáº¿u kiá»ƒm!</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e;">ðŸ—‘ï¸ ÄÃ£ xÃ³a phiáº¿u kiá»ƒm vÃ  hoÃ n láº¡i tá»“n kho thÃ nh cÃ´ng!</div>
<?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0; min-height: 500px;">

    <form action="index.php" method="GET" class="Há»‡ thá»‘ng-filter-bar">
        <input type="hidden" name="action" value="inventory_check_list">

        <div style="position: relative; flex: 1; max-width: 350px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">ðŸ”</span>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="TÃ¬m kiáº¿m theo mÃ£ phiáº¿u kiá»ƒm...">
        </div>

        <select name="status">
            <option value="">-- Táº¥t cáº£ tráº¡ng thÃ¡i --</option>
            <option value="ÄÃ£ cÃ¢n báº±ng" <?php echo (($_GET['status'] ?? '') == 'ÄÃ£ cÃ¢n báº±ng') ? 'selected' : ''; ?>>ÄÃ£ cÃ¢n báº±ng</option>
            <option value="ÄÃ£ há»§y" <?php echo (($_GET['status'] ?? '') == 'ÄÃ£ há»§y') ? 'selected' : ''; ?>>ÄÃ£ há»§y</option>
        </select>

        <button type="submit" class="filter-btn">Lá»c</button>

        <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
            <a href="index.php?action=inventory_check_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; margin-left: 10px;">XÃ³a bá»™ lá»c</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($checks)): ?>
        <table class="ic-table">
            <thead>
                <tr>
                    <th>MÃ£ phiáº¿u</th>
                    <th>NgÃ y kiá»ƒm</th>
                    <th>NhÃ¢n viÃªn</th>
                    <th>Ghi chÃº</th>
                    <th>Tráº¡ng thÃ¡i</th>
                    <th style="text-align: center;">Thao tÃ¡c</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checks as $c): ?>
                    <tr>
                        <td style="color:#0088ff; font-weight: bold;">#CHK<?php echo $c['id']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?></td>
                        <td style="font-weight: 500;"><?php echo htmlspecialchars($c['employee']); ?></td>
                        <td><?php echo htmlspecialchars($c['note']); ?></td>
                        <td>
                            <?php if ($c['status'] == 'ÄÃ£ cÃ¢n báº±ng'): ?>
                                <span style="background:#eafff0; color:#108043; padding:4px 8px; border-radius:4px; font-size:12px; border: 1px solid #8ce09f; font-weight: 600;">ÄÃ£ cÃ¢n báº±ng</span>
                            <?php else: ?>
                                <span style="background:#f4f6f8; color:#637381; padding:4px 8px; border-radius:4px; font-size:12px; font-weight: 600;"><?php echo htmlspecialchars($c['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="index.php?action=edit_inventory_check&id=<?php echo $c['id']; ?>" style="text-decoration: none; font-size: 16px; margin-right: 12px;" title="Sá»­a phiáº¿u">âœï¸</a>
                            <a href="index.php?action=delete_inventory_check&id=<?php echo $c['id']; ?>" onclick="return confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»‘n xÃ³a phiáº¿u kiá»ƒm kho nÃ y? Tá»“n kho sáº½ Ä‘Æ°á»£c tá»± Ä‘á»™ng hoÃ n láº¡i.');" style="text-decoration: none; font-size: 16px;" title="XÃ³a phiáº¿u">ðŸ—‘ï¸</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiá»ƒn thá»‹ 1 - <?php echo count($checks); ?> trÃªn tá»•ng <?php echo count($checks); ?> phiáº¿u kiá»ƒm kho
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">ðŸ“‹</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">ChÆ°a cÃ³ phiáº¿u kiá»ƒm kho nÃ o</h3>
            <p style="color: #637381; margin-bottom: 20px;">KhÃ´ng tÃ¬m tháº¥y phiáº¿u kiá»ƒm kho phÃ¹ há»£p vá»›i Ä‘iá»u kiá»‡n lá»c.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

