<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array $baseProducts Khai bÃ¡o áº©n Ä‘á»ƒ mÃ¡ch cho VS Code biáº¿t biáº¿n nÃ y tá»“n táº¡i */
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
        font-weight: 500;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        margin-left: 10px;
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
        border-bottom: 1px solid #f4f6f8;
        padding-bottom: 10px;
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
        font-size: 14px;
        box-sizing: border-box;
    }

    .row-flex {
        display: flex;
        gap: 15px;
    }

    .row-flex .form-group {
        flex: 1;
    }
</style>

<form action="index.php?action=add_conversion" method="POST">
    <div class="Há»‡ thá»‘ng-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_list" style="color:#637381; text-decoration:none; margin-right: 10px;">â†</a> ThÃªm phiÃªn báº£n quy Ä‘á»•i</h2>
        <div>
            <a href="index.php?action=product_list" class="btn-cancel">Há»§y</a>
            <button type="submit" class="btn-save">ThÃªm phiÃªn báº£n</button>
        </div>
    </div>

    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ThÃ´ng tin quy Ä‘á»•i</div>

                <div class="form-group">
                    <label>Chá»n phiÃªn báº£n gá»‘c (Sáº£n pháº©m máº¹) <span style="color:red;">*</span></label>
                    <select name="parent_id" id="parent_id" class="form-control" required onchange="calculatePrice()">
                        <option value="">-- TÃ¬m kiáº¿m tÃªn hoáº·c mÃ£ SKU sáº£n pháº©m gá»‘c --</option>
                        <?php foreach ($baseProducts as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['base_price']; ?>" data-unit="<?php echo $p['unit']; ?>">
                                <?php echo htmlspecialchars($p['product_name']); ?> (ÄVT: <?php echo htmlspecialchars($p['unit'] ?? 'ChÆ°a rÃµ'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row-flex">
                    <div class="form-group">
                        <label>ÄÆ¡n vá»‹ quy Ä‘á»•i <span style="color:red;">*</span></label>
                        <input type="text" name="unit" class="form-control" placeholder="VD: Lá»‘c, ThÃ¹ng, Há»™p..." required>
                    </div>
                    <div class="form-group">
                        <label>Sá»‘ lÆ°á»£ng quy Ä‘á»•i <span style="color:red;">*</span></label>
                        <input type="number" name="conversion_qty" id="conversion_qty" class="form-control" placeholder="VD: 6, 12, 24" min="2" required oninput="calculatePrice()">
                        <span style="font-size: 12px; color: #637381; margin-top: 5px; display: block;">LÆ°u Ã½: Sá»‘ lÆ°á»£ng pháº£i lÃ  sá»‘ nguyÃªn > 1</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ThÃ´ng tin bÃ¡n hÃ ng</div>

                <div class="form-group">
                    <label>MÃ£ SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="VD: THUNG-IP15">
                </div>

                <div class="form-group">
                    <label>MÃ£ váº¡ch / Barcode</label>
                    <input type="text" name="barcode" class="form-control" placeholder="QuÃ©t mÃ£ váº¡ch (náº¿u cÃ³)">
                </div>

                <div class="form-group">
                    <label>GiÃ¡ bÃ¡n gá»£i Ã½ (â‚«) <span style="color:red;">*</span></label>
                    <input type="number" name="base_price" id="base_price" class="form-control" value="0" required>
                    <span style="font-size: 12px; color: #637381; margin-top: 5px; display: block;">Há»‡ thá»‘ng gá»£i Ã½: GiÃ¡ SP gá»‘c x Sá»‘ lÆ°á»£ng quy Ä‘á»•i</span>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // PhÃ©p thuáº­t: Tá»± Ä‘á»™ng tÃ­nh giÃ¡ bÃ¡n gá»£i Ã½ giá»‘ng y há»‡t tÃ­nh nÄƒng cá»§a Há»‡ thá»‘ng
    function calculatePrice() {
        let selectBox = document.getElementById("parent_id");
        let qtyInput = document.getElementById("conversion_qty").value;
        let priceInput = document.getElementById("base_price");

        if (selectBox.selectedIndex > 0 && qtyInput >= 1) {
            let basePrice = selectBox.options[selectBox.selectedIndex].getAttribute('data-price');
            // TÃ­nh giÃ¡ = GiÃ¡ sáº£n pháº©m gá»‘c * Sá»‘ lÆ°á»£ng quy Ä‘á»•i
            let suggestedPrice = parseFloat(basePrice) * parseFloat(qtyInput);
            priceInput.value = suggestedPrice;
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

