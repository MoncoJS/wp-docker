<?php
session_start();
include('./configs/dbconfig.php'); // รวมไฟล์ config.php สำหรับการเชื่อมต่อฐานข้อมูล

if (isset($_POST['login'])) {
    // รับข้อมูลจากฟอร์ม
    $username = $_POST['username'];
    $password = $_POST['password'];

    // เปลี่ยนเป็นค้นหาด้วย user_login และตรวจสอบกับ user_nicename
    $sql = "SELECT ID, user_login, user_nicename FROM wp_users WHERE user_login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Bind username
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // หากพบผู้ใช้ในฐานข้อมูล
        $user = $result->fetch_assoc();

        // Debug information
        echo "<pre>";
        echo "Login attempt:<br>";
        echo "Username: " . $username . "<br>";
        echo "Password: " . $password . "<br>";
        echo "Stored nicename: " . $user['user_nicename'] . "<br>";
        echo "</pre>";

        // ตรวจสอบรหัสผ่านกับ user_nicename แทน
        if ($password === $user['user_nicename']) {
            // หากรหัสผ่านถูกต้อง
            $_SESSION['user_id'] = $user['ID']; // เก็บข้อมูลผู้ใช้ใน session
            $_SESSION['user_login'] = $user['user_login'];

            // ส่งผู้ใช้ไปยังหน้าหลัก
            header('Location: dashboard.php');
            exit();
        }
    }
    $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>เข้าสู่ระบบ</h2>
    <?php if (isset($error)): ?>
        <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="username">ชื่อผู้ใช้:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">รหัสผ่าน:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit" name="login">Login</button>
    </form>
</body>
</html>
