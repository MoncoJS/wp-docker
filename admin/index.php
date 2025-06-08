<?php
session_start();
require_once('configs/dbconfig.php');
require_once('includes/auth.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$display_name = $_SESSION['display_name'] ?? 'ผู้ใช้';
$department = $_SESSION['department'] ?? '';

function canShowMenu($menu) {
    $dept_permissions = [
        'HRM' => ['employees'],
        'ICU' => ['saline_requests', 'equipment'],
        'EMR' => ['saline_requests', 'equipment'],
        'PHA' => ['saline_requests'],
        'HD' => ['reports', 'employees', 'equipment']
    ];

    $user_dept = $_SESSION['dept_code'] ?? '';
    return isset($dept_permissions[$user_dept]) && 
           in_array($menu, $dept_permissions[$user_dept]);
}

function loadPage($page) {
    $page = strtolower($page);
    $pagePath = __DIR__ . '/pages/' . $page . '.php';
    
    try {
        if (file_exists($pagePath)) {
            // Set variables for page
            global $conn, $user_type, $department;
            
            // Buffer the output
            ob_start();
            include($pagePath);
            return ob_get_clean();
        }
        return '<div class="alert alert-danger">ไม่พบหน้า ' . htmlspecialchars($page) . '</div>';
    } catch (Exception $e) {
        return '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' . $e->getMessage() . '</div>';
    }
}

// Handle AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header('Content-Type: application/json');
    $page = $_GET['page'] ?? 'home';
    echo json_encode([
        'content' => loadPage($page)
    ]);
    exit;
}
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
                        <a class="nav-link menu-link active" href="#" data-page="home">หน้าหลัก</a>
                    </li>

                    <?php if (canShowMenu('saline_requests')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="saline_requests">ระบบเบิกน้ำเกลือ</a>
                    </li>
                    <?php endif; ?>

                    <?php if (canShowMenu('employees')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="employees">จัดการพนักงาน</a>
                    </li>
                    <?php endif; ?>

                    <?php if (canShowMenu('equipment')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="equipment">ระบบยืมคืนเครื่องมือแพทย์</a>
                    </li>
                    <?php endif; ?>

                    <?php if (canShowMenu('reports')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#" data-page="reports">รายงาน</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <span class="navbar-text">
                    <?php echo htmlspecialchars($display_name); ?>
                    (<?php echo $department; ?>) |
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
        $(document).ready(function () {
            function loadContent(page) {
                $('#content-area').html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
                
                $.ajax({
                    url: window.location.pathname,
                    method: 'GET',
                    data: { 
                        page: page,
                        ajax: 1
                    },
                    success: function (response) {
                        if (response.content) {
                            $('#content-area').html(response.content);
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#content-area').html(
                            '<div class="alert alert-danger">' +
                            'เกิดข้อผิดพลาดในการโหลดหน้า: ' + error +
                            '</div>'
                        );
                    }
                });
            }
            
            // Load initial page (dashboard)
            loadContent('Dashboard');
            
            $('.menu-link').click(function (e) {
                e.preventDefault();
                $('.menu-link').removeClass('active');
                $(this).addClass('active');
                var page = $(this).data('page');
                loadContent(page);
            });
        });
    </script>
</body>

</html>