<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Admin Database Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        .login-container {
            max-width: 400px;
            margin: 50px auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Admin Login</h3>
                </div>
                <div class="card-body">
                    <?php
                    include 'configs/dbconfig.php';

                    $message = '';

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_login']) && isset($_POST['user_pass'])) {
                        $sql = "SELECT * FROM wp_users WHERE user_login = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $_POST['user_login']);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            // ใช้ password_verify ตรวจสอบรหัสผ่าน
                            $stored_hash = str_replace('$wp$', '$2y$', $user['user_pass']);
                            if (password_verify($_POST['user_pass'], $stored_hash)) {
                                $message = '<div class="alert alert-success">เข้าสู่ระบบสำเร็จ!</div>';
                            } else {
                                $message = '<div class="alert alert-danger">รหัสผ่านไม่ถูกต้อง</div>';
                            }
                        } else {
                            $message = '<div class="alert alert-danger">ไม่พบชื่อผู้ใช้นี้</div>';
                        }
                        $stmt->close();
                    }
                    ?>

                    <?php if ($message): ?>
                        <?php echo $message; ?>
                    <?php endif; ?>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="user_login" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" name="user_login" id="user_login" placeholder="กรอกชื่อผู้ใช้"
                                class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_pass" class="form-label">รหัสผ่าน</label>
                            <input type="password" name="user_pass" id="user_pass" placeholder="กรอกรหัสผ่าน"
                                class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>