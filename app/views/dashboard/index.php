<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2 style="margin-bottom: 20px;">Tổng quan hệ thống</h2>

<div class="dashboard-stats">
    <div class="stat-box">
        <h3>Điện thoại trong kho</h3>
        <div class="number" style="color: #52c41a;"><?php echo $inStock; ?> Máy</div>
    </div>
    <div class="stat-box">
        <h3>Đã bán ra</h3>
        <div class="number" style="color: #1890ff;"><?php echo $sold; ?> Máy</div>
    </div>
    <div class="stat-box">
        <h3>Đang bảo hành</h3>
        <div class="number" style="color: #ff4d4f;"><?php echo $warranty; ?> Máy</div>
    </div>
</div>

<div class="card">
    <h3 class="card-title">Hoạt động gần đây</h3>
    <p>Hệ thống đang hoạt động ổn định. Số liệu ở trên đã được đồng bộ trực tiếp (Real-time) từ Cơ sở dữ liệu của bạn!</p>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
