<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
$groups = $groups ?? [];
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
        border-radius: 8px 8px 0 0;
    }

    .v3-form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        min-width: 250px;
    }

    .v3-table {
        width: 100%;
        border-collapse: collapse;
    }

    .v3-table th {
        background: #f4f6f8;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        text-align: left;
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

    .v3-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .v3-bg-manual {
        background: #e5f0ff;
        color: #0088ff;
    }

    .v3-bg-auto {
        background: #fff8ea;
        color: #8a6100;
        border: 1px solid #ffea8a;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Danh sách nhóm khách hàng</div>
    <a href="index.php?action=create_customer_group" class="btn-primary">+ Tạo nhóm khách hàng</a>
</div>

<div class="v3-card">
    <div class="v3-filter-bar">
        <input type="text" id="txt_search" class="v3-form-control" placeholder="Tìm kiếm tên nhóm khách hàng..." oninput="filterGroups()">
        <select id="sel_type" class="v3-form-control" style="min-width: 150px;" onchange="filterGroups()">
            <option value="">-- Tất cả phân loại --</option>
            <option value="manual">Nhóm thủ công</option>
            <option value="auto">Nhóm tự động</option>
        </select>
    </div>

    <table class="v3-table">
        <thead>
            <tr>
                <th style="width: 50%;">Tên nhóm khách hàng</th>
                <th style="width: 20%; text-align: center;">Số lượng khách hàng</th>
                <th style="width: 30%;">Phân loại nhóm</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $g): ?>
                <tr class="group-row" data-name="<?php echo strtolower($g['group_name']); ?>" data-type="<?php echo $g['group_type']; ?>" onclick="window.location.href='index.php?action=customer_group_detail&id=<?php echo $g['id']; ?>'">
                    <td style="font-weight: 600; color: #0088ff;"><?php echo htmlspecialchars($g['group_name']); ?></td>
                    <td style="text-align: center; font-weight: bold;">
                        <?php echo $g['group_type'] == 'manual' ? $g['manual_count'] : 'Auto'; ?> <span style="font-weight:normal; color:#8c98a4;">KH</span>
                    </td>
                    <td>
                        <span class="v3-badge <?php echo $g['group_type'] == 'manual' ? 'v3-bg-manual' : 'v3-bg-auto'; ?>">
                            <?php echo $g['group_type'] == 'manual' ? 'Thủ công' : '⚡ Tự động'; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function filterGroups() {
        let kw = document.getElementById('txt_search').value.toLowerCase();
        let type = document.getElementById('sel_type').value;
        document.querySelectorAll('.group-row').forEach(row => {
            let mName = !kw || row.getAttribute('data-name').includes(kw);
            let mType = !type || row.getAttribute('data-type') === type;
            row.style.display = (mName && mType) ? '' : 'none';
        });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
