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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active menu-link" href="#" data-page="home">หน้าหลัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="dashboard">แผงควบคุม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="saline">ระบบเบิกน้ำเกลือ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="equipment">ระบบยืมคืนเครื่องมือแพทย์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="reports">รายงาน</a>
                    </li>
                </ul>
                <span class="navbar-text">
                    ยินดีต้อนรับ <?php echo htmlspecialchars($username); ?> | 
                    <a href="logout.php" class="text-danger text-decoration-none">ออกจากระบบ</a>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div id="content-area">
            <!-- Content will be loaded here -->
            <div class="text-center">
                <h2>ยินดีต้อนรับเข้าสู่ระบบ</h2>
                <p>กรุณาเลือกเมนูที่ต้องการใช้งาน</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.menu-link').click(function(e) {
            e.preventDefault();
            
            $('.menu-link').removeClass('active');
            $(this).addClass('active');
            
            var page = $(this).data('page');
            loadContent(page);
        });

        function loadContent(page) {
            $.ajax({
                url: 'pages/' + page + '.php',
                method: 'GET',
                success: function(response) {
                    $('#content-area').html(response);
                },
                error: function() {
                    $('#content-area').html('<div class="alert alert-danger">ไม่พบหน้าที่ต้องการ</div>');
                }
            });
        }
    });
    </script>
</body>
</html>