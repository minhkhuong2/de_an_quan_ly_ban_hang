<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    /* CSS BỐ CỤC CHUẨN SAPO OMNIAI V3 */
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
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-primary:hover {
        background: #0070d2;
    }

    .v3-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dfe3e8;
        overflow: hidden;
    }

    /* Thanh tìm kiếm & Lọc */
    .filter-bar {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        display: flex;
        gap: 15px;
        background: #fafbfc;
    }

    .search-input {
        width: 300px;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        font-size: 14px;
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
        cursor: pointer;
    }

    .order-code {
        color: #0088ff;
        font-weight: 600;
        text-decoration: none;
    }

    .order-code:hover {
        text-decoration: underline;
    }

    /* Huy hiệu trạng thái (Badges) */
    .badge {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    /* Trạng thái thanh toán */
    .badge-paid {
        background: #eafff0;
        color: #108043;
        border: 1px solid #8ce09f;
    }

    .badge-pending {
        background: #fff8ea;
        color: #8a6100;
        border: 1px solid #ffea8a;
    }

    .badge-partial {
        background: #e5f0ff;
        color: #006fbb;
        border: 1px solid #b3d4ff;
    }

    /* Trạng thái Đơn hàng */
    .badge-completed {
        background: #f4f6f8;
        color: #637381;
        border: 1px solid #c4cdd5;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Danh sách đơn hàng</div>
    <a href="index.php?action=create_order" class="btn-primary">Tạo đơn hàng</a>
</div>

<div class="v3-card">
    <div class="filter-bar">
        <input type="text" class="search-input" placeholder="🔍 Tìm theo mã đơn hàng, tên, SĐT...">
    </div>

    <table class="v3-table">
        <thead>
            <tr>
                <th>Mã đơn hàng</th>
                <th>Ngày tạo</th>
                <th>Khách hàng</th>
                <th>Trạng thái thanh toán</th>
                <th>Trạng thái giao hàng</th>
                <th style="text-align: right;">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px 0; color: #8c98a4;">
                        Chưa có đơn hàng nào được tạo.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <tr onclick="window.location='index.php?action=view_order&id=<?php echo $o['id']; ?>'">

                        <td><a href="#" class="order-code"><?php echo htmlspecialchars($o['order_code']); ?></a></td>

                        <td style="color: #637381;"><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>

                        <td><?php echo !empty($o['customer_name']) ? htmlspecialchars($o['customer_name']) : '<span style="color:#8c98a4;">Khách lẻ</span>'; ?></td>

                        <td>
                            <?php
                            if ($o['payment_status'] == 'paid') {
                                echo '<span class="badge badge-paid">Đã thanh toán</span>';
                            } elseif ($o['payment_status'] == 'partial') {
                                echo '<span class="badge badge-partial">Thanh toán một phần</span>';
                            } else {
                                echo '<span class="badge badge-pending">Chưa thanh toán</span>';
                            }
                            ?>
                        </td>

                        <td>
                            <?php
                            if ($o['order_status'] == 'completed') {
                                echo '<span class="badge badge-completed">Đã hoàn thành</span>';
                            } else {
                                echo '<span class="badge badge-pending">Đang xử lý</span>';
                            }
                            ?>
                        </td>

                        <td style="text-align: right; font-weight: 600;">
                            <?php echo number_format($o['grand_total'], 0, '', '.'); ?> ₫
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
