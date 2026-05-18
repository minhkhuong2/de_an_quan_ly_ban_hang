<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        margin-top: 8px;
        margin-bottom: 15px;
        box-sizing: border-box;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }
</style>

<div class="sapo-card">
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <a href="index.php?action=customer_list" style="text-decoration:none; color:#637381; margin-right:15px; font-size: 20px;">←</a>
        <h2 style="font-size: 20px; margin: 0; color: #212b36;">Thêm khách hàng mới</h2>
    </div>

    <form action="index.php?action=add_customer" method="POST">
        <label style="font-weight: 500;">Tên khách hàng <span style="color:red;">*</span></label>
        <input type="text" name="customer_name" class="form-control" placeholder="VD: Nguyễn Văn A" required>

        <label style="font-weight: 500;">Số điện thoại <span style="color:red;">*</span></label>
        <input type="text" name="phone" class="form-control" placeholder="VD: 0987654321" required>

        <label style="font-weight: 500;">Email</label>
        <input type="email" name="email" class="form-control" placeholder="VD: email@gmail.com">

        <label style="font-weight: 500;">Nhóm khách hàng</label>
        <select name="customer_group" class="form-control">
            <option value="Khách lẻ">Khách lẻ</option>
            <option value="Khách buôn / Sỉ">Khách buôn / Sỉ</option>
            <option value="Khách VIP">Khách VIP</option>
        </select>

        <label style="font-weight: 500;">Địa chỉ</label>
        <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ giao hàng..."></textarea>

        <button type="submit" class="btn-save">LƯU KHÁCH HÀNG</button>
    </form>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
