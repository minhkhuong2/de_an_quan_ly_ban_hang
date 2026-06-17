<?php
/** 
 * @var array $active_sources
 * @var array $inactive_sources 
 * @var int $total_created
 */
require_once __DIR__ . '/../layout/header.php'; ?>

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
        margin-bottom: 20px;
    }

    /* Tabs style */
    .tabs-bar {
        display: flex;
        background: #fafbfc;
        border-bottom: 1px solid #dfe3e8;
        padding: 0 15px;
        border-radius: 8px 8px 0 0;
    }

    .tab-item {
        padding: 15px 20px;
        font-size: 14.5px;
        font-weight: 600;
        color: #637381;
        text-decoration: none;
        cursor: pointer;
        border-bottom: 3px solid transparent;
    }

    .tab-item.active {
        color: #0088ff;
        border-bottom-color: #0088ff;
    }

    /* Filter Bar */
    .filter-bar {
        padding: 15px 20px;
        display: flex;
        gap: 15px;
        border-bottom: 1px solid #dfe3e8;
        background: #fff;
        align-items: center;
    }

    .form-control {
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
    }

    /* Table Source */
    .table-sources {
        width: 100%;
        border-collapse: collapse;
    }

    .table-sources th {
        background: #f4f6f8;
        color: #637381;
        font-weight: 600;
        font-size: 13px;
        text-align: left;
        padding: 12px 20px;
        border-bottom: 1px solid #dfe3e8;
    }

    .table-sources td {
        padding: 14px 20px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
        vertical-align: middle;
    }

    .badge-type {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }

    .badge-default {
        background: #e5f0ff;
        color: #0088ff;
    }

    .badge-custom {
        background: #eafff0;
        color: #108043;
    }

    /* Context Menu Thao tác (...) */
    .action-menu-container {
        position: relative;
        display: inline-block;
    }

    .btn-trigger-menu {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #637381;
        padding: 5px 10px;
    }

    .action-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 30px;
        background: #fff;
        min-width: 150px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        z-index: 500;
    }

    .action-dropdown a {
        display: block;
        padding: 10px 15px;
        color: #212b36;
        text-decoration: none;
        font-size: 13.5px;
    }

    .action-dropdown a:hover {
        background: #f4f6f8;
        color: #0088ff;
    }

    /* Modal layout */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        width: 450px;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 5px;
        color: #212b36;
    }

    .form-group label span {
        color: red;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Quản lý nguồn đơn hàng <span style="font-size:14px; color:#637381; font-weight:normal; margin-left:10px;">(Số nguồn đã tạo: <?php echo $total_created; ?>/250)</span></div>
    <button style="background: #0088ff; color: #fff; border: none; padding: 10px 15px; border-radius: 4px; font-weight: 600; cursor: pointer;" onclick="openCreateModal()">+ Tạo nguồn đơn</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size:14px;">✅ Thao tác cập nhật danh sách nguồn đơn thành công!</div>
<?php endif; ?>

<div class="v3-card">
    <div class="tabs-bar">
        <div class="tab-item active" onclick="switchTab('active', this)">Đang sử dụng (<?php echo count($active_sources); ?>)</div>
        <div class="tab-item" onclick="switchTab('inactive', this)">Ngừng sử dụng (<?php echo count($inactive_sources); ?>)</div>
    </div>

    <div class="filter-bar">
        <input type="text" id="txt_search" class="form-control" style="width: 250px;" placeholder="Tìm kiếm tên nguồn đơn..." oninput="filterSources()">
        <select id="sel_category" class="form-control" style="width: 220px;" onchange="filterSources()">
            <option value="">-- Tất cả danh mục nguồn --</option>
            <option value="Website">Website</option>
            <option value="Sàn TMĐT">Sàn TMĐT</option>
            <option value="Mạng xã hội, Livestream">Mạng xã hội, Livestream</option>
            <option value="Bán tại cửa hàng, Hotline">Bán tại cửa hàng, Hotline</option>
            <option value="Nhân viên tự tạo (Nội bộ)">Nhân viên tự tạo (Nội bộ)</option>
            <option value="Đối tác & Cộng tác viên">Đối tác & Cộng tác viên</option>
        </select>
        <select id="sel_type" class="form-control" style="width: 150px;" onchange="filterSources()">
            <option value="">-- Tất cả loại --</option>
            <option value="Mặc định">Mặc định</option>
            <option value="Tủy chỉnh">Tùy chỉnh</option>
        </select>
    </div>

    <div id="panel_active">
        <table class="table-sources">
            <thead>
                <tr>
                    <th style="width: 5%;">Ưu tiên</th>
                    <th style="width: 35%;">Tên nguồn đơn hàng</th>
                    <th style="width: 30%;">Danh mục nguồn</th>
                    <th style="width: 15%;">Loại nguồn</th>
                    <th style="width: 15%; text-align: right; padding-right:25px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($active_sources)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color:#8c98a4; padding:30px;">Không có nguồn đơn nào đang hoạt động.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($active_sources as $s): ?>
                        <tr class="source-row" data-name="<?php echo strtolower($s['source_name']); ?>" data-cat="<?php echo $s['category_name']; ?>" data-type="<?php echo $s['source_type']; ?>">
                            <td style="color:#637381; font-weight:bold; cursor:move;">⠿ <?php echo $s['sort_order']; ?></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:30px; height:30px; background:#f4f6f8; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:16px;">
                                        <?php echo $s['source_type'] == 'Mặc định' ? '⚙️' : '👑'; ?>
                                    </div>
                                    <b style="color:#0088ff;"><?php echo htmlspecialchars($s['source_name']); ?></b>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($s['category_name']); ?></td>
                            <td>
                                <span class="badge-type <?php echo $s['source_type'] == 'Mặc định' ? 'badge-default' : 'badge-custom'; ?>">
                                    <?php echo $s['source_type']; ?>
                                </span>
                            </td>
                            <td style="text-align: right; padding-right:25px;">
                                <div class="action-menu-container">
                                    <button class="btn-trigger-menu" onclick="toggleDropdownMenu(event, <?php echo $s['id']; ?>)">•••</button>
                                    <div class="action-dropdown" id="drop_<?php echo $s['id']; ?>">
                                        <a href="index.php?action=toggle_source_status&id=<?php echo $s['id']; ?>&status=Đang sử dụng" onclick="return confirm('Ngừng sử dụng nguồn này?')">🚫 Ngừng sử dụng</a>

                                        <?php if ($s['source_type'] === 'Tùy chỉnh'): ?>
                                            <a href="javascript:void(0)" onclick='openEditModal(<?php echo json_encode($s); ?>)'>✏️ Chỉnh sửa</a>
                                            <a href="index.php?action=delete_order_source&id=<?php echo $s['id']; ?>" style="color:red;" onclick="return confirm('Xóa vĩnh viễn nguồn tùy chỉnh này khỏi hệ thống?')">🗑️ Xóa hoàn toàn</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="panel_inactive" style="display: none;">
        <table class="table-sources">
            <thead>
                <tr>
                    <th style="width: 40%;">Tên nguồn đơn hàng</th>
                    <th style="width: 30%;">Danh mục nguồn</th>
                    <th style="width: 15%;">Loại nguồn</th>
                    <th style="width: 15%; text-align: right; padding-right:25px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inactive_sources)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#8c98a4; padding:30px;">Không có nguồn đơn nào đang ngừng hoạt động.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inactive_sources as $s): ?>
                        <tr class="source-row" data-name="<?php echo strtolower($s['source_name']); ?>" data-cat="<?php echo $s['category_name']; ?>" data-type="<?php echo $s['source_type']; ?>">
                            <td><b style="color:#637381; text-decoration:line-through;"><?php echo htmlspecialchars($s['source_name']); ?></b></td>
                            <td><?php echo htmlspecialchars($s['category_name']); ?></td>
                            <td><span class="badge-type badge-default"><?php echo $s['source_type']; ?></span></td>
                            <td style="text-align: right; padding-right:25px;">
                                <a href="index.php?action=toggle_source_status&id=<?php echo $s['id']; ?>&status=Ngừng sử dụng" style="font-size:13px; color:#108043; font-weight:600; text-decoration:none;">🔄 Kích hoạt lại</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="source_modal">
    <div class="modal-content">
        <h3 id="modal_title" style="margin-bottom: 20px; color: #0088ff;">+ Tạo nguồn đơn hàng mới</h3>
        <form id="modal_form" action="index.php?action=store_order_source" method="POST">
            <input type="hidden" name="id" id="source_id">

            <div class="form-group">
                <label>Tên nguồn đơn hàng <span>*</span></label>
                <input type="text" name="source_name" id="source_name" class="form-control" style="width:100%;" required placeholder="VD: CTV Lan Anh, Nhóm Zalo Sỉ">
            </div>

            <div class="form-group">
                <label>Danh mục nguồn đơn <span>*</span></label>
                <select name="category_name" id="category_name" class="form-control" style="width:100%;" required>
                    <option value="Website">Website</option>
                    <option value="Sàn TMĐT">Sàn TMĐT</option>
                    <option value="Mạng xã hội, Livestream">Mạng xã hội, Livestream</option>
                    <option value="Bán tại cửa hàng, Hotline">Bán tại cửa hàng, Hotline</option>
                    <option value="Nhân viên tự tạo (Nội bộ)">Nhân viên tự tạo (Nội bộ)</option>
                    <option value="Đối tác & Cộng tác viên">Đối tác & Cộng tác viên</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('source_modal').style.display='none'">Hủy</button>
                <button type="submit" id="btn_submit_modal" style="background: #0088ff; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: 600;">Tạo nguồn</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Xử lý chuyển đổi qua lại giữa 2 Tab Đang sử dụng / Ngừng sử dụng
    function switchTab(type, element) {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        element.classList.add('active');

        document.getElementById('panel_active').style.display = (type === 'active') ? 'block' : 'none';
        document.getElementById('panel_inactive').style.display = (type === 'inactive') ? 'block' : 'none';
    }

    // Logic mở menu thả xuống khi bấm nút ...
    function toggleDropdownMenu(event, id) {
        event.stopPropagation();
        document.querySelectorAll('.action-dropdown').forEach(d => {
            if (d.id !== 'drop_' + id) d.style.display = 'none';
        });
        let currentDrop = document.getElementById('drop_' + id);
        currentDrop.style.display = (currentDrop.style.display === 'block') ? 'none' : 'block';
    }
    window.onclick = () => document.querySelectorAll('.action-dropdown').forEach(d => d.style.display = 'none');

    // Bộ lọc Client-side mượt mà theo đúng yêu cầu tìm kiếm (Mục 2)
    function filterSources() {
        let keyword = document.getElementById('txt_search').value.toLowerCase().trim();
        let category = document.getElementById('sel_category').value;
        let type = document.getElementById('sel_type').value;

        document.querySelectorAll('.source-row').forEach(row => {
            let matchesName = !keyword || row.getAttribute('data-name').includes(keyword);
            let matchesCat = !category || row.getAttribute('data-cat') === category;
            let matchesType = !type || row.getAttribute('data-type') === type;

            if (matchesName && matchesCat && matchesType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Mở Form Tạo mới
    function openCreateModal() {
        document.getElementById('modal_title').innerText = "+ Tạo nguồn đơn hàng mới";
        document.getElementById('modal_form').action = "index.php?action=store_order_source";
        document.getElementById('source_id').value = "";
        document.getElementById('source_name').value = "";
        document.getElementById('category_name').value = "Website";
        document.getElementById('btn_submit_modal').innerText = "Tạo nguồn";
        document.getElementById('source_modal').style.display = 'flex';
    }

    // Mở Form Chỉnh sửa nạp dữ liệu cũ (Mục 5.b)
    function openEditModal(srcObj) {
        document.getElementById('modal_title').innerText = "✏️ Chỉnh sửa nguồn đơn hàng";
        document.getElementById('modal_form').action = "index.php?action=update_order_source";
        document.getElementById('source_id').value = srcObj.id;
        document.getElementById('source_name').value = srcObj.source_name;
        document.getElementById('category_name').value = srcObj.category_name;
        document.getElementById('btn_submit_modal').innerText = "Lưu thay đổi";
        document.getElementById('source_modal').style.display = 'flex';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
