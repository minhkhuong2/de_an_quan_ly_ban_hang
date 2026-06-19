<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
/** @var array|null $customers */
// Đảm bảo $customers luôn là mảng, chống lỗi null
$safe_customers = is_array($customers) ? $customers : [];
?>

<style>
    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh sách khách hàng</h2>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn-outline" onclick="document.getElementById('export_modal').style.display='flex'">📥 Xuất file</button>
        <a href="index.php?action=add_customer" class="btn-primary">+ Thêm khách hàng</a>
        <button type="button" class="btn-outline" onclick="document.getElementById('export_bulk_debt_modal').style.display='flex'">📥 Xuất công nợ</button>
    </div>
</div>

<?php if (isset($_GET['success_delete'])): ?>
    <div style="background:#eafff0; color:#108043; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid #8ce09f; font-weight:500;">✅ Đã xóa khách hàng thành công!</div>
<?php endif; ?>

<div class="card" style="background:#fff; border-radius:8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding:0; min-height: 400px;">
    <form action="index.php" method="GET" style="display: flex; gap: 10px; padding: 15px; border-bottom: 1px solid #dfe3e8; background: #fafbfc;">
        <input type="hidden" name="action" value="customer_list">
        <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Tìm theo tên, mã KH, SĐT..." style="flex:1; padding: 8px 12px; border: 1px solid #c4cdd5; border-radius: 4px; outline: none;">
        <button type="submit" class="btn-outline">Lọc</button>
    </form>

    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #fff; color: #637381; border-bottom: 1px solid #dfe3e8; font-size: 14px;">
                <th style="padding: 12px 15px; width: 5%; text-align: center;"><input type="checkbox" id="checkAll" onchange="toggleAllCustomers(this)"></th>
                <th style="padding: 12px 15px;">Mã KH</th>
                <th style="padding: 12px 15px;">Tên khách hàng</th>
                <th style="padding: 12px 15px;">Điện thoại</th>
                <th style="padding: 12px 15px; text-align: right;">Công nợ</th>
                <th style="padding: 12px 15px; text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($safe_customers)): ?>
                <?php foreach ($safe_customers as $c): ?>
                    <tr style="border-bottom: 1px solid #f4f6f8; font-size: 14px;">
                        <td style="padding: 12px 15px; text-align: center;">
                            <input type="checkbox" class="chk-customer" value="<?php echo $c['id']; ?>">
                        </td>
                        <td style="padding: 12px 15px; color:#0088ff; font-weight: bold;"><?php echo htmlspecialchars($c['customer_code'] ?? ''); ?></td>
                        <td style="padding: 12px 15px; font-weight: 500;">
                            <?php echo htmlspecialchars(trim(($c['last_name'] ?? '') . ' ' . ($c['first_name'] ?? ''))); ?>
                        </td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($c['phone'] ?? ''); ?></td>
                        <td style="padding: 12px 15px; text-align: right; font-weight: bold; color: <?php echo ($c['debt'] ?? 0) > 0 ? '#d82c0d' : '#108043'; ?>;">
                            <?php echo number_format($c['debt'] ?? 0, 0, ',', '.'); ?> ₫
                        </td>
                        <td style="padding: 12px 15px; text-align: center;">
                            <a href="index.php?action=edit_customer&id=<?php echo $c['id'] ?? 0; ?>" style="text-decoration: none; margin-right: 10px;" title="Sửa">✏️</a>
                            <a href="index.php?action=delete_customer&id=<?php echo $c['id'] ?? 0; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');" style="text-decoration: none;" title="Xóa">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #637381;">Chưa có dữ liệu khách hàng.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="export_modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 450px; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color: #212b36;">Xuất file khách hàng</h3>

        <p style="font-weight: 600; font-size: 14px; margin-bottom: 10px; color: #212b36;">Chọn danh sách khách hàng cần xuất:</p>
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
            <label style="cursor: pointer; font-size:14px; color:#212b36;"><input type="radio" name="export_opt" value="all" checked> Tất cả khách hàng</label>
            <label style="cursor: pointer; font-size:14px; color:#212b36;"><input type="radio" name="export_opt" value="page"> Chỉ khách hàng trên trang hiện tại</label>
            <label style="cursor: pointer; font-size:14px; color:#212b36;"><input type="radio" name="export_opt" value="selected"> Khách hàng được chọn (Tick ở bảng)</label>
        </div>

        <div style="background: #f4f9ff; padding: 12px; border-radius: 4px; font-size: 13px; color: #0056b3; margin-bottom: 20px; line-height: 1.5;">
            📧 <b>Lưu ý:</b> File danh sách Excel (CSV) sẽ được gửi tự động qua email của tài khoản đang thao tác.
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button class="btn-outline" onclick="document.getElementById('export_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" onclick="processExportCustomers()">Tiến hành Xuất file</button>
        </div>
    </div>
