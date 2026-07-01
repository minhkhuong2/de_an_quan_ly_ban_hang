<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
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

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .table-list {
        width: 100%;
        border-collapse: collapse;
    }

    .table-list th {
        background: #f4f6f8;
        padding: 12px 15px;
        text-align: left;
        color: #637381;
        font-size: 13px;
    }

    .table-list td {
        padding: 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-pending { background: #fff8ea; color: #8a6100; }
    .status-completed { background: #eafff0; color: #108043; }

    .nav-tabs {
        display: flex;
        gap: 20px;
        border-bottom: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .nav-tab {
        padding: 10px 0;
        cursor: pointer;
        color: #637381;
        font-weight: 500;
        text-decoration: none;
    }

    .nav-tab.active {
        color: #0088ff;
        border-bottom: 2px solid #0088ff;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Danh sách phiếu trả hàng</div>
</div>

<div class="v3-card">
    <div class="nav-tabs">
        <a href="index.php?action=return_order_list&status=all" class="nav-tab <?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'active' : ''; ?>">Tất cả phiếu trả</a>
        <a href="index.php?action=return_order_list&status=pending_receive" class="nav-tab <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending_receive') ? 'active' : ''; ?>">Chưa nhận hàng</a>
        <a href="index.php?action=return_order_list&status=pending_refund" class="nav-tab <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending_refund') ? 'active' : ''; ?>">Chưa hoàn tiền</a>
        <a href="index.php?action=return_order_list&status=completed" class="nav-tab <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : ''; ?>">Đã hoàn thành</a>
    </div>

    <table class="table-list">
        <thead>
            <tr>
                <th>Mã phiếu trả</th>
                <th>Mã đơn hàng</th>
                <th>Khách hàng</th>
                <th>Trạng thái nhận</th>
                <th>Trạng thái hoàn tiền</th>
                <th>Giá trị hoàn/thu thêm</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($returns)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #8c98a4;">Chưa có phiếu trả hàng nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($returns as $ret): ?>
                    <tr>
                        <td><a href="index.php?action=return_detail&id=<?php echo $ret['id']; ?>" style="color: #0088ff; font-weight: bold; text-decoration: none;"><?php echo htmlspecialchars($ret['return_code']); ?></a></td>
                        <td><a href="index.php?action=view_order&id=<?php echo $ret['order_id']; ?>" style="color: #0088ff; text-decoration: none;"><?php echo htmlspecialchars($ret['order_code']); ?></a></td>
                        <td><?php echo htmlspecialchars($ret['last_name'] . ' ' . $ret['first_name']); ?> <br><small style="color: #637381;"><?php echo htmlspecialchars($ret['customer_phone']); ?></small></td>
                        <td>
                            <span class="status-badge <?php echo $ret['receive_status'] == 'received' ? 'status-completed' : 'status-pending'; ?>">
                                <?php echo $ret['receive_status'] == 'received' ? 'Đã nhận' : 'Chưa nhận'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $ret['refund_status'] == 'refunded' ? 'status-completed' : 'status-pending'; ?>">
                                <?php echo $ret['refund_status'] == 'refunded' ? 'Đã hoàn/thu' : 'Chờ xử lý'; ?>
                            </span>
                        </td>
                        <td style="font-weight: bold; color: <?php echo $ret['refund_amount'] > 0 ? '#d82c0d' : '#108043'; ?>">
                            <?php 
                                if ($ret['refund_amount'] > 0) {
                                    echo "Cần hoàn: " . number_format($ret['refund_amount'], 0, ',', '.') . 'đ';
                                } elseif ($ret['refund_amount'] < 0) {
                                    echo "Thu thêm: " . number_format(abs($ret['refund_amount']), 0, ',', '.') . 'đ';
                                } else {
                                    echo "0đ";
                                }
                            ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($ret['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
