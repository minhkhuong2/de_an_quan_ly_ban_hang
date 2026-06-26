<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
    .Há»‡ thá»‘ng-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .Há»‡ thá»‘ng-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #c4cdd5;
        border-radius: 4px;
        outline: none;
        font-size: 14px;
        margin-top: 8px;
    }

    .btn-save {
        background: #0088ff;
        color: #fff;
        border: none;
        padding: 10px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        margin-top: 15px;
        width: 100%;
    }

    .btn-save:hover {
        background: #0070d2;
    }

    .Há»‡ thá»‘ng-table {
        width: 100%;
        border-collapse: collapse;
    }

    .Há»‡ thá»‘ng-table th,
    .Há»‡ thá»‘ng-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f4f6f8;
        text-align: left;
    }

    .Há»‡ thá»‘ng-table th {
        color: #637381;
        font-weight: 500;
        font-size: 14px;
        background: #fafbfc;
    }

    .Há»‡ thá»‘ng-grid {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .col-form {
        flex: 0 0 30%;
    }

    .col-table {
        flex: 1;
    }
</style>

<div class="Há»‡ thá»‘ng-header-bar">
    <h2 style="font-size: 20px; font-weight: bold; color: #212b36;">Danh má»¥c sáº£n pháº©m</h2>
</div>

<div class="Há»‡ thá»‘ng-grid">
    <div class="col-form">
        <div class="Há»‡ thá»‘ng-card">
            <h3 style="font-size: 16px; margin-bottom: 15px; color: #212b36;">ThÃªm danh má»¥c má»›i</h3>
            <form action="index.php?action=product_category" method="POST">
                <input type="hidden" name="add_category" value="1">

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 500; font-size: 14px;">TÃªn danh má»¥c <span style="color:red;">*</span></label>
                    <input type="text" name="category_name" class="form-control" placeholder="VD: Äiá»‡n thoáº¡i di Ä‘á»™ng" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-weight: 500; font-size: 14px;">MÃ´ táº£</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nháº­p mÃ´ táº£ cho danh má»¥c nÃ y..."></textarea>
                </div>

                <button type="submit" class="btn-save">+ ThÃªm má»›i danh má»¥c</button>
            </form>
        </div>
    </div>

    <div class="col-table">
        <div class="Há»‡ thá»‘ng-card" style="padding: 0; overflow: hidden;">
            <?php if (!empty($categories)): ?>
                <table class="Há»‡ thá»‘ng-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>TÃªn danh má»¥c</th>
                            <th>MÃ´ táº£</th>
                            <th style="width: 100px; text-align: center;">Thao tÃ¡c</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $row): ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td style="color: #0088ff; font-weight: 500;"><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td style="color: #637381;"><?php echo htmlspecialchars($row['description']); ?></td>
                                <td style="text-align: center;">
                                    <a href="index.php?action=product_category&delete_id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('Báº¡n cÃ³ cháº¯c cháº¯n muá»‘n xÃ³a danh má»¥c nÃ y?');"
                                        style="color: #ff4d4f; text-decoration: none; font-size: 14px;">ðŸ—‘ï¸ XÃ³a</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 50px 20px;">
                    <div style="font-size: 50px; margin-bottom: 15px;">ðŸ“‚</div>
                    <h3 style="font-size: 16px; color: #212b36;">ChÆ°a cÃ³ danh má»¥c nÃ o</h3>
                    <p style="color: #637381; font-size: 14px;">HÃ£y sá»­ dá»¥ng form bÃªn trÃ¡i Ä‘á»ƒ thÃªm danh má»¥c Ä‘áº§u tiÃªn.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

