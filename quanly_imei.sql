-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 01, 2026 lúc 09:17 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanly_imei`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `account_name`, `account_number`, `bank_name`, `status`) VALUES
(1, 'CÔNG TY AAKC', '1903123456789', 'Techcombank', 'active'),
(2, 'BUI VAN KHUONG', '0123456789', 'MB Bank', 'active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Vietnam',
  `province` varchar(100) NOT NULL,
  `district` varchar(100) DEFAULT NULL COMMENT 'Trống nếu dùng địa chỉ 2 cấp',
  `ward` varchar(100) NOT NULL,
  `address_detail` text NOT NULL,
  `is_new_address_format` tinyint(1) DEFAULT 0 COMMENT '0: 3 cấp, 1: 2 cấp',
  `has_inventory` tinyint(1) DEFAULT 1 COMMENT '1: Có quản lý kho, 0: Không',
  `is_default` tinyint(1) DEFAULT 0,
  `is_pickup_location` tinyint(1) DEFAULT 0,
  `routing_priority` int(11) DEFAULT 0,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `branches`
--

INSERT INTO `branches` (`id`, `branch_code`, `branch_name`, `phone`, `email`, `country`, `province`, `district`, `ward`, `address_detail`, `is_new_address_format`, `has_inventory`, `is_default`, `is_pickup_location`, `routing_priority`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CN001', 'Chi nhánh Trung tâm (VNPost)', '0988111222', NULL, 'Vietnam', 'Hà Nội', 'Cầu Giấy', 'Dịch Vọng', 'Tầng 6 Tòa nhà Ladeco', 0, 1, 1, 1, 0, 'active', '2026-06-24 16:38:20', '2026-06-24 16:38:20'),
(2, 'CN002', 'Chi nhánh Grab Express', '0988333444', NULL, 'Vietnam', 'Hà Nội', NULL, 'Dịch Vọng', 'Tầng 1 Tòa nhà Ladeco', 1, 0, 0, 0, 0, 'active', '2026-06-24 16:38:20', '2026-07-01 11:04:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` varchar(320) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Hiển thị',
  `selection_type` varchar(20) DEFAULT 'manual',
  `match_type` varchar(10) DEFAULT 'all',
  `auto_rules` text DEFAULT NULL,
  `sort_order` varchar(50) DEFAULT 'newest',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `description`, `alias`, `seo_title`, `seo_description`, `status`, `selection_type`, `match_type`, `auto_rules`, `sort_order`, `created_at`) VALUES
