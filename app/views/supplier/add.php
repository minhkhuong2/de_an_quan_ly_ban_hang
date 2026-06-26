<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 20px;
    }

    .Há»‡ thá»‘ng-card-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #212b36;
        padding-bottom: 12px;
        border-bottom: 1px solid #dfe3e8;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: #0088ff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 136, 255, 0.2);
    }

    .row-2-cols {
        display: flex;
        gap: 20px;
    }

    .row-2-cols>div {
        flex: 1;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2 style="font-size: 22px; font-weight: bold; color: #212b36;">
        <a href="index.php?action=supplier_list" style="text-decoration:none; color:#637381; margin-right: 10px;">â†</a>
        ThÃªm má»›i nhÃ  cung cáº¥p
    </h2>
</div>

<form action="index.php?action=add_supplier" method="POST">
    <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start;">

        <div style="flex: 1 1 65%; min-width: 600px;">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“ ThÃ´ng tin chung</div>
                <div class="form-group">
                    <label>TÃªn nhÃ  cung cáº¥p <span style="color:red;">*</span></label>
                    <input type="text" name="supplier_name" class="form-control" placeholder="Nháº­p tÃªn nhÃ  cung cáº¥p..." required>
                </div>

                <div class="row-2-cols">
                    <div class="form-group">
                        <label>MÃ£ nhÃ  cung cáº¥p</label>
                        <input type="text" name="supplier_code" class="form-control" placeholder="Äá»ƒ trá»‘ng há»‡ thá»‘ng tá»± táº¡o (VD: SUP0001)">
                    </div>
                    <div class="form-group">
                        <label>Sá»‘ Ä‘iá»‡n thoáº¡i</label>
                        <input type="text" name="phone" class="form-control" placeholder="Nháº­p sá»‘ Ä‘iá»‡n thoáº¡i...">
                    </div>
                </div>

                <div class="row-2-cols">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Nháº­p Ä‘á»‹a chá»‰ email...">
                    </div>
                    <div class="form-group">
                        <label>MÃ£ sá»‘ thuáº¿</label>
                        <input type="text" name="tax_code" class="form-control" placeholder="Nháº­p mÃ£ sá»‘ thuáº¿...">
                    </div>
                </div>
            </div>

            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">ðŸ“ ThÃ´ng tin Ä‘á»‹a chá»‰</div>
                <div class="form-group">
                    <label>Äá»‹a chá»‰ cá»¥ thá»ƒ</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Sá»‘ nhÃ , ngÃµ, phÆ°á»ng/xÃ£, quáº­n/huyá»‡n, tá»‰nh/thÃ nh phá»‘..."></textarea>
                </div>
            </div>
        </div>

        <div style="flex: 1 1 30%; min-width: 300px;">
            <div class="Há»‡ thá»‘ng-card">
                <div class="Há»‡ thá»‘ng-card-title">âš™ï¸ ThÃ´ng tin khÃ¡c</div>
                <div class="form-group">
                    <label>NhÃ³m nhÃ  cung cáº¥p</label>
                    <select class="form-control">
                        <option>BÃ¡n buÃ´n</option>
                        <option>Äáº¡i lÃ½</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>NhÃ¢n viÃªn phá»¥ trÃ¡ch</label>
                    <input type="text" name="employee" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? 'Admin'); ?>" readonly style="background:#f4f6f8; cursor:not-allowed; color:#0088ff; font-weight:bold;">
                </div>

                <div class="form-group">
                    <label>Ghi chÃº</label>
                    <textarea class="form-control" rows="4" placeholder="MÃ´ táº£ vá» nhÃ  cung cáº¥p nÃ y..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; border-top: 1px solid #dfe3e8; padding-top: 20px; padding-bottom: 40px;">
        <button type="button" style="padding: 10px 20px; border-radius: 4px; border: 1px solid #c4cdd5; background: #fff; cursor: pointer; font-weight:500;" onclick="window.location.href='index.php?action=supplier_list'">Há»§y bá»</button>
        <button type="submit" style="padding: 10px 20px; border-radius: 4px; border: none; background: #0088ff; color: #fff; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,136,255,0.2);">ðŸ’¾ LÆ°u nhÃ  cung cáº¥p</button>
    </div>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

