<?php
session_start();
require_once('configs/dbconfig.php');

// Clear any output buffering
ob_clean();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Try employee login
    $emp_sql = "SELECT * FROM employees WHERE email = ? AND status = 'active'";
    $emp_stmt = $conn->prepare($emp_sql);
    $emp_stmt->bind_param("s", $username);
    $emp_stmt->execute();
    $employee = $emp_stmt->get_result()->fetch_assoc();
    
    if ($employee && $employee['phone'] === $password) {
        // Get department info
        $dept_sql = "SELECT dept_code, dept_name FROM departments WHERE id = ?";
        $dept_stmt = $conn->prepare($dept_sql);
        $dept_stmt->bind_param("i", $employee['dept_id']);
        $dept_stmt->execute();
        $dept = $dept_stmt->get_result()->fetch_assoc();

        $_SESSION['user_id'] = 'emp_' . $employee['id'];
        $_SESSION['emp_id'] = $employee['emp_id'];
        $_SESSION['email'] = $employee['email'];
        $_SESSION['firstname'] = $employee['firstname']; 
        $_SESSION['lastname'] = $employee['lastname'];
        $_SESSION['dept_id'] = $employee['dept_id'];
        $_SESSION['dept_code'] = $dept['dept_code'];
        $_SESSION['department'] = $dept['dept_name'];
        $_SESSION['display_name'] = $employee['firstname'] . ' ' . $employee['lastname'];

        // Update last login
        $conn->query("UPDATE employees SET last_login = NOW() WHERE id = {$employee['id']}");
        header("Location: index.php");
        exit();
    } 
    // Try wp_users login next
    $wp_sql = "SELECT * FROM wp_users WHERE user_login = ? OR user_email = ?";
    $wp_stmt = $conn->prepare($wp_sql);
    $wp_stmt->bind_param("ss", $username, $username);
    $wp_stmt->execute();
    $wp_user = $wp_stmt->get_result()->fetch_assoc();
    
    if ($wp_user && $password === $wp_user['user_nicename']) {
        $_SESSION['user_id'] = 'wp_' . $wp_user['ID'];
        $_SESSION['username'] = $wp_user['user_login']; 
        $_SESSION['display_name'] = $wp_user['display_name'];
        $_SESSION['email'] = $wp_user['user_email'];
        $_SESSION['department'] = 'สมาชิกเว็บไซต์';

        header("Location: index.php");
        exit();
    }
    
    $error = "อีเมล/ชื่อผู้ใช้ หรือรหัสผ่านไม่ถูกต้อง";
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        h2 {
            color: #2d3748;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s;
            outline: none;
            font-family: 'Kanit', sans-serif;
        }
        input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        button {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Kanit', sans-serif;
        }
        button:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }
        .error {
            background: #fff5f5;
            color: #c53030;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
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
                <label>ชื่อผู้ใช้ / อีเมล</label>
                <input type="text" name="username" required placeholder="กรอกชื่อผู้ใช้หรืออีเมล">
            </div>
            <div class="form-group">
                <label>รหัสผ่าน / เบอร์โทรศัพท์</label>
                <input type="password" name="password" required placeholder="กรอกรหัสผ่านหรือเบอร์โทรศัพท์">
            </div>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
