<?php

/** @var array $transfer */
/** @var array $branches */
/** @var array $bank_accounts */
require_once __DIR__ . '/../layout/header.php';
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
        font-size: 15px;
    }

    .card-body {
        padding: 20px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 13.5px;
        margin-bottom: 6px;
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
        background: #fff;
    }

    .form-control:focus {
        border-color: #0088ff;
    }

    .form-control:disabled {
        background: #f4f6f8;
        color: #919eab;
        cursor: not-allowed;
        border-style: dashed;
    }

    .btn-outline {
        background: #fff;
        color: #212b36;
        border: 1px solid #c4cdd5;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
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
    <div class="v3-title"><a href="index.php?action=fund_transfers" style="text-decoration:none; color:#637381; margin-right:10px;">â†</a> Chi tiáº¿t phiáº¿u chuyá»ƒn quá»¹: <?php echo htmlspecialchars($transfer['transfer_code']); ?></div>

    <div style="display: flex; gap: 10px;">
        <a href="index.php?action=fund_transfers" class="btn-outline">Quay láº¡i</a>
        <button type="button" class="btn-outline" style="color: #d82c0d; border-color: #fca5a5;" onclick="processSingleDeleteFundTransfer(<?php echo $transfer['id']; ?>)">ðŸ—‘ï¸ XÃ³a phiáº¿u</button>
        <button type="button" class="btn-primary" onclick="document.getElementById('frm_update_fund').submit()">ðŸ’¾ LÆ°u cáº­p nháº­t phiáº¿u</button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#eafff0; color:#108043; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #33d067; font-size:14px;">âœ… Cáº­p nháº­t thÃ´ng tin chá»©ng tá»« chá»©ng minh dÃ²ng tiá»n thÃ nh cÃ´ng!</div>
<?php endif; ?>

<form id="frm_update_fund" action="index.php?action=update_fund_transfer" method="POST">
    <input type="hidden" name="id" value="<?php echo $transfer['id']; ?>">

    <div class="grid-2">
        <div class="v3-card">
            <div class="card-header">2.1. ThÃ´ng tin chung cá»‘ Ä‘á»‹nh (KhÃ´ng thá»ƒ sá»­a)</div>
            <div class="card-body">
                <div class="form-group">
                    <label>MÃ£ phiáº¿u chi Ä‘á»‹nh danh</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($transfer['transfer_code']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>HÃ¬nh thá»©c nguá»“n chuyá»ƒn tiá»n tá»«</label>
                    <input type="text" class="form-control" value="<?php echo $transfer['from_type'] === 'cash' ? 'ðŸ’µ Tiá»n máº·t chi nhÃ¡nh ná»™p' : 'ðŸ¦ Sá»• tiá»n gá»­i ngÃ¢n hÃ ng chuyá»ƒn'; ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Sá»‘ tiá»n Ä‘Ã£ chuyá»ƒn quá»¹ ná»™i bá»™</label>
                    <input type="text" class="form-control" style="font-size:16px; font-weight:bold; color:#108043;" value="<?php echo number_format($transfer['amount'], 0, ',', '.'); ?> â‚«" disabled>
                </div>
                <div class="form-group">
                    <label>NgÃ y ghi nháº­n táº¡o phiáº¿u</label>
                    <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i:s', strtotime($transfer['created_at'])); ?>" disabled>
                </div>
            </div>
        </div>

        <div class="v3-card">
            <div class="card-header">2.2. ThÃ´ng tin bá»• sung & Cáº­p nháº­t (Quy táº¯c Há»‡ thá»‘ng)</div>
            <div class="card-body">

                <div class="form-group">
                    <label>Diá»…n giáº£i ná»™i dung phiáº¿u chi chuyá»ƒn <span>*</span></label>
                    <textarea name="description" class="form-control" rows="2" required placeholder="Nháº­p lÃ½ do..."><?php echo htmlspecialchars($transfer['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>ThÃ´ng tin tham chiáº¿u giao dá»‹ch (MÃ£ UNC, MÃ£ ngÃ¢n hÃ ng...)</label>
                    <input type="text" name="reference_code" class="form-control" value="<?php echo htmlspecialchars($transfer['reference_code']); ?>" placeholder="Nháº­p mÃ£ Ä‘á»‘i soÃ¡t...">
                </div>

                <div class="form-group">
                    <label>NÆ¡i nháº­n quá»¹ tiá»n (Chi nhÃ¡nh / NgÃ¢n hÃ ng Ä‘Ã­ch)</label>
                    <?php if ($transfer['to_id'] > 0): ?>
                        <input type="text" class="form-control" value="<?php echo $transfer['to_type'] === 'cash' ? 'ðŸ’µ Tiá»n máº·t' : 'ðŸ¦ NgÃ¢n hÃ ng'; ?> (ÄÃ£ chá»‘t sá»• quá»¹)" disabled>
                    <?php else: ?>
                        <select name="to_id_update" class="form-control" style="border-color: #0088ff; background: #f4f9ff;">
                            <option value="">-- Chá»n Ä‘Ã­ch Ä‘áº¿n Ä‘á»ƒ bá»• sung vÃ o sá»• quá»¹ --</option>
                            <?php if ($transfer['to_type'] === 'cash'): ?>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($bank_accounts as $bank): ?>
                                    <option value="<?php echo $bank['id']; ?>"><?php echo htmlspecialchars($bank['bank_name']) . ' (' . htmlspecialchars($bank['account_number']) . ')'; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p style="font-size:12px; color:#0088ff; margin-top:4px;">âœ¨ Há»‡ thá»‘ng khuyÃªn dÃ¹ng: Báº¡n Ä‘Æ°á»£c bá»• sung thÃ´ng tin do lÃºc táº¡o Ä‘ang Ä‘á»ƒ trá»‘ng.</p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>NgÃ y phÃ¡t sinh giao dá»‹ch nháº­n tiá»n (NgÃ y vÃ o sá»• thá»±c táº¿)</label>
                    <?php if (!empty($transfer['transaction_date'])): ?>
                        <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($transfer['transaction_date'])); ?>" disabled>
                    <?php else: ?>
                        <input type="datetime-local" name="transaction_date_update" class="form-control" style="border-color: #0088ff; background: #f4f9ff;">
                        <p style="font-size:12px; color:#0088ff; margin-top:4px;">âœ¨ Thao tÃ¡c: Chá»n ngÃ y giá» thá»±c táº¿ tiá»n tinh tinh vÃ o tÃ i khoáº£n.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    // AJAX xá»­ lÃ½ xÃ³a Ä‘Æ¡n láº» tá»« trang chi tiáº¿t
    function processSingleDeleteFundTransfer(fundId) {
        let confirmMsg = "âš ï¸ Cáº¢NH BÃO LÆ¯U Ã: Thao tÃ¡c xÃ³a phiáº¿u chuyá»ƒn quá»¹ ná»™i bá»™ khÃ´ng thá»ƒ khÃ´i phá»¥c vÃ  sáº½ lÃ m áº£nh hÆ°á»Ÿng Ä‘áº¿n bÃ¡o cÃ¡o tÃ i chÃ­nh!\n\nBáº¡n cÃ³ thá»±c sá»± muá»‘n xÃ³a phiáº¿u nÃ y khÃ´ng?";

        if (confirm(confirmMsg)) {
            fetch('index.php?action=api_delete_fund', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: fundId
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        alert(res.msg);
                        // Chuyá»ƒn hÆ°á»›ng vá» trang danh sÃ¡ch sau khi xÃ³a thÃ nh cÃ´ng
                        window.location.href = 'index.php?action=fund_transfers&success=deleted_single';
                    } else {
                        alert("Lá»—i tá»« há»‡ thá»‘ng: " + res.msg);
                    }
                });
        }
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

