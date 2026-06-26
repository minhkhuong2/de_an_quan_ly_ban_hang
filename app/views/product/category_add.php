<?php require_once __DIR__ . '/../layout/header.php'; ?>
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

<form action="index.php?action=add_category" method="POST">
    <div class="Há»‡ thá»‘ng-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_category" style="text-decoration:none; color:#637381; margin-right:10px;">â†</a> ThÃªm má»›i danh má»¥c</h2>
        <div><a href="index.php?action=product_category" class="btn-cancel">Há»§y</a> <button type="submit" class="btn-save">LÆ°u</button></div>
    </div>

    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">ThÃ´ng tin chung</h3>
                <label>TÃªn danh má»¥c <span style="color:red;">*</span></label>
                <input type="text" name="category_name" class="form-control" required>
                <label>MÃ´ táº£</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Chá»n sáº£n pháº©m</h3>
                <label class="radio-box">
                    <input type="radio" name="selection_type" value="manual" checked onchange="toggleAutoRules()">
                    <strong>ThÃªm sáº£n pháº©m thá»§ cÃ´ng</strong>
                </label>
                <p style="color: #637381; font-size: 14px; margin-left: 25px; margin-bottom: 20px;">Báº¡n sáº½ chá»n tá»«ng sáº£n pháº©m Ä‘á»ƒ thÃªm vÃ o danh má»¥c nÃ y.</p>

                <label class="radio-box">
                    <input type="radio" name="selection_type" value="auto" onchange="toggleAutoRules()">
                    <strong>ThÃªm sáº£n pháº©m tá»± Ä‘á»™ng</strong>
                </label>
                <p style="color: #637381; font-size: 14px; margin-left: 25px;">Sáº£n pháº©m tá»± Ä‘á»™ng Ä‘Æ°á»£c thÃªm vÃ o danh má»¥c náº¿u thá»a mÃ£n Ä‘iá»u kiá»‡n.</p>

                <div id="auto-rules-container" style="display: none; background: #fafbfc; border: 1px solid #dfe3e8; padding: 15px; border-radius: 6px; margin-top: 15px;">
                    <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px;">
                        <span>Sáº£n pháº©m pháº£i thá»a mÃ£n:</span>
                        <label><input type="radio" name="match_type" value="all" checked> Táº¥t cáº£ Ä‘iá»u kiá»‡n</label>
                        <label><input type="radio" name="match_type" value="any"> Má»™t trong cÃ¡c Ä‘iá»u kiá»‡n</label>
                    </div>

                    <div id="rules-list">
                        <div class="rule-row">
                            <select name="rule_field[]" class="form-control" style="margin:0; flex:1;">
                                <option value="TÃªn sáº£n pháº©m">TÃªn sáº£n pháº©m</option>
                                <option value="Loáº¡i sáº£n pháº©m">Loáº¡i sáº£n pháº©m</option>
                                <option value="NhÃ  sáº£n xuáº¥t">NhÃ  sáº£n xuáº¥t</option>
                                <option value="GiÃ¡ sáº£n pháº©m">GiÃ¡ sáº£n pháº©m</option>
                                <option value="Tag sáº£n pháº©m">Tag sáº£n pháº©m</option>
                            </select>
                            <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;">
                                <option value="báº±ng">báº±ng</option>
                                <option value="báº¯t Ä‘áº§u vá»›i">báº¯t Ä‘áº§u vá»›i</option>
                                <option value="káº¿t thÃºc vá»›i">káº¿t thÃºc vá»›i</option>
                                <option value="chá»©a tá»«">chá»©a tá»«</option>
                                <option value="lá»›n hÆ¡n">lá»›n hÆ¡n</option>
                                <option value="nhá» hÆ¡n">nhá» hÆ¡n</option>
                            </select>
                            <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" placeholder="Nháº­p giÃ¡ trá»‹...">
                            <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">ðŸ—‘ï¸</button>
                        </div>
                    </div>
                    <button type="button" onclick="addRule()" style="background:#fff; border:1px solid #0088ff; color:#0088ff; padding:6px 12px; border-radius:4px; cursor:pointer; margin-top:10px;">+ ThÃªm Ä‘iá»u kiá»‡n</button>
                </div>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Tráº¡ng thÃ¡i</h3>
                <div class="radio-box"><input type="radio" name="status" value="Hiá»ƒn thá»‹" checked> Hiá»ƒn thá»‹</div>
                <div class="radio-box"><input type="radio" name="status" value="áº¨n"> áº¨n</div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Sáº¯p xáº¿p sáº£n pháº©m</h3>
                <select name="sort_order" class="form-control">
                    <option value="newest">Theo ngÃ y táº¡o: Tá»« má»›i Ä‘áº¿n cÅ©</option>
                    <option value="oldest">Theo ngÃ y táº¡o: Tá»« cÅ© Ä‘áº¿n má»›i</option>
                    <option value="price_asc">Theo giÃ¡: Tá»« tháº¥p Ä‘áº¿n cao</option>
                    <option value="price_desc">Theo giÃ¡: Tá»« cao Ä‘áº¿n tháº¥p</option>
                    <option value="name_asc">Theo tÃªn: A-Z</option>
                </select>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleAutoRules() {
        var type = document.querySelector('input[name="selection_type"]:checked').value;
        document.getElementById('auto-rules-container').style.display = (type === 'auto') ? 'block' : 'none';
    }

    function addRule() {
        var div = document.createElement('div');
        div.className = 'rule-row';
        div.innerHTML = `
            <select name="rule_field[]" class="form-control" style="margin:0; flex:1;"><option value="TÃªn sáº£n pháº©m">TÃªn sáº£n pháº©m</option><option value="Loáº¡i sáº£n pháº©m">Loáº¡i sáº£n pháº©m</option><option value="NhÃ  sáº£n xuáº¥t">NhÃ  sáº£n xuáº¥t</option><option value="GiÃ¡ sáº£n pháº©m">GiÃ¡ sáº£n pháº©m</option><option value="Tag sáº£n pháº©m">Tag sáº£n pháº©m</option></select>
            <select name="rule_operator[]" class="form-control" style="margin:0; flex:1;"><option value="báº±ng">báº±ng</option><option value="báº¯t Ä‘áº§u vá»›i">báº¯t Ä‘áº§u vá»›i</option><option value="káº¿t thÃºc vá»›i">káº¿t thÃºc vá»›i</option><option value="chá»©a tá»«">chá»©a tá»«</option><option value="lá»›n hÆ¡n">lá»›n hÆ¡n</option><option value="nhá» hÆ¡n">nhá» hÆ¡n</option></select>
            <input type="text" name="rule_value[]" class="form-control" style="margin:0; flex:1;" placeholder="Nháº­p giÃ¡ trá»‹...">
            <button type="button" onclick="this.parentElement.remove()" style="background:#fff; border:1px solid #c4cdd5; padding:8px 12px; border-radius:4px; cursor:pointer;">ðŸ—‘ï¸</button>
        `;
        document.getElementById('rules-list').appendChild(div);
    }
</script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>

