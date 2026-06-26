<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php $c = $customer ?? []; ?>

<style>
    .Há»‡ thá»‘ng-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
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

<form action="index.php?action=edit_customer&id=<?php echo $c['id']; ?>" method="POST">
    <div class="Há»‡ thá»‘ng-header-bar">
        <h2 style="font-size: 20px; margin:0;"><a href="index.php?action=customer_list" style="text-decoration:none; color:#637381;">â†</a> Sá»­a: <?php echo htmlspecialchars(trim(($c['last_name'] ?? '') . ' ' . ($c['first_name'] ?? ''))); ?></h2>
        <div class="Há»‡ thá»‘ng-btn-group">
            <button type="button" class="btn-cancel" onclick="window.location.href='index.php?action=customer_list'">Há»§y</button>
            <button type="submit" class="btn-save">LÆ°u thay Ä‘á»•i</button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">âœ… LÆ°u khÃ¡ch hÃ ng thÃ nh cÃ´ng!</div><?php endif; ?>

    <div class="Há»‡ thá»‘ng-grid">
        <div class="Há»‡ thá»‘ng-col-left">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ThÃ´ng tin cÆ¡ báº£n</div>
                <div class="row-flex">
                    <div class="form-group"><label>MÃ£ KH</label><input type="text" name="customer_code" class="form-control" value="<?php echo htmlspecialchars($c['customer_code'] ?? ''); ?>"></div>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Há»</label><input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($c['last_name'] ?? ''); ?>"></div>
                    <div class="form-group"><label>TÃªn <span style="color:red;">*</span></label><input type="text" name="first_name" class="form-control" required value="<?php echo htmlspecialchars($c['first_name'] ?? ''); ?>"></div>
                </div>
                <div class="row-flex">
                    <div class="form-group"><label>Äiá»‡n thoáº¡i</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($c['phone'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($c['email'] ?? ''); ?>"></div>
                </div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">Äá»‹a chá»‰ giao hÃ ng</div>
                <div class="row-flex">
                    <div class="form-group"><label>Tá»‰nh/ThÃ nh phá»‘</label><input type="text" name="province" class="form-control" value="<?php echo htmlspecialchars($c['province'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Quáº­n/Huyá»‡n</label><input type="text" name="district" class="form-control" value="<?php echo htmlspecialchars($c['district'] ?? ''); ?>"></div>
                    <div class="form-group"><label>PhÆ°á»ng/XÃ£</label><input type="text" name="ward" class="form-control" value="<?php echo htmlspecialchars($c['ward'] ?? ''); ?>"></div>
                </div>
                <div class="form-group"><label>Äá»‹a chá»‰ cá»¥ thá»ƒ</label><input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($c['address'] ?? ''); ?>"></div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <?php $hasInvoice = !empty($c['tax_code']) || !empty($c['company_name']); ?>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <input type="checkbox" id="has_invoice" style="width:16px; height:16px;" onchange="toggleInvoice()" <?php echo $hasInvoice ? 'checked' : ''; ?>>
                    <label for="has_invoice" style="font-weight: bold; margin:0; font-size: 16px; cursor:pointer;">KhÃ¡ch cÃ³ thÃ´ng tin xuáº¥t hÃ³a Ä‘Æ¡n</label>
                </div>

                <div id="invoice-box" style="display: <?php echo $hasInvoice ? 'block' : 'none'; ?>; background: #fafbfc; padding: 15px; border-radius: 6px; border: 1px solid #dfe3e8;">
                    <div class="row-flex">
                        <div class="form-group"><label>MÃ£ sá»‘ thuáº¿</label><input type="text" name="tax_code" class="form-control" value="<?php echo htmlspecialchars($c['tax_code'] ?? ''); ?>"></div>
                        <div class="form-group"><label>TÃªn cÃ´ng ty</label><input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($c['company_name'] ?? ''); ?>"></div>
                    </div>
                    <div class="form-group"><label>Äá»‹a chá»‰ xuáº¥t hÃ³a Ä‘Æ¡n</label><input type="text" name="invoice_address" class="form-control" value="<?php echo htmlspecialchars($c['invoice_address'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Email nháº­n hÃ³a Ä‘Æ¡n</label><input type="email" name="invoice_email" class="form-control" value="<?php echo htmlspecialchars($c['invoice_email'] ?? ''); ?>"></div>
                </div>
            </div>
        </div>

        <div class="Há»‡ thá»‘ng-col-right">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">KhÃ¡c</div>
                <div style="margin-bottom: 15px; display: flex; align-items: flex-start; gap: 8px;">
                    <input type="checkbox" name="accept_marketing" value="1" style="margin-top: 3px;" <?php echo ($c['accept_marketing'] ?? 0) ? 'checked' : ''; ?>>
                    <span style="font-size: 14px; color: #212b36;">KhÃ¡ch hÃ ng muá»‘n nháº­n thÃ´ng tin tiáº¿p thá»‹, quáº£ng cÃ¡o</span>
                </div>
                <div class="form-group"><label>Ghi chÃº</label><textarea name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($c['notes'] ?? ''); ?></textarea></div>
                <div class="form-group"><label>Tags</label><input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($c['tags'] ?? ''); ?>"></div>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleInvoice() {
        document.getElementById('invoice-box').style.display = document.getElementById('has_invoice').checked ? 'block' : 'none';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

