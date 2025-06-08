<?php
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        if (isAjaxRequest()) {
            http_response_code(401);
            die(json_encode(['error' => 'Unauthorized']));
        }
        header('Location: ../login.php');
        exit();
    }
}

function hasPermission($module) {
    // Permission mapping by department code
    $dept_permissions = [
        'HRM' => ['employees'],              // ฝ่ายบุคคล
        'ICU' => ['saline_requests'],        // ไอซียู
        'EMR' => ['saline_requests'],        // ฉุกเฉิน
        'OBG' => ['equipment'],              // สูติ-นรีเวช
        'SUR' => ['equipment', 'reports'],   // ศัลยกรรม
        'RAD' => ['equipment'],              // รังสี
        'ANE' => ['equipment']               // วิสัญญี
    ];

    $dept_code = $_SESSION['dept_code'] ?? '';
    return isset($dept_permissions[$dept_code]) && 
           in_array($module, $dept_permissions[$dept_code]);
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
