<?php
// Lấy action hiện tại từ URL (Mặc định là dashboard nếu không có)
$current_action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// CẤU HÌNH MENU TỰ ĐỘNG BẰNG MẢNG PHP
$menus = [
    [
        'action' => 'dashboard',
        'title' => '🏠 Tổng quan',
        'url' => 'index.php?action=dashboard'
    ],
    [
        'action' => 'list',
        'title' => '📦 Quản lý Kho IMEI',
        'url' => 'index.php?action=list'
    ],
    [
        'action' => 'products_group',
        'title' => '🛍️ Sản phẩm',
        'url' => '#',
        'sub' => [
            ['action' => 'product_list', 'title' => 'Danh sách sản phẩm', 'url' => 'index.php?action=product_list'],
            ['action' => 'product_category', 'title' => 'Danh mục sản phẩm', 'url' => 'index.php?action=product_category'],
            ['action' => 'product_price', 'title' => 'Bảng giá', 'url' => 'index.php?action=product_price'],
        ]
    ],
    [
        'action' => 'pos',
        'title' => '🛒 Bán hàng tại quầy (POS)',
        'url' => 'index.php?action=pos',
        'custom_color' => '#52c41a' // Màu xanh lá cho nổi bật
    ],
    [
        'action' => 'search',
        'title' => '🔍 Trang Khách Hàng',
        'url' => 'index.php?action=search',
        'is_blank' => true,
        'custom_color' => '#ff4d4f'
    ]

];
?>
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

            <?php foreach ($menus as $menu): ?>

                <?php if (isset($menu['sub'])): ?>
                    <?php
                    $isActiveGroup = false;
                    foreach ($menu['sub'] as $sub) {
                        if ($current_action == $sub['action']) $isActiveGroup = true;
                    }
                    ?>
                    <li style="<?php echo $isActiveGroup ? 'background-color: #1890ff;' : ''; ?>">

                        <a href="<?php echo $menu['url']; ?>"
                            onclick="toggleSubMenu(event, this)"
                            style="<?php echo $isActiveGroup ? 'color: white; font-weight: bold;' : ''; ?>">
                            <?php echo $menu['title']; ?> <span style="float: right;">▼</span>
                        </a>

                        <ul style="list-style: none; padding-left: 20px; background: #000c17; <?php echo $isActiveGroup ? 'display: block;' : 'display: none;'; ?>">
                            <?php foreach ($menu['sub'] as $sub): ?>
                                <li>
                                    <a href="<?php echo $sub['url']; ?>" style="font-size: 14px; padding: 10px 20px; <?php echo ($current_action == $sub['action']) ? 'color: #1890ff;' : ''; ?>">
                                        <?php echo $sub['title']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                <?php else: ?>

                    <li style="<?php echo ($current_action == $menu['action']) ? 'background-color: #1890ff;' : ''; ?>">
                        <a href="<?php echo $menu['url']; ?>"
                            target="<?php echo isset($menu['is_blank']) ? '_blank' : '_self'; ?>"
                            style="<?php echo ($current_action == $menu['action']) ? 'color: white; font-weight: bold;' : (isset($menu['custom_color']) ? 'color: ' . $menu['custom_color'] . ';' : ''); ?>">
                            <?php echo $menu['title']; ?>
                        </a>
                    </li>

                <?php endif; ?>

            <?php endforeach; ?>

        </ul>
    </nav>

    <div class="main-wrapper">
        <div class="top-navbar">
            <h3>Hệ thống Quản trị AAKC (IMEI/Serial)</h3>
            <div>Xin chào, Admin</div>
        </div>
        <div class="content-area">

            <script>
                function toggleSubMenu(event, element) {
                    // Ngăn chặn việc nhảy trang khi bấm vào link có dấu #
                    event.preventDefault();

                    // Tìm danh sách menu con (thẻ <ul>) nằm ngay bên dưới nút vừa bấm
                    var submenu = element.nextElementSibling;

                    // Nếu đang ẩn thì hiện ra, nếu đang hiện thì ẩn đi
                    if (submenu.style.display === 'none' || submenu.style.display === '') {
                        submenu.style.display = 'block';
                    } else {
                        submenu.style.display = 'none';
                    }
                }
            </script>
