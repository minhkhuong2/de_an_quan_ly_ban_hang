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
        border: 1px solid #dfe3e8;
        margin-bottom: 20px;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
        font-size: 15px;
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 8px;
        color: #212b36;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        box-sizing: border-box;
    }

    .radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
    }

    .condition-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        align-items: center;
        background: #f4f6f8;
        padding: 15px;
        border-radius: 6px;
        border: 1px dashed #c4cdd5;
    }

    .btn-remove-cond {
        background: none;
        border: none;
        color: #d82c0d;
        font-size: 18px;
        cursor: pointer;
        padding: 0 5px;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="v3-header">
    <div class="v3-title">Thêm mới nhóm khách hàng</div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline" onclick="window.location.href='index.php?action=customer_list'">Hủy</button>
        <button class="btn-primary" onclick="document.getElementById('frm_group').submit()">💾 Tạo nhóm</button>
    </div>
</div>

<form id="frm_group" action="index.php?action=store_customer_group" method="POST">
    <div class="v3-card">
        <div class="card-header">Thông tin chung</div>
        <div class="card-body">
            <div class="form-group">
                <label>Tên nhóm <span>*</span></label>
                <input type="text" name="group_name" class="form-control" required placeholder="VD: Khách VIP chi tiêu trên 10 triệu">
            </div>
            <div class="form-group">
                <label>Ghi chú (Tùy chọn)</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Mô tả nhóm khách hàng..."></textarea>
            </div>
        </div>
    </div>

    <div class="v3-card">
        <div class="card-header">Phân loại nhóm</div>
        <div class="card-body">
            <div class="radio-group">
                <label class="radio-item">
                    <input type="radio" name="group_type" value="manual" checked onchange="toggleAutoBox()"> <b>Thủ công</b> (Tự thêm KH bằng tay)
                </label>
                <label class="radio-item">
                    <input type="radio" name="group_type" value="auto" onchange="toggleAutoBox()"> <b style="color: #0088ff;">Tự động</b> (Hệ thống tự động gom KH theo điều kiện)
                </label>
            </div>

            <div id="auto_condition_box" style="display: none; border-top: 1px solid #dfe3e8; padding-top: 15px;">
                <p style="font-size: 14px; font-weight: 600; margin-bottom: 10px;">Khách hàng thỏa mãn:</p>
                <div class="radio-group" style="margin-bottom: 15px;">
                    <label class="radio-item"><input type="radio" name="condition_match" value="all" checked> Tất cả các điều kiện (AND)</label>
                    <label class="radio-item"><input type="radio" name="condition_match" value="any"> Một trong các điều kiện (OR)</label>
                </div>

                <div id="conditions_container"></div>

                <button type="button" class="btn-outline" style="margin-top: 10px; color: #0088ff; border-color: #0088ff;" onclick="addConditionRow()">+ Thêm điều kiện</button>
            </div>
        </div>
    </div>
</form>

<script>
    // Logic Ẩn/Hiện khối Tự động
    function toggleAutoBox() {
        let type = document.querySelector('input[name="group_type"]:checked').value;
        let box = document.getElementById('auto_condition_box');
        if (type === 'auto') {
            box.style.display = 'block';
            if (document.getElementById('conditions_container').children.length === 0) addConditionRow(); // Thêm dòng đầu tiên
        } else {
            box.style.display = 'none';
        }
    }

    // Từ điển logic các trường (Khớp 100% mục 2 tài liệu
    const ruleConfig = {
        'total_spend': {
            label: 'Tổng chi tiêu',
            type: 'number',
            ops: ['Bằng (=)', 'Không bằng (≠)', 'Lớn hơn (>)', 'Lớn hơn hoặc bằng (≥)', 'Nhỏ hơn (<)', 'Nhỏ hơn hoặc bằng (≤)']
        },
        'total_orders': {
            label: 'Tổng số đơn hàng',
            type: 'number',
            ops: ['Bằng (=)', 'Không bằng (≠)', 'Lớn hơn (>)', 'Lớn hơn hoặc bằng (≥)', 'Nhỏ hơn (<)', 'Nhỏ hơn hoặc bằng (≤)']
        },
        'province': {
            label: 'Địa chỉ (Tỉnh/Thành)',
            type: 'text',
            ops: ['Là', 'Không là']
        },
        'tags': {
            label: 'Đã được tag với',
            type: 'text',
            ops: ['Là']
        },
        'accept_marketing': {
            label: 'Nhận email quảng cáo',
            type: 'select',
            ops: ['Là'],
            options: ['Có', 'Không']
        },
        'has_account': {
            label: 'Trạng thái tài khoản',
            type: 'select',
            ops: ['Là'],
            options: ['Có tài khoản', 'Chưa có tài khoản', 'Đã gửi lời mời']
        }
    };

    // Hàm thêm 1 dòng điều kiện động
    function addConditionRow() {
        let container = document.getElementById('conditions_container');
        let rowId = 'row_' + Date.now();

        let fieldSelect = `<select name="cond_field[]" class="form-control" style="width: 30%;" onchange="updateRowLogic('${rowId}')" id="field_${rowId}">`;
        for (let key in ruleConfig) {
            fieldSelect += `<option value="${key}">${ruleConfig[key].label}</option>`;
        }
        fieldSelect += `</select>`;

        let html = `
            <div class="condition-row" id="${rowId}">
                ${fieldSelect}
                <select name="cond_operator[]" class="form-control" style="width: 30%;" id="op_${rowId}"></select>
                <div id="val_container_${rowId}" style="width: 35%;"></div>
                <button type="button" class="btn-remove-cond" onclick="document.getElementById('${rowId}').remove()">✖</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        updateRowLogic(rowId); // Khởi tạo logic cho dòng vừa sinh ra
    }

    // Hàm cập nhật Operator và Input tương ứng khi người dùng đổi Field
    function updateRowLogic(rowId) {
        let fieldKey = document.getElementById('field_' + rowId).value;
        let config = ruleConfig[fieldKey];

        // 1. Cập nhật Phép toán (Operators)
        let opSelect = document.getElementById('op_' + rowId);
        opSelect.innerHTML = config.ops.map(op => `<option value="${op}">${op}</option>`).join('');

        // 2. Cập nhật Ô nhập giá trị (Values)
        let valContainer = document.getElementById('val_container_' + rowId);
        if (config.type === 'select') {
            valContainer.innerHTML = `<select name="cond_value[]" class="form-control">` +
                config.options.map(opt => `<option value="${opt}">${opt}</option>`).join('') + `</select>`;
        } else if (config.type === 'number') {
            valContainer.innerHTML = `<input type="number" name="cond_value[]" class="form-control" placeholder="Nhập số..." required>`;
        } else {
            valContainer.innerHTML = `<input type="text" name="cond_value[]" class="form-control" placeholder="Nhập giá trị..." required>`;
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
