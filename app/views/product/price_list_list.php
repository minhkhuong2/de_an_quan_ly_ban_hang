<?php

/** @var array $price_lists */
require_once __DIR__ . '/../layout/header.php';
?>

<style>
    /* Đã thêm tiền tố v3- và !important để chống xung đột với Template Admin cũ */
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        width: 100%;
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
        margin-bottom: 20px;
        width: 100%;
        display: block;
        overflow: hidden;
    }

    /* Tabs & Filters */
    .v3-tabs-bar {
        display: flex;
        background: #fafbfc;
        border-bottom: 1px solid #dfe3e8;
        padding: 0 15px;
        border-radius: 8px 8px 0 0;
        width: 100%;
        flex-wrap: wrap;
    }

    .v3-tab-item {
        padding: 15px 20px;
        font-size: 14px;
        font-weight: 600;
        color: #637381;
        text-decoration: none;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }

    .v3-tab-item.active {
        color: #0088ff;
        border-bottom-color: #0088ff;
    }

    .v3-filter-bar {
        padding: 15px 20px;
        display: flex;
        gap: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
        align-items: center;
        flex-wrap: wrap;
        width: 100%;
    }

    .v3-form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
        min-width: 180px;
    }

    .v3-form-control:focus {
        border-color: #0088ff;
    }

    /* Nhãn lọc Hệ thống */
    .v3-filter-tag {
        background: #e5f0ff;
        color: #0088ff;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #b3d4ff;
    }

    .v3-filter-tag span {
        cursor: pointer;
        font-weight: bold;
        color: #8c98a4;
    }

    .v3-filter-tag span:hover {
        color: #d82c0d;
    }

    /* Table Listing (Đã bọc khung cuộn ngang chống bóp nghẹt) */
    .v3-table-wrapper {
        width: 100%;
        overflow-x: auto;
        display: block;
    }

    .v3-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
        table-layout: fixed;
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
        vertical-align: middle;
        word-wrap: break-word;
    }

    .v3-table tr:hover {
        background: #fafbfc;
    }

    .v3-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }

    .v3-status-active {
        background: #eafff0;
        color: #108043;
        border: 1px solid #8ce09f;
    }

    .v3-status-draft {
        background: #fff8ea;
        color: #8a6100;
        border: 1px solid #ffea8a;
    }

    .v3-btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        display: inline-block;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Danh sách bảng giá sản phẩm</div>
    <a href="index.php?action=add_price_list" class="v3-btn-primary">+ Tạo bảng giá mới</a>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] == 'items_saved'): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size:14px;">✅ Đã cấu hình và lưu danh sách sản phẩm áp dụng bảng giá thành công!</div>
<?php endif; ?>

<div class="v3-card">
    <div class="v3-tabs-bar">
        <div class="v3-tab-item active" onclick="filterByTab('all', this)">Tất cả bảng giá</div>
        <div class="v3-tab-item" onclick="filterByTab('customer_group', this)">Theo nhóm khách hàng</div>
        <div class="v3-tab-item" onclick="filterByTab('branch', this)">Theo chi nhánh</div>
        <div class="v3-tab-item" onclick="filterByTab('channel', this)">Theo kênh bán hàng</div>
    </div>

    <div class="v3-filter-bar">
        <input type="text" id="search_input" class="v3-form-control" style="width: 280px;" placeholder="Tìm theo mã hoặc tên bảng giá..." oninput="applyFilters()">

        <select id="filter_status" class="v3-form-control" style="width: 180px;" onchange="applyFilters()">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="active">Đang áp dụng</option>
            <option value="draft">Ngừng áp dụng / Nháp</option>
        </select>

        <div id="filter_tags_box" style="display: flex; gap: 10px; align-items: center; margin-left: 10px;"></div>
    </div>

    <div class="v3-table-wrapper">
        <table class="v3-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Mã bảng giá</th>
                    <th style="width: 30%;">Tên bảng giá</th>
                    <th style="width: 23%;">Loại bảng giá</th>
                    <th style="width: 20%;">Mức điều chỉnh giá</th>
                    <th style="width: 15%;">Trạng thái</th>
                </tr>
            </thead>
            <tbody id="price_list_tbody">
                <?php if (empty($price_lists)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color:#8c98a4; padding:30px;">Hệ thống chưa ghi nhận bảng giá nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($price_lists as $pl): ?>
                        <?php
                        $type_text = 'Theo kênh bán hàng';
                        if ($pl['target_type'] == 'customer_group') $type_text = 'Theo nhóm khách hàng';
                        if ($pl['target_type'] == 'branch') $type_text = 'Theo chi nhánh';

                        $adj_text = ($pl['adjustment_type'] == 'increase' ? 'Tăng: +' : 'Giảm: -') . $pl['adjustment_value'] . '%';
                        $adj_color = ($pl['adjustment_type'] == 'increase' ? '#d82c0d' : '#108043');
                        ?>
                        <tr class="pl-row" style="cursor: pointer;" onclick="window.location.href='index.php?action=price_list_detail&id=<?php echo $pl['id']; ?>'"
                            data-id="BG<?php echo str_pad($pl['id'], 3, '0', STR_PAD_LEFT); ?>"
                            data-name="<?php echo strtolower($pl['name']); ?>"
                            data-target-type="<?php echo $pl['target_type']; ?>"
                            data-status="<?php echo $pl['status']; ?>">

                            <td style="font-weight: 600; color: #637381;">BG<?php echo str_pad($pl['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td><b style="color:#0088ff;"><?php echo htmlspecialchars($pl['name']); ?></b></td>
                            <td><?php echo $type_text; ?></td>
                            <td style="font-weight: 600; color: <?php echo $adj_color; ?>;"><?php echo $adj_text; ?></td>
                            <td>
                                <span class="v3-badge <?php echo $pl['status'] == 'active' ? 'v3-status-active' : 'v3-status-draft'; ?>">
                                    <?php echo $pl['status'] == 'active' ? 'Đang áp dụng' : 'Ngừng áp dụng'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    let activeTabType = 'all';

    function filterByTab(type, element) {
        document.querySelectorAll('.v3-tab-item').forEach(t => t.classList.remove('active'));
        element.classList.add('active');
        activeTabType = type;
        applyFilters();
    }

    function applyFilters() {
        let kw = document.getElementById('search_input').value.toLowerCase().trim();
        let status = document.getElementById('filter_status').value;
        let tagsBox = document.getElementById('filter_tags_box');

        tagsBox.innerHTML = '';

        if (status) {
            let statusText = status === 'active' ? 'Đang áp dụng' : 'Ngừng áp dụng';
            tagsBox.innerHTML = `<div class="v3-filter-tag">Trạng thái: ${statusText} <span onclick="clearStatusFilter()">×</span></div>`;
        }

        document.querySelectorAll('.pl-row').forEach(row => {
            let matchesKw = !kw || row.getAttribute('data-id').toLowerCase().includes(kw) || row.getAttribute('data-name').includes(kw);
            let matchesTab = (activeTabType === 'all') || row.getAttribute('data-target-type') === activeTabType;
            let matchesStatus = !status || row.getAttribute('data-status') === status;

            if (matchesKw && matchesTab && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function clearStatusFilter() {
        document.getElementById('filter_status').value = '';
        applyFilters();
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
