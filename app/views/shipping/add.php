<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 15px;
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
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        box-sizing: border-box;
    }
</style>

<div style="max-width: 600px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 20px; font-weight: bold;"><a href="index.php?action=shipping_list" style="text-decoration:none; color:#637381;">←</a> Kết nối Đơn vị vận chuyển</h2>
</div>

<form action="index.php?action=add_shipping" method="POST" class="sapo-card">
    <div style="display: flex; gap:15px;">
        <div class="form-group" style="flex:2;"><label>Tên đối tác (VD: GHN Express) <span style="color:red;">*</span></label><input type="text" name="partner_name" class="form-control" required></div>
        <div class="form-group" style="flex:1;"><label>Mã ĐVVC (VD: GHN)</label><input type="text" name="partner_code" class="form-control" required></div>
    </div>

    <div class="form-group">
        <label>Phí vận chuyển mặc định (₫)</label>
        <input type="text" name="base_fee" class="form-control" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
    </div>

    <div style="display: flex; gap:15px;">
        <div class="form-group" style="flex:1;">
            <label>Giới hạn số lần giao lại (Theo chính sách)</label>
            <input type="number" name="max_retry" class="form-control" value="3" min="1" max="5">
            <span style="font-size: 12px; color: #637381;">AAKC mặc định 3 lần</span>
        </div>
        <div class="form-group" style="flex:1;">
            <label>Trạng thái kết nối</label>
            <select name="status" class="form-control">
                <option value="Đang kết nối">Đang kết nối</option>
                <option value="Ngừng kết nối">Ngừng kết nối</option>
            </select>
        </div>
    </div>

    <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 10px; background: #fafbfc; padding: 10px; border: 1px solid #dfe3e8; border-radius: 4px;">
        <input type="checkbox" name="allow_cod" value="1" checked id="allow_cod" style="width: 16px; height: 16px;">
        <label for="allow_cod" style="margin:0; font-weight: bold; color: #108043;">Hỗ trợ thu hộ tiền (COD)</label>
    </div>

    <div class="form-group" style="margin-top: 15px;">
        <label>Chính sách & Ghi chú bồi thường</label>
        <textarea name="notes" class="form-control" rows="4" placeholder="VD: Giao tối đa 3 lần. Miễn trừ trách nhiệm nếu..."></textarea>
    </div>

    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" style="background:#0088ff; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;">💾 Lưu kết nối</button>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
