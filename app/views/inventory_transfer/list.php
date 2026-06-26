<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $transfers */ ?>

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
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sÃ¡ch phiáº¿u chuyá»ƒn kho</h2>
    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">â†‘ Xuáº¥t file</button>
        <a href="index.php?action=add_transfer" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Táº¡o phiáº¿u chuyá»ƒn kho</a>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; min-height: 500px;">

    <form action="index.php" method="GET" class="Há»‡ thá»‘ng-filter-bar">
        <input type="hidden" name="action" value="transfer_list">

        <div style="position: relative; flex: 1; max-width: 350px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">ðŸ”</span>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="TÃ¬m kiáº¿m theo mÃ£ phiáº¿u...">
        </div>

        <select name="status">
            <option value="">-- Táº¥t cáº£ tráº¡ng thÃ¡i --</option>
            <option value="Phiáº¿u nhÃ¡p" <?php echo (($_GET['status'] ?? '') == 'Phiáº¿u nhÃ¡p') ? 'selected' : ''; ?>>Phiáº¿u nhÃ¡p</option>
            <option value="Äang chuyá»ƒn" <?php echo (($_GET['status'] ?? '') == 'Äang chuyá»ƒn') ? 'selected' : ''; ?>>Äang chuyá»ƒn</option>
            <option value="ÄÃ£ nháº­n" <?php echo (($_GET['status'] ?? '') == 'ÄÃ£ nháº­n') ? 'selected' : ''; ?>>ÄÃ£ nháº­n</option>
        </select>

        <button type="submit" class="filter-btn">Lá»c</button>

        <?php if (!empty($_GET['search']) || !empty($_GET['status'])): ?>
            <a href="index.php?action=transfer_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; margin-left: 10px;">XÃ³a bá»™ lá»c</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($transfers)): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">MÃ£ phiáº¿u</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">NgÃ y táº¡o</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Chi nhÃ¡nh chuyá»ƒn</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Chi nhÃ¡nh nháº­n</th>
                    <th style="padding: 12px; text-align: center; color: #637381; font-weight: 500;">Sá»‘ lÆ°á»£ng</th>
                    <th style="padding: 12px; text-align: center; color: #637381; font-weight: 500;">Tráº¡ng thÃ¡i</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transfers as $row): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8; cursor: pointer;" onclick="window.location.href='index.php?action=view_transfer&id=<?php echo $row['id']; ?>'">
                        <td style="padding: 12px; font-weight: bold; color: #0088ff;">#TRN<?php echo $row['id']; ?></td>
                        <td style="padding: 12px; color: #212b36;"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td style="padding: 12px; color: #212b36; font-weight: 500;">ðŸ  <?php echo htmlspecialchars($row['from_branch']); ?></td>
                        <td style="padding: 12px; color: #212b36; font-weight: 500;">ðŸ¢ <?php echo htmlspecialchars($row['to_branch']); ?></td>
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #212b36;"><?php echo $row['total_qty']; ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <?php if ($row['status'] == 'Phiáº¿u nhÃ¡p'): ?>
                                <span style="background: #f4f6f8; color: #637381; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Phiáº¿u nhÃ¡p</span>
                            <?php elseif ($row['status'] == 'Äang chuyá»ƒn'): ?>
                                <span style="background: #e6f7ff; color: #0088ff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Äang chuyá»ƒn</span>
                            <?php else: ?>
                                <span style="background: #eafff0; color: #108043; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">ÄÃ£ nháº­n</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiá»ƒn thá»‹ 1 - <?php echo count($transfers); ?> trÃªn tá»•ng <?php echo count($transfers); ?> phiáº¿u chuyá»ƒn kho
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">ðŸšš</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">ChÆ°a cÃ³ phiáº¿u chuyá»ƒn kho nÃ o</h3>
            <p style="color: #637381; margin-bottom: 20px;">KhÃ´ng tÃ¬m tháº¥y phiáº¿u chuyá»ƒn kho phÃ¹ há»£p vá»›i Ä‘iá»u kiá»‡n lá»c.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

