<?php require_once __DIR__ . '/../layout/header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* CSS CÔ LẬP KHẮC PHỤC TRIỆT ĐỂ LỖI VỠ GIAO DIỆN HUB */
    .hub-wrapper {
        padding: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 1px solid #dfe3e8;
        padding-bottom: 15px;
    }

    .v3-title {
        font-size: 24px;
        font-weight: 700;
        color: #212b36;
        margin: 0;
    }

    /* Cấu trúc Grid chia 3 cột cố định, không bị dồn hàng dọc nữa */
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        width: 100%;
        box-sizing: border-box;
    }

    /* Định dạng lại từng thẻ Card cấu hình */
    .setting-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #dfe3e8;
        display: flex;
        gap: 15px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
        align-items: flex-start;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        box-sizing: border-box;
    }

    .setting-card:hover {
        border-color: #0088ff;
        box-shadow: 0 4px 12px rgba(0, 136, 255, 0.12);
        transform: translateY(-2px);
    }

    /* Vùng chứa Icon hình vuông */
    .setting-icon {
        width: 48px;
        height: 48px;
        background: #f4f6f8;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #637381;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    .setting-card:hover .setting-icon {
        background: #e5f0ff;
        color: #0088ff;
    }

    /* Khối chữ thông tin bên phải icon */
    .setting-info {
        flex-grow: 1;
    }

    .setting-info h4 {
        margin: 0 0 6px 0;
        font-size: 16px;
        font-weight: 600;
        color: #212b36;
    }

    .setting-info p {
        margin: 0;
        font-size: 13px;
        color: #637381;
        line-height: 1.5;
    }

    /* Responsive cho màn hình nhỏ nếu cần */
    @media (max-width: 1024px) {
        .settings-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="hub-wrapper">
    <div class="v3-header">
        <h1 class="v3-title">⚙️ Cấu hình hệ thống cửa hàng</h1>
    </div>

    <div class="settings-grid">
        <a href="index.php?action=store_settings" class="setting-card">
            <div class="setting-icon"><i class="fa-solid fa-store"></i></div>
            <div class="setting-info">
                <h4>Cấu hình chung</h4>
                <p>Thiết lập tên cửa hàng, số điện thoại, địa chỉ hotline, email và mã số thuế.</p>
            </div>
        </a>

        <a href="javascript:void(0)" class="setting-card" onclick="alert('Tính năng Quản lý chi nhánh đang được thiết lập!')">
            <div class="setting-icon"><i class="fa-solid fa-code-branch"></i></div>
            <div class="setting-info">
                <h4>Quản lý chi nhánh</h4>
                <p>Thêm mới, sửa xóa thông tin các chi nhánh bán hàng và kho hàng trực thuộc.</p>
            </div>
        </a>

        <a href="index.php?action=payment_methods" class="setting-card">
            <div class="setting-icon"><i class="fa-solid fa-credit-card"></i></div>
            <div class="setting-info">
                <h4>Phương thức thanh toán</h4>
                <p>Cấu hình luồng tiền: Tiền mặt, Quẹt thẻ POS, Chuyển khoản ngân hàng, Ví điện tử.</p>
            </div>
        </a>

        <a href="javascript:void(0)" class="setting-card" onclick="alert('Tính năng cấu hình Thuế giá trị gia tăng đang được thiết lập!')">
            <div class="setting-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="setting-info">
                <h4>Cấu hình Thuế (VAT)</h4>
                <p>Thiết lập danh mục thuế suất áp dụng tự động cho các nhóm mặt hàng điện thoại.</p>
            </div>
        </a>

        <a href="javascript:void(0)" class="setting-card" onclick="alert('Tính năng cấu hình Đối tác vận chuyển đang được thiết lập!')">
            <div class="setting-icon"><i class="fa-solid fa-truck-fast"></i></div>
            <div class="setting-info">
                <h4>Đối tác vận chuyển</h4>
                <p>Kết nối API Giao Hàng Nhanh, Viettel Post và quản lý phí giao hàng khu vực.</p>
            </div>
        </a>

        <a href="javascript:void(0)" class="setting-card" onclick="alert('Tính năng quản lý vai trò tài khoản nhân viên đang được thiết lập!')">
            <div class="setting-icon"><i class="fa-solid fa-users-gear"></i></div>
            <div class="setting-info">
                <h4>Quản lý nhân viên</h4>
                <p>Thêm mới tài khoản nhân sự và phân quyền hạn (Quản lý kho, Thu ngân, Admin).</p>
            </div>
        </a>

        <a href="index.php?action=pos_settings" class="setting-card">
            <div class="setting-icon"><i class="fa-solid fa-desktop"></i></div>
            <div class="setting-info">
                <h4>Cấu hình POS bán hàng</h4>
                <p>Thiết lập giao diện thao tác nhanh cho màn hình cảm ứng bán lẻ tại quầy trung tâm.</p>
            </div>
        </a>

        <a href="index.php?action=order_sources" class="setting-card">
            <div class="setting-icon"><i class="fa-solid fa-globe"></i></div>
            <div class="setting-info">
                <h4>Nguồn đơn hàng</h4>
                <p>Quản lý các kênh ghi nhận doanh thu: Đơn tại quầy, Website, Facebook, Shopee...</p>
            </div>
        </a>

        <a href="javascript:void(0)" class="setting-card" onclick="alert('Tính năng biên tập Mẫu in hóa đơn đang được thiết lập!')">
            <div class="setting-icon"><i class="fa-solid fa-print"></i></div>
            <div class="setting-info">
                <h4>Cấu hình Mẫu in</h4>
                <p>Tùy chỉnh định dạng phông chữ, biểu mẫu in biên lai POS (K80) hoặc hóa đơn A4.</p>
            </div>
        </a>

        <a href="javascript:void(0)" class="setting-card" onclick="alert('Tính năng Nhật ký kiểm toán hoạt động đang được thiết lập!')">
            <div class="setting-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="setting-info">
                <h4>Nhật ký hoạt động</h4>
                <p>Hệ thống ghi nhận lịch sử thay đổi thông tin, bảo mật dữ liệu nguồn của hệ thống.</p>
            </div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
