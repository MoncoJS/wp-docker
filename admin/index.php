<?php
session_start();
require_once('configs/dbconfig.php');

// ตรวจสอบการล็อกอิน
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
        }
        .navbar {
            background: #333;
            padding: 10px;
            color: white;
            margin-bottom: 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
        }
        .content {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">หน้าหลัก</a>
        <a href="logout.php">ออกจากระบบ</a>
        <span style="float:right">ยินดีต้อนรับ <?php echo htmlspecialchars($username); ?></span>
    </div>
    <div class="content">
        <h1>ยินดีต้อนรับเข้าสู่ระบบ</h1>
        <p>คุณได้เข้าสู่ระบบแล้ว</p>
    </div>
</body>
</html>