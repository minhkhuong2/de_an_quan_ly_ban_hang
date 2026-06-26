<?php
// ÄÆ°á»ng dáº«n file: app/views/layout/header.php

// Láº¥y action hiá»‡n táº¡i Ä‘á»ƒ lÃ m ná»•i báº­t (active) menu Ä‘ang Ä‘Æ°á»£c chá»n
$current_action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Khá»Ÿi táº¡o máº£ng an toÃ n Ä‘á»ƒ trÃ¡nh lá»—i bÃ¡o Ä‘á» Session
$user_session = isset($_SESSION['user']) ? $_SESSION['user'] : ['role' => 'NhÃ¢n viÃªn', 'full_name' => 'KhÆ°Æ¡ng'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quáº£n lÃ½ Cá»­a hÃ ng - AKC Store</title>
    <!-- FontAwesome cho Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Reset cÆ¡ báº£n */
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

        /* Sidebar (Menu bÃªn trÃ¡i) */
        .sidebar {
            width: 260px;
            background: #111827;
            color: #9ca3af;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 100;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            padding: 24px 20px;
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .sidebar .logo i {
            color: #3b82f6;
            font-size: 26px;
        }

        .sidebar .menu {
            padding: 20px 15px;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar .menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar .menu::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 10px;
        }

        .sidebar .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 18px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar .menu-item i {
            font-size: 18px;
            width: 24px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .sidebar .menu-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            transform: translateX(4px);
        }

        .sidebar .menu-item:hover i {
            transform: scale(1.1);
            color: #60a5fa;
        }

        .sidebar .menu-item.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .sidebar .menu-item.active i {
            color: #ffffff;
        }

        .sidebar .menu-item.pos-btn {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.2);
            margin-bottom: 10px;
        }

        .sidebar .menu-item.pos-btn:hover {
            background: #10b981;
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .sidebar .menu-item.pos-btn:hover i {
            color: #ffffff;
        }

        .sidebar .menu-heading {
            padding: 18px 15px 8px 15px;
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 700;
            letter-spacing: 0.8px;
        }

        /* Dropdown Submenu cho ÄÆ¡n hÃ ng */
        .sidebar .has-submenu {
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            padding: 0;
            background: none !important;
            box-shadow: none !important;
        }

        .sidebar .has-submenu .menu-link {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
            padding: 12px 18px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .sidebar .has-submenu .menu-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }

        .sidebar .submenu {
            list-style: none;
            padding-left: 45px;
            width: 100%;
            margin-top: 4px;
        }

        .sidebar .submenu li a {
            display: block;
            padding: 8px 0;
            color: #9ca3af;
            text-decoration: none;
            font-size: 13.5px;
            transition: 0.3s;
        }

        .sidebar .submenu li a:hover {
            color: #60a5fa;
            transform: translateX(4px);
        }

        /* Khung ná»™i dung chÃ­nh */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Topbar (Thanh trÃªn cÃ¹ng) */
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

        /* Khu vá»±c hiá»ƒn thá»‹ View */
        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo"><i class="fa-solid fa-store"></i> AKC Store</div>
        <div class="menu">
            <a href="index.php?action=dashboard" class="menu-item <?php echo ($current_action == 'dashboard') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie"></i> Tá»•ng quan
            </a>
            <a href="index.php?action=pos" class="menu-item pos-btn">
                <i class="fa-solid fa-cash-register"></i> BÃ¡n hÃ ng (POS)
            </a>

            <div class="menu-heading">Quáº£n lÃ½ Sáº£n pháº©m</div>
            <a href="index.php?action=product_list" class="menu-item <?php echo ($current_action == 'product_list' || $current_action == 'add_product' || $current_action == 'edit_product') ? 'active' : ''; ?>">
                <i class="fa-solid fa-box-open"></i> Danh sÃ¡ch sáº£n pháº©m
            </a>
            <a href="index.php?action=product_category" class="menu-item <?php echo ($current_action == 'product_category' || $current_action == 'add_category' || $current_action == 'edit_category') ? 'active' : ''; ?>">
                <i class="fa-solid fa-folder-tree"></i> Danh má»¥c
            </a>

            <a href="index.php?action=product_price" <?php echo ($current_action == 'product_price' || $current_action == 'add_price_list' || $current_action == 'price_list_detail') ? 'style="color:#60a5fa;"' : ''; ?>>
                <i class="fa-solid fa-tags" style="font-size: 10px; margin-right: 5px;"></i> Báº£ng giÃ¡
            </a>

            <div class="menu-heading">Quáº£n lÃ½ Kho</div>
            <a href="index.php?action=inventory_list" class="menu-item <?php echo ($current_action == 'inventory_list') ? 'active' : ''; ?>">
                <i class="fa-solid fa-building"></i> Tá»“n kho
            </a>
            <a href="index.php?action=purchase_list" class="menu-item <?php echo ($current_action == 'purchase_list' || $current_action == 'add_purchase' || $current_action == 'view_purchase') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> Äáº·t hÃ ng nháº­p
            </a>
            <a href="index.php?action=po_receipt_list" class="menu-item <?php echo ($current_action == 'po_receipt_list' || $current_action == 'direct_receive' || $current_action == 'receive_purchase') ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck-ramp-box"></i> Nháº­p hÃ ng
            </a>
            <a href="index.php?action=transfer_list" class="menu-item <?php echo ($current_action == 'transfer_list' || $current_action == 'add_transfer' || $current_action == 'view_transfer') ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck-moving"></i> Chuyá»ƒn kho
            </a>
            <a href="index.php?action=purchase_return_list" class="menu-item <?php echo ($current_action == 'purchase_return_list' || $current_action == 'add_purchase_return') ? 'active' : ''; ?>">
                <i class="fa-solid fa-arrow-right-arrow-left"></i> Tráº£ hÃ ng nháº­p
            </a>
            <a href="index.php?action=inventory_check_list" class="menu-item <?php echo ($current_action == 'inventory_check_list' || $current_action == 'add_inventory_check' || $current_action == 'edit_inventory_check') ? 'active' : ''; ?>">
                <i class="fa-solid fa-clipboard-check"></i> Kiá»ƒm kho
            </a>

            <div class="menu-heading">Äá»‘i tÃ¡c & KhÃ¡ch hÃ ng</div>
            <a href="index.php?action=supplier_list" class="menu-item <?php echo ($current_action == 'supplier_list' || $current_action == 'add_supplier' || $current_action == 'edit_supplier') ? 'active' : ''; ?>">
                <i class="fa-solid fa-handshake"></i> NhÃ  cung cáº¥p
            </a>

            <div class="menu-item has-submenu">
                <a href="javascript:void(0)" class="menu-link <?php echo (in_array($current_action, ['customer_list', 'customer_groups', 'create_customer_group', 'customer_group_detail'])) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> KhÃ¡ch hÃ ng
                </a>
                <ul class="submenu">
                    <li><a href="index.php?action=customer_list" <?php echo ($current_action == 'customer_list') ? 'style="color:#60a5fa;"' : ''; ?>><i class="fa-solid fa-list-ul" style="font-size: 10px; margin-right: 5px;"></i> Danh sÃ¡ch khÃ¡ch hÃ ng</a></li>
                    <li><a href="index.php?action=customer_groups" <?php echo (in_array($current_action, ['customer_groups', 'create_customer_group', 'customer_group_detail'])) ? 'style="color:#60a5fa;"' : ''; ?>><i class="fa-solid fa-users-rectangle" style="font-size: 10px; margin-right: 5px;"></i> NhÃ³m khÃ¡ch hÃ ng</a></li>
                </ul>
            </div>

            <a href="index.php?action=debt_app_list" class="menu-item <?php echo (in_array($current_action, ['debt_app_list', 'debt_app_detail'])) ? 'active' : ''; ?>" style="color: #e67e22;">
                <i class="fa-solid fa-calculator"></i> Quáº£n lÃ½ cÃ´ng ná»£
            </a>

            <div class="menu-heading" style="margin-top: 15px;">TÃ i chÃ­nh & Khuyáº¿n máº¡i</div>
            <a href="index.php?action=promo_list" class="menu-item <?php echo in_array($current_action, ['promo_list', 'add_promo', 'view_promo', 'edit_promo', 'copy_promo', 'promo_settings']) ? 'active' : ''; ?>">
                <i class="fa-solid fa-gift"></i> Quáº£n lÃ½ Khuyáº¿n máº¡i
            </a>

            <div class="menu-item has-submenu">
                <a href="index.php?action=fund_dashboard" class="menu-link <?php echo (in_array($current_action, ['fund_dashboard', 'fund_transfers', 'create_fund_transfer', 'fund_transfer_detail', 'create_receipt', 'create_expense', 'expense_list', 'expense_detail', 'receipt_list', 'receipt_detail', 'fund_reasons'])) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-wallet"></i> Sá»• quá»¹
                </a>
                <ul class="submenu">
                    <li>
                        <a href="index.php?action=expense_list" <?php echo (in_array($current_action, ['expense_list', 'expense_detail'])) ? 'style="color:#60a5fa;"' : ''; ?>>
                            <i class="fa-solid fa-list" style="font-size: 10px; margin-right: 5px;"></i> Danh sÃ¡ch phiáº¿u chi
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=create_receipt" <?php echo ($current_action == 'create_receipt') ? 'style="color:#60a5fa;"' : ''; ?>>
                            <i class="fa-solid fa-arrow-down" style="font-size: 10px; margin-right: 5px;"></i> Táº¡o Phiáº¿u thu (Thu ná»£)
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=create_expense" <?php echo ($current_action == 'create_expense') ? 'style="color:#60a5fa;"' : ''; ?>>
                            <i class="fa-solid fa-arrow-up" style="font-size: 10px; margin-right: 5px;"></i> Táº¡o Phiáº¿u chi (Chi ná»£)
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=fund_transfers" <?php echo (in_array($current_action, ['fund_transfers', 'create_fund_transfer', 'fund_transfer_detail'])) ? 'style="color:#60a5fa;"' : ''; ?>>
                            <i class="fa-solid fa-money-bill-transfer" style="font-size: 10px; margin-right: 5px;"></i> Phiáº¿u chuyá»ƒn quá»¹ ná»™i bá»™
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=receipt_list" <?php echo (in_array($current_action, ['receipt_list', 'receipt_detail'])) ? 'style="color:#60a5fa;"' : ''; ?>>
                            <i class="fa-solid fa-list-ol" style="font-size: 10px; margin-right: 5px;"></i> Danh sÃ¡ch phiáº¿u thu
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=fund_reasons" <?php echo ($current_action == 'fund_reasons') ? 'style="color:#60a5fa;"' : ''; ?>>
                            <i class="fa-solid fa-list-check" style="font-size: 10px; margin-right: 5px;"></i> LÃ½ do thu chi
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=print_cashbook&type=cash" target="_blank">
                            <i class="fa-solid fa-print" style="font-size: 10px; margin-right: 5px; color:#e67e22;"></i> ðŸ–¨ï¸ In Sá»• quá»¹ Tiá»n máº·t
                        </a>
                    </li>
                    <li>
                        <a href="index.php?action=print_cashbook&type=bank" target="_blank">
                            <i class="fa-solid fa-print" style="font-size: 10px; margin-right: 5px; color:#0088ff;"></i> ðŸ–¨ï¸ In Sá»• quá»¹ Tiá»n gá»­i
                        </a>
                    </li>
                </ul>
            </div>

            <div class="menu-item has-submenu">
                <a href="javascript:void(0)" class="menu-link <?php echo (in_array($current_action, ['order_list', 'create_order', 'order_processing'])) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-cart-shopping"></i> ÄÆ¡n hÃ ng
                </a>
                <ul class="submenu">
                    <li><a href="index.php?action=order_processing" <?php echo ($current_action == 'order_processing') ? 'style="color:#60a5fa;"' : ''; ?>><i class="fa-solid fa-box-open" style="font-size: 10px; margin-right: 5px;"></i> Xá»­ lÃ½ Ä‘Æ¡n hÃ ng</a></li>
                    <li><a href="index.php?action=order_list" <?php echo ($current_action == 'order_list') ? 'style="color:#60a5fa;"' : ''; ?>><i class="fa-solid fa-list-ul" style="font-size: 10px; margin-right: 5px;"></i> Danh sÃ¡ch Ä‘Æ¡n hÃ ng</a></li>
                    <li><a href="index.php?action=create_order" <?php echo ($current_action == 'create_order') ? 'style="color:#60a5fa;"' : ''; ?>><i class="fa-solid fa-plus" style="font-size: 10px; margin-right: 5px;"></i> Táº¡o Ä‘Æ¡n hÃ ng (Online)</a></li>
                </ul>
            </div>
            <div class="menu-heading" style="margin-top: 15px;">Váº­n Chuyá»ƒn</div>
            <a href="index.php?action=shipment_list" class="menu-item <?php echo ($current_action == 'shipment_list') ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck"></i> Quáº£n lÃ½ Váº­n Ä‘Æ¡n
            </a>

            <div class="menu-heading" style="margin-top: 15px;">Há»‡ thá»‘ng</div>
            <a href="index.php?action=settings_hub" class="menu-item <?php echo (in_array($current_action, ['settings_hub', 'store_settings', 'pos_settings', 'payment_methods', 'order_sources', 'order_settings'])) ? 'active' : ''; ?>">
                <i class="fa-solid fa-gear"></i> Cáº¥u hÃ¬nh há»‡ thá»‘ng
            </a>
            <ul class="submenu">
                <li>
                    <a href="index.php?action=store_settings" <?php echo ($current_action == 'store_settings') ? 'style="color:#60a5fa;"' : ''; ?>>
                        <i class="fa-solid fa-store" style="font-size: 10px; margin-right: 5px;"></i> Thiáº¿t láº­p cá»­a hÃ ng
                    </a>
                </li>
                <li>
                    <a href="index.php?action=pos_settings" <?php echo ($current_action == 'pos_settings') ? 'style="color:#60a5fa;"' : ''; ?>>
                        <i class="fa-solid fa-desktop" style="font-size: 10px; margin-right: 5px;"></i> Cáº¥u hÃ¬nh POS táº¡i quáº§y
                    </a>
                </li>
                <li>
                    <a href="index.php?action=payment_methods" <?php echo ($current_action == 'payment_methods') ? 'style="color:#60a5fa;"' : ''; ?>>
                        <i class="fa-solid fa-credit-card" style="font-size: 10px; margin-right: 5px;"></i> PhÆ°Æ¡ng thá»©c thanh toÃ¡n
                    </a>
                </li>
                <li>
                    <a href="index.php?action=order_sources" <?php echo ($current_action == 'order_sources') ? 'style="color:#60a5fa;"' : ''; ?>>
                        <i class="fa-solid fa-globe" style="font-size: 10px; margin-right: 5px;"></i> Quáº£n lÃ½ Nguá»“n Ä‘Æ¡n hÃ ng
                    </a>
                </li>
                <li>
                    <a href="index.php?action=order_settings" <?php echo ($current_action == 'order_settings') ? 'style="color:#60a5fa;"' : ''; ?>>
                        <i class="fa-solid fa-boxes-packing" style="font-size: 10px; margin-right: 5px;"></i> Quy trÃ¬nh xá»­ lÃ½ Ä‘Æ¡n hÃ ng
                    </a>
                </li>
            </ul>
        </div>
    </div>
    </div>

    <div class="main-container">

        <div class="topbar">
            <div class="user-profile">
                <span>ðŸ‘¤ Chá»©c vá»¥: <strong style="color:#0088ff;"><?php echo htmlspecialchars($user_session['role'] ?? 'Quáº£n trá»‹ viÃªn'); ?></strong> | </span>
                <span>Xin chÃ o, <strong><?php echo htmlspecialchars($user_session['full_name'] ?? 'Admin'); ?></strong></span>
                <a href="index.php?action=logout" style="margin-left: 15px; color: #ff4d4f; text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid #ff4d4f; padding: 4px 8px; border-radius: 4px;">ðŸšª ÄÄƒng xuáº¥t</a>
            </div>
        </div>

        <div class="content-wrapper">

