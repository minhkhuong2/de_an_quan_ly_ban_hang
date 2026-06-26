<?php
require_once __DIR__ . '/../layout/header.php';
$category = $category ?? [];
?>
<style>
    .Há»‡ thá»‘ng-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-cancel {
        background: #fff;
        border: 1px solid #c4cdd5;
        padding: 8px 16px;
        border-radius: 4px;
        color: #212b36;
        text-decoration: none;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
    }

    .Há»‡ thá»‘ng-grid {
        display: flex;
        gap: 20px;
    }

    .Há»‡ thá»‘ng-col-left {
        flex: 0 0 68%;
    }

    .Há»‡ thá»‘ng-col-right {
        flex: 1;
    }

    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .Há»‡ thá»‘ng-card {
        overflow: hidden;
        box-sizing: border-box;
    }

    .form-control {
        box-sizing: border-box;
        width: 100%;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        margin-top: 5px;
        margin-bottom: 15px;
    }

    .radio-box {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .rule-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
    }
</style>

<form action="" method="POST">
    <div class="Há»‡ thá»‘ng-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_category" style="text-decoration:none; color:#637381; margin-right:10px;">â†</a> <?php echo htmlspecialchars($category['category_name'] ?? ''); ?></h2>
        <div><a href="index.php?action=product_category" class="btn-cancel">Há»§y</a> <button type="submit" class="btn-save">LÆ°u thay Ä‘á»•i</button></div>
    </div>

    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">âœ… Táº¡o danh má»¥c thÃ nh cÃ´ng!</div><?php endif; ?>
    <?php if (isset($_GET['updated'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">âœ… Cáº­p nháº­t danh má»¥c thÃ nh cÃ´ng!</div><?php endif; ?>

    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">ThÃ´ng tin chung</h3>
                <label>TÃªn danh má»¥c *</label> <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category['category_name'] ?? ''); ?>" required>
                <label>MÃ´ táº£</label> <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Chá»n sáº£n pháº©m</h3>
                <label class="radio-box">
                    <input type="radio" name="selection_type" value="manual" onchange="toggleAutoRules()" <?php echo (($category['selection_type'] ?? 'manual') == 'manual') ? 'checked' : ''; ?>> <strong>ThÃªm sáº£n pháº©m thá»§ cÃ´ng</strong>
                </label>
                <label class="radio-box" style="margin-top:15px;">
                    <input type="radio" name="selection_type" value="auto" onchange="toggleAutoRules()" <?php echo (($category['selection_type'] ?? '') == 'auto') ? 'checked' : ''; ?>> <strong>ThÃªm sáº£n pháº©m tá»± Ä‘á»™ng</strong>
                </label>

                <div id="auto-rules-container" style="display: <?php echo (($category['selection_type'] ?? '') == 'auto') ? 'block' : 'none'; ?>; background: #fafbfc; border: 1px solid #dfe3e8; padding: 15px; border-radius: 6px; margin-top: 15px;">
                    <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                        <span>Thá»a mÃ£n:</span>
                        <label><input type="radio" name="match_type" value="all" <?php echo (($category['match_type'] ?? 'all') == 'all') ? 'checked' : ''; ?>> Táº¥t cáº£ Ä‘iá»u kiá»‡n</label>
                        <label><input type="radio" name="match_type" value="any" <?php echo (($category['match_type'] ?? '') == 'any') ? 'checked' : ''; ?>> Má»™t trong cÃ¡c Ä‘iá»u kiá»‡n</label>
                    </div>

                    <div id="rules-list">
                        <?php
                        $rules_json = $category['auto_rules'] ?? '[]';
                        $rules = json_decode($rules_json, true);
                        if (!empty($rules)): foreach ($rules as $r):
                        ?>
                                <div class="rule-row">
                                    <select name="rule_field[]" class="form-control" style="margin:0; flex:1;">
                                        <option value="TÃªn sáº£n pháº©m" <?php echo (($r['field'] ?? '') == 'TÃªn sáº£n pháº©m') ? 'selected' : ''; ?>>TÃªn sáº£n pháº©m</option>
                                        <option value="Loáº¡i sáº£n pháº©m" <?php echo (($r['field'] ?? '') == 'Loáº¡i sáº£n pháº©m') ? 'selected' : ''; ?>>Loáº¡i sáº£n pháº©m</option>
                                        <option value="NhÃ  sáº£n xuáº¥t" <?php echo (($r['field'] ?? '') == 'NhÃ  sáº£n xuáº¥t') ? 'selected' : ''; ?>>NhÃ  sáº£n xuáº¥t</option>
                                        <option value="GiÃ¡ sáº£n pháº©m" <?php echo (($r['field'] ?? '') == 'GiÃ¡ sáº£n pháº©m') ? 'selected' : ''; ?>>GiÃ¡ sáº£n pháº©m</option>
                                        <option value="Tag sáº£n pháº©m" <?php echo (($r['field'] ?? '') == 'Tag sáº£n pháº©m') ? 'selected' : ''; ?>>Tag sáº£n pháº©m</option>
                                    </select>
                                    <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;">
                                        <option value="báº±ng" <?php echo (($r['operator'] ?? '') == 'báº±ng') ? 'selected' : ''; ?>>báº±ng</option>
                                        <option value="báº¯t Ä‘áº§u vá»›i" <?php echo (($r['operator'] ?? '') == 'báº¯t Ä‘áº§u vá»›i') ? 'selected' : ''; ?>>báº¯t Ä‘áº§u vá»›i</option>
                                        <option value="káº¿t thÃºc vá»›i" <?php echo (($r['operator'] ?? '') == 'káº¿t thÃºc vá»›i') ? 'selected' : ''; ?>>káº¿t thÃºc vá»›i</option>
                                        <option value="chá»©a tá»«" <?php echo (($r['operator'] ?? '') == 'chá»©a tá»«') ? 'selected' : ''; ?>>chá»©a tá»«</option>
                                        <option value="lá»›n hÆ¡n" <?php echo (($r['operator'] ?? '') == 'lá»›n hÆ¡n') ? 'selected' : ''; ?>>lá»›n hÆ¡n</option>
                                        <option value="nhá» hÆ¡n" <?php echo (($r['operator'] ?? '') == 'nhá» hÆ¡n') ? 'selected' : ''; ?>>nhá» hÆ¡n</option>
                                    </select>
                                    <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" value="<?php echo htmlspecialchars($r['value'] ?? ''); ?>">
                                    <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">ðŸ—‘ï¸</button>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="rule-row">
                                <select name="rule_field[]" class="form-control" style="margin:0; flex:1;">
                                    <option value="TÃªn sáº£n pháº©m">TÃªn sáº£n pháº©m</option>
                                    <option value="Loáº¡i sáº£n pháº©m">Loáº¡i sáº£n pháº©m</option>
                                </select>
                                <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;">
                                    <option value="báº±ng">báº±ng</option>
                                    <option value="chá»©a tá»«">chá»©a tá»«</option>
                                </select>
                                <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;">
                                <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">ðŸ—‘ï¸</button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" onclick="addRule()" style="background:#fff; border:1px solid #0088ff; color:#0088ff; padding:6px 12px; border-radius:4px; cursor:pointer; margin-top:10px;">+ ThÃªm Ä‘iá»u kiá»‡n</button>
                </div>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Tráº¡ng thÃ¡i</h3>
                <div class="radio-box"><input type="radio" name="status" value="Hiá»ƒn thá»‹" <?php echo (($category['status'] ?? 'Hiá»ƒn thá»‹') == 'Hiá»ƒn thá»‹') ? 'checked' : ''; ?>> Hiá»ƒn thá»‹</div>
                <div class="radio-box"><input type="radio" name="status" value="áº¨n" <?php echo (($category['status'] ?? '') == 'áº¨n') ? 'checked' : ''; ?>> áº¨n</div>
            </div>
            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Sáº¯p xáº¿p sáº£n pháº©m</h3>
                <select name="sort_order" class="form-control">
                    <option value="newest" <?php echo (($category['sort_order'] ?? 'newest') == 'newest') ? 'selected' : ''; ?>>Theo ngÃ y táº¡o: Tá»« má»›i Ä‘áº¿n cÅ©</option>
                    <option value="price_asc" <?php echo (($category['sort_order'] ?? '') == 'price_asc') ? 'selected' : ''; ?>>Theo giÃ¡: Tá»« tháº¥p Ä‘áº¿n cao</option>
                    <option value="name_asc" <?php echo (($category['sort_order'] ?? '') == 'name_asc') ? 'selected' : ''; ?>>Theo tÃªn: A-Z</option>
                </select>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleAutoRules() {
        document.getElementById('auto-rules-container').style.display = (document.querySelector('input[name="selection_type"]:checked').value === 'auto') ? 'block' : 'none';
    }

    function addRule() {
        var div = document.createElement('div');
        div.className = 'rule-row';
        div.innerHTML = `<select name="rule_field[]" class="form-control" style="margin:0; flex:1;"><option value="TÃªn sáº£n pháº©m">TÃªn sáº£n pháº©m</option><option value="Loáº¡i sáº£n pháº©m">Loáº¡i sáº£n pháº©m</option><option value="NhÃ  sáº£n xuáº¥t">NhÃ  sáº£n xuáº¥t</option><option value="GiÃ¡ sáº£n pháº©m">GiÃ¡ sáº£n pháº©m</option><option value="Tag sáº£n pháº©m">Tag sáº£n pháº©m</option></select> <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;"><option value="báº±ng">báº±ng</option><option value="báº¯t Ä‘áº§u vá»›i">báº¯t Ä‘áº§u vá»›i</option><option value="káº¿t thÃºc vá»›i">káº¿t thÃºc vá»›i</option><option value="chá»©a tá»«">chá»©a tá»«</option><option value="lá»›n hÆ¡n">lá»›n hÆ¡n</option><option value="nhá» hÆ¡n">nhá» hÆ¡n</option></select> <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" placeholder="Nháº­p giÃ¡ trá»‹..."> <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">ðŸ—‘ï¸</button>`;
        document.getElementById('rules-list').appendChild(div);
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>

