<?php
function renderNavbar() {
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">ระบบจัดการโรงพยาบาล</div>
            <div class="nav-links">
                <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">แผงควบคุม</a>
                <a href="patients.php" class="<?php echo $current_page == 'patients.php' ? 'active' : ''; ?>">ผู้ป่วย</a>
                <a href="appointments.php" class="<?php echo $current_page == 'appointments.php' ? 'active' : ''; ?>">การนัดหมาย</a>
                <a href="doctors.php" class="<?php echo $current_page == 'doctors.php' ? 'active' : ''; ?>">แพทย์</a>
                <a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">ตั้งค่า</a>
                <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
            </div>
            <div class="nav-user">
                <?php if(isset($_SESSION['username'])): ?>
                    <span>ผู้ใช้: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php
}
