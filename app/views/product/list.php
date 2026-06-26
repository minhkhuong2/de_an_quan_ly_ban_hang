<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $products */
/** @var array $categories */ ?>
<style>
    .Há»‡ thá»‘ng-filter-bar {
        display: flex;
        gap: 10px;
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
    }

    .Há»‡ thá»‘ng-filter-bar input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .Há»‡ thá»‘ng-filter-bar select,
    .Há»‡ thá»‘ng-filter-bar button {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .Há»‡ thá»‘ng-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .Há»‡ thá»‘ng-table th,
    .Há»‡ thá»‘ng-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
        font-size: 14px;
        vertical-align: middle;
        word-wrap: break-word;
    }

    .Há»‡ thá»‘ng-table th {
        color: #637381;
        font-weight: 500;
        background: #fafbfc;
        border-bottom: 1px solid #dfe3e8;
    }

    .col-cb {
        width: 40px;
        text-align: center !important;
    }

    .col-img {
        width: 60px;
    }

    .col-name {
        width: auto;
    }

    .col-num {
        width: 100px;
        text-align: right !important;
    }

    .col-text {
        width: 130px;
    }

    .Há»‡ thá»‘ng-table input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0088ff;
    }

    .Há»‡ thá»‘ng-dropdown {
        position: relative;
        display: inline-block;
    }

    .Há»‡ thá»‘ng-dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 200px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
        border-radius: 4px;
        border: 1px solid #dfe3e8;
        top: 100%;
        left: 0;
        margin-top: 5px;
    }

    .Há»‡ thá»‘ng-dropdown-content::before {
        content: "";
        position: absolute;
        top: -10px;
        left: 0;
        width: 100%;
        height: 10px;
        background: transparent;
    }

    .Há»‡ thá»‘ng-dropdown:hover .Há»‡ thá»‘ng-dropdown-content {
        display: block;
    }

    .Há»‡ thá»‘ng-dropdown-content a,
    .Há»‡ thá»‘ng-dropdown-content label {
        color: #212b36;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
        font-weight: 400;
        font-size: 14px;
        cursor: pointer;
    }

    .Há»‡ thá»‘ng-dropdown-content a:hover,
    .Há»‡ thá»‘ng-dropdown-content label:hover {
        background-color: #f4f6f8;
        color: #0088ff;
    }

    .dropdown-btn {
        background: #0088ff;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .action-btn {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        color: #212b36;
    }

    .badge-type {
        background: #f4f6f8;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        color: #637381;
        border: 1px solid #c4cdd5;
        margin-top: 4px;
        display: inline-block;
    }

    /* CSS cho form Sá»­a tá»“n kho nhanh */
    .stock-form-input {
        width: 100%;
        padding: 6px;
        box-sizing: border-box;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
    }

    .stock-form-input:focus {
        border-color: #0088ff;
    }

    /* Bá»” SUNG CSS CHO PHIÃŠN Báº¢N (VARIANTS) */
    .master-row:hover {
        background: #f9fafb;
        cursor: pointer;
    }

    .variant-container {
        background: #fafbfc;
        padding: 15px 30px;
        border-radius: 6px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        margin: 5px 0;
        border: 1px solid #dfe3e8;
    }

    .variant-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #dfe3e8;
        table-layout: fixed;
    }

    .variant-table th {
        background: #f4f6f8;
        color: #212b36;
        font-size: 13px;
        padding: 10px;
        border-bottom: 1px solid #dfe3e8;
    }

    .variant-table td {
        padding: 10px;
        border-bottom: 1px solid #f4f6f8;
        font-size: 13px;
    }

    .badge-variant {
        background: #e6f7ff;
        color: #0050b3;
        border: 1px solid #91d5ff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
        margin-top: 4px;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sÃ¡ch sáº£n pháº©m</h2>
    <div style="display: flex; gap: 10px; align-items: center;">
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">â†‘ Xuáº¥t file</button>
        <button style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 14px;">â†“ Nháº­p file</button>

        <div class="Há»‡ thá»‘ng-dropdown">
            <button class="dropdown-btn">+ ThÃªm sáº£n pháº©m â–¼</button>
            <div class="Há»‡ thá»‘ng-dropdown-content" style="right: 0; left: auto;">
                <a href="index.php?action=add_product">ThÃªm sáº£n pháº©m thÆ°á»ng</a>
                <a href="index.php?action=add_conversion">ThÃªm phiÃªn báº£n quy Ä‘á»•i</a>
                <a href="index.php?action=add_combo">ThÃªm sáº£n pháº©m Combo</a>
                <div style="height: 1px; background: #dfe3e8; margin: 5px 0;"></div>
                <a href="#">Sáº£n pháº©m LÃ´ - HSD</a>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">âœ… Thao tÃ¡c thÃ nh cÃ´ng!</div><?php endif; ?>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 0; overflow: visible; min-height: 400px;">

    <form action="index.php" method="GET" class="Há»‡ thá»‘ng-filter-bar">
        <input type="hidden" name="action" value="product_list">

        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 10px; top: 10px; color: #637381;">ðŸ”</span>
            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="TÃ¬m kiáº¿m theo mÃ£ SKU, tÃªn sáº£n pháº©m, barcode..." style="padding-left: 35px; width: 100%;">
        </div>

        <select name="type" onchange="this.form.submit()">
            <option value="">-- HÃ¬nh thá»©c SP --</option>
            <option value="ThÆ°á»ng" <?php echo (($_GET['type'] ?? '') == 'ThÆ°á»ng') ? 'selected' : ''; ?>>Sáº£n pháº©m thÆ°á»ng</option>
            <option value="Combo" <?php echo (($_GET['type'] ?? '') == 'Combo') ? 'selected' : ''; ?>>Sáº£n pháº©m Combo</option>
            <option value="Quy Ä‘á»•i" <?php echo (($_GET['type'] ?? '') == 'Quy Ä‘á»•i') ? 'selected' : ''; ?>>Sáº£n pháº©m quy Ä‘á»•i</option>
        </select>

        <select name="category" onchange="this.form.submit()">
            <option value="">-- Danh má»¥c --</option>
            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category_name']); ?>" <?php echo (($_GET['category'] ?? '') == $cat['category_name']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>

        <button type="submit" style="background: #e6f7ff; color: #0088ff; border-color: #91d5ff;">Lá»c káº¿t quáº£</button>
        <?php if (!empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['category'])): ?>
            <a href="index.php?action=product_list" style="text-decoration:none; padding: 8px 12px; color: #ff4d4f; border: 1px solid #ffa39e; border-radius: 4px;">XÃ³a bá»™ lá»c</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($products)): ?>
        <table class="Há»‡ thá»‘ng-table">
            <thead>
                <tr id="normal-header">
                    <th class="col-cb"><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th class="col-img">áº¢nh</th>
                    <th class="col-name">Sáº£n pháº©m</th>
                    <th class="col-num">CÃ³ thá»ƒ bÃ¡n</th>
                    <th class="col-num">Tá»“n kho</th>
                    <th class="col-text">Danh má»¥c</th>
                    <th class="col-text">NhÃ£n hiá»‡u</th>
                    <th class="col-text">NgÃ y táº¡o</th>
                </tr>

                <tr id="action-header" style="display: none; background: #e6f7ff; border-top: 1px solid #91d5ff; border-bottom: 1px solid #91d5ff;">
                    <th class="col-cb"><input type="checkbox" checked onclick="toggleAll(this)"></th>
                    <th colspan="7" style="color: #212b36; font-weight: normal; overflow: visible;">
                        ÄÃ£ chá»n <strong id="selected-count">1</strong> sáº£n pháº©m

                        <div class="Há»‡ thá»‘ng-dropdown" style="margin-left: 20px;">
                            <button class="action-btn">Chá»n thao tÃ¡c â–¼</button>
                            <div class="Há»‡ thá»‘ng-dropdown-content">
                                <a>âœï¸ Sá»­a sáº£n pháº©m hÃ ng loáº¡t</a>
                                <div style="height: 1px; background: #dfe3e8; margin: 5px 0;"></div>
                                <a href="#" id="btn-delete" onclick="return confirm('Sáº£n pháº©m Ä‘Ã£ xÃ³a khÃ´ng thá»ƒ khÃ´i phá»¥c. Báº¡n cÃ³ cháº¯c cháº¯n muá»‘n xÃ³a?');" style="color: #ff4d4f;">ðŸ—‘ï¸ XÃ³a sáº£n pháº©m</a>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $row):
                    // Bá»” SUNG: Chá»‰ hiá»ƒn thá»‹ sáº£n pháº©m cha, áº©n sáº£n pháº©m con Ä‘i trá»« khi Ä‘ang tÃ¬m kiáº¿m
                    if (!empty($_GET['search']) || empty($row['parent_id'])):
                        $hasVariants = !empty($row['variants']);
                ?>
                        <tr class="product-row master-row" onclick="toggleVariants('<?php echo $row['id']; ?>', event)">
                            <td class="col-cb"><input type="checkbox" class="row-checkbox" value="<?php echo $row['id']; ?>" onclick="event.stopPropagation(); toggleRow(this)"></td>

                            <td class="col-img">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image']); ?>" style="width:40px; height:40px; border-radius:4px; object-fit:cover; border:1px solid #dfe3e8;">
                                <?php else: ?>
                                    <div style="width:40px; height:40px; background:#f4f6f8; border: 1px solid #dfe3e8; border-radius:4px; text-align:center; line-height:40px; font-size: 20px;">ðŸ“±</div>
                                <?php endif; ?>
                            </td>

                            <td class="col-name">
                                <div style="display: flex; align-items: flex-start; gap: 6px;">
                                    <?php if ($hasVariants): ?>
                                        <span id="icon-<?php echo $row['id']; ?>" style="color: #637381; font-size: 11px; margin-top: 4px; display: inline-block; width: 12px;">â–¶</span>
                                    <?php endif; ?>

                                    <div>
                                        <a href="index.php?action=edit_product&id=<?php echo $row['id']; ?>" style="color: #0088ff; font-weight: 500; text-decoration: none; font-size: 15px;" onclick="event.stopPropagation();">
                                            <?php echo htmlspecialchars($row['product_name']); ?>
                                        </a><br>
                                        <span style="color: #637381; font-size: 12px;"><?php echo !empty($row['sku']) ? htmlspecialchars($row['sku']) : '---'; ?></span>

                                        <?php if ($hasVariants): ?>
                                            <br><span class="badge-variant"><?php echo count($row['variants']); ?> phiÃªn báº£n</span>
                                        <?php elseif (!empty($row['parent_id'])): ?>
                                            <br><span class="badge-type">ðŸ“¦ Sáº£n pháº©m quy Ä‘á»•i</span>
                                        <?php elseif (isset($row['product_type']) && $row['product_type'] == 'Combo'): ?>
                                            <br><span class="badge-type">ðŸŽ Sáº£n pháº©m Combo</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <td class="col-num" style="color: <?php echo (isset($row['co_the_ban']) && $row['co_the_ban'] > 0) ? '#108043' : '#212b36'; ?>; font-weight: 500;">
                                <?php echo isset($row['co_the_ban']) ? $row['co_the_ban'] : '0'; ?>
                            </td>

                            <td class="col-num" style="position: relative; color: <?php echo (isset($row['ton_kho']) && $row['ton_kho'] > 0) ? '#108043' : '#212b36'; ?>; font-weight: 500;">

                                <div id="stock-view-<?php echo $row['id']; ?>" style="display: flex; justify-content: flex-end; align-items: center; gap: 8px;">
                                    <span><?php echo isset($row['ton_kho']) ? $row['ton_kho'] : '0'; ?></span>
                                    <?php if (empty($row['parent_id']) && ($row['product_type'] ?? '') != 'Combo'): ?>
                                        <a href="javascript:void(0)" onclick="event.stopPropagation(); openStockPopup(<?php echo $row['id']; ?>, <?php echo $row['ton_kho'] ?? 0; ?>)" style="color: #0088ff; text-decoration: none; font-size: 14px;" title="Cáº­p nháº­t tá»“n kho">âœï¸</a>
                                    <?php endif; ?>
                                </div>

                                <div id="stock-popup-<?php echo $row['id']; ?>" onclick="event.stopPropagation();" style="display: none; position: absolute; right: 10px; top: 45px; background: #fff; border: 1px solid #dfe3e8; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 6px; padding: 15px; width: 240px; z-index: 100; text-align: left;">
                                    <div style="font-weight: bold; margin-bottom: 12px; color: #212b36; font-size: 14px; border-bottom: 1px solid #f4f6f8; padding-bottom: 8px;">Chá»‰nh sá»­a tá»“n kho</div>
                                    <form action="index.php?action=quick_update_stock" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                        <div style="margin-bottom: 12px;">
                                            <label style="font-size: 12px; color: #637381; display: block; margin-bottom: 4px; font-weight: 500;">Tá»“n kho má»›i</label>
                                            <input type="number" name="new_stock" id="new_stock_<?php echo $row['id']; ?>" class="stock-form-input" value="<?php echo $row['ton_kho'] ?? 0; ?>" oninput="calcAdj(<?php echo $row['id']; ?>, <?php echo $row['ton_kho'] ?? 0; ?>)">
                                        </div>
                                        <div style="margin-bottom: 15px;">
                                            <label style="font-size: 12px; color: #637381; display: block; margin-bottom: 4px; font-weight: 500;">Äiá»u chá»‰nh (+/-)</label>
                                            <input type="number" id="adj_<?php echo $row['id']; ?>" class="stock-form-input" value="0" oninput="calcNew(<?php echo $row['id']; ?>, <?php echo $row['ton_kho'] ?? 0; ?>)">
                                        </div>
                                        <div style="display: flex; gap: 8px; justify-content: flex-end; padding-top: 5px; border-top: 1px solid #f4f6f8;">
                                            <button type="button" onclick="closeStockPopup(<?php echo $row['id']; ?>)" style="background: #fff; border: 1px solid #c4cdd5; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">Há»§y</button>
                                            <button type="submit" style="background: #0088ff; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">LÆ°u</button>
                                        </div>
                                    </form>
                                </div>
                            </td>

                            <td class="col-text" style="color: #0088ff; font-weight: 500;">
                                <?php echo htmlspecialchars($row['smart_categories'] ?? '---'); ?>
                            </td>

                            <td class="col-text"><?php echo !empty($row['brand']) ? htmlspecialchars($row['brand']) : '---'; ?></td>
                            <td class="col-text" style="color: #637381;"><?php echo date('d/m/Y', strtotime($row['created_at'] ?? date('Y-m-d'))); ?></td>
                        </tr>

                        <?php if ($hasVariants): ?>
                            <tr id="variant-row-<?php echo $row['id']; ?>" style="display: none; background: #fafbfc;">
                                <td colspan="8" style="padding: 10px 20px;">
                                    <div class="variant-container">
                                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color:#212b36;">ðŸ“‹ CÃ¡c phiÃªn báº£n quy Ä‘á»•i / thuá»™c tÃ­nh con:</h4>
                                        <table class="variant-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%;">MÃ£ SKU</th>
                                                    <th style="width: 40%;">TÃªn phiÃªn báº£n</th>
                                                    <th style="width: 15%; text-align: right;">GiÃ¡ vá»‘n</th>
                                                    <th style="width: 15%; text-align: right;">GiÃ¡ bÃ¡n láº»</th>
                                                    <th style="width: 10%; text-align: center;">Tá»“n kho</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($row['variants'] as $variant): ?>
                                                    <tr>
                                                        <td style="font-weight: 600; color: #637381;"><?php echo htmlspecialchars($variant['sku']); ?></td>
                                                        <td>
                                                            <a href="index.php?action=edit_product&id=<?php echo $variant['id']; ?>" style="color: #0088ff; font-weight: 500; text-decoration: none;">
                                                                <?php echo htmlspecialchars($variant['product_name']); ?>
                                                            </a>
                                                        </td>
                                                        <td style="text-align: right; color: #cf1322; font-weight: 500;"><?php echo number_format($variant['cost_price'] ?? 0, 0, ',', '.'); ?> â‚«</td>
                                                        <td style="text-align: right; color: #212b36; font-weight: bold;"><?php echo number_format($variant['price'] ?? ($variant['base_price'] ?? 0), 0, ',', '.'); ?> â‚«</td>
                                                        <td style="text-align: center; font-weight: bold; color: #108043;"><?php echo (int)($variant['stock'] ?? 0); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>

                <?php endif;
                endforeach; ?>
            </tbody>
        </table>

        <div style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; color: #637381; font-size: 14px; border-top: 1px solid #dfe3e8;">
            <span>Hiá»ƒn thá»‹ 1 - <?php echo count($products); ?> trÃªn tá»•ng <?php echo count($products); ?> sáº£n pháº©m</span>
        </div>

    <?php else: ?>
        <?php
        $is_filtering = !empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['category']);
        ?>

        <?php if ($is_filtering): ?>
            <div style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 80px; margin-bottom: 20px;">ðŸ”</div>
                <h3 style="font-size: 20px; color: #212b36; font-weight: bold;">KhÃ´ng tÃ¬m tháº¥y sáº£n pháº©m nÃ o</h3>
                <p style="color: #637381; margin-bottom: 25px;">Thá»­ thay Ä‘á»•i tá»« khÃ³a tÃ¬m kiáº¿m hoáº·c xÃ³a cÃ¡c bá»™ lá»c hiá»‡n táº¡i.</p>
                <a href="index.php?action=product_list" style="background: #fff; border: 1px solid #c4cdd5; color: #212b36; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">XÃ³a bá»™ lá»c</a>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 80px; margin-bottom: 20px;">ðŸ›ï¸</div>
                <h3 style="font-size: 20px; color: #212b36; font-weight: bold;">Cá»­a hÃ ng cá»§a báº¡n chÆ°a cÃ³ sáº£n pháº©m nÃ o</h3>
                <p style="color: #637381; margin-bottom: 25px;">ThÃªm má»›i hoáº·c nháº­p danh sÃ¡ch sáº£n pháº©m Ä‘á»ƒ báº¯t Ä‘áº§u bÃ¡n hÃ ng.</p>
                <a href="index.php?action=add_product" style="background: #0088ff; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500;">+ ThÃªm sáº£n pháº©m ngay</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    // JS HIá»†U á»¨NG CHECKBOX & CHá»ŒN HÃ€NG LOáº T (Cá»¦A KHÆ¯Æ NG)
    function toggleRow(checkbox) {
        checkbox.closest('tr').style.background = checkbox.checked ? '#f4f6f8' : 'transparent';
        updateActionBar();
    }

    function toggleAll(masterCheckbox) {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = masterCheckbox.checked;
            toggleRow(cb);
        });
    }

    function updateActionBar() {
        let checked = document.querySelectorAll('.row-checkbox:checked');
        let normalHeader = document.getElementById('normal-header');
        let actionHeader = document.getElementById('action-header');

        if (checked.length > 0) {
            normalHeader.style.display = 'none';
            actionHeader.style.display = 'table-row';
            document.getElementById('selected-count').innerText = checked.length;
            document.getElementById('btn-delete').href = 'index.php?action=delete_product&id=' + checked[0].value;
        } else {
            normalHeader.style.display = 'table-row';
            actionHeader.style.display = 'none';
        }
    }

    // JS Xá»¬ LÃ POPUP Tá»’N KHO THÃ”NG MINH (Cá»¦A KHÆ¯Æ NG)
    let currentPopupId = null;

    function openStockPopup(id, currentStock) {
        if (currentPopupId && currentPopupId !== id) {
            closeStockPopup(currentPopupId);
        }
        const popup = document.getElementById('stock-popup-' + id);
        const view = document.getElementById('stock-view-' + id);

        if (popup.style.display === 'none' || popup.style.display === '') {
            popup.style.display = 'block';
            view.style.display = 'none';
            document.getElementById('new_stock_' + id).value = currentStock;
            document.getElementById('adj_' + id).value = 0;
            currentPopupId = id;
        } else {
            closeStockPopup(id);
        }
    }

    function closeStockPopup(id) {
        document.getElementById('stock-popup-' + id).style.display = 'none';
        document.getElementById('stock-view-' + id).style.display = 'flex';
        if (currentPopupId === id) currentPopupId = null;
    }

    function calcAdj(id, originalStock) {
        let newStock = parseInt(document.getElementById('new_stock_' + id).value) || 0;
        document.getElementById('adj_' + id).value = newStock - originalStock;
    }

    function calcNew(id, originalStock) {
        let adjustment = parseInt(document.getElementById('adj_' + id).value) || 0;
        document.getElementById('new_stock_' + id).value = originalStock + adjustment;
    }

    document.addEventListener('click', function(event) {
        if (currentPopupId) {
            const popup = document.getElementById('stock-popup-' + currentPopupId);
            const view = document.getElementById('stock-view-' + currentPopupId);
            if (!popup.contains(event.target) && !view.contains(event.target)) {
                closeStockPopup(currentPopupId);
            }
        }
    });

    // Bá»” SUNG: JS Má»ž Rá»˜NG PHIÃŠN Báº¢N Sáº¢N PHáº¨M (VARIANTS)
    function toggleVariants(productId, event) {
        // TrÃ¡nh tÃ¬nh tráº¡ng báº¥m vÃ o checkbox hoáº·c Ã´ nháº­p liá»‡u mÃ  nÃ³ cÅ©ng bá»‹ sá»• xuá»‘ng
        if (event.target.tagName.toLowerCase() === 'a' || event.target.tagName.toLowerCase() === 'input' || event.target.tagName.toLowerCase() === 'button') {
            return;
        }

        const variantRow = document.getElementById('variant-row-' + productId);
        const icon = document.getElementById('icon-' + productId);

        if (variantRow) {
            if (variantRow.style.display === 'none') {
                variantRow.style.display = 'table-row';
                if (icon) icon.innerText = 'â–¼';
            } else {
                variantRow.style.display = 'none';
                if (icon) icon.innerText = 'â–¶';
            }
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