(1, 'Điện thoại di động', 'Các dòng smartphone mới nhất hiện nay từ các thương hiệu hàng đầu.', 'dien-thoai-di-dong', 'Điện thoại di động chính hãng', 'Mua bán điện thoại di động giá rẻ, bảo hành uy tín.', 'Hiển thị', 'manual', 'all', '[]', 'newest', '2026-05-25 17:00:54'),
(2, 'Phụ kiện chính hãng', 'Cáp sạc, tai nghe, ốp lưng, sạc dự phòng, kính cường lực...', 'phu-kien-chinh-hang', 'Phụ kiện điện thoại chính hãng', 'Phụ kiện điện thoại zin bóc máy, hàng chuẩn hãng.', 'Hiển thị', 'manual', 'all', '[]', 'newest', '2026-05-25 17:00:54'),
(3, 'Đồng hồ thông minh', 'Apple Watch, Samsung Galaxy Watch, Garmin, Huawei...', 'dong-ho-thong-minh', 'Đồng hồ thông minh giá tốt', 'Smartwatch theo dõi sức khỏe, thể thao chuyên nghiệp.', 'Hiển thị', 'manual', 'all', '[]', 'newest', '2026-05-25 17:00:54'),
(4, 'Sản phẩm Apple (Tự động)', 'Hệ thống tự động gom các sản phẩm thuộc nhãn hiệu Apple vào đây.', 'san-pham-apple', 'Sản phẩm Apple chính hãng', 'Tất cả sản phẩm thuộc hệ sinh thái Apple.', 'Hiển thị', 'auto', 'all', '[{\"field\":\"brand\",\"operator\":\"equals\",\"value\":\"Apple\"}]', 'newest', '2026-05-25 17:00:54'),
(5, 'Khuyến mãi Sốc (Tự động)', 'Hệ thống tự động gom các sản phẩm có giá trị giảm giá > 0.', 'khuyen-mai-soc', 'Sản phẩm đang khuyến mãi', 'Tổng hợp hàng giảm giá, xả kho ưu đãi lớn.', 'Hiển thị', 'auto', 'any', '[{\"field\":\"compare_price\",\"operator\":\"greater_than\",\"value\":\"0\"}]', 'price_desc', '2026-05-25 17:00:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `accept_marketing` tinyint(1) DEFAULT 0,
  `province` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `ward` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_code` varchar(50) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `invoice_address` text DEFAULT NULL,
  `invoice_email` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `debt` decimal(15,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `customer_code`, `last_name`, `first_name`, `phone`, `email`, `accept_marketing`, `province`, `district`, `ward`, `address`, `tax_code`, `company_name`, `invoice_address`, `invoice_email`, `notes`, `tags`, `debt`, `created_at`) VALUES
(1, 'KH0001', 'Bùi Văn', 'Khương', '0987654321', '', 0, 'Hà Nội', 'Thượng Tín', 'Xã Nhị Khê', '', '', '', '', '', '', '', 900000.00, '2026-06-06 23:23:37'),
(2, 'KH0002', ' Nguyễn Sơn', 'Trường', '0867473783', 'sontruong2005@gmail.com', 1, 'Ninh Bình', 'Hải Hậu', 'Hải Anh', 'Số 23', '', '', '', '', '', '', 0.00, '2026-06-14 22:37:42'),
(999, 'KH0003', 'Khách Hàng', 'Test', '0123456789', '', 1, '', '', '', 'Hà Nội', '', '', '', '', '', '', 0.00, '2026-06-27 01:48:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_debt_history`
--

CREATE TABLE `customer_debt_history` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `transaction_type` varchar(50) NOT NULL COMMENT 'order, return, receipt, payment, adjustment',
  `reference_code` varchar(100) DEFAULT NULL COMMENT 'Mã đơn hoặc mã phiếu',
  `debt_increase` decimal(15,2) DEFAULT 0.00 COMMENT 'Ghi Tăng công nợ (+)',
  `debt_decrease` decimal(15,2) DEFAULT 0.00 COMMENT 'Ghi Giảm công nợ (-)',
  `balance` decimal(15,2) NOT NULL COMMENT 'Dư nợ cuối cùng sau giao dịch',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_debt_history`
--

INSERT INTO `customer_debt_history` (`id`, `customer_id`, `transaction_type`, `reference_code`, `debt_increase`, `debt_decrease`, `balance`, `description`, `created_at`) VALUES
(1, 1, 'payment', 'PC20260620003', 300000.00, 0.00, 900000.00, 'Hạch toán tăng nợ từ phiếu chi thủ công PC20260620003', '2026-06-19 17:58:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_groups`
--

CREATE TABLE `customer_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `group_type` enum('manual','auto') DEFAULT 'manual',
  `condition_match` enum('all','any') DEFAULT 'all',
  `conditions_json` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_groups`
--

INSERT INTO `customer_groups` (`id`, `group_name`, `description`, `group_type`, `condition_match`, `conditions_json`, `created_at`) VALUES
(1, 'Khách hàng VIP', NULL, 'manual', 'all', NULL, '2026-06-19 14:37:52'),
(2, 'Khách hàng Sỉ (Đại lý)', NULL, 'manual', 'all', NULL, '2026-06-19 14:37:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_group_members`
--

CREATE TABLE `customer_group_members` (
  `group_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_code` varchar(50) DEFAULT NULL,
  `payment_method` enum('cash','bank') NOT NULL,
  `recipient_group` enum('customer','supplier','employee','shipper','payment_partner','other') NOT NULL DEFAULT 'other',
  `recipient_id` int(11) DEFAULT NULL,
  `recipient_name` varchar(150) NOT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `expense_reason` varchar(100) NOT NULL,
  `expense_category` varchar(100) DEFAULT NULL,
  `is_debt_affected` tinyint(1) DEFAULT 0,
  `is_automatic` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `reference_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `expenses`
--

INSERT INTO `expenses` (`id`, `expense_code`, `payment_method`, `recipient_group`, `recipient_id`, `recipient_name`, `bank_account_id`, `customer_id`, `amount`, `expense_reason`, `expense_category`, `is_debt_affected`, `is_automatic`, `description`, `branch_id`, `transaction_date`, `reference_code`, `created_at`) VALUES
(1, 'PC20260620001', 'cash', 'employee', 1, 'Nguyễn Văn Nhân Viên', NULL, NULL, 5000000.00, 'Chi trả tiền lương nhân viên', 'Chi phí nhân sự', 0, 0, 'Thanh toán lương tháng 5 cho nhân viên kho', 1, '2026-06-20 08:30:00', 'BL-05-2026', '2026-06-19 17:58:16'),
(2, 'PC20260620002', 'bank', 'other', NULL, 'Công ty Điện lực Hải Phòng', 1, NULL, 1250000.00, 'Thanh toán hóa đơn điện nước', 'Chi phí tiện ích văn phòng', 0, 0, 'Đóng tiền điện tháng 5/2026', 1, '2026-06-20 09:15:00', 'HD-EVN-2026', '2026-06-19 17:58:16'),
(3, 'PC20260620003', 'cash', 'customer', 1, 'Khách hàng VIP 01', NULL, NULL, 300000.00, 'Chi hỗ trợ / Khuyến mại', 'Chi phí Marketing', 1, 0, 'Chi hỗ trợ sự kiện cho khách hàng (có hạch toán tăng nợ)', 1, '2026-06-20 10:00:00', 'KM-003', '2026-06-19 17:58:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `e_invoices`
--

CREATE TABLE `e_invoices` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL COMMENT 'Số hóa đơn (Tự tăng)',
  `invoice_symbol` varchar(20) NOT NULL COMMENT 'Ký hiệu HĐ (VD: 1C26TAA)',
  `cqt_code` varchar(100) DEFAULT NULL COMMENT 'Mã tra cứu Cơ quan Thuế (Giả lập)',
  `status` enum('draft','issued') DEFAULT 'issued',
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `fund_transfers`
--

CREATE TABLE `fund_transfers` (
  `id` int(11) NOT NULL,
  `transfer_code` varchar(50) DEFAULT NULL,
  `from_type` enum('cash','bank') NOT NULL,
  `from_id` int(11) NOT NULL,
  `to_type` enum('cash','bank') NOT NULL,
  `to_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `reference_code` varchar(100) DEFAULT NULL,
  `status` enum('completed','cancelled') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `fund_transfers`
--

INSERT INTO `fund_transfers` (`id`, `transfer_code`, `from_type`, `from_id`, `to_type`, `to_id`, `amount`, `description`, `transaction_date`, `reference_code`, `status`, `created_at`) VALUES
(1, 'PCQ20260619001', 'cash', 1, 'bank', 1, 15000000.00, 'Nộp tiền mặt doanh thu bán lẻ trong ngày vào tài khoản Techcombank', '2026-06-19 08:30:00', 'UNC-TCB-001', 'completed', '2026-06-18 18:10:15'),
(2, 'PCQ20260619002', 'bank', 1, 'cash', 1, 5000000.00, 'Rút tiền mặt từ thẻ Techcombank về làm quỹ tiêu vặt tại chi nhánh', '2026-06-18 14:15:00', 'RTM-002', 'completed', '2026-06-18 18:10:15'),
(3, 'PCQ20260619003', 'bank', 1, 'bank', 2, 25000000.00, 'Chuyển khoản vốn lưu động từ Techcombank sang MB Bank', '2026-06-19 10:45:00', 'CK-MB-003', 'completed', '2026-06-18 18:10:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `handover_items`
--

CREATE TABLE `handover_items` (
  `id` int(11) NOT NULL,
  `handover_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `package_code` varchar(50) DEFAULT NULL,
  `waybill_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `handover_records`
--

CREATE TABLE `handover_records` (
  `id` int(11) NOT NULL,
  `record_code` varchar(50) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `shipping_partner_id` int(11) DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `tags` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_checks`
--

CREATE TABLE `inventory_checks` (
  `id` int(11) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `employee` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Đã cân bằng',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory_checks`
--

INSERT INTO `inventory_checks` (`id`, `branch`, `employee`, `note`, `status`, `created_at`) VALUES
(2, 'Cửa hàng chính', 'Admin', 'okj', 'Đã cân bằng', '2026-06-02 22:41:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_check_details`
--

CREATE TABLE `inventory_check_details` (
  `id` int(11) NOT NULL,
  `check_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `system_stock` int(11) DEFAULT NULL,
  `actual_stock` int(11) DEFAULT NULL,
  `difference` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory_check_details`
--

INSERT INTO `inventory_check_details` (`id`, `check_id`, `product_id`, `system_stock`, `actual_stock`, `difference`, `reason`) VALUES
(2, 2, 8, 8, 8, 0, 'Nhập sai');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_transfers`
--

CREATE TABLE `inventory_transfers` (
  `id` int(11) NOT NULL,
  `from_branch` varchar(255) DEFAULT NULL,
  `to_branch` varchar(255) DEFAULT NULL,
  `employee` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Phiếu nháp',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_transfer_details`
--

CREATE TABLE `inventory_transfer_details` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `created_at` datetime DEFAULT current_timestamp(),
  `total_product_discount` decimal(15,2) DEFAULT 0.00,
  `total_order_discount` decimal(15,2) DEFAULT 0.00,
  `original_shipping_fee` decimal(15,2) DEFAULT 0.00,
  `total_shipping_discount` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `amount_paid` decimal(15,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'cash',
  `order_status` varchar(50) DEFAULT 'pending',
  `shipping_status` varchar(50) DEFAULT 'pending',
  `sales_channel` varchar(50) DEFAULT 'pos',
  `is_archived` tinyint(1) DEFAULT 0 COMMENT '0: Bình thường, 1: Đã lưu trữ',
  `packing_status` varchar(50) DEFAULT 'pending' COMMENT 'pending, packing, packed',
  `assigned_staff_id` int(11) DEFAULT NULL COMMENT 'ID Nhân viên phụ trách',
  `tags` varchar(255) DEFAULT NULL COMMENT 'Các tag cách nhau dấu phẩy (VD: V.I.P, Gấp)',
  `has_e_invoice` tinyint(1) DEFAULT 0 COMMENT '1: Đã xuất Hóa đơn điện tử',
  `printed_delivery` tinyint(1) DEFAULT 0,
  `printed_picking_order` tinyint(1) DEFAULT 0,
  `printed_picking_product` tinyint(1) DEFAULT 0,
  `packer_id` int(11) DEFAULT NULL,
  `main_note` text DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `packaging_staff_id` int(11) DEFAULT NULL,
  `package_status` varchar(50) DEFAULT 'pending',
  `package_code` varchar(50) DEFAULT NULL,
  `waybill_code` varchar(50) DEFAULT NULL,
  `draft_status` varchar(20) DEFAULT NULL,
  `email_status` enum('unsent','sent','scheduled') DEFAULT 'unsent',
  `email_sent_at` datetime DEFAULT NULL,
  `recovery_url` varchar(255) DEFAULT NULL,
  `invoice_status` enum('not_requested','requested','pending_issue','issued','failed') DEFAULT 'not_requested',
  `invoice_tax_code` varchar(50) DEFAULT NULL,
  `invoice_company_name` varchar(255) DEFAULT NULL,
  `invoice_address` varchar(255) DEFAULT NULL,
  `invoice_buyer_name` varchar(255) DEFAULT NULL,
  `invoice_phone` varchar(20) DEFAULT NULL,
  `invoice_email` varchar(255) DEFAULT NULL,
  `invoice_no_receipt` tinyint(1) DEFAULT 0,
  `invoice_date` datetime DEFAULT NULL,
  `invoice_symbol` varchar(50) DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_cqt_code` varchar(100) DEFAULT NULL,
  `invoice_lookup_code` varchar(100) DEFAULT NULL,
  `reconciliation_status` enum('Chưa đối soát','Đang đối soát','Đã đối soát') DEFAULT 'Chưa đối soát',
  `cod_partner` decimal(15,2) DEFAULT 0.00,
  `shipping_fee_partner` decimal(15,2) DEFAULT 0.00,
  `shipping_partner_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `customer_id`, `customer_name`, `phone`, `address`, `subtotal`, `total_amount`, `paid_amount`, `payment_status`, `created_at`, `total_product_discount`, `total_order_discount`, `original_shipping_fee`, `total_shipping_discount`, `tax_amount`, `grand_total`, `amount_paid`, `payment_method`, `order_status`, `shipping_status`, `sales_channel`, `is_archived`, `packing_status`, `assigned_staff_id`, `tags`, `has_e_invoice`, `printed_delivery`, `printed_picking_order`, `printed_picking_product`, `packer_id`, `main_note`, `delivery_date`, `packaging_staff_id`, `package_status`, `package_code`, `waybill_code`, `draft_status`, `email_status`, `email_sent_at`, `recovery_url`, `invoice_status`, `invoice_tax_code`, `invoice_company_name`, `invoice_address`, `invoice_buyer_name`, `invoice_phone`, `invoice_email`, `invoice_no_receipt`, `invoice_date`, `invoice_symbol`, `invoice_number`, `invoice_cqt_code`, `invoice_lookup_code`, `reconciliation_status`, `cod_partner`, `shipping_fee_partner`, `shipping_partner_name`) VALUES
(1, 'DH001', 1, NULL, NULL, NULL, 0.00, 200000.00, 0.00, 'unpaid', '2026-01-01 10:00:00', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'cash', 'processing', 'shipping', 'pos', 0, 'packing', NULL, NULL, 0, 0, 1, 1, NULL, NULL, NULL, NULL, 'pending', NULL, 'GHN8586264', NULL, 'unsent', NULL, NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 0.00, 30000.00, 'Giao Hàng Nhanh'),
(3, 'DH003', 1, NULL, NULL, NULL, 0.00, 100000.00, 0.00, 'unpaid', '2026-01-10 10:00:00', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'cash', 'pending', 'pending', 'pos', 0, 'pending', NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, 'unsent', NULL, NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 0.00, 0.00, NULL),
(5, 'ORD-TEST-001', 999, NULL, NULL, NULL, 500000.00, 0.00, 0.00, 'unpaid', '2026-06-27 01:48:21', 0.00, 0.00, 0.00, 0.00, 0.00, 500000.00, 0.00, 'cash', 'completed', 'shipping', 'Website', 0, 'pending', NULL, NULL, 0, 1, 0, 0, NULL, 'TEST LẠI ', NULL, NULL, 'pending', NULL, 'VT7858753', NULL, 'unsent', NULL, NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 500000.00, 30000.00, 'Viettel Post'),
(6, 'ORD-TEST-002', 999, NULL, NULL, NULL, 1500000.00, 0.00, 0.00, 'unpaid', '2026-06-27 01:48:21', 0.00, 0.00, 0.00, 0.00, 0.00, 1500000.00, 0.00, 'cash', 'confirmed', 'pending', 'Website', 0, 'pending', NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, 'unsent', NULL, NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 0.00, 0.00, NULL),
(7, 'ORD-TEST-003', 999, NULL, NULL, NULL, 2000000.00, 0.00, 0.00, 'unpaid', '2026-06-27 01:48:21', 0.00, 0.00, 0.00, 0.00, 0.00, 2000000.00, 0.00, 'cash', 'confirmed', 'returning', 'pos', 0, 'pending', NULL, NULL, 0, 1, 0, 0, NULL, NULL, NULL, NULL, 'pending', NULL, 'GHN7288585', NULL, 'unsent', NULL, NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 2000000.00, 30000.00, 'Giao Hàng Nhanh'),
(10, 'ABANDON-101', 1, 'Khách Vãng Lai', NULL, NULL, 0.00, 500000.00, 0.00, 'unpaid', '2026-07-01 23:49:22', 0.00, 0.00, 0.00, 0.00, 0.00, 500000.00, 0.00, 'cash', 'incomplete', 'pending', 'pos', 0, 'pending', NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, 'unsent', NULL, NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 0.00, 0.00, NULL),
(11, 'ABANDON-102', 1, 'Khách Vãng Lai', NULL, NULL, 0.00, 1200000.00, 0.00, 'unpaid', '2026-07-01 01:49:22', 0.00, 0.00, 0.00, 0.00, 0.00, 1200000.00, 0.00, 'cash', 'incomplete', 'pending', 'pos', 0, 'pending', NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, 'sent', '2026-07-01 02:49:22', NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 0.00, 0.00, NULL),
(12, 'ABANDON-103', 1, 'Khách Vãng Lai', NULL, NULL, 0.00, 150000.00, 0.00, 'unpaid', '2026-06-27 01:49:22', 0.00, 0.00, 0.00, 0.00, 0.00, 150000.00, 0.00, 'cash', 'incomplete', 'pending', 'pos', 1, 'pending', NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, 'unsent', NULL, NULL, 'not_requested', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Chưa đối soát', 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_exchange_items`
--

CREATE TABLE `order_exchange_items` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT 0,
  `price` decimal(15,2) DEFAULT 0.00,
  `discount` decimal(15,2) DEFAULT 0.00,
  `line_total` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `original_price` decimal(15,2) DEFAULT 0.00,
  `promo_discount` decimal(15,2) DEFAULT 0.00,
  `manual_discount` decimal(15,2) DEFAULT 0.00,
  `final_price` decimal(15,2) DEFAULT 0.00,
  `line_total` decimal(15,2) DEFAULT 0.00,
  `is_gift` tinyint(1) DEFAULT 0,
  `note` varchar(255) DEFAULT NULL,
  `returned_qty` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `qty`, `original_price`, `promo_discount`, `manual_discount`, `final_price`, `line_total`, `is_gift`, `note`, `returned_qty`) VALUES
(1, 5, 6, 'iPhone 15 Pro Max 256GB', 'IP15PM-256', 1, 29000000.00, 0.00, 0.00, 29000000.00, 29000000.00, 0, NULL, 0),
(2, 6, 6, 'iPhone 15 Pro Max 256GB', 'IP15PM-256', 2, 29000000.00, 0.00, 0.00, 29000000.00, 58000000.00, 0, NULL, 0),
(3, 6, 7, 'Samsung Galaxy S24 Ultra', 'S24U-512', 2, 31990000.00, 0.00, 0.00, 31990000.00, 63980000.00, 0, NULL, 0),
(4, 6, 8, 'Apple Watch Series 9', 'AW-S9-41', 2, 9500000.00, 0.00, 0.00, 9500000.00, 19000000.00, 0, NULL, 0),
(5, 7, 7, 'Samsung Galaxy S24 Ultra', 'S24U-512', 1, 31990000.00, 0.00, 0.00, 31990000.00, 31990000.00, 0, NULL, 0),
(6, 10, 1, 'Sản phẩm 1', NULL, 2, 0.00, 0.00, 0.00, 250000.00, 500000.00, 0, NULL, 0),
(7, 11, 2, 'Sản phẩm 2', NULL, 1, 0.00, 0.00, 0.00, 1200000.00, 1200000.00, 0, NULL, 0),
(8, 12, 3, 'Sản phẩm 3', NULL, 1, 0.00, 0.00, 0.00, 150000.00, 150000.00, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_returns`
--

CREATE TABLE `order_returns` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `return_code` varchar(50) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `total_return_value` decimal(15,2) DEFAULT 0.00,
  `total_exchange_value` decimal(15,2) DEFAULT 0.00,
  `refund_amount` decimal(15,2) DEFAULT 0.00,
  `receive_status` enum('pending','received') DEFAULT 'pending',
  `refund_status` enum('pending','refunded') DEFAULT 'pending',
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_return_items`
--

CREATE TABLE `order_return_items` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty_returned` int(11) DEFAULT 0,
  `return_price` decimal(15,2) DEFAULT 0.00,
  `line_total` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_sources`
--

CREATE TABLE `order_sources` (
  `id` int(11) NOT NULL,
  `source_name` varchar(100) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `source_type` varchar(50) DEFAULT 'Mặc định',
  `status` varchar(50) DEFAULT 'Đang sử dụng',
  `logo_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_sources`
--

INSERT INTO `order_sources` (`id`, `source_name`, `category_name`, `source_type`, `status`, `logo_url`, `sort_order`, `created_at`) VALUES
(1, 'Website', 'Website', 'Mặc định', 'Đang sử dụng', NULL, 1, '2026-06-17 16:08:22'),
(2, 'Admin', 'Nhân viên tự tạo (Nội bộ)', 'Mặc định', 'Đang sử dụng', NULL, 2, '2026-06-17 16:08:22'),
(3, 'Hotline', 'Bán tại cửa hàng, Hotline', 'Mặc định', 'Đang sử dụng', NULL, 3, '2026-06-17 16:08:22'),
(4, 'Facebook', 'Mạng xã hội, Livestream', 'Mặc định', 'Ngừng sử dụng', NULL, 4, '2026-06-17 16:08:22'),
(5, 'Shopee', 'Sàn TMĐT', 'Mặc định', 'Ngừng sử dụng', NULL, 5, '2026-06-17 16:08:22'),
(6, 'Tiktok Shop', 'Sàn TMĐT', 'Mặc định', 'Ngừng sử dụng', NULL, 6, '2026-06-17 16:08:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('manual','integrated') DEFAULT 'manual',
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0,
  `config_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `code`, `name`, `type`, `is_active`, `is_default`, `config_data`, `created_at`) VALUES
(1, 'cash', 'Tiền mặt', 'manual', 1, 1, NULL, '2026-06-16 15:48:28'),
(2, 'transfer', 'Chuyển khoản thủ công', 'manual', 1, 0, NULL, '2026-06-16 15:48:28'),
(3, 'vietqr', 'Thanh toán VietQR', 'integrated', 1, 0, '{\"fullname\":\"BUI VAN KHUONG\",\"id_card\":\"0203102394\",\"phone\":\"0397739126\",\"email\":\"buikhuong2005@gmail.com\",\"account_no\":\"0397739126\",\"bank_code\":\"MB\"}', '2026-06-16 15:48:28'),
(4, 'zalopay', 'Ví ZaloPay', 'integrated', 0, 0, NULL, '2026-06-16 15:48:28'),
(5, 'momo', 'Ví MoMo', 'integrated', 0, 0, NULL, '2026-06-16 15:48:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `price_lists`
--

CREATE TABLE `price_lists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `target_id` int(11) NOT NULL,
  `adjustment_type` varchar(50) NOT NULL,
  `adjustment_value` decimal(5,2) NOT NULL DEFAULT 0.00,
  `auto_add_new_product` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `price_lists`
--

INSERT INTO `price_lists` (`id`, `name`, `target_type`, `target_id`, `adjustment_type`, `adjustment_value`, `auto_add_new_product`, `status`, `created_at`) VALUES
(1, 'Bảng giá VIP & Sỉ', 'customer_group', 2, 'decrease', 15.00, 1, 'active', '2026-06-18 17:39:31'),
(2, 'Sale Tết Chi Nhánh Hà Nội', 'branch', 1, 'decrease', 10.00, 0, 'active', '2026-06-18 17:39:31'),
(3, 'Giá Ưu Đãi Kênh Shopee', 'channel', 1, 'decrease', 5.00, 1, 'active', '2026-06-18 17:39:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `price_list_items`
--

CREATE TABLE `price_list_items` (
  `price_list_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `custom_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `price_list_items`
--

INSERT INTO `price_list_items` (`price_list_id`, `product_id`, `custom_price`) VALUES
(1, 1, 17000000.00),
(1, 2, 12750000.00),
(2, 1, 18000000.00),
(2, 2, 13500000.00),
(3, 1, 19000000.00),
(3, 2, 14250000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `product_type` varchar(20) DEFAULT 'Thường',
  `conversion_qty` int(11) DEFAULT 1,
  `product_name` varchar(255) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `base_price` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sku` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `available` int(11) DEFAULT 0,
  `trading` int(11) DEFAULT 0,
  `incoming` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `compare_price` int(11) DEFAULT NULL,
  `cost_price` int(11) DEFAULT NULL,
  `apply_tax` tinyint(1) DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `dang_ve_kho` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `parent_id`, `product_type`, `conversion_qty`, `product_name`, `brand`, `base_price`, `created_at`, `sku`, `barcode`, `unit`, `stock`, `available`, `trading`, `incoming`, `description`, `image`, `compare_price`, `cost_price`, `apply_tax`, `category`, `tags`, `dang_ve_kho`) VALUES
(6, NULL, 'Thường', 1, 'iPhone 15 Pro Max 256GB', 'Apple', 29000000.00, '2026-05-25 17:27:33', 'IP15PM-256', NULL, 'Cái', 14, 15, 0, 0, NULL, NULL, 34990000, NULL, 0, 'Điện thoại di động', NULL, 0),
(7, NULL, 'Thường', 1, 'Samsung Galaxy S24 Ultra', 'Samsung', 31990000.00, '2026-05-25 17:27:33', 'S24U-512', NULL, 'Cái', 10, 10, 0, 0, NULL, NULL, 0, NULL, 0, 'Điện thoại di động', NULL, 5),
(8, NULL, 'Thường', 1, 'Apple Watch Series 9', 'Apple', 9500000.00, '2026-05-25 17:27:33', 'AW-S9-41', NULL, 'Chiếc', 6, 8, 0, 0, NULL, NULL, 10500000, NULL, 0, 'Đồng hồ thông minh', NULL, 0),
(9, NULL, 'Thường', 1, 'Cáp sạc nhanh 20W Type-C', 'Khác', 250000.00, '2026-05-25 17:27:33', 'CAP-20W-TC', NULL, 'Sợi', 49, 50, 0, 0, NULL, NULL, 0, NULL, 0, 'Phụ kiện chính hãng', NULL, 0),
(10, NULL, 'Thường', 1, 'Tai nghe AirPods Pro 2', 'Apple', 5800000.00, '2026-05-25 17:27:33', 'AP-PRO2', '', 'Hộp', 11, 11, 0, 0, '', NULL, 6200000, 0, 0, 'Phụ kiện chính hãng', '', 1),
(11, NULL, 'Thường', 1, 'Tai nghe AirPods Pro 2', 'Apple', 5800000.00, '2026-06-05 17:46:11', 'AP-PRO2', '', 'Hộp', 0, 0, 0, 0, '', '', 6200000, 0, 0, 'Phụ kiện chính hãng', '', 0),
(12, 11, 'Thường', 1, 'Tai nghe AirPods Pro 2 - Đỏ', 'Apple', 5800000.00, '2026-06-05 17:52:10', 'AP-PRO2-1', NULL, NULL, 0, 0, 0, 0, NULL, '', NULL, 0, 0, 'Phụ kiện chính hãng', NULL, 0),
(13, 11, 'Thường', 1, 'Tai nghe AirPods Pro 2 - Xanh', 'Apple', 5800000.00, '2026-06-05 17:52:10', 'AP-PRO2-2', NULL, NULL, 0, 0, 0, 0, NULL, '', NULL, 0, 0, 'Phụ kiện chính hãng', NULL, 0),
(14, 11, 'Thường', 1, 'Tai nghe AirPods Pro 2 - 256GB', 'Apple', 5800000.00, '2026-06-05 17:52:41', 'AP-PRO2-1', NULL, NULL, 0, 0, 0, 0, NULL, '', NULL, 0, 0, 'Phụ kiện chính hãng', NULL, 0),
(15, 11, 'Thường', 1, 'Tai nghe AirPods Pro 2 - 128GB', 'Apple', 5800000.00, '2026-06-05 17:52:41', 'AP-PRO2-2', NULL, NULL, 0, 0, 0, 0, NULL, '', NULL, 0, 0, 'Phụ kiện chính hãng', NULL, 0),
(16, 11, 'Thường', 1, 'Tai nghe AirPods Pro 2 - 64GB', 'Apple', 5800000.00, '2026-06-05 17:52:41', 'AP-PRO2-3', NULL, NULL, 0, 0, 0, 0, NULL, '', NULL, 0, 0, 'Phụ kiện chính hãng', NULL, 0),
(17, NULL, 'Thường', 1, 'Tai nghe AirPods Pro V3', 'Apple', 11120000.00, '2026-06-05 17:56:30', 'AP-PROv3 5', '', 'Cái', 0, 0, 0, 0, '', '', 11020000, 10000000, 1, 'Sản phẩm Apple (Tự động)', '', 0),
(19, NULL, 'Thường', 1, 'Tai nghe AirPods Pro V5', '', 220000.00, '2026-06-05 18:12:02', 'AP-PROv5', '', '', 20, 20, 0, 0, '', '', 0, 120000, 0, 'Sản phẩm Apple (Tự động)', '', 0),
(21, 19, 'Thường', 1, 'Tai nghe AirPods Pro V5 - xanh', '', 0.00, '2026-06-05 18:12:02', 'SKU-2', '', '', 0, 0, 0, 0, '', '', 0, 0, 0, 'Sản phẩm Apple (Tự động)', '', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_combo_details`
--

CREATE TABLE `product_combo_details` (
  `id` int(11) NOT NULL,
  `combo_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_inventory`
--

CREATE TABLE `product_inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `bin_location` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_inventory`
--

INSERT INTO `product_inventory` (`id`, `product_id`, `branch_id`, `stock_quantity`, `bin_location`, `updated_at`) VALUES
(1, 19, 1, 10, '', '2026-06-14 22:48:46'),
(4, 21, 1, 5, '', '2026-06-16 00:41:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_items`
--

CREATE TABLE `product_items` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `imei_code` varchar(20) DEFAULT NULL,
  `serial_number` varchar(50) NOT NULL,
  `status` enum('Trong kho','Đã bán','Đang bảo hành') DEFAULT 'Trong kho',
  `import_date` date DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `promo_name` varchar(255) NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL COMMENT 'Để trống nếu là KM tự động',
  `promo_type` enum('discount_order','discount_product','gift_by_order','gift_by_product','free_shipping') DEFAULT 'discount_order',
  `discount_type` enum('percent','amount') DEFAULT 'amount',
  `discount_value` decimal(15,2) DEFAULT 0.00,
  `min_order_value` decimal(15,2) DEFAULT 0.00 COMMENT 'Điều kiện áp dụng',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('Đang áp dụng','Chưa áp dụng','Ngừng áp dụng') DEFAULT 'Chưa áp dụng',
  `created_at` datetime DEFAULT current_timestamp(),
  `usage_limit` int(11) DEFAULT NULL COMMENT 'Số lượng áp dụng (NULL = Vô hạn)',
  `description` text DEFAULT NULL,
  `no_end_date` tinyint(1) DEFAULT 0 COMMENT '1 = Không có ngày kết thúc',
  `advanced_timing` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Lưu giờ, ngày, tháng áp dụng' CHECK (json_valid(`advanced_timing`)),
  `branch_scope` enum('all','specific') DEFAULT 'all',
  `specific_branches` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Mảng ID chi nhánh' CHECK (json_valid(`specific_branches`)),
  `customer_scope` enum('all','specific') DEFAULT 'all',
  `customer_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Điều kiện khách hàng' CHECK (json_valid(`customer_conditions`)),
  `gift_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Cấu hình chi tiết điều kiện mua và danh sách sản phẩm quà tặng' CHECK (json_valid(`gift_settings`)),
  `max_shipping_discount` decimal(15,2) DEFAULT NULL COMMENT 'Mức giảm phí ship tối đa',
  `used_count` int(11) DEFAULT 0 COMMENT 'Số lần mã/chương trình đã được sử dụng',
  `max_discount_amount` decimal(15,2) DEFAULT NULL COMMENT 'Mức giảm tối đa (nếu giảm theo %)',
  `min_product_qty` int(11) DEFAULT 0 COMMENT 'Số lượng sản phẩm tối thiểu',
  `once_per_customer` tinyint(1) DEFAULT 0 COMMENT '1 = Mỗi khách chỉ dùng 1 lần',
  `sales_channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kênh bán hàng: POS, Web, FB...' CHECK (json_valid(`sales_channels`)),
  `allowed_combinations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Cho phép kết hợp với loại KM nào' CHECK (json_valid(`allowed_combinations`)),
  `apply_once_per_order` tinyint(1) DEFAULT 1 COMMENT '1: Tính 1 lần/đơn, 0: Nhân lên theo số lượng SP',
  `product_apply_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Lưu thông tin: Áp dụng tất cả, hay SP cụ thể, hay Danh mục cụ thể' CHECK (json_valid(`product_apply_settings`)),
  `shipping_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Cấu hình Freeship: Tỉnh thành, Điều kiện phí ship tối đa' CHECK (json_valid(`shipping_settings`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `promo_name`, `promo_code`, `promo_type`, `discount_type`, `discount_value`, `min_order_value`, `start_date`, `end_date`, `status`, `created_at`, `usage_limit`, `description`, `no_end_date`, `advanced_timing`, `branch_scope`, `specific_branches`, `customer_scope`, `customer_conditions`, `gift_settings`, `max_shipping_discount`, `used_count`, `max_discount_amount`, `min_product_qty`, `once_per_customer`, `sales_channels`, `allowed_combinations`, `apply_once_per_order`, `product_apply_settings`, `shipping_settings`) VALUES
(4, 'Đơn hàng mới', 'KMBNJ0W1J8', 'discount_order', 'amount', 90000.00, 0.00, '2026-06-12 00:00:00', '2099-12-31 23:59:59', 'Đang áp dụng', '2026-06-12 22:31:00', NULL, '', 1, NULL, 'all', NULL, 'all', NULL, NULL, NULL, 0, NULL, 0, 0, '[\"pos\",\"web\"]', NULL, 0, NULL, NULL),
(7, 'Test4 ', 'KMVK0G0DP7', 'discount_order', 'amount', 0.00, 0.00, '2026-06-12 00:00:00', '2026-07-12 23:59:59', 'Ngừng áp dụng', '2026-06-12 23:17:11', 12, '', 0, NULL, 'all', NULL, 'all', NULL, NULL, NULL, 0, NULL, 0, 0, '[\"web\"]', NULL, 0, NULL, NULL),
(8, 'Test5', 'mienphi', 'discount_order', 'percent', 50.00, 0.00, '2026-06-12 00:00:00', '2026-06-13 23:59:59', 'Ngừng áp dụng', '2026-06-12 23:25:00', 23, '', 0, NULL, 'all', NULL, 'all', NULL, NULL, NULL, 0, NULL, 0, 1, '[\"pos\",\"web\"]', '[\"product\",\"order\",\"shipping\"]', 0, NULL, NULL),
(9, 'TEST5', 'GIAM20', 'discount_order', 'amount', 200000.00, 0.00, '2026-06-17 00:00:00', '2099-12-31 23:59:59', 'Đang áp dụng', '2026-06-17 00:57:29', NULL, '', 1, NULL, 'all', NULL, 'all', NULL, NULL, NULL, 0, NULL, 0, 0, '[\"pos\",\"web\"]', '[\"product\",\"order\",\"shipping\"]', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `employee` varchar(255) DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `payment_status` varchar(50) DEFAULT 'Chưa thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `supplier_name`, `branch`, `employee`, `expected_date`, `reference`, `status`, `total_amount`, `created_at`, `paid_amount`, `payment_status`) VALUES
(3, '', 'Cửa hàng chính', 'Admin', '2026-06-03', '', 'Đã hủy', 250000.00, '2026-06-03 00:08:33', 0.00, 'Chưa thanh toán'),
(5, 'Cửa hàng chính (Hà Nội)', 'Cửa hàng chính', 'Admin', '2026-06-19', '', 'Chờ nhập', 125000.00, '2026-06-03 00:28:21', 125000.00, 'Đã thanh toán'),
(6, '', 'Cửa hàng chính', 'Admin', '2026-06-03', '', 'Đã hủy', 3000000.00, '2026-06-03 00:36:48', 0.00, 'Chưa thanh toán');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `purchase_order_details`
--

INSERT INTO `purchase_order_details` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 10, 1, 900000.00),
(2, 2, 6, 10, 0.00),
(3, 3, 9, 5, 50000.00),
(7, 6, 6, 1, 3000000.00),
(10, 5, 7, 5, 25000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `employee` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Đã trả hàng',
  `created_at` datetime DEFAULT NULL,
  `refunded_amount` decimal(15,2) DEFAULT 0.00,
  `refund_status` varchar(50) DEFAULT 'Chưa hoàn tiền'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `purchase_return_details`
--

CREATE TABLE `purchase_return_details` (
  `id` int(11) NOT NULL,
  `return_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `receipt_code` varchar(50) DEFAULT NULL,
  `payment_method` enum('cash','bank') NOT NULL,
  `payer_group` enum('customer','supplier','employee','shipper','payment_partner','other') NOT NULL DEFAULT 'other',
  `payer_id` int(11) DEFAULT NULL,
  `payer_name` varchar(150) NOT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `receipt_reason` varchar(100) NOT NULL,
  `payment_strategy` varchar(20) DEFAULT 'oldest_first',
  `is_automatic` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `reference_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `receipts`
--

INSERT INTO `receipts` (`id`, `receipt_code`, `payment_method`, `payer_group`, `payer_id`, `payer_name`, `bank_account_id`, `customer_id`, `amount`, `receipt_reason`, `payment_strategy`, `is_automatic`, `description`, `branch_id`, `transaction_date`, `reference_code`, `created_at`) VALUES
(1, 'PT20260620001', 'bank', 'other', NULL, 'Ngân hàng Techcombank', NULL, NULL, 150000.00, 'Thu lãi tiền gửi ngân hàng', 'oldest_first', 0, 'Lãi không kỳ hạn tháng 5', 1, '2026-06-20 14:00:00', NULL, '2026-06-20 17:09:14'),
(2, 'PT20260620002', 'cash', 'employee', 1, 'Nguyễn Văn Nhân Viên', NULL, NULL, 500000.00, 'Nhân viên hoàn ứng', 'oldest_first', 0, 'Hoàn tiền thừa mua bưu phẩm', 1, '2026-06-20 15:30:00', NULL, '2026-06-20 17:09:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `saved_filters`
--

CREATE TABLE `saved_filters` (
  `id` int(11) NOT NULL,
  `module_name` varchar(50) NOT NULL COMMENT 'orders, shipments, products',
  `filter_name` varchar(100) NOT NULL,
  `filter_data` text NOT NULL COMMENT 'Lưu chuỗi JSON các điều kiện lọc',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `saved_filters`
--

INSERT INTO `saved_filters` (`id`, `module_name`, `filter_name`, `filter_data`, `created_at`) VALUES
(1, 'orders', 'Nợ COD / Chưa thanh toán', '{\"payment_status\":\"pending\"}', '2026-06-24 18:54:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `admin_email` varchar(100) DEFAULT NULL,
  `notify_email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Vietnam',
  `province` varchar(100) DEFAULT NULL,
  `tax_code` varchar(50) DEFAULT NULL,
  `inventory_mode` enum('simple','full') DEFAULT 'full',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `logo`, `store_name`, `business_name`, `phone`, `email`, `admin_email`, `notify_email`, `address`, `country`, `province`, `tax_code`, `inventory_mode`, `updated_at`) VALUES
(1, 'uploads/logo_store_1782318653.png', 'AAKC Store Điện Thoại', 'Hộ kinh doanh AAKC', '0987654321', 'contact@aakc.vn', '', '', 'Chi nhánh trung tâm vận hành hệ thống', 'Vietnam', 'Hà Nội', '0123456789', 'full', '2026-06-24 16:30:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipments`
--

CREATE TABLE `shipments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `tracking_code` varchar(100) NOT NULL COMMENT 'Mã vận đơn của Hãng',
  `partner_code` varchar(50) NOT NULL COMMENT 'ghn, vtp, ghtk, self',
  `status` varchar(50) DEFAULT 'pending' COMMENT 'pending, picking, delivering, delivered, returning, returned, cancelled',
  `cod_amount` decimal(15,2) DEFAULT 0.00,
  `shipping_fee` decimal(15,2) DEFAULT 0.00,
  `recon_status` enum('unreconciled','reconciled') DEFAULT 'unreconciled',
  `recon_id` int(11) DEFAULT NULL COMMENT 'ID của biên bản đối soát',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shipments`
--

INSERT INTO `shipments` (`id`, `order_id`, `branch_id`, `tracking_code`, `partner_code`, `status`, `cod_amount`, `shipping_fee`, `recon_status`, `recon_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'GHTK2606267833', 'ghtk', 'pending', 0.00, 0.00, 'unreconciled', NULL, '2026-06-25 18:44:53', '2026-06-25 18:44:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_partners`
--

CREATE TABLE `shipping_partners` (
  `id` int(11) NOT NULL,
  `partner_name` varchar(100) NOT NULL,
  `partner_code` varchar(50) NOT NULL,
  `base_fee` decimal(15,2) DEFAULT 0.00,
  `allow_cod` tinyint(1) DEFAULT 1,
  `max_retry` int(11) DEFAULT 3,
  `status` enum('Đang kết nối','Ngừng kết nối') DEFAULT 'Đang kết nối',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `shipping_partners`
--

INSERT INTO `shipping_partners` (`id`, `partner_name`, `partner_code`, `base_fee`, `allow_cod`, `max_retry`, `status`, `notes`, `created_at`) VALUES
(1, 'GHN Express', 'GHN', 30000.00, 1, 3, 'Đang kết nối', NULL, '2026-06-07 15:34:57'),
(2, 'J&T Express', 'JNT', 25000.00, 1, 3, 'Đang kết nối', NULL, '2026-06-07 15:34:57'),
(3, 'Ninja Van', 'NINJA', 28000.00, 1, 3, 'Đang kết nối', NULL, '2026-06-07 15:34:57'),
(4, 'SPX Express', 'SPX', 20000.00, 1, 3, 'Đang kết nối', NULL, '2026-06-07 15:34:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_rates`
--

CREATE TABLE `shipping_rates` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `zone_name` varchar(255) NOT NULL,
  `provinces` text DEFAULT NULL COMMENT 'Các tỉnh áp dụng',
  `rate_type` enum('custom','partner') DEFAULT 'custom',
  `rate_name` varchar(255) NOT NULL,
  `base_fee` decimal(15,2) DEFAULT 0.00,
  `partner_code` varchar(50) DEFAULT NULL COMMENT 'ghn, vtp, ghtk',
  `handling_fee_type` enum('percent','amount') DEFAULT 'amount',
  `handling_fee_value` decimal(15,2) DEFAULT 0.00,
  `estimated_time` varchar(100) DEFAULT NULL,
  `min_order_value` decimal(15,2) DEFAULT NULL,
  `max_order_value` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_reconciliations`
--

CREATE TABLE `shipping_reconciliations` (
  `id` int(11) NOT NULL,
  `recon_code` varchar(50) NOT NULL,
  `partner_code` varchar(50) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `total_cod` decimal(15,2) DEFAULT 0.00,
  `total_fee` decimal(15,2) DEFAULT 0.00,
  `total_received` decimal(15,2) DEFAULT 0.00 COMMENT 'Thực nhận = COD - Fee',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_settings`
--

CREATE TABLE `shipping_settings` (
  `id` int(11) NOT NULL,
  `weight_mode` enum('product','custom') DEFAULT 'product',
  `default_weight` int(11) DEFAULT 500 COMMENT 'Khối lượng Gram',
  `length` int(11) DEFAULT 10,
  `width` int(11) DEFAULT 10,
  `height` int(11) DEFAULT 10,
  `delivery_requirement` enum('no_check','check_no_try','check_and_try') DEFAULT 'check_no_try',
  `auto_sync_return` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shipping_settings`
--

INSERT INTO `shipping_settings` (`id`, `weight_mode`, `default_weight`, `length`, `width`, `height`, `delivery_requirement`, `auto_sync_return`, `updated_at`) VALUES
(1, 'product', 500, 10, 10, 10, 'check_no_try', 1, '2026-06-24 18:03:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `staffs`
--

CREATE TABLE `staffs` (
  `id` int(11) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'Nhân viên',
  `permissions` text DEFAULT NULL,
  `status` enum('Chờ xác nhận','Đang kích hoạt') DEFAULT 'Chờ xác nhận',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `staffs`
--

INSERT INTO `staffs` (`id`, `last_name`, `first_name`, `email`, `phone`, `password`, `role`, `permissions`, `status`, `created_at`) VALUES
(1, 'Bùi Văn', 'Khương (Admin)', 'admin@gmail.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NULL, 'Đang kích hoạt', '2026-06-07 14:23:14'),
(2, 'TU', 'tu nha vien', 'Tu@gmail.com', '0458237990', NULL, 'Nhân viên bán hàng', '[\"order_view\",\"order_create\",\"product_view\",\"customer_manage\"]', 'Chờ xác nhận', '2026-06-07 15:22:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_code` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_code` varchar(50) DEFAULT NULL,
  `debt` decimal(15,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_code`, `supplier_name`, `phone`, `email`, `address`, `tax_code`, `debt`, `created_at`) VALUES
(1, 'SUP0001', 'Công ty Cổ phần Thế Giới Di Động', '18001060', NULL, 'Hồ Chí Minh', NULL, 0.00, '2026-06-03 23:48:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('advanced_wave_picking', '{\"scan_shelf\":0, \"scan_item_pick\":0, \"scan_item_pack\":0, \"strict_wave\":0}'),
('allow_negative_sale_warning', '1'),
('auto_archive_order', '0'),
('auto_delete_transaction', '1'),
('order_workflow', 'basic'),
('reminder_email_hours', '1');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tax_settings`
--

CREATE TABLE `tax_settings` (
  `id` int(11) NOT NULL,
  `is_tax_enabled` tinyint(1) DEFAULT 0 COMMENT 'Quản lý thông tin thuế',
  `default_tax_sales` tinyint(1) DEFAULT 1 COMMENT 'Mặc định tính thuế bán hàng',
  `default_tax_purchases` tinyint(1) DEFAULT 0 COMMENT 'Mặc định tính thuế nhập hàng',
  `price_includes_tax` tinyint(1) DEFAULT 1 COMMENT 'Giá đã bao gồm thuế',
  `tax_on_shipping` tinyint(1) DEFAULT 0 COMMENT 'Ghi nhận thuế lên phí vận chuyển',
  `general_purchase_tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Thuế nhập hàng (%)',
  `general_sales_tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Thuế bán hàng (%)',
  `shipping_tax_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Thuế vận chuyển (%)',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tax_settings`
--

INSERT INTO `tax_settings` (`id`, `is_tax_enabled`, `default_tax_sales`, `default_tax_purchases`, `price_includes_tax`, `tax_on_shipping`, `general_purchase_tax_rate`, `general_sales_tax_rate`, `shipping_tax_rate`, `updated_at`) VALUES
(1, 1, 1, 0, 1, 0, 0.00, 1.50, 0.00, '2026-06-24 17:42:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transaction_reasons`
--

CREATE TABLE `transaction_reasons` (
  `id` int(11) NOT NULL,
  `type` enum('receipt','expense') NOT NULL,
  `reason_name` varchar(150) NOT NULL,
  `is_system` tinyint(1) DEFAULT 0 COMMENT '1: Mặc định không được xóa, 0: Tự tạo',
  `apply_to` varchar(50) DEFAULT 'all' COMMENT 'customer, supplier, employee, payment_partner, shipper, all',
  `is_reported` tinyint(1) DEFAULT 1 COMMENT '1: Ghi nhận báo cáo KQKD, 0: Không',
  `expense_category` varchar(100) DEFAULT NULL COMMENT 'Nhóm chi phí TT88',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `transaction_reasons`
--

INSERT INTO `transaction_reasons` (`id`, `type`, `reason_name`, `is_system`, `apply_to`, `is_reported`, `expense_category`, `created_at`) VALUES
(1, 'receipt', 'Thu tiền bán hàng', 1, 'customer', 1, NULL, '2026-06-20 19:27:52'),
(2, 'receipt', 'Thu tiền hoàn trả từ Nhà cung cấp', 1, 'supplier', 1, NULL, '2026-06-20 19:27:52'),
(3, 'receipt', 'Thu tiền đối tác thanh toán', 1, 'payment_partner', 1, NULL, '2026-06-20 19:27:52'),
(4, 'receipt', 'Thu tiền đối tác vận chuyển', 1, 'shipper', 1, NULL, '2026-06-20 19:27:52'),
(5, 'receipt', 'Thu khác', 1, 'all', 1, NULL, '2026-06-20 19:27:52'),
(6, 'expense', 'Trả tiền nhập hàng', 1, 'supplier', 1, 'Giá vốn hàng bán', '2026-06-20 19:27:52'),
(7, 'expense', 'Trả tiền NCC (không theo hóa đơn)', 1, 'supplier', 1, 'Giá vốn hàng bán', '2026-06-20 19:27:52'),
(8, 'expense', 'Chi hoàn tiền khách trả hàng', 1, 'customer', 1, 'Chi phí bán hàng', '2026-06-20 19:27:52'),
(9, 'expense', 'Trả nợ đối tác thanh toán', 1, 'payment_partner', 1, 'Chi phí bán hàng', '2026-06-20 19:27:52'),
(10, 'expense', 'Trả nợ đối tác vận chuyển', 1, 'shipper', 1, 'Chi phí bán hàng', '2026-06-20 19:27:52'),
(11, 'expense', 'Chi khác', 1, 'all', 1, 'Chi phí khác', '2026-06-20 19:27:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'Thu ngân',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'Bùi Văn Khương', 'Admin', '2026-05-18 16:17:36'),
(2, 'user1', 'e10adc3949ba59abbe56e057f20f883e', 'Nguyễn Vân Sơn ', 'Nhân viên kho', '2026-05-18 17:08:09');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_code` (`branch_code`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`);

--
-- Chỉ mục cho bảng `customer_debt_history`
--
ALTER TABLE `customer_debt_history`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `customer_group_members`
--
ALTER TABLE `customer_group_members`
  ADD PRIMARY KEY (`group_id`,`customer_id`);

--
-- Chỉ mục cho bảng `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_code` (`expense_code`);

--
-- Chỉ mục cho bảng `e_invoices`
--
ALTER TABLE `e_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `fund_transfers`
--
ALTER TABLE `fund_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transfer_code` (`transfer_code`);

--
-- Chỉ mục cho bảng `handover_items`
--
ALTER TABLE `handover_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `handover_id` (`handover_id`);

--
-- Chỉ mục cho bảng `handover_records`
--
ALTER TABLE `handover_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `record_code` (`record_code`);

--
-- Chỉ mục cho bảng `inventory_checks`
--
ALTER TABLE `inventory_checks`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `inventory_check_details`
--
ALTER TABLE `inventory_check_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `inventory_transfers`
--
ALTER TABLE `inventory_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `inventory_transfer_details`
--
ALTER TABLE `inventory_transfer_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Chỉ mục cho bảng `order_exchange_items`
--
ALTER TABLE `order_exchange_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_returns`
--
ALTER TABLE `order_returns`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_return_items`
--
ALTER TABLE `order_return_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_sources`
--
ALTER TABLE `order_sources`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `price_lists`
--
ALTER TABLE `price_lists`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `price_list_items`
--
ALTER TABLE `price_list_items`
  ADD PRIMARY KEY (`price_list_id`,`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `product_combo_details`
--
ALTER TABLE `product_combo_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `combo_id` (`combo_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_inventory`
--
ALTER TABLE `product_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_branch` (`product_id`,`branch_id`);

--
-- Chỉ mục cho bảng `product_items`
--
ALTER TABLE `product_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD UNIQUE KEY `imei_code` (`imei_code`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promo_code` (`promo_code`);

--
-- Chỉ mục cho bảng `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_code` (`receipt_code`);

--
-- Chỉ mục cho bảng `saved_filters`
--
ALTER TABLE `saved_filters`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_code` (`tracking_code`);

--
-- Chỉ mục cho bảng `shipping_partners`
--
ALTER TABLE `shipping_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partner_code` (`partner_code`);

--
-- Chỉ mục cho bảng `shipping_rates`
--
ALTER TABLE `shipping_rates`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `shipping_reconciliations`
--
ALTER TABLE `shipping_reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recon_code` (`recon_code`);

--
-- Chỉ mục cho bảng `shipping_settings`
--
ALTER TABLE `shipping_settings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_code` (`supplier_code`);

--
-- Chỉ mục cho bảng `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Chỉ mục cho bảng `tax_settings`
--
ALTER TABLE `tax_settings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `transaction_reasons`
--
ALTER TABLE `transaction_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT cho bảng `customer_debt_history`
--
ALTER TABLE `customer_debt_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `customer_groups`
--
ALTER TABLE `customer_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `e_invoices`
--
ALTER TABLE `e_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `fund_transfers`
--
ALTER TABLE `fund_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `handover_items`
--
ALTER TABLE `handover_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `handover_records`
--
ALTER TABLE `handover_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `inventory_checks`
--
ALTER TABLE `inventory_checks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `inventory_check_details`
--
ALTER TABLE `inventory_check_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `inventory_transfers`
--
ALTER TABLE `inventory_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `inventory_transfer_details`
--
ALTER TABLE `inventory_transfer_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `order_exchange_items`
--
ALTER TABLE `order_exchange_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `order_returns`
--
ALTER TABLE `order_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_return_items`
--
ALTER TABLE `order_return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_sources`
--
ALTER TABLE `order_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `price_lists`
--
ALTER TABLE `price_lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `product_combo_details`
--
ALTER TABLE `product_combo_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `product_inventory`
--
ALTER TABLE `product_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `product_items`
--
ALTER TABLE `product_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `saved_filters`
--
ALTER TABLE `saved_filters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `shipping_partners`
--
ALTER TABLE `shipping_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `shipping_rates`
--
ALTER TABLE `shipping_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `shipping_reconciliations`
--
ALTER TABLE `shipping_reconciliations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `shipping_settings`
--
ALTER TABLE `shipping_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `staffs`
--
ALTER TABLE `staffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tax_settings`
--
ALTER TABLE `tax_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `transaction_reasons`
--
ALTER TABLE `transaction_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `handover_items`
--
ALTER TABLE `handover_items`
  ADD CONSTRAINT `handover_items_ibfk_1` FOREIGN KEY (`handover_id`) REFERENCES `handover_records` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `product_combo_details`
--
ALTER TABLE `product_combo_details`
  ADD CONSTRAINT `product_combo_details_ibfk_1` FOREIGN KEY (`combo_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_combo_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `product_items`
--
ALTER TABLE `product_items`
  ADD CONSTRAINT `product_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
