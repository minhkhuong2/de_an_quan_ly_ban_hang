<?php
require_once __DIR__ . '/../layout/header.php';
?>

<div class="header-container" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:10px;">
        <a href="index.php?action=handover_list" style="color:#637381; text-decoration:none; font-size:20px;">←</a>
        <h2 style="margin:0;">Tạo biên bản bàn giao</h2>
    </div>
</div>

<div style="display:flex; gap:20px;">
    <div style="flex:2;">
        <div class="v3-card" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #dfe3e8; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:20px;">
            <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Thêm kiện hàng</h3>
            <div style="display:flex; gap:10px; margin-bottom:15px;">
                <input type="text" id="package_input" class="form-control" placeholder="Quét mã vạch hoặc nhập mã đơn/mã kiện..." style="flex:1; padding:10px;">
                <button class="btn-outline" onclick="addPackage()">Thêm vào danh sách</button>
                <button class="btn-outline" onclick="alert('Tính năng nhập thủ công hàng loạt!')">Nhập thủ công</button>
            </div>
            
            <table class="table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Mã đơn hàng</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Mã kiện hàng</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid #dfe3e8;">Trạng thái</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody id="package_list_tbody">
                    <!-- Javascript will append items here -->
                    <tr id="empty_row"><td colspan="4" style="text-align:center; padding:20px; color:#637381;">Chưa có kiện hàng nào.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="flex:1;">
        <div class="v3-card" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #dfe3e8; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Thông tin chung</h3>
            
            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:500;">Chi nhánh bàn giao</label>
                <select id="branch_id" class="form-control" style="width:100%; padding:8px;">
                    <option value="">-- Chọn chi nhánh --</option>
                    <?php foreach($branches as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:500;">Đối tác vận chuyển</label>
                <select id="partner_id" class="form-control" style="width:100%; padding:8px;">
                    <option value="">-- Chọn đối tác --</option>
                    <?php foreach($partners as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="border-top:1px dashed #dfe3e8; margin-top:20px; padding-top:20px; text-align:right;">
                <button class="btn-primary" onclick="saveHandover()" style="width:100%; padding:10px; font-size:16px;">Tạo biên bản</button>
            </div>
        </div>
    </div>
</div>

<script>
    let addedPackages = [];

    function addPackage() {
        let code = document.getElementById('package_input').value.trim();
        if(!code) return;

        // Giả lập tìm thấy kiện hàng dựa trên mã
        let pkg = {
            order_id: Math.floor(Math.random() * 100) + 1,
            order_code: "SON-" + code,
            package_code: "PKG-" + code,
            waybill_code: "WB-" + code,
            status: "Chờ lấy hàng"
        };

        addedPackages.push(pkg);
        renderPackages();
        document.getElementById('package_input').value = '';
    }

    function removePackage(index) {
        addedPackages.splice(index, 1);
        renderPackages();
    }

    function renderPackages() {
        let tbody = document.getElementById('package_list_tbody');
        tbody.innerHTML = '';
        if(addedPackages.length === 0) {
            tbody.innerHTML = '<tr id="empty_row"><td colspan="4" style="text-align:center; padding:20px; color:#637381;">Chưa có kiện hàng nào.</td></tr>';
            return;
        }

        addedPackages.forEach((p, idx) => {
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding:10px; border-bottom:1px solid #dfe3e8; font-weight:500;">${p.order_code}</td>
                <td style="padding:10px; border-bottom:1px solid #dfe3e8;">${p.package_code}</td>
                <td style="padding:10px; border-bottom:1px solid #dfe3e8;">${p.status}</td>
                <td style="padding:10px; border-bottom:1px solid #dfe3e8; text-align:right;">
                    <a href="javascript:void(0)" onclick="removePackage(${idx})" style="color:#d82c0d;"><i class="fa-solid fa-trash"></i></a>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function saveHandover() {
        let branch_id = document.getElementById('branch_id').value;
        let partner_id = document.getElementById('partner_id').value;

        if(!branch_id || !partner_id) {
            alert("Vui lòng chọn chi nhánh và đối tác vận chuyển.");
            return;
        }

        if(addedPackages.length === 0) {
            alert("Vui lòng thêm ít nhất 1 kiện hàng vào biên bản.");
            return;
        }

        fetch('index.php?action=store_handover', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                branch_id: branch_id,
                partner_id: partner_id,
                packages: addedPackages
            })
        }).then(res => res.json()).then(res => {
            if(res.status == 'success') {
                alert("Tạo biên bản thành công!");
                window.location.href = 'index.php?action=handover_detail&id=' + res.id;
            } else {
                alert("Lỗi: " + res.msg);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
