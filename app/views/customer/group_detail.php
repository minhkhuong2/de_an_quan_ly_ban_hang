<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php
$group = $group ?? ['group_name' => '', 'group_type' => 'manual', 'condition_match' => 'all', 'conditions_json' => '[]', 'id' => 0];
$members = $members ?? [];
$all_customers = $all_customers ?? [];
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
        margin-bottom: 20px;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dfe3e8;
        background: #fafbfc;
        font-weight: 600;
        color: #212b36;
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

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
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

    .rule-badge {
        background: #f4f6f8;
        border: 1px dashed #c4cdd5;
        padding: 10px 15px;
        border-radius: 6px;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
        font-size: 14px;
        color: #0088ff;
    }

    /* Modal */
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
        max-height: 80vh;
        display: flex;
        flex-direction: column;
    }

    .modal-list {
        overflow-y: auto;
        flex: 1;
        margin: 15px 0;
        border: 1px solid #dfe3e8;
        border-radius: 4px;
    }
</style>

<div class="v3-header">
    <div class="v3-title"><a href="index.php?action=customer_groups" style="text-decoration:none; color:#637381; margin-right:10px;">←</a> <?php echo htmlspecialchars($group['group_name']); ?></div>
    <div style="display: flex; gap: 10px;">
        <button class="btn-outline">📥 Xuất file</button>
        <?php if ($group['group_type'] === 'manual'): ?>
            <button class="btn-outline">⬆️ Nhập file</button>
            <button class="btn-primary" onclick="document.getElementById('add_modal').style.display='flex'">+ Thêm khách hàng</button>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067;">✅ Cập nhật danh sách thành viên thành công!</div>
<?php endif; ?>

<?php if ($group['group_type'] === 'auto'): ?>
    <div class="v3-card">
        <div class="card-header" style="display: flex; justify-content: space-between;">
            <span>Điều kiện lọc tự động</span>
            <span style="color:#e67e22; font-size: 13px;">Match: <?php echo $group['condition_match'] == 'all' ? 'Thỏa mãn TẤT CẢ (AND)' : 'Thỏa mãn MỘT TRONG (OR)'; ?></span>
        </div>
        <div style="padding: 20px;">
            <?php
            $rules = json_decode($group['conditions_json'], true);
            if ($rules) {
                foreach ($rules as $r) {
                    echo "<div class='rule-badge'><b>{$r['field']}</b> {$r['operator']} <b>{$r['value']}</b></div>";
                }
            }
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="v3-card">
    <div class="card-header">Danh sách khách hàng trong nhóm (<?php echo count($members); ?>)</div>
    <table class="v3-table">
        <thead>
            <tr>
                <th>Mã KH</th>
                <th>Tên khách hàng</th>
                <th>Điện thoại</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($members)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #8c98a4; padding: 30px;">Nhóm này chưa có khách hàng nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($members as $m): ?>
                    <tr>
                        <td style="color:#0088ff; font-weight:bold;"><?php echo htmlspecialchars($m['customer_code'] ?? '---'); ?></td>
                        <td><?php echo htmlspecialchars(trim(($m['last_name'] ?? '') . ' ' . ($m['first_name'] ?? ''))); ?></td>
                        <td><?php echo htmlspecialchars($m['phone'] ?? '---'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($group['group_type'] === 'manual'): ?>
    <div id="add_modal" class="modal">
        <div class="modal-content">
            <h3 style="margin-top:0; border-bottom:1px solid #dfe3e8; padding-bottom:10px;">Thêm khách hàng vào nhóm</h3>
            <p style="font-size:14px; color:#637381; margin-top:10px;">Vui lòng tick chọn khách hàng muốn đưa vào nhóm này:</p>

            <form action="index.php?action=add_group_members" method="POST" style="display:flex; flex-direction:column; flex:1;">
                <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">

                <div class="modal-list">
                    <table class="v3-table">
                        <tbody>
                            <?php if (empty($all_customers)): ?>
                                <tr>
                                    <td style="text-align: center; color: #8c98a4; padding: 20px;">Tất cả khách hàng đều đã nằm trong nhóm này.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($all_customers as $ac): ?>
                                    <tr>
                                        <td style="width:5%;"><input type="checkbox" name="customer_ids[]" value="<?php echo $ac['id']; ?>" style="width:16px; height:16px;"></td>
                                        <td><b><?php echo htmlspecialchars(trim(($ac['last_name'] ?? '') . ' ' . ($ac['first_name'] ?? ''))); ?></b><br><small style="color:#637381;"><?php echo $ac['phone']; ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" class="btn-outline" onclick="document.getElementById('add_modal').style.display='none'">Hủy</button>
                    <button type="submit" class="btn-primary">Lưu thành viên</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
