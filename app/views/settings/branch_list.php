<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/**
 * @var array $branches
 * @var array $active_branches
 */
?>

<style>
    /* CÃ¡c CSS cÅ© giá»¯ nguyÃªn */
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

    /* CSS Äáº¶C BIá»†T CHO DANH SÃCH KÃ‰O THáº¢ */
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
    <div class="v3-title"><a href="index.php?action=settings_hub" style="text-decoration:none; color:#637381; margin-right:10px;">â†</a> Quáº£n lÃ½ chi nhÃ¡nh</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="openRoutingModal()"><i class="fa-solid fa-sort"></i> CÃ i Ä‘áº·t CN nháº­n Ä‘Æ¡n Online</button>
        <button class="btn-primary" onclick="openModal()">+ ThÃªm má»›i chi nhÃ¡nh</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-weight:500;">âœ… Cáº­p nháº­t chi nhÃ¡nh thÃ nh cÃ´ng!</div>
<?php endif; ?>
<?php if (isset($_GET['success_transfer'])): ?>
    <div style="background:#fff8ea; color:#8a6100; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #ffea8a; font-weight:500;">ðŸ“¦ ÄÃ£ xÃ³a dá»¯ liá»‡u kho vÃ  chuyá»ƒn giao giao dá»‹ch thÃ nh cÃ´ng!</div>
<?php endif; ?>

<div class="v3-card">
    <table class="v3-table">
        <thead>
            <tr>
                <th>TÃªn chi nhÃ¡nh</th>
                <th>MÃ£ CN</th>
                <th>ThÃ´ng tin liÃªn há»‡</th>
                <th>Quáº£n lÃ½ kho</th>
                <th>Tráº¡ng thÃ¡i</th>
                <th style="text-align:right;">Thao tÃ¡c</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($branches as $b): ?>
                <tr>
                    <td>
                        <a href="javascript:void(0)" style="color:#0088ff; font-weight:bold; text-decoration:none;" onclick='editBranch(<?php echo json_encode($b); ?>)'>
                            <?php echo htmlspecialchars($b['branch_name']); ?>
                        </a>
                        <?php if ($b['is_default'] == 1) echo '<span class="badge badge-default">Máº·c Ä‘á»‹nh</span>'; ?>
                    </td>
                    <td style="color:#637381; font-weight:500;"><?php echo $b['branch_code']; ?></td>
                    <td>
                        <div style="font-size:13px;">ðŸ“ž <?php echo htmlspecialchars($b['phone'] ?? '---'); ?></div>
                        <div style="font-size:12px; color:#637381; margin-top:3px;">ðŸ“ <?php echo htmlspecialchars($b['address_detail']); ?></div>
                    </td>
                    <td><?php echo $b['has_inventory'] ? 'âœ… CÃ³ kho' : '<span style="color:#919eab;">KhÃ´ng</span>'; ?></td>
                    <td>
                        <?php
                        if ($b['status'] == 'active') echo '<span class="badge badge-active">Äang hoáº¡t Ä‘á»™ng</span>';
                        elseif ($b['status'] == 'inactive') echo '<span class="badge badge-inactive">Ngá»«ng hoáº¡t Ä‘á»™ng</span>';
                        else echo '<span class="badge badge-expired">Háº¿t háº¡n</span>';
                        ?>
                    </td>
                    <td style="text-align:right;">
                        <?php if ($b['is_default'] == 0): ?>
                            <?php if ($b['status'] == 'active'): ?>
                                <a href="index.php?action=toggle_branch_status&id=<?php echo $b['id']; ?>&status=inactive" class="btn-outline" style="font-size:12px; color:#d82c0d; border-color:#fca5a5;">Ngá»«ng HÄ</a>
                            <?php else: ?>
                                <a href="index.php?action=toggle_branch_status&id=<?php echo $b['id']; ?>&status=active" class="btn-primary" style="font-size:12px; background:#108043;">KÃ­ch hoáº¡t</a>
                                <button class="btn-outline" style="font-size:12px; color:#8a6100; border-color:#ffea8a; margin-top:5px;" onclick="openTransferModal(<?php echo $b['id']; ?>, '<?php echo htmlspecialchars($b['branch_name'], ENT_QUOTES); ?>')">XÃ³a kho</button>
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
        <h3 id="modal_title" style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">ThÃªm má»›i chi nhÃ¡nh</h3>
        <form action="index.php?action=store_branch" method="POST">
            <input type="hidden" name="id" id="modal_id">

            <div class="grid-2">
                <div class="form-group">
                    <label>TÃªn chi nhÃ¡nh <span>*</span></label>
                    <input type="text" name="branch_name" id="branch_name" class="form-control" required placeholder="VD: Há»‡ thá»‘ng Cáº§u Giáº¥y">
                </div>
                <div class="form-group">
                    <label>MÃ£ chi nhÃ¡nh</label>
                    <input type="text" name="branch_code" id="branch_code" class="form-control" placeholder="Tá»± Ä‘á»™ng sinh náº¿u Ä‘á»ƒ trá»‘ng">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Sá»‘ Ä‘iá»‡n thoáº¡i</label>
                    <input type="text" name="phone" id="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
            </div>

            <div style="background:#f4f6f8; padding:15px; border-radius:6px; margin-bottom:15px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <label style="margin:0; font-weight:bold; color:#0088ff;">ðŸ“ ThÃ´ng tin Ä‘á»‹a chá»‰</label>
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
                        Äá»‹a chá»‰ má»›i (2 Cáº¥p)
                        <label class="switch">
                            <input type="checkbox" name="is_new_address_format" id="is_new_address_format" value="1" onchange="toggleAddressFormat()">
                            <span class="slider"></span>
                        </label>
                    </label>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Tá»‰nh/ThÃ nh phá»‘ <span>*</span></label>
                        <input type="text" name="province" id="province" class="form-control" required>
                    </div>
                    <div class="form-group" id="district_group">
                        <label>Quáº­n/Huyá»‡n <span>*</span></label>
                        <input type="text" name="district" id="district" class="form-control">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>PhÆ°á»ng/XÃ£ <span>*</span></label>
                        <input type="text" name="ward" id="ward" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Äá»‹a chá»‰ cá»¥ thá»ƒ <span>*</span></label>
                        <input type="text" name="address_detail" id="address_detail" class="form-control" required>
                    </div>
                </div>
            </div>

            <div style="border-top:1px dashed #c4cdd5; padding-top:15px; margin-bottom:15px;">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:10px;">
                    <label class="switch"><input type="checkbox" name="has_inventory" id="has_inventory" value="1" checked><span class="slider"></span></label>
                    <div><b>Thiáº¿t láº­p lÃ m chi nhÃ¡nh quáº£n lÃ½ kho</b><br><small style="color:#637381;">Tá»± Ä‘á»™ng lÆ°u kho toÃ n bá»™ sáº£n pháº©m táº¡i chi nhÃ¡nh nÃ y.</small></div>
                </label>

                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:10px;">
                    <input type="checkbox" name="is_default" id="is_default" value="1" style="width:16px;height:16px;">
                    <div><b>Chi nhÃ¡nh máº·c Ä‘á»‹nh</b><br><small style="color:#637381;">Ãp dá»¥ng máº·c Ä‘á»‹nh cho cÃ¡c giao dá»‹ch trÃªn toÃ n há»‡ thá»‘ng.</small></div>
                </label>

                <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" name="is_pickup_location" id="is_pickup_location" value="1" style="width:16px;height:16px;">
                    <div><b>Äá»‹a chá»‰ láº¥y hÃ ng</b><br><small style="color:#637381;">Sá»­ dá»¥ng lÃ m Ä‘iá»ƒm Ä‘á»ƒ Ä‘Æ¡n vá»‹ váº­n chuyá»ƒn (GHN, ViettelPost) Ä‘áº¿n láº¥y hÃ ng.</small></div>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('branch_modal').style.display='none'">Há»§y</button>
                <button type="submit" class="btn-primary">LÆ°u chi nhÃ¡nh</button>
            </div>
        </form>
    </div>
