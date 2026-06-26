<?php
require_once __DIR__ . '/../layout/header.php';
$product = $product ?? [];
$db = (new Database())->getConnection();
$existing_variants = (new ProductModel($db))->getVariantsByProductId($product['id'] ?? 0);
/** @var array $dynamic_branches */ // Máº£ng 5 chi nhÃ¡nh tá»± Ä‘á»™ng tá»« Controller
?>

<style>
    .Há»‡ thá»‘ng-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .Há»‡ thá»‘ng-header-bar h2 {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .Há»‡ thá»‘ng-header-bar h2 a {
        text-decoration: none;
        color: #637381;
        font-size: 18px;
    }

    .Há»‡ thá»‘ng-btn-group button {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid transparent;
        font-size: 14px;
    }

    .btn-cancel {
        background: #fff;
        border-color: #c4cdd5 !important;
        color: #212b36;
        margin-right: 10px;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
    }

    .btn-save:hover {
        background: #0070d2;
    }

    .Há»‡ thá»‘ng-grid {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .Há»‡ thá»‘ng-col-left {
        flex: 0 0 68%;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .Há»‡ thá»‘ng-col-right {
        flex: 0 0 calc(32% - 20px);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .Há»‡ thá»‘ng-card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #212b36;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #212b36;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        transition: all 0.2s;
        font-size: 14px;
        color: #212b36;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #0088ff;
        box-shadow: 0 0 0 1px #0088ff;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }

    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 14px;
        color: #212b36;
    }

    .checkbox-group input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #0088ff;
        margin-top: 2px;
    }

    .upload-box {
        border: 2px dashed #c4cdd5;
        border-radius: 6px;
        padding: 30px;
        text-align: center;
        color: #637381;
        cursor: pointer;
        background: #fafbfc;
        transition: 0.3s;
    }

    .upload-box:hover {
        background: #f4f6f8;
        border-color: #0088ff;
    }

    .link-blue {
        color: #0088ff;
        text-decoration: none;
        font-size: 14px;
    }

    .variant-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background: #fff;
    }

    .variant-table th {
        background: #f4f6f8;
        padding: 10px;
        font-size: 13px;
        color: #212b36;
        text-align: left;
        border-bottom: 1px solid #dfe3e8;
    }

    .variant-table td {
        padding: 10px;
        border-bottom: 1px solid #f4f6f8;
        vertical-align: middle;
        font-size: 13px;
    }

    .variant-input {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 13px;
        outline: none;
    }

    .variant-input:focus {
        border-color: #0088ff;
    }

    .bulk-edit-toolbar {
        display: flex;
        gap: 10px;
        align-items: center;
        background: #fff;
        padding: 10px;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .bulk-btn {
        padding: 6px 12px;
        background: #f4f6f8;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        font-weight: 500;
    }

    .bulk-btn:hover {
        background: #dfe3e8;
    }
</style>

<form action="index.php?action=edit_product&id=<?php echo $product['id'] ?? ''; ?>" method="POST" enctype="multipart/form-data" id="productForm">
    <div class="Há»‡ thá»‘ng-header-bar">
        <h2><a href="index.php?action=product_list">â†</a> Chá»‰nh sá»­a: <?php echo htmlspecialchars($product['product_name'] ?? ''); ?></h2>
        <div class="Há»‡ thá»‘ng-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=product_list'">Há»§y</button>
            <button type="submit" class="btn-save">LÆ°u thay Ä‘á»•i</button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">âœ… Cáº­p nháº­t sáº£n pháº©m thÃ nh cÃ´ng!</div><?php endif; ?>
    <?php if (isset($_GET['success_delete'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e; font-weight:500;">ðŸ—‘ï¸ ÄÃ£ xÃ³a phiÃªn báº£n sáº£n pháº©m thÃ nh cÃ´ng!</div><?php endif; ?>

    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">1. ThÃ´ng tin chung</div>
                <div class="form-group"><label>TÃªn sáº£n pháº©m <span style="color:red;">*</span> ðŸŒŸ</label><input type="text" id="main_product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>" required></div>
                <div class="row-flex">
                    <div class="form-group"><label>MÃ£ sáº£n pháº©m / SKU</label><input type="text" id="main_sku" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Barcode</label><input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($product['barcode'] ?? ''); ?>"></div>
                </div>
                <div class="form-group" style="width: 48%;"><label>ÄÆ¡n vá»‹ tÃ­nh</label><input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($product['unit'] ?? ''); ?>"></div>
                <div class="form-group"><label>MÃ´ táº£ sáº£n pháº©m ðŸŒŸ</label><textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea></div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">2. Thiáº¿t láº­p giÃ¡ sáº£n pháº©m</div>
                <div class="row-flex">
                    <div class="form-group"><label>GiÃ¡ bÃ¡n</label>
                        <div style="display: flex; position: relative;"><input type="text" id="main_price" name="base_price" class="form-control currency-input" value="<?php echo number_format($product['price'] ?? ($product['base_price'] ?? 0), 0, '', '.'); ?>" style="padding-right: 30px; font-weight: bold; color: #212b36;"><span style="position: absolute; right: 10px; top: 10px; color: #637381;">â‚«</span></div>
                    </div>
                    <div class="form-group"><label>GiÃ¡ so sÃ¡nh â“˜</label>
                        <div style="display: flex; position: relative;"><input type="text" name="compare_price" class="form-control currency-input" value="<?php echo number_format($product['compare_price'] ?? 0, 0, '', '.'); ?>" style="padding-right: 30px;"><span style="position: absolute; right: 10px; top: 10px; color: #637381;">â‚«</span></div>
                    </div>
                </div>
                <div class="form-group" style="width: 48%;"><label>GiÃ¡ vá»‘n â“˜</label>
                    <div style="display: flex; position: relative;"><input type="text" id="main_cost" name="cost_price" class="form-control currency-input" value="<?php echo number_format($product['cost_price'] ?? 0, 0, '', '.'); ?>" style="padding-right: 30px; font-weight: bold; color: #cf1322;"><span style="position: absolute; right: 10px; top: 10px; color: #637381;">â‚«</span></div>
                </div>
                <div class="checkbox-group"><input type="checkbox" id="tax" name="apply_tax" value="1" <?php echo (isset($product['apply_tax']) && $product['apply_tax'] == 1) ? 'checked' : ''; ?>><label for="tax" style="margin:0;">Ãp dá»¥ng thuáº¿</label></div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">4. ThÃ´ng tin kho hÃ ng (PhÃ¢n bá»• theo chi nhÃ¡nh)</div>
                <div class="checkbox-group"><input type="checkbox" checked disabled><label style="margin:0;">Quáº£n lÃ½ sá»‘ lÆ°á»£ng tá»“n kho (Theo mÃ£ IMEI)</label></div>
                <div class="checkbox-group"><input type="checkbox" name="allow_negative" id="allow_negative"><label for="allow_negative" style="margin:0;">Cho phÃ©p bÃ¡n Ã¢m</label></div>

                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: #fafbfc; border-top: 1px solid #dfe3e8; border-bottom: 1px solid #dfe3e8;">
                            <th style="padding: 10px 12px; text-align: left; font-size: 13px; color: #212b36;">Kho / Chi nhÃ¡nh</th>
                            <th style="padding: 10px 12px; text-align: left; font-size: 13px; color: #212b36; width: 120px;">Tá»“n kho</th>
                            <th style="padding: 10px 12px; text-align: left; font-size: 13px; color: #212b36;">Vá»‹ trÃ­ lÆ°u kho (Bin Location) â“˜</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($branches_db)): foreach ($branches_db as $b): ?>
                                <tr style="border-bottom: 1px solid #f4f6f8;">
                                    <td style="padding: 10px 12px; font-weight: 500; font-size: 13px; color: #0088ff;">
                                        ðŸ¢ <?php echo htmlspecialchars($b['branch_name']); ?>
                                        <?php if ($b['is_default']): ?><span style="font-size:10px; background:#ffea8a; color:#8a6100; padding:2px 4px; border-radius:4px; margin-left:4px;">Máº·c Ä‘á»‹nh</span><?php endif; ?>
                                    </td>
                                    <td style="padding: 10px 12px;">
                                        <input type="number" name="branch_stock[<?php echo $b['id']; ?>]" class="form-control" value="0" style="padding: 6px;">
                                    </td>
                                    <td style="padding: 10px 12px;">
                                        <input type="text" name="branch_location[<?php echo $b['id']; ?>]" class="form-control" placeholder="VD: A-D10-K456" style="padding: 6px;">
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="3" style="padding: 15px; text-align: center; color: #cf1322;">Vui lÃ²ng vÃ o Cáº¥u hÃ¬nh -> Quáº£n lÃ½ chi nhÃ¡nh Ä‘á»ƒ táº¡o kho trÆ°á»›c!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">5. ThÃ´ng tin váº­n chuyá»ƒn</div>
                <div class="checkbox-group">
                    <input type="checkbox" checked name="require_shipping" id="require_shipping" onchange="document.getElementById('weight-box').style.display = this.checked ? 'block' : 'none';">
                    <label for="require_shipping" style="margin:0;">Sáº£n pháº©m yÃªu cáº§u váº­n chuyá»ƒn</label>
                </div>

                <div id="weight-box" style="margin-top: 15px; display: block;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px;">Khá»‘i lÆ°á»£ng</label>
                    <div style="display: flex; width: 50%;">
                        <input type="number" name="weight" class="form-control" value="0" style="border-radius: 4px 0 0 4px; border-right: none;">
                        <select name="weight_unit" class="form-control" style="width: 80px; border-radius: 0 4px 4px 0; background: #fafbfc;">
                            <option value="g">g</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>
                    <p style="font-size: 12px; color: #637381; margin-top: 5px;">Khá»‘i lÆ°á»£ng dÃ¹ng Ä‘á»ƒ tÃ­nh phÃ­ váº­n chuyá»ƒn cá»§a bÆ°u tÃ¡.</p>
                </div>
            </div>

            <?php if (!empty($existing_variants)): ?>
                <div class="Há»‡ thá»‘ng-card">
                    <div class="Há»‡ thá»‘ng-card-title" style="color: #0050b3; margin-bottom: 5px;">ðŸ“¦ CÃ¡c phiÃªn báº£n hiá»‡n táº¡i</div>
                    <table class="variant-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">TÃªn phiÃªn báº£n</th>
                                <th style="width: 20%;">MÃ£ SKU</th>
                                <th style="width: 20%;">GiÃ¡ bÃ¡n (â‚«)</th>
                                <th style="width: 15%; text-align: center;">Tá»“n kho</th>
                                <th style="width: 15%; text-align: center;">Thao tÃ¡c</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($existing_variants as $v): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #0088ff;"><?php echo htmlspecialchars($v['product_name']); ?></td>
                                    <td style="color: #637381; font-weight: 500;"><?php echo htmlspecialchars($v['sku']); ?></td>
                                    <td style="font-weight: bold; color: #212b36;"><?php echo number_format($v['price'] ?? $v['base_price'] ?? 0, 0, ',', '.'); ?> â‚«</td>
                                    <td style="text-align: center; font-weight: bold; color: #108043;"><?php echo $v['stock']; ?></td>
                                    <td style="text-align: center;">
                                        <a href="index.php?action=edit_product&id=<?php echo $v['id']; ?>" style="color:#ff9900; text-decoration:none; font-weight:bold; background:#fff8ea; padding:4px 8px; border-radius:4px;">âœï¸ Sá»­a</a>
                                        <a href="index.php?action=delete_product&id=<?php echo $v['id']; ?>&parent_id=<?php echo $product['id']; ?>" onclick="return confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»‘n xÃ³a phiÃªn báº£n nÃ y?');" style="color:#cf1322; text-decoration:none; font-weight:bold; background:#fff1f0; padding:4px 8px; border-radius:4px; margin-left: 5px;">ðŸ—‘ï¸ XÃ³a</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="Há»‡ thá»‘ng-card" id="attribute-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div class="Há»‡ thá»‘ng-card-title" style="margin:0;">6. Bá»• sung Thuá»™c tÃ­nh / PhiÃªn báº£n má»›i</div>
                    <a href="javascript:void(0)" class="link-blue" onclick="addAttributeRow()">+ ThÃªm thuá»™c tÃ­nh má»›i</a>
                </div>
                <p style="font-size: 14px; color: #637381; margin: 0; margin-bottom: 15px;" id="attr-hint">Nháº­p mÃ u sáº¯c/dung lÆ°á»£ng má»›i Ä‘á»ƒ tá»± Ä‘á»™ng sinh phiÃªn báº£n.</p>
                <table style="width: 100%; border-collapse: collapse;" id="attributeTable">
                    <tbody id="attributeBody"></tbody>
                </table>
            </div>

            <div class="Há»‡ thá»‘ng-card" id="variants-card" style="display: none; border: 1px solid #91d5ff; background: #e6f7ff;">
                <div class="Há»‡ thá»‘ng-card-title" style="color: #0050b3; margin-bottom: 5px;">ðŸš€ Danh sÃ¡ch phiÃªn báº£n má»›i</div>
                <div class="bulk-edit-toolbar">
                    <strong style="font-size: 13px; color: #212b36;">Sá»­a nhanh hÃ ng loáº¡t:</strong>
                    <input type="text" id="bulk_price" class="variant-input currency-input" placeholder="GiÃ¡ bÃ¡n..." style="width: 100px;">
                    <button type="button" class="bulk-btn" onclick="applyBulk('var_price', 'bulk_price')">Ãp dá»¥ng GiÃ¡</button>
                    <input type="text" id="bulk_cost" class="variant-input currency-input" placeholder="GiÃ¡ vá»‘n..." style="width: 100px; margin-left: 10px;">
                    <button type="button" class="bulk-btn" onclick="applyBulk('var_cost', 'bulk_cost')">Ãp dá»¥ng Vá»‘n</button>
                    <input type="number" id="bulk_stock" class="variant-input" placeholder="Tá»“n kho..." style="width: 90px; margin-left: 10px;">
                    <button type="button" class="bulk-btn" onclick="applyBulk('var_stock', 'bulk_stock')">Ãp dá»¥ng Kho</button>
                </div>
                <table class="variant-table" id="variantsTable">
                    <thead>
                        <tr>
                            <th style="width: 25%;">PhiÃªn báº£n má»›i</th>
                            <th style="width: 18%;">MÃ£ SKU</th>
                            <th style="width: 18%;">GiÃ¡ bÃ¡n (â‚«)</th>
                            <th style="width: 18%;">GiÃ¡ vá»‘n (â‚«)</th>
                            <th style="width: 11%; text-align: center;">Tá»“n kho</th>
                            <th style="width: 10%; text-align: center;">Thao tÃ¡c</th>
                        </tr>
                    </thead>
                    <tbody id="variantsBody"></tbody>
                </table>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">3. ThÃªm áº£nh sáº£n pháº©m</div>
                <div class="upload-box" onclick="document.getElementById('file-upload').click()">
                    <input type="file" id="file-upload" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                    <div id="upload-placeholder" style="display: <?php echo !empty($product['image']) ? 'none' : 'block'; ?>;">
                        <div style="font-size: 24px; color: #0088ff; margin-bottom: 10px;">+</div>KÃ©o tháº£ hoáº·c táº£i áº£nh tá»« thiáº¿t bá»‹
                    </div>
                    <img id="image-preview" src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : ''; ?>" style="display: <?php echo !empty($product['image']) ? 'block' : 'none'; ?>; max-width: 100%; max-height: 200px; margin: 0 auto; border-radius: 6px; object-fit: cover;">
                </div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">8. KÃªnh bÃ¡n hÃ ng</div>
                <div class="checkbox-group" style="align-items: flex-start;"><input type="checkbox" checked>
                    <div><label style="margin:0; font-weight: 500;">Website</label><br><a href="#" class="link-blue" style="font-size: 13px;">Äáº·t lá»‹ch hiá»ƒn thá»‹</a></div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;"><input type="checkbox" checked>
                    <div><label style="margin:0; font-weight: 500;">POS</label><br><a href="#" class="link-blue" style="font-size: 13px;">Ãp dá»¥ng báº£ng giÃ¡ POS</a></div>
                </div>
                <div class="checkbox-group" style="align-items: flex-start; margin-top: 15px;"><input type="checkbox" checked>
                    <div><label style="margin:0; font-weight: 500;"></label></div>
                </div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">10. ThÃ´ng tin bá»• sung</div>
                <div class="form-group"><label>10.1 Danh má»¥c â“˜</label><select class="form-control" name="category">
                        <option value="">Chá»n danh má»¥c</option><?php if (!empty($dynamic_categories)): foreach ($dynamic_categories as $catName): ?><option value="<?php echo htmlspecialchars($catName); ?>" <?php echo (strcasecmp(($product['category'] ?? ''), $catName) == 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($catName); ?></option><?php endforeach;
                                                                                                                                                                                                                                                                                                                                                    endif; ?>
                    </select></div>
                <div class="form-group"><label>10.2 NhÃ£n hiá»‡u</label><input type="text" name="brand" list="brand_list" class="form-control" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>" placeholder="GÃµ hoáº·c chá»n nhÃ£n hiá»‡u..."><datalist id="brand_list"><?php if (!empty($dynamic_brands)): foreach ($dynamic_brands as $brandName): ?><option value="<?php echo htmlspecialchars($brandName); ?>"></option><?php endforeach;
                                                                                                                                                                                                                                                                                                                                                                                                                            endif; ?></datalist></div>
                <div class="form-group"><label>10.3 Loáº¡i sáº£n pháº©m</label><input type="text" list="type_list" class="form-control" placeholder="GÃµ hoáº·c chá»n loáº¡i SP..."><datalist id="type_list"><?php if (!empty($dynamic_types)): foreach ($dynamic_types as $typeName): ?><option value="<?php echo htmlspecialchars($typeName); ?>"></option><?php endforeach;
                                                                                                                                                                                                                                                                                                                                            endif; ?></datalist></div>
                <div class="form-group">
                    <label>10.4 NhÃ³m ngÃ nh nghá» tÃ­nh thuáº¿ â“˜</label>
                    <select class="form-control" name="tax_category">
                        <option value="">Chá»n nhÃ³m ngÃ nh nghá»</option>
                        <option value="101">101 - Hoáº¡t Ä‘á»™ng bÃ¡n buÃ´n, bÃ¡n láº» hÃ ng hÃ³a</option>
                        <option value="201">201 - Dá»‹ch vá»¥ lÆ°u trÃº, bá»‘c xáº¿p, bÆ°u chÃ­nh...</option>
                        <option value="301">301 - Sáº£n xuáº¥t, gia cÃ´ng, cháº¿ biáº¿n...</option>
                        <option value="401">401 - Hoáº¡t Ä‘á»™ng kinh doanh khÃ¡c (thuáº¿ 5%)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.getElementById('productForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
    });

    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('image-preview');
            output.src = reader.result;
            output.style.display = 'block';
            document.getElementById('upload-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    let attributeCount = 0;

    function addAttributeRow() {
        if (attributeCount >= 3) {
            alert("Tá»‘i Ä‘a 3 thuá»™c tÃ­nh cho má»™t sáº£n pháº©m.");
            return;
        }
        document.getElementById('attr-hint').style.display = 'none';
        let tbody = document.getElementById('attributeBody');
        let tr = document.createElement('tr');
        tr.className = "attr-row";
        tr.innerHTML = `
            <td style="padding: 10px 0; border-top: 1px solid #f4f6f8;">
                <div class="row-flex" style="align-items: flex-end;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;"><label style="font-size: 13px;">TÃªn thuá»™c tÃ­nh</label><input type="text" class="form-control attr-name-input" placeholder="VD: MÃ u sáº¯c..."></div>
                    <div class="form-group" style="flex: 2; margin-bottom: 0;"><label style="font-size: 13px;">GiÃ¡ trá»‹ (Nháº­p rá»“i báº¥m Enter hoáº·c pháº©y)</label><input type="text" class="form-control attr-val-input" placeholder="VD: Äá», Xanh" onkeyup="handleAttrInput(event, this)"></div>
                    <div style="padding-bottom: 8px;"><a href="javascript:void(0)" onclick="removeAttributeRow(this)" style="color: #ff4d4f; font-size: 20px; font-weight: bold; padding: 0 10px; text-decoration: none;">Ã—</a></div>
                </div>
            </td>`;
        tbody.appendChild(tr);
        attributeCount++;
    }

    function handleAttrInput(e, inputElem) {
        if (e.key === 'Enter' || e.key === ',') {
            if (e.key === 'Enter') inputElem.value += ', ';
            generateVariants();
        }
    }

    function removeAttributeRow(btn) {
        btn.closest('tr').remove();
        attributeCount--;
        if (attributeCount === 0) document.getElementById('attr-hint').style.display = 'block';
        generateVariants();
    }

    function cartesianProduct(arr) {
        return arr.reduce((a, b) => a.flatMap(x => b.map(y => [...x, y])), [
            []
        ]);
    }

    function generateVariants() {
        let attrRows = document.querySelectorAll('.attr-row');
        let validAttributes = [];
        attrRows.forEach(row => {
            let vals = row.querySelector('.attr-val-input').value.split(',').map(v => v.trim()).filter(v => v !== '');
            if (vals.length > 0) validAttributes.push(vals);
        });

        let variantsCard = document.getElementById('variants-card');
        let variantsBody = document.getElementById('variantsBody');
        variantsBody.innerHTML = '';
        if (validAttributes.length === 0) {
            variantsCard.style.display = 'none';
            return;
        }

        variantsCard.style.display = 'block';
        let mainPrice = document.getElementById('main_price').value || "0";
        let mainCost = document.getElementById('main_cost').value || "0";
        let mainSku = document.getElementById('main_sku').value || "SKU";

        cartesianProduct(validAttributes).forEach((combo, index) => {
            let variantName = combo.join(' - ');
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="font-weight: bold; color: #0088ff;">${variantName}<input type="hidden" name="var_name[]" value="${variantName}"></td>
                <td><input type="text" name="var_sku[]" class="variant-input" value="${mainSku}-${Math.floor(Math.random() * 1000)}"></td>
                <td><input type="text" name="var_price[]" class="variant-input var-currency var_price" value="${mainPrice}"></td>
                <td><input type="text" name="var_cost[]" class="variant-input var-currency var_cost" value="${mainCost}"></td>
                <td><input type="number" name="var_stock[]" class="variant-input var_stock" value="0" style="text-align: center;"></td>
                <td style="text-align: center;"><a href="javascript:void(0)" onclick="this.closest('tr').remove()" style="color:#cf1322; font-size:16px; text-decoration:none;" title="XÃ³a báº£n nÃ y">ðŸ—‘ï¸</a></td>`;
            variantsBody.appendChild(tr);
        });
        attachCurrencyFormat();
    }

    function applyBulk(targetClass, inputId) {
        let val = document.getElementById(inputId).value;
        if (val === "") return;
        document.querySelectorAll('.' + targetClass).forEach(input => {
            input.value = val;
        });
    }

    // CHá»ˆNH Sá»¬A KHO Gá»C
    const originalStock = <?php echo (int)($product['stock'] ?? 0); ?>;

    function toggleStockEdit() {
        const box = document.getElementById('stock-edit-box');
        const display = document.getElementById('stock-display');
        if (box.style.display === 'none') {
            box.style.display = 'block';
            display.style.display = 'none';
        } else {
            box.style.display = 'none';
            display.style.display = 'flex';
        }
    }

    function calculateStockDiff() {
        document.getElementById('stock_adjustment').value = (parseInt(document.getElementById('new_stock').value) || 0) - originalStock;
    }

    function calculateNewStock() {
        document.getElementById('new_stock').value = originalStock + (parseInt(document.getElementById('stock_adjustment').value) || 0);
    }

    function attachCurrencyFormat() {
        document.querySelectorAll('.var-currency, .currency-input').forEach(function(input) {
            let newObj = input.cloneNode(true);
            input.parentNode.replaceChild(newObj, input);
            newObj.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value !== '') e.target.value = parseInt(value, 10).toLocaleString('vi-VN').replace(/,/g, '.');
                else e.target.value = '';
            });
        });
    }
    attachCurrencyFormat();
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>

