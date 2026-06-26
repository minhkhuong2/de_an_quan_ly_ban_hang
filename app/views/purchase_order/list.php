<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php /** @var array $orders */ ?>

<style>
    /* CSS CHUẨN AKC V3 */
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 22px;
        font-weight: bold;
        color: #212b36;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        padding: 10px 16px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        padding: 10px 16px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
        overflow: hidden;
    }

    /* Tabs Trạng thái */
    .po-tabs {
        display: flex;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        padding: 0 20px;
        overflow-x: auto;
    }

    .po-tab {
        padding: 15px 20px;
        color: #637381;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
    }

    .po-tab:hover {
        color: #0088ff;
    }

    .po-tab.active {
        color: #0088ff;
        border-bottom-color: #0088ff;
        font-weight: 600;
    }

    /* Thanh Filter & Search */
    .filter-bar {
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dfe3e8;
    }

    .search-input {
        width: 350px;
        padding: 8px 12px 8px 30px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
        outline: none;
    }

    .search-input:focus {
        border-color: #0088ff;
    }

    /* Bảng dữ liệu */
    .v3-table {
        width: 100%;
        border-collapse: collapse;
    }

    .v3-table th {
        background: #f4f6f8;
        text-align: left;
        padding: 12px 20px;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 1px solid #dfe3e8;
    }

    .v3-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
        vertical-align: middle;
    }

    .v3-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .text-blue {
        color: #0088ff;
        font-weight: 600;
        text-decoration: none;
    }

    .text-blue:hover {
        text-decoration: underline;
    }

    /* Badges */
    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-draft {
        background: #f4f6f8;
        color: #637381;
    }

    .badge-pending {
        background: #e6f7ff;
        color: #0088ff;
    }

    .badge-success {
        background: #eafff0;
        color: #108043;
    }

    .badge-danger {
        background: #fff1f0;
        color: #cf1322;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Danh sách đơn đặt hàng nhập</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="alert('Tính năng Xuất file Excel sẽ được lập trình bằng thư viện PHPExcel ở giai đoạn sau.')">📥 Xuất file</button>
        <a href="index.php?action=add_purchase" class="btn-primary">+ Tạo đơn đặt hàng</a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?><div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Thao tác đơn hàng thành công!</div><?php endif; ?>
<?php if (isset($_GET['success_delete'])): ?><div style="background:#fff1f0; color:#cf1322; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #ffa39e; font-weight:500;">🗑️ Đã xóa vĩnh viễn đơn đặt hàng và hoàn trả lại số lượng kho!</div><?php endif; ?>

<div class="v3-card">

    <?php $current_status = $_GET['status'] ?? ''; ?>
    <div class="po-tabs">
        <a href="index.php?action=purchase_list" class="po-tab <?php echo $current_status == '' ? 'active' : ''; ?>">Tất cả đơn</a>
        <a href="index.php?action=purchase_list&status=Đơn nháp" class="po-tab <?php echo $current_status == 'Đơn nháp' ? 'active' : ''; ?>">Đơn nháp</a>
        <a href="index.php?action=purchase_list&status=Chờ nhập" class="po-tab <?php echo $current_status == 'Chờ nhập' ? 'active' : ''; ?>">Chờ nhập</a>
        <a href="index.php?action=purchase_list&status=Nhập toàn bộ" class="po-tab <?php echo $current_status == 'Nhập toàn bộ' ? 'active' : ''; ?>">Nhập toàn bộ</a>
        <a href="index.php?action=purchase_list&status=Đã hủy" class="po-tab <?php echo $current_status == 'Đã hủy' ? 'active' : ''; ?>">Đã hủy</a>
    </div>

    <div class="filter-bar">
        <form method="GET" action="index.php" style="position: relative;">
            <input type="hidden" name="action" value="purchase_list">
            <?php if ($current_status): ?><input type="hidden" name="status" value="<?php echo htmlspecialchars($current_status); ?>"><?php endif; ?>

            <span style="position: absolute; left: 10px; top: 8px; color: #8c98a4;">🔍</span>
            <input type="text" name="keyword" class="search-input" placeholder="Tìm theo mã PON, nhà cung cấp..." value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">

            <?php if (isset($_GET['keyword']) && $_GET['keyword'] !== ''): ?>
                <a href="index.php?action=purchase_list<?php echo $current_status ? '&status=' . $current_status : ''; ?>" style="margin-left: 10px; color: #d82c0d; text-decoration: none; font-size: 13px;">Xóa lọc</a>
            <?php endif; ?>
        </form>

        <button class="btn-outline" style="padding: 6px 12px; font-size: 13px;">⚙️ Tùy chỉnh cột</button>
    </div>

    <table class="v3-table">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Ngày hẹn giao</th>
                <th>Nhà cung cấp</th>
                <th>Trạng thái</th>
                <th style="text-align: right;">Tổng tiền</th>
                <th style="text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 50px; margin-bottom: 15px;">📋</div>
                        <h3 style="font-size: 18px; color: #212b36; font-weight: bold;">Không tìm thấy đơn đặt hàng nào</h3>
                        <p style="color: #637381; margin-bottom: 20px;">Đổi từ khóa tìm kiếm hoặc tạo đơn mới.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <a href="index.php?action=view_purchase&id=<?php echo $order['id']; ?>" class="text-blue">#PON<?php echo $order['id']; ?></a>
                        </td>

                        <td style="color: #637381;"><?php echo date('d/m/Y', strtotime($order['expected_date'])); ?></td>

                        <td style="font-weight: 500; color: #212b36;"><?php echo htmlspecialchars($order['supplier_name']); ?></td>

                        <td>
                            <?php if ($order['status'] == 'Chờ nhập'): ?>
                                <span class="badge badge-pending">Chờ nhập</span>
                            <?php elseif ($order['status'] == 'Đã hủy'): ?>
                                <span class="badge badge-danger">Đã hủy</span>
                            <?php elseif ($order['status'] == 'Nhập toàn bộ'): ?>
                                <span class="badge badge-success">Nhập toàn bộ</span>
                            <?php else: ?>
                                <span class="badge badge-draft">Đơn nháp</span>
                            <?php endif; ?>
                        </td>

                        <td style="text-align: right; font-weight: bold; color: #212b36;">
                            <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫
                        </td>

                        <td style="text-align: center;">
                            <?php if (in_array($order['status'], ['Đơn nháp', 'Chờ nhập'])): ?>
                                <a href="index.php?action=edit_purchase&id=<?php echo $order['id']; ?>" style="text-decoration: none; font-size: 16px; margin-right: 12px;" title="Sửa đơn">✏️</a>
                                <a href="index.php?action=delete_purchase&id=<?php echo $order['id']; ?>" onclick="return confirm('⚠️ CẢNH BÁO: Bạn có chắc chắn muốn xóa đơn đặt hàng này không?');" style="text-decoration: none; font-size: 16px;" title="Xóa đơn">🗑️</a>
                            <?php else: ?>
                                <span style="color: #c4cdd5; font-size: 12px;">Chỉ xem</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
