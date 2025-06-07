<?php
session_start();
require_once('configs/dbconfig.php');
require_once('components/navbar.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php renderNavbar(); ?>
    
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>แผงควบคุม</h1>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>ผู้ใช้ทั้งหมด</h3>
                <p class="stat-number">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM wp_users");
                    echo $result->fetch_assoc()['total'];
                    ?>
                </p>
            </div>
            <div class="stat-card">
                <h3>บทความทั้งหมด</h3>
                <p class="stat-number">
                    <?php
                    $result = $conn->query("SELECT COUNT(*) as total FROM wp_posts WHERE post_type='post'");
                    echo $result->fetch_assoc()['total'];
                    ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
