<?php
ob_start(); // เริ่ม output buffering
session_start();

// เช็ค session ก่อน
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once('configs/dbconfig.php');

// ตรวจสอบการ submit form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT ID, user_login, user_nicename FROM wp_users WHERE user_login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่านกับ user_nicename
        if ($password == $user['user_nicename']) {
            // ย้าย session handling มาไว้ก่อนที่จะมี output ใดๆ
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['username'] = $user['user_login'];
            
            // ใช้ absolute URL
            header("Location: index.php");
            exit();
        }
    }
    $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <style>
        body { 
            font-family: sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .login-box {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>เข้าสู่ระบบ</h2>
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
