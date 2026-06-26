<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $branches
 * @var array $active_branches
 */
?>

<style>
    /* Các CSS cũ giữ nguyên */
    .v3-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .v3-title {
        font-size: 24px;
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

    .v3-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .v3-table th {
        background: #f4f6f8;
        padding: 12px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 13px;
        color: #637381;
    }

    .v3-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #dfe3e8;
        font-size: 14px;
        color: #212b36;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-active {
        background: #eafff0;
        color: #108043;
        border: 1px solid #8ce09f;
    }

    .badge-inactive {
        background: #f4f6f8;
        color: #637381;
        border: 1px solid #c4cdd5;
    }

    .badge-expired {
        background: #ffe4e4;
        color: #d82c0d;
        border: 1px solid #ffb8b8;
    }

    .badge-default {
        background: #e5f0ff;
        color: #0088ff;
        border: 1px solid #b3d4ff;
        margin-left: 5px;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
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

    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #c4cdd5;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #0088ff;
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    .modal {
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
        width: 600px;
        padding: 25px;
        border-radius: 8px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 5px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    /* CSS ĐẶC BIỆT CHO DANH SÁCH KÉO THẢ */
    .sortable-list {
        list-style: none;
        padding: 0;
        margin: 15px 0;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        background: #fafbfc;
    }

    .sortable-item {
        padding: 12px 15px;
        border-bottom: 1px solid #dfe3e8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        cursor: grab;
        transition: background 0.2s;
    }

    .sortable-item:last-child {
        border-bottom: none;
    }

    .sortable-item:active {
        cursor: grabbing;
        background: #f4f9ff;
    }

    .sortable-item.dragging {
        opacity: 0.5;
        background: #e5f0ff;
    }

    .drag-handle {
        color: #919eab;
        font-size: 16px;
        margin-right: 15px;
        cursor: grab;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=settings_hub" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> Quản lý chi nhánh</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="openRoutingModal()"><i class="fa-solid fa-sort"></i> Cài đặt CN nhận đơn Online</button>
        <button class="btn-primary" onclick="openModal()">+ Thêm mới chi nhánh</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">✅ Cập nhật chi nhánh thành công!</div>
<?php endif; ?>
<?php if (isset($_GET['success_transfer'])): ?>
    <div style="background:#fff8ea; color:#8a6100; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #ffea8a; font-weight:500;">📦 Đã xóa dữ liệu kho và chuyển giao giao dịch thành công!</div>
<?php endif; ?>

<div class="v3-card">
    <table class="v3-table">
        <thead>
            <tr>
                <th>Tên chi nhánh</th>
                <th>Mã CN</th>
                <th>Thông tin liên hệ</th>
                <th>Quản lý kho</th>
                <th>Trạng thái</th>
                <th style="text-align:right;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($branches as $b): ?>
                <tr>
                    <td>
                        <a href="javascript:void(0)" style="color:#0088ff; font-weight:bold; text-decoration:none;" onclick='editBranch(<?php echo json_encode($b); ?>)'>
                            <?php echo htmlspecialchars($b['branch_name']); ?>
                        </a>
                        <?php if ($b['is_default'] == 1) echo '<span class="badge badge-default">Mặc định</span>'; ?>
                    </td>
                    <td style="color:#637381; font-weight:500;"><?php echo $b['branch_code']; ?></td>
                    <td>
                        <div style="font-size:13px;">📞 <?php echo htmlspecialchars($b['phone'] ?? '---'); ?></div>
                        <div style="font-size:12px; color:#637381; margin-top:3px;">📍 <?php echo htmlspecialchars($b['address_detail']); ?></div>
                    </td>
                    <td><?php echo $b['has_inventory'] ? '✅ Có kho' : '<span style="color:#919eab;">Không</span>'; ?></td>
                    <td>
                        <?php
                        if ($b['status'] == 'active') echo '<span class="badge badge-active">Đang hoạt động</span>';
                        elseif ($b['status'] == 'inactive') echo '<span class="badge badge-inactive">Ngừng hoạt động</span>';
                        else echo '<span class="badge badge-expired">Hết hạn</span>';
                        ?>
                    </td>
                    <td style="text-align:right;">
                        <?php if ($b['is_default'] == 0): ?>
                            <?php if ($b['status'] == 'active'): ?>
                                <a href="index.php?action=toggle_branch_status&id=<?php echo $b['id']; ?>&status=inactive" class="btn-outline" style="font-size:12px; color:#d82c0d; border-color:#fca5a5;">Ngừng HĐ</a>
                            <?php else: ?>
                                <a href="index.php?action=toggle_branch_status&id=<?php echo $b['id']; ?>&status=active" class="btn-primary" style="font-size:12px; background:#108043;">Kích hoạt</a>
                                <button class="btn-outline" style="font-size:12px; color:#8a6100; border-color:#ffea8a; margin-top:5px;" onclick="openTransferModal(<?php echo $b['id']; ?>, '<?php echo htmlspecialchars($b['branch_name'], ENT_QUOTES); ?>')">Xóa kho</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="branch_modal" class="modal">
    <div class="modal-content">
        <h3 id="modal_title" style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Thêm mới chi nhánh</h3>
        <form action="index.php?action=store_branch" method="POST">
            <input type="hidden" name="id" id="modal_id">

            <div class="grid-2">
                <div class="form-group">
                    <label>Tên chi nhánh <span>*</span></label>
                    <input type="text" name="branch_name" id="branch_name" class="form-control" required placeholder="VD: Sapo Cầu Giấy">
                </div>
                <div class="form-group">
                    <label>Mã chi nhánh</label>
                    <input type="text" name="branch_code" id="branch_code" class="form-control" placeholder="Tự động sinh nếu để trống">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
            </div>

            <div style="background:#f4f6f8; padding:15px; border-radius:6px; margin-bottom:15px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <label style="margin:0; font-weight:bold; color:#0088ff;">📍 Thông tin địa chỉ</label>
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
                        Địa chỉ mới (2 Cấp)
                        <label class="switch">
                            <input type="checkbox" name="is_new_address_format" id="is_new_address_format" value="1" onchange="toggleAddressFormat()">
                            <span class="slider"></span>
                        </label>
                    </label>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Tỉnh/Thành phố <span>*</span></label>
                        <input type="text" name="province" id="province" class="form-control" required>
                    </div>
                    <div class="form-group" id="district_group">
                        <label>Quận/Huyện <span>*</span></label>
                        <input type="text" name="district" id="district" class="form-control">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Phường/Xã <span>*</span></label>
                        <input type="text" name="ward" id="ward" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ cụ thể <span>*</span></label>
                        <input type="text" name="address_detail" id="address_detail" class="form-control" required>
                    </div>
                </div>
            </div>

            <div style="border-top:1px dashed #c4cdd5; padding-top:15px; margin-bottom:15px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:10px;">
                    <label class="switch"><input type="checkbox" name="has_inventory" id="has_inventory" value="1" checked><span class="slider"></span></label>
                    <div><b>Thiết lập làm chi nhánh quản lý kho</b><br><small style="color:#637381;">Tự động lưu kho toàn bộ sản phẩm tại chi nhánh này.</small></div>
                </label>

                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:10px;">
                    <input type="checkbox" name="is_default" id="is_default" value="1" style="width:16px;height:16px;">
                    <div><b>Chi nhánh mặc định</b><br><small style="color:#637381;">Áp dụng mặc định cho các giao dịch trên toàn hệ thống.</small></div>
                </label>

                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="is_pickup_location" id="is_pickup_location" value="1" style="width:16px;height:16px;">
                    <div><b>Địa chỉ lấy hàng</b><br><small style="color:#637381;">Sử dụng làm điểm để đơn vị vận chuyển (GHN, ViettelPost) đến lấy hàng.</small></div>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('branch_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary">Lưu chi nhánh</button>
            </div>
        </form>
    </div>
</div>

<div id="transfer_modal" class="modal">
    <div class="modal-content" style="width:450px;">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color:#d82c0d;">📦 Xóa dữ liệu kho</h3>
        <p style="font-size:14px; color:#212b36; line-height:1.5;">Chi nhánh <b><span id="transfer_branch_name"></span></b> đang có các giao dịch xuất/nhập kho dở dang. Để xóa dữ liệu kho an toàn, bạn bắt buộc phải chọn một chi nhánh khác để tiếp nhận các giao dịch này.</p>

        <form action="index.php?action=transfer_branch_data" method="POST">
            <input type="hidden" name="from_branch_id" id="from_branch_id">

            <div class="form-group" style="background:#fff8ea; padding:15px; border-radius:6px; border:1px solid #ffea8a; margin-top:15px;">
                <label style="color:#8a6100;">Chọn chi nhánh tiếp nhận giao dịch: <span>*</span></label>
                <select name="to_branch_id" class="form-control" required style="border-color:#8a6100;">
                    <option value="">-- Chọn chi nhánh đang hoạt động --</option>
                    <?php foreach ($active_branches as $ab): ?>
                        <option value="<?php echo $ab['id']; ?>"><?php echo htmlspecialchars($ab['branch_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('transfer_modal').style.display='none'">Hủy</button>
                <button type="submit" class="btn-primary" style="background:#d82c0d;" onclick="return confirm('Xác nhận di dời dữ liệu sang chi nhánh mới và xóa cờ quản lý kho của chi nhánh này?')">Xác nhận & Xóa kho</button>
            </div>
        </form>
    </div>
</div>

<div id="routing_modal" class="modal">
    <div class="modal-content" style="width: 500px;">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color:#212b36;"><i class="fa-solid fa-sort"></i> Cài đặt thứ tự nhận đơn Online</h3>
        <p style="font-size: 13px; color: #637381; margin-bottom: 10px;">
            Nhấn giữ biểu tượng <i class="fa-solid fa-grip-vertical"></i> và kéo thả lên xuống để sắp xếp ưu tiên. Chi nhánh xếp trên cùng sẽ được gọi lấy hàng đầu tiên (nếu đủ tồn kho).<br>
            <i style="color:#8a6100;">*Chỉ hiển thị chi nhánh: Đang hoạt động + Có quản lý kho + Là điểm lấy hàng.</i>
        </p>

        <ul class="sortable-list" id="routing_list">
            <?php
            // Lọc các chi nhánh đủ điều kiện nhận đơn Online
            $eligible_branches = array_filter($branches, function ($b) {
                return $b['status'] == 'active' && $b['has_inventory'] == 1 && $b['is_pickup_location'] == 1;
            });

            if (empty($eligible_branches)):
            ?>
                <li style="padding: 20px; text-align: center; color: #d82c0d;">Không có chi nhánh nào đủ điều kiện nhận đơn.</li>
            <?php else: ?>
                <?php foreach ($eligible_branches as $index => $eb): ?>
                    <li class="sortable-item" draggable="true" data-id="<?php echo $eb['id']; ?>">
                        <div style="display:flex; align-items:center;">
                            <i class="fa-solid fa-grip-vertical drag-handle"></i>
                            <div>
                                <b style="color: #0088ff;"><?php echo htmlspecialchars($eb['branch_name']); ?></b>
                                <div style="font-size: 12px; color: #637381;">Ưu tiên số: <span class="priority-num"><?php echo $index + 1; ?></span></div>
                            </div>
                        </div>
                        <span style="font-size: 12px; color: #108043; background: #eafff0; padding: 2px 6px; border-radius: 4px;">Sẵn sàng</span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button type="button" class="btn-outline" onclick="document.getElementById('routing_modal').style.display='none'">Hủy</button>
            <button type="button" class="btn-primary" onclick="saveRoutingPriority()">💾 Lưu thứ tự</button>
        </div>
    </div>
</div>
<script>
    function openRoutingModal() {
        document.getElementById('routing_modal').style.display = 'flex';
    }

    // --- LOGIC KÉO THẢ (DRAG & DROP) BẰNG VANILLA JS ---
    const sortableList = document.getElementById("routing_list");
    let draggedItem = null;

    sortableList.addEventListener("dragstart", (e) => {
        draggedItem = e.target.closest('.sortable-item');
        setTimeout(() => draggedItem.classList.add("dragging"), 0);
    });

    sortableList.addEventListener("dragend", (e) => {
        draggedItem.classList.remove("dragging");
        updatePriorityNumbers();
    });

    sortableList.addEventListener("dragover", (e) => {
        e.preventDefault();
        const afterElement = getDragAfterElement(sortableList, e.clientY);
        const currentItem = document.querySelector(".dragging");
        if (afterElement == null) {
            sortableList.appendChild(currentItem);
        } else {
            sortableList.insertBefore(currentItem, afterElement);
        }
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll(".sortable-item:not(.dragging)")];
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return {
                    offset: offset,
                    element: child
                };
            } else {
                return closest;
            }
        }, {
            offset: Number.NEGATIVE_INFINITY
        }).element;
    }

    // Cập nhật lại số thứ tự hiển thị sau khi kéo
    function updatePriorityNumbers() {
        const items = sortableList.querySelectorAll('.sortable-item');
        items.forEach((item, index) => {
            item.querySelector('.priority-num').innerText = index + 1;
        });
    }

    // AJAX Lưu xuống Database
    function saveRoutingPriority() {
        const items = sortableList.querySelectorAll('.sortable-item');
        const priorities = Array.from(items).map(item => item.getAttribute('data-id'));

        fetch('index.php?action=update_branch_priority', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    priorities: priorities
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                window.location.reload();
            });
    }

    function toggleAddressFormat() {
        let isNew = document.getElementById('is_new_address_format').checked;
        let districtGroup = document.getElementById('district_group');
        let districtInput = document.getElementById('district');

        if (isNew) {
            districtGroup.style.display = 'none';
            districtInput.required = false;
        } else {
            districtGroup.style.display = 'block';
            districtInput.required = true;
        }
    }

    function openModal() {
        document.getElementById('frm_store_settings')?.reset(); // Nếu có form trùng id thì cẩn thận, ở đây form ko có id
        document.getElementById('modal_id').value = '';
        document.getElementById('branch_code').readOnly = false;
        document.getElementById('modal_title').innerText = 'Thêm mới chi nhánh';
        document.getElementById('branch_modal').style.display = 'flex';
        toggleAddressFormat();
    }

    function editBranch(b) {
        document.getElementById('modal_id').value = b.id;
        document.getElementById('branch_name').value = b.branch_name;
        document.getElementById('branch_code').value = b.branch_code;
        document.getElementById('branch_code').readOnly = true; // Sapo ko cho sửa mã

        document.getElementById('phone').value = b.phone;
        document.getElementById('email').value = b.email;
        document.getElementById('province').value = b.province;
        document.getElementById('district').value = b.district;
        document.getElementById('ward').value = b.ward;
        document.getElementById('address_detail').value = b.address_detail;

        document.getElementById('is_new_address_format').checked = (b.is_new_address_format == 1);
        document.getElementById('has_inventory').checked = (b.has_inventory == 1);
        document.getElementById('is_default').checked = (b.is_default == 1);
        document.getElementById('is_pickup_location').checked = (b.is_pickup_location == 1);

        document.getElementById('modal_title').innerText = '⚙️ Chỉnh sửa: ' + b.branch_name;
        document.getElementById('branch_modal').style.display = 'flex';
        toggleAddressFormat();
    }

    function openTransferModal(id, name) {
        document.getElementById('from_branch_id').value = id;
        document.getElementById('transfer_branch_name').innerText = name;
        document.getElementById('transfer_modal').style.display = 'flex';
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
