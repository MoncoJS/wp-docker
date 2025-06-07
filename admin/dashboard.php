<?php
session_start();
if (!isset($_SESSION['user_login'])) {
    // ถ้าไม่มีการเข้าสู่ระบบ ให้เปลี่ยนไปที่หน้าล็อกอิน
    header('Location: login.php');
    exit();
}

echo "ยินดีต้อนรับ, " . $_SESSION['user_login'] . "! คุณเข้าสู่ระบบสำเร็จแล้ว.";
?>