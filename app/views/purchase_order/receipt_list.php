<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $receipts */ ?>

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

    .filter-btn {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: #212b36;
    }

    .filter-btn:hover {
        background: #f4f6f8;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sÃ¡ch Ä‘Æ¡n nháº­p hÃ ng</h2>
    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">â†‘ Xuáº¥t file</button>
        <a href="index.php?action=direct_receive" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ Táº¡o Ä‘Æ¡n nháº­p hÃ ng</a>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; min-height: 500px;">

    <form action="index.php" method="GET" class="Há»‡ thá»‘ng-filter-bar">
        <input type="hidden" name="action" value="po_receipt_list">

        <div style="position: relative; flex: 1; max-width: 400px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">ðŸ”</span>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="TÃ¬m kiáº¿m theo mÃ£ Ä‘Æ¡n, nhÃ  cung cáº¥p...">
        </div>

        <button type="button" class="filter-btn">Tráº¡ng thÃ¡i â–¼</button>
        <button type="button" class="filter-btn">Bá»™ lá»c khÃ¡c âš™ï¸</button>

        <?php if (!empty($_GET['search'])): ?>
            <a href="index.php?action=po_receipt_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; margin-left: 10px;">XÃ³a bá»™ lá»c</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($receipts)): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #fafbfc; border-bottom: 1px solid #dfe3e8;">
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">MÃ£ phiáº¿u nháº­p</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">NgÃ y nháº­p</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">NhÃ  cung cáº¥p</th>
                    <th style="padding: 12px; text-align: left; color: #637381; font-weight: 500;">Chi nhÃ¡nh</th>
                    <th style="padding: 12px; text-align: center; color: #637381; font-weight: 500;">Sá»‘ lÆ°á»£ng</th>
                    <th style="padding: 12px; text-align: right; color: #637381; font-weight: 500;">GiÃ¡ trá»‹ Ä‘Æ¡n</th>
                    <th style="padding: 12px; text-align: center; color: #637381; font-weight: 500;">Tráº¡ng thÃ¡i</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receipts as $row): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8;">
                        <td style="padding: 12px; font-weight: 500;">
                            <a href="index.php?action=view_purchase&id=<?php echo $row['id']; ?>" style="color: #0088ff; text-decoration: none;">
                                #PN<?php echo $row['id']; ?>
                            </a>
                        </td>
                        <td style="padding: 12px; color: #212b36;"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td style="padding: 12px; color: #212b36;"><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                        <td style="padding: 12px; color: #637381;"><?php echo htmlspecialchars($row['branch']); ?></td>
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #212b36;"><?php echo $row['total_qty']; ?></td>
                        <td style="padding: 12px; text-align: right; font-weight: 500; color: #212b36;"><?php echo number_format($row['total_amount'], 0, ',', '.'); ?>â‚«</td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: #eafff0; color: #108043; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; border: 1px solid #8ce09f;">ÄÃ£ nháº­p kho</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiá»ƒn thá»‹ 1 - <?php echo count($receipts); ?> trÃªn tá»•ng <?php echo count($receipts); ?> phiáº¿u nháº­p
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">ðŸ“¦</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">ChÆ°a cÃ³ phiáº¿u nháº­p hÃ ng nÃ o</h3>
            <p style="color: #637381; margin-bottom: 20px;">Phiáº¿u nháº­p hÃ ng sáº½ xuáº¥t hiá»‡n á»Ÿ Ä‘Ã¢y sau khi báº¡n xÃ¡c nháº­n nháº­p kho tá»« ÄÆ¡n Ä‘áº·t hÃ ng hoáº·c Nháº­p hÃ ng trá»±c tiáº¿p.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

