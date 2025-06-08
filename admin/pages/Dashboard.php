<?php
require_once('../includes/auth.php');
checkAuth();

// Get saline inventory count
$saline_sql = "SELECT COUNT(*) as total FROM saline_inventory WHERE expiry_date > CURRENT_DATE";
$saline_count = $conn->query($saline_sql)->fetch_assoc();

// Get equipment count
$equipment_sql = "SELECT COUNT(*) as total FROM equipment_loans WHERE status = 'borrowed'";
$equipment_count = $conn->query($equipment_sql)->fetch_assoc();
?>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            น้ำเกลือคงเหลือ</div>
                        <div class="h5 mb-0 fw-bold"><?php echo $saline_count['total']; ?> ขวด</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            เครื่องมือแพทย์ที่ถูกยืม</div>
                        <div class="h5 mb-0 fw-bold"><?php echo $equipment_count['total']; ?> รายการ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold">รายการล่าสุด</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>รายการ</th>
                        <th>ผู้ดำเนินการ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $activities_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['user']); ?></td>
                            <td><span class="badge bg-<?php echo getStatusBadge($row['status']); ?>">
                                <?php echo getStatusText($row['status']); ?>
                            </span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
