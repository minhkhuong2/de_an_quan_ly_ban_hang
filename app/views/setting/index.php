<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .settings-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .settings-header {
        font-size: 24px;
        font-weight: bold;
        color: #212b36;
        margin-bottom: 20px;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .setting-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        gap: 15px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        border: 1px solid transparent;
        transition: all 0.2s;
        align-items: flex-start;
    }

    .setting-card:hover {
        border-color: #0088ff;
        box-shadow: 0 4px 12px rgba(0, 136, 255, 0.15);
        transform: translateY(-2px);
    }

    .setting-icon {
        font-size: 24px;
        background: #f4f6f8;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .setting-info h3 {
        font-size: 15px;
        font-weight: bold;
        color: #0088ff;
        margin: 0 0 5px 0;
    }

    .setting-info p {
        font-size: 13px;
        color: #637381;
        margin: 0;
        line-height: 1.4;
    }
</style>

<div class="settings-container">
    <div class="settings-header">Cấu hình</div>

    <div class="settings-grid">
        <a href="index.php?action=general_settings" class="setting-card">
            <div class="setting-icon">⚙️</div>
            <div class="setting-info">
                <h3>Cấu hình chung</h3>
                <p>Cấu hình thông tin cửa hàng, địa chỉ, số điện thoại, email...</p>
            </div>
        </a>

        <a href="index.php?action=staff_list" class="setting-card">
            <div class="setting-icon">👥</div>
            <div class="setting-info">
                <h3>Quản lý nhân viên</h3>
                <p>Quản lý danh sách nhân viên và phân quyền sử dụng phần mềm.</p>
            </div>
        </a>

        <a href="index.php?action=shipping_list" class="setting-card">
            <div class="setting-icon">🚚</div>
            <div class="setting-info">
                <h3>Đối tác vận chuyển</h3>
                <p>Kết nối và cấu hình phí giao hàng với GHN, J&T, NinjaVan...</p>
            </div>
        </a>

        <a href="index.php?action=branch_list" class="setting-card">
            <div class="setting-icon">🏢</div>
            <div class="setting-info">
                <h3>Quản lý chi nhánh</h3>
                <p>Quản lý danh sách các chi nhánh của cửa hàng.</p>
            </div>
        </a>

        <a href="#" class="setting-card">
            <div class="setting-icon">💳</div>
            <div class="setting-info">
                <h3>Phương thức thanh toán</h3>
                <p>Cấu hình các phương thức thanh toán như Tiền mặt, Chuyển khoản, Thẻ.</p>
            </div>
        </a>

        <a href="#" class="setting-card">
            <div class="setting-icon">💰</div>
            <div class="setting-info">
                <h3>Thuế</h3>
                <p>Thiết lập mức thuế GTGT cho sản phẩm và đơn hàng.</p>
            </div>
        </a>

        <a href="#" class="setting-card">
            <div class="setting-icon">🖨️</div>
            <div class="setting-info">
                <h3>Mẫu in</h3>
                <p>Cấu hình mẫu in hóa đơn, phiếu giao hàng, mã vạch.</p>
            </div>
        </a>

        <a href="#" class="setting-card">
            <div class="setting-icon">🛒</div>
            <div class="setting-info">
                <h3>Kênh bán hàng</h3>
                <p>Quản lý kết nối các kênh Website, POS, Mạng xã hội.</p>
            </div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
