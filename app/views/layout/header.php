<?php
// Đường dẫn file: app/views/layout/header.php

// Lấy action hiện tại để làm nổi bật (active) menu đang được chọn
$current_action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Khởi tạo mảng an toàn để tránh lỗi báo đỏ Session
$user_session = isset($_SESSION['user']) ? $_SESSION['user'] : ['role' => 'Nhân viên', 'full_name' => 'Khương'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Cửa hàng - AAKC Sapo</title>
    <style>
        /* CSS Reset cơ bản */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #212b36;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar (Menu bên trái) */
        .sidebar {
            width: 240px;
            background: #001529;
            color: #a6adb4;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 100;
        }

        .sidebar .logo {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #fff;
            text-align: center;
            border-bottom: 1px solid #002140;
            letter-spacing: 1px;
        }

        .sidebar .menu {
            padding: 10px 0;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #a6adb4;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar .menu-item:hover {
            color: #fff;
        }

        .sidebar .menu-item.active {
            background: #1890ff;
            color: #fff;
            font-weight: 500;
        }

        /* Nút POS nổi bật */
        .sidebar .menu-item.pos-btn {
            color: #52c41a;
            font-weight: bold;
        }

        .sidebar .menu-item.pos-btn:hover {
            color: #73d13d;
        }

        /* Khung nội dung chính */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Topbar (Thanh trên cùng) */
        .topbar {
            background: #fff;
            height: 60px;
            box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 20px;
            z-index: 10;
            flex-shrink: 0;
        }

        .topbar .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .topbar .avatar {
            width: 32px;
            height: 32px;
            background: #0088ff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Khu vực hiển thị View */
        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">🛒 AAKC Store</div>
        <div class="menu">
            <a href="index.php?action=dashboard" class="menu-item <?php echo ($current_action == 'dashboard') ? 'active' : ''; ?>">
                📊 Tổng quan
            </a>

            <a href="index.php?action=pos" class="menu-item pos-btn">
                🛍️ Bán hàng (POS)
            </a>

            <div style="padding: 15px 20px 5px 20px; font-size: 11px; text-transform: uppercase; color: #637381; font-weight: bold; letter-spacing: 0.5px;">Quản lý Sản phẩm</div>
            <a href="index.php?action=product_list" class="menu-item <?php echo ($current_action == 'product_list' || $current_action == 'add_product' || $current_action == 'edit_product') ? 'active' : ''; ?>">
                📦 Danh sách sản phẩm
            </a>
            <a href="index.php?action=product_category" class="menu-item <?php echo ($current_action == 'product_category' || $current_action == 'add_category' || $current_action == 'edit_category') ? 'active' : ''; ?>">
                📂 Danh mục
            </a>
            <a href="index.php?action=product_price" class="menu-item <?php echo ($current_action == 'product_price' || $current_action == 'add_price') ? 'active' : ''; ?>">
                🏷️ Bảng giá
            </a>

            <div style="padding: 15px 20px 5px 20px; font-size: 11px; text-transform: uppercase; color: #637381; font-weight: bold; letter-spacing: 0.5px;">Quản lý Kho</div>
            <a href="index.php?action=inventory_list" class="menu-item <?php echo ($current_action == 'inventory_list') ? 'active' : ''; ?>">🏢 Tồn kho</a>

            <a href="index.php?action=purchase_list" class="menu-item <?php echo ($current_action == 'purchase_list' || $current_action == 'add_purchase' || $current_action == 'view_purchase') ? 'active' : ''; ?>">📥 Đặt hàng nhập</a>

            <a href="index.php?action=receipt_list" class="menu-item <?php echo ($current_action == 'receipt_list' || $current_action == 'direct_receive' || $current_action == 'receive_purchase') ? 'active' : ''; ?>">📦 Nhập hàng</a>

            <a href="index.php?action=purchase_return_list" class="menu-item <?php echo ($current_action == 'purchase_return_list' || $current_action == 'add_purchase_return') ? 'active' : ''; ?>">📤 Trả hàng nhập</a>

            <a href="index.php?action=inventory_check_list" class="menu-item <?php echo ($current_action == 'inventory_check_list' || $current_action == 'add_inventory_check') ? 'active' : ''; ?>">📋 Kiểm kho</a>

            <a href="index.php?action=supplier_list" class="menu-item <?php echo ($current_action == 'supplier_list' || $current_action == 'add_supplier' || $current_action == 'edit_supplier') ? 'active' : ''; ?>">🤝 Nhà cung cấp</a>



            <div style="padding: 15px 20px 5px 20px; font-size: 11px; text-transform: uppercase; color: #637381; font-weight: bold; letter-spacing: 0.5px;">Đối tác & Khác</div>
            <a href="index.php?action=customer_list" class="menu-item <?php echo ($current_action == 'customer_list' || $current_action == 'add_customer') ? 'active' : ''; ?>">
                👥 Khách hàng
            </a>
            <a href="#" class="menu-item">📄 Đơn hàng</a>
            <a href="#" class="menu-item">⚙️ Cài đặt</a>
        </div>
    </div>

    <div class="main-container">

        <div class="topbar">
            <div class="user-profile">
                <span>👤 Chức vụ: <strong style="color:#0088ff;"><?php echo htmlspecialchars($user_session['role']); ?></strong> | </span>
                <span>Xin chào, <strong><?php echo htmlspecialchars($user_session['full_name']); ?></strong></span>
                <a href="index.php?action=logout" style="margin-left: 15px; color: #ff4d4f; text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid #ff4d4f; padding: 4px 8px; border-radius: 4px;">🚪 Đăng xuất</a>
            </div>
        </div>

        <div class="content-wrapper">
