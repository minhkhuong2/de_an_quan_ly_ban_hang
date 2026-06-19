<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
$customers = $customers ?? [];
?>

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
        border: 1px solid #dfe3e8;
    }

    .v3-filter-bar {
        padding: 15px 20px;
        display: flex;
        gap: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        align-items: center;
    }

    .v3-form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v3-table th {
        background: #f4f6f8;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        padding: 12px 20px;
        border-bottom: 1px solid #dfe3e8;
    }

    .v3-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .v3-table tbody tr:hover {
        background: #fafbfc;
        cursor: pointer;
    }

    .text-right {
        text-align: right !important;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="v3-header">
    <div class="v3-title">📊 Ứng dụng Quản lý công nợ khách hàng</div>
    <button class="btn-outline">📥 Xuất file báo cáo</button>
</div>

<div class="v3-card">
    <div class="v3-filter-bar">
        <span style="font-size:14px; color:#637381; font-weight:600;">Bộ lọc thời gian:</span>
        <input type="date" class="v3-form-control" value="2026-01-01">
        <span>➔</span>
        <input type="date" class="v3-form-control" value="2026-06-19">
        <button class="btn-outline" style="padding: 6px 12px;">Áp dụng</button>
    </div>

    <table class="v3-table">
        <thead>
            <tr>
                <th>Tên khách hàng</th>
                <th>Số điện thoại</th>
                <th class="text-right">Nợ đầu kỳ</th>
                <th class="text-right">Phát sinh trong kỳ</th>
                <th class="text-right">Nợ cuối kỳ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
                <?php
                // Mô phỏng số dư kế toán cho sinh động đồ án
                $mock_dau_ky = $c['debt'] * 0.4;
                $mock_phat_sinh = $c['debt'] * 0.6;
                ?>
                <tr onclick="window.location.href='index.php?action=debt_app_detail&id=<?php echo $c['id']; ?>'">
                    <td><b style="color:#0088ff;"><?php echo htmlspecialchars($c['last_name'] . ' ' . $c['first_name']); ?></b><br><small style="color:#637381;"><?php echo $c['customer_code']; ?></small></td>
                    <td><?php echo htmlspecialchars($c['phone'] ?: '---'); ?></td>
                    <td class="text-right" style="color:#637381;"><?php echo number_format($mock_dau_ky, 0, ',', '.'); ?> ₫</td>
                    <td class="text-right" style="color:#e67e22;"><?php echo $mock_phat_sinh > 0 ? '+' . number_format($mock_phat_sinh, 0, ',', '.') : '0'; ?> ₫</td>
                    <td class="text-right" style="font-weight: bold; color:<?php echo $c['debt'] >= 0 ? '#d82c0d' : '#108043'; ?>;">
                        <?php echo number_format($c['debt'], 0, ',', '.'); ?> ₫
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
