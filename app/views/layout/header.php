<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản lý Bán hàng AAKC</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="sidebar">
        <div class="sidebar-header" style="font-size: 24px; font-weight: bold; letter-spacing: 1px; color: #fff;">
            AAKC POS
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php?action=dashboard">🏠 Tổng quan</a></li>
            <li><a href="index.php?action=list">📦 Quản lý Kho IMEI</a></li>

            <li style="background-color: #1890ff;">
                <a href="#" style="color: white; font-weight: bold;">🛍️ Sản phẩm <span style="float: right;">▼</span></a>
                <ul style="list-style: none; padding-left: 20px; background: #000c17;">
                    <li><a href="index.php?action=product_list" style="font-size: 14px; padding: 10px 20px;">Danh sách sản phẩm</a></li>
                    <li><a href="index.php?action=product_category" style="font-size: 14px; padding: 10px 20px;">Danh mục sản phẩm</a></li>
                    <li><a href="index.php?action=product_price" style="font-size: 14px; padding: 10px 20px;">Bảng giá</a></li>
                </ul>
            </li>

            <li><a href="index.php?action=search" target="_blank" style="color: #ff4d4f;">🔍 Trang Khách Hàng</a></li>
        </ul>
    </nav>

    <div class="main-wrapper">
        <div class="top-navbar">
            <h3>Hệ thống Quản trị AAKC (IMEI/Serial)</h3>
            <div>Xin chào, Admin</div>
        </div>
        <div class="content-area">
