<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $inventoryList */ ?>

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
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .filter-btn:hover {
        background: #f4f6f8;
    }

    .Há»‡ thá»‘ng-table {
        width: 100%;
        border-collapse: collapse;
    }

    .Há»‡ thá»‘ng-table th,
    .Há»‡ thá»‘ng-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
        font-size: 14px;
        vertical-align: middle;
    }

    .Há»‡ thá»‘ng-table th {
        color: #637381;
        font-weight: 500;
        background: #fafbfc;
        border-bottom: 1px solid #dfe3e8;
    }

    .col-img {
        width: 50px;
    }

    .col-num {
        text-align: right !important;
    }

    /* Giao diá»‡n bá»™ lá»c Tá»“n kho Xá»• xuá»‘ng */
    .filter-dropdown {
        position: relative;
        display: inline-block;
    }

    .filter-dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 250px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
        border-radius: 4px;
        border: 1px solid #dfe3e8;
        top: 100%;
        left: 0;
        margin-top: 5px;
        padding: 15px;
    }

    .Há»‡ thá»‘ng-tabs {
        display: flex;
        gap: 20px;
        border-bottom: 1px solid #dfe3e8;
        padding: 0 20px;
        background: #f4f6f8;
        border-radius: 8px 8px 0 0;
    }

    .Há»‡ thá»‘ng-tab {
        padding: 15px 0;
        color: #637381;
        cursor: pointer;
        font-weight: 500;
        font-size: 14px;
        border-bottom: 2px solid transparent;
    }

    .Há»‡ thá»‘ng-tab.active {
        color: #0088ff;
        border-bottom: 2px solid #0088ff;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Quáº£n lÃ½ kho</h2>
    <div style="display: flex; gap: 10px;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">â†‘ Xuáº¥t file</button>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; min-height: 500px;">

    <div class="Há»‡ thá»‘ng-tabs">
        <div class="Há»‡ thá»‘ng-tab active">Táº¥t cáº£ phiÃªn báº£n sáº£n pháº©m</div>
        <div class="Há»‡ thá»‘ng-tab">Sáº¯p háº¿t hÃ ng</div>
        <div class="Há»‡ thá»‘ng-tab">VÆ°á»£t Ä‘á»‹nh má»©c</div>
    </div>

    <form action="index.php" method="GET" class="Há»‡ thá»‘ng-filter-bar">
        <input type="hidden" name="action" value="inventory_list">

        <div style="position: relative; flex: 1; max-width: 400px;">
            <span style="position: absolute; left: 10px; top: 9px; color: #637381;">ðŸ”</span>
            <input type="text" name="search" class="search-input" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="TÃ¬m tÃªn phiÃªn báº£n, SKU, Barcode">
        </div>

        <div class="filter-dropdown" id="stockFilterDiv">
            <button type="button" class="filter-btn" onclick="toggleStockFilter()">
                Tá»“n kho <span>â–¼</span>
            </button>
            <div class="filter-dropdown-content" id="stockFilterPopup">
                <div style="font-weight: 500; margin-bottom: 10px; font-size: 14px;">Khoáº£ng tá»“n kho</div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="number" name="stock_min" value="<?php echo htmlspecialchars($_GET['stock_min'] ?? ''); ?>" placeholder="Tá»«..." style="width: 100%; padding: 8px; border: 1px solid #c4cdd5; border-radius: 4px;">
                    <span>-</span>
                    <input type="number" name="stock_max" value="<?php echo htmlspecialchars($_GET['stock_max'] ?? ''); ?>" placeholder="Äáº¿n..." style="width: 100%; padding: 8px; border: 1px solid #c4cdd5; border-radius: 4px;">
                </div>
                <div style="margin-top: 15px; text-align: right;">
                    <button type="button" onclick="toggleStockFilter()" style="padding: 6px 12px; background: #fff; border: 1px solid #c4cdd5; border-radius: 4px; cursor: pointer; margin-right: 5px;">Há»§y</button>
                    <button type="submit" style="padding: 6px 12px; background: #0088ff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Lá»c</button>
                </div>
            </div>
        </div>

        <button type="button" class="filter-btn">NgÃ y táº¡o <span>â–¼</span></button>
        <button type="button" class="filter-btn">Bá»™ lá»c khÃ¡c <span>âš™ï¸</span></button>

        <?php if (!empty($_GET['search']) || isset($_GET['stock_min']) && $_GET['stock_min'] !== '' || isset($_GET['stock_max']) && $_GET['stock_max'] !== ''): ?>
            <a href="index.php?action=inventory_list" style="color: #ff4d4f; text-decoration: none; font-size: 14px; margin-left: 10px;">XÃ³a bá»™ lá»c</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($inventoryList)): ?>
        <table class="Há»‡ thá»‘ng-table">
            <thead>
                <tr>
                    <th class="col-img">áº¢nh</th>
                    <th>Sáº£n pháº©m / PhiÃªn báº£n</th>
                    <th>SKU / Barcode</th>
                    <th class="col-num">CÃ³ thá»ƒ bÃ¡n â†•</th>
                    <th class="col-num">Tá»“n kho â†•</th>
                    <th class="col-num">Äang giao dá»‹ch</th>
                    <th class="col-num">Äang vá» kho</th>
                    <th class="col-num">GiÃ¡ bÃ¡n</th>
                    <th class="col-num">GiÃ¡ vá»‘n</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventoryList as $row): ?>
                    <tr>
                        <td class="col-img">
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" style="width:40px; height:40px; border-radius:4px; object-fit:cover; border:1px solid #dfe3e8;">
                            <?php else: ?>
                                <div style="width:40px; height:40px; background:#f4f6f8; border: 1px solid #dfe3e8; border-radius:4px; text-align:center; line-height:40px;">ðŸ“¦</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?action=edit_product&id=<?php echo $row['id']; ?>" style="color: #0088ff; font-weight: 500; text-decoration: none;">
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </a><br>
                            <span style="color: #637381; font-size: 12px;">ÄVT: <?php echo htmlspecialchars($row['unit'] ?? '---'); ?></span>
                        </td>
                        <td style="color: #637381; font-size: 13px;">
                            SKU: <?php echo !empty($row['sku']) ? htmlspecialchars($row['sku']) : '---'; ?><br>
                            Bar: <?php echo !empty($row['barcode']) ? htmlspecialchars($row['barcode']) : '---'; ?>
                        </td>
                        <td class="col-num" style="color: #108043; font-weight: bold;">
                            <?php echo $row['co_the_ban']; ?>
                        </td>
                        <td class="col-num" style="color: #212b36; font-weight: bold;">
                            <?php echo $row['ton_kho']; ?>
                        </td>
                        <td class="col-num" style="color: #637381;"><?php echo $row['dang_giao_dich']; ?></td>
                        <td class="col-num" style="color: #d82c0d;"><?php echo $row['dang_ve_kho']; ?></td>
                        <td class="col-num"><?php echo number_format($row['base_price'], 0, ',', '.'); ?>â‚«</td>
                        <td class="col-num"><?php echo number_format($row['cost_price'], 0, ',', '.'); ?>â‚«</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            Hiá»ƒn thá»‹ 1 - <?php echo count($inventoryList); ?> trÃªn tá»•ng <?php echo count($inventoryList); ?> phiÃªn báº£n
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 60px; margin-bottom: 20px; color: #c4cdd5;">ðŸ“¦</div>
            <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">KhÃ´ng cÃ³ dá»¯ liá»‡u tá»“n kho</h3>
            <p style="color: #637381;">KhÃ´ng tÃ¬m tháº¥y phiÃªn báº£n sáº£n pháº©m nÃ o phÃ¹ há»£p vá»›i bá»™ lá»c hiá»‡n táº¡i.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    // JS Má»Ÿ popup lá»c khoáº£ng Tá»“n kho
    function toggleStockFilter() {
        var popup = document.getElementById("stockFilterPopup");
        if (popup.style.display === "block") {
            popup.style.display = "none";
        } else {
            popup.style.display = "block";
        }
    }

    // ÄÃ³ng popup khi click ra ngoÃ i
    document.addEventListener('click', function(event) {
        var div = document.getElementById('stockFilterDiv');
        var popup = document.getElementById('stockFilterPopup');
        if (!div.contains(event.target)) {
            popup.style.display = 'none';
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