</div>

<div id="transfer_modal" class="modal">
    <div class="modal-content" style="width:450px;">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color:#d82c0d;">ðŸ“¦ XÃ³a dá»¯ liá»‡u kho</h3>
        <p style="font-size:14px; color:#212b36; line-height:1.5;">Chi nhÃ¡nh <b><span id="transfer_branch_name"></span></b> Ä‘ang cÃ³ cÃ¡c giao dá»‹ch xuáº¥t/nháº­p kho dá»Ÿ dang. Äá»ƒ xÃ³a dá»¯ liá»‡u kho an toÃ n, báº¡n báº¯t buá»™c pháº£i chá»n má»™t chi nhÃ¡nh khÃ¡c Ä‘á»ƒ tiáº¿p nháº­n cÃ¡c giao dá»‹ch nÃ y.</p>

        <form action="index.php?action=transfer_branch_data" method="POST">
            <input type="hidden" name="from_branch_id" id="from_branch_id">

            <div class="form-group" style="background:#fff8ea; padding:15px; border-radius:6px; border:1px solid #ffea8a; margin-top:15px;">
                <label style="color:#8a6100;">Chá»n chi nhÃ¡nh tiáº¿p nháº­n giao dá»‹ch: <span>*</span></label>
                <select name="to_branch_id" class="form-control" required style="border-color:#8a6100;">
                    <option value="">-- Chá»n chi nhÃ¡nh Ä‘ang hoáº¡t Ä‘á»™ng --</option>
                    <?php foreach ($active_branches as $ab): ?>
                        <option value="<?php echo $ab['id']; ?>"><?php echo htmlspecialchars($ab['branch_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-outline" onclick="document.getElementById('transfer_modal').style.display='none'">Há»§y</button>
                <button type="submit" class="btn-primary" style="background:#d82c0d;" onclick="return confirm('XÃ¡c nháº­n di dá»i dá»¯ liá»‡u sang chi nhÃ¡nh má»›i vÃ  xÃ³a cá» quáº£n lÃ½ kho cá»§a chi nhÃ¡nh nÃ y?')">XÃ¡c nháº­n & XÃ³a kho</button>
            </div>
        </form>
    </div>
</div>

<div id="routing_modal" class="modal">
    <div class="modal-content" style="width: 500px;">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color:#212b36;"><i class="fa-solid fa-sort"></i> CÃ i Ä‘áº·t thá»© tá»± nháº­n Ä‘Æ¡n Online</h3>
        <p style="font-size: 13px; color: #637381; margin-bottom: 10px;">
            Nháº¥n giá»¯ biá»ƒu tÆ°á»£ng <i class="fa-solid fa-grip-vertical"></i> vÃ  kÃ©o tháº£ lÃªn xuá»‘ng Ä‘á»ƒ sáº¯p xáº¿p Æ°u tiÃªn. Chi nhÃ¡nh xáº¿p trÃªn cÃ¹ng sáº½ Ä‘Æ°á»£c gá»i láº¥y hÃ ng Ä‘áº§u tiÃªn (náº¿u Ä‘á»§ tá»“n kho).<br>
            <i style="color:#8a6100;">*Chá»‰ hiá»ƒn thá»‹ chi nhÃ¡nh: Äang hoáº¡t Ä‘á»™ng + CÃ³ quáº£n lÃ½ kho + LÃ  Ä‘iá»ƒm láº¥y hÃ ng.</i>
        </p>

        <ul class="sortable-list" id="routing_list">
            <?php
            // Lá»c cÃ¡c chi nhÃ¡nh Ä‘á»§ Ä‘iá»u kiá»‡n nháº­n Ä‘Æ¡n Online
            $eligible_branches = array_filter($branches, function ($b) {
                return $b['status'] == 'active' && $b['has_inventory'] == 1 && $b['is_pickup_location'] == 1;
            });

            if (empty($eligible_branches)):
            ?>
                <li style="padding: 20px; text-align: center; color: #d82c0d;">KhÃ´ng cÃ³ chi nhÃ¡nh nÃ o Ä‘á»§ Ä‘iá»u kiá»‡n nháº­n Ä‘Æ¡n.</li>
            <?php else: ?>
                <?php foreach ($eligible_branches as $index => $eb): ?>
                    <li class="sortable-item" draggable="true" data-id="<?php echo $eb['id']; ?>">
                        <div style="display:flex; align-items:center;">
                            <i class="fa-solid fa-grip-vertical drag-handle"></i>
                            <div>
                                <b style="color: #0088ff;"><?php echo htmlspecialchars($eb['branch_name']); ?></b>
                                <div style="font-size: 12px; color: #637381;">Æ¯u tiÃªn sá»‘: <span class="priority-num"><?php echo $index + 1; ?></span></div>
                            </div>
                        </div>
                        <span style="font-size: 12px; color: #108043; background: #eafff0; padding: 2px 6px; border-radius: 4px;">Sáºµn sÃ ng</span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button type="button" class="btn-outline" onclick="document.getElementById('routing_modal').style.display='none'">Há»§y</button>
            <button type="button" class="btn-primary" onclick="saveRoutingPriority()">ðŸ’¾ LÆ°u thá»© tá»±</button>
        </div>
    </div>
</div>
<script>
    function openRoutingModal() {
        document.getElementById('routing_modal').style.display = 'flex';
    }

    // --- LOGIC KÃ‰O THáº¢ (DRAG & DROP) Báº°NG VANILLA JS ---
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

    // Cáº­p nháº­t láº¡i sá»‘ thá»© tá»± hiá»ƒn thá»‹ sau khi kÃ©o
    function updatePriorityNumbers() {
        const items = sortableList.querySelectorAll('.sortable-item');
        items.forEach((item, index) => {
            item.querySelector('.priority-num').innerText = index + 1;
        });
    }

    // AJAX LÆ°u xuá»‘ng Database
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
        document.getElementById('frm_store_settings')?.reset(); // Náº¿u cÃ³ form trÃ¹ng id thÃ¬ cáº©n tháº­n, á»Ÿ Ä‘Ã¢y form ko cÃ³ id
        document.getElementById('modal_id').value = '';
        document.getElementById('branch_code').readOnly = false;
        document.getElementById('modal_title').innerText = 'ThÃªm má»›i chi nhÃ¡nh';
        document.getElementById('branch_modal').style.display = 'flex';
        toggleAddressFormat();
    }

    function editBranch(b) {
        document.getElementById('modal_id').value = b.id;
        document.getElementById('branch_name').value = b.branch_name;
        document.getElementById('branch_code').value = b.branch_code;
        document.getElementById('branch_code').readOnly = true; // Há»‡ thá»‘ng ko cho sá»­a mÃ£

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

        document.getElementById('modal_title').innerText = 'âš™ï¸ Chá»‰nh sá»­a: ' + b.branch_name;
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