</div>

<div id="export_bulk_debt_modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 450px; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px; color: #212b36;">Xuất file công nợ khách hàng</h3>

        <p style="font-weight: 600; font-size: 14px; margin-bottom: 10px; color: #212b36;">Loại xuất file: Danh sách công nợ theo tất cả khách hàng</p>

        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
            <label style="cursor: pointer; font-size:14px; color:#212b36;"><input type="radio" name="bulk_export_opt" value="all" checked> Tất cả công nợ của các khách hàng</label>
            <label style="cursor: pointer; font-size:14px; color:#212b36;"><input type="radio" name="bulk_export_opt" value="page"> Công nợ trên trang hiện tại</label>
        </div>

        <div style="background: #f4f9ff; padding: 12px; border-radius: 4px; font-size: 13px; color: #0056b3; margin-bottom: 20px; line-height: 1.5;">
            📧 <b>Lưu ý:</b> File danh sách Excel sẽ được gửi tự động qua email của tài khoản đang thao tác.
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button class="btn-outline" style="background: #fff; border: 1px solid #c4cdd5; padding: 8px 15px; border-radius: 4px; cursor: pointer;" onclick="document.getElementById('export_bulk_debt_modal').style.display='none'">Hủy</button>
            <button class="btn-primary" style="background: #0088ff; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;" onclick="processBulkExportDebt()">Xuất file</button>
        </div>
    </div>
</div>

<script>
    // Hàm chọn tất cả checkbox
    function toggleAllCustomers(masterCheckbox) {
        let checkboxes = document.querySelectorAll('.chk-customer');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
    }

    // Hàm xử lý xuất file gửi lên Server
    function processExportCustomers() {
        let opt = document.querySelector('input[name="export_opt"]:checked').value;

        // Lấy danh sách ID thực tế các khách hàng đang được tick chọn
        let selectedIds = Array.from(document.querySelectorAll('.chk-customer:checked')).map(cb => parseInt(cb.value));

        // Validate nghiệp vụ: Chọn mục 3 mà không tick ai thì báo lỗi
        if (opt === 'selected' && selectedIds.length === 0) {
            alert("Vui lòng tick chọn ít nhất 1 khách hàng trên bảng để xuất!");
            return;
        }

        // Gọi API lên Controller
        fetch('index.php?action=api_export_customers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    export_type: opt,
                    selected_ids: selectedIds
                })
            })
            .then(res => res.json())
            .then(res => {
                alert("✅ Thành công!\n" + res.msg);
                document.getElementById('export_modal').style.display = 'none';
            });
    }

    function processBulkExportDebt() {
        let opt = document.querySelector('input[name="bulk_export_opt"]:checked').value;

        fetch('index.php?action=api_export_debt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    export_scope: 'bulk',
                    export_type: opt
                })
            })
            .then(res => res.json())
            .then(res => {
                alert(res.msg);
                document.getElementById('export_bulk_debt_modal').style.display = 'none';
            });
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
