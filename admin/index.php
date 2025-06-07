<?php
session_start();
require_once('configs/dbconfig.php');

// ตรวจสอบการล็อกอิน
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'] ?? 'ผู้ใช้';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            background: #f7fafc;
        }
        .navbar {
            background: #2d3748;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: rgba(255,255,255,0.1);
        }
        .welcome-text {
            color: #e2e8f0;
        }
        .content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        h1 {
            color: #2d3748;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-content">
            <div class="nav-links">
                <a href="#">หน้าหลัก</a>
                <a href="#">จัดการข้อมูล</a>
                <a href="#">รายงาน</a>
            </div>
            <div class="welcome-text">
                ยินดีต้อนรับ <?php echo htmlspecialchars($username); ?> | 
                <a href="logout.php" style="color: #fc8181;">ออกจากระบบ</a>
            </div>
        </div>
    </div>
    <div class="content">
        <h1>ระบบจัดการ</h1>
        <p>คุณได้เข้าสู่ระบบในชื่อ <?php echo htmlspecialchars($username); ?></p>
    </div>
</body>
</html>