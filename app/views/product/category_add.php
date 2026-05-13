<?php require_once __DIR__ . '/../layout/header.php'; ?>
<style>
    .sapo-header-bar {
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
        cursor: pointer;
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

    .sapo-grid {
        display: flex;
        gap: 20px;
    }

    .sapo-col-left {
        flex: 0 0 68%;
    }

    .sapo-col-right {
        flex: 1;
    }

    .sapo-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        margin-top: 8px;
        outline: none;
    }

    .radio-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
</style>

<form action="index.php?action=add_category" method="POST">
    <div class="sapo-header-bar">
        <h2 style="font-size: 20px; margin: 0; color: #212b36;"><a href="index.php?action=product_category" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Thêm danh mục</h2>
        <div>
            <a href="index.php?action=product_category" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-save">Lưu</button>
        </div>
    </div>

    <div class="sapo-grid">
        <div class="sapo-col-left">
            <div class="sapo-card">
                <div style="font-weight:bold; margin-bottom:15px;">Thông tin chung</div>
                <div style="margin-bottom:15px;">
                    <label>Tên danh mục *</label>
                    <input type="text" name="category_name" class="form-control" placeholder="Nhập tên danh mục" required>
                </div>
                <div>
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" rows="5"></textarea>
                </div>
            </div>
            <div class="sapo-card">
                <div style="font-weight:bold; margin-bottom:15px;">Tối ưu SEO</div>
                <p style="color: #637381; font-size: 14px;">Thiết lập các thẻ tiêu đề, mô tả để danh mục dễ dàng được tìm thấy trên Google.</p>
                <a href="#" style="color: #0088ff; text-decoration: none; font-size: 14px;">Chỉnh sửa SEO</a>
            </div>
        </div>

        <div class="sapo-col-right">
            <div class="sapo-card">
                <div style="font-weight:bold; margin-bottom:15px;">Trạng thái</div>
                <div class="radio-group"><input type="radio" name="status" value="Hiển thị" checked> Hiển thị</div>
                <div class="radio-group"><input type="radio" name="status" value="Ẩn"> Ẩn</div>
            </div>
        </div>
    </div>
</form>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
