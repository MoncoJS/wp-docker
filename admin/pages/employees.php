<?php
require_once(__DIR__ . '/../configs/dbconfig.php');
require_once(__DIR__ . '/../includes/auth.php');

checkAuth();
if (!hasPermission('employees')) die('Access Denied');

// ฟังก์ชันสร้างรหัสพนักงาน
function getNextEmpId($conn, $dept_code) {
    $pattern = $dept_code . '%';
    $sql = "SELECT emp_id FROM employees WHERE emp_id LIKE ? ORDER BY emp_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = intval(substr($row['emp_id'], -3));
        $nextId = $lastId + 1;
    } else {
        $nextId = 1;
    }
    
    return $dept_code . str_pad($nextId, 3, '0', STR_PAD_LEFT);
}

// บันทึกข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dept_id'])) {
        $dept = $conn->query("SELECT dept_code FROM departments WHERE id = {$_POST['dept_id']}")->fetch_assoc();
        $emp_id = getNextEmpId($conn, $dept['dept_code']);
        
        $stmt = $conn->prepare("INSERT INTO employees (emp_id, firstname, lastname, email, phone, dept_id, start_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", 
            $emp_id,
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['dept_id'],
            $_POST['start_date']
        );
        
        if ($stmt->execute()) {
            header("Location: ?saved=1");
            exit;
        }
        $error = $conn->error;
    }
}

// ดึงข้อมูลแผนก
$departments = $conn->query("SELECT * FROM departments WHERE status = 'active' ORDER BY dept_name");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">เพิ่มพนักงานใหม่</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label>แผนก *</label>
                            <select name="dept_id" class="form-select" required>
                                <option value="">เลือกแผนก</option>
                                <?php while($dept = $departments->fetch_assoc()): ?>
                                <option value="<?= $dept['id'] ?>"><?= $dept['dept_name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label>ชื่อ *</label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>นามสกุล *</label>
                            <input type="text" name="lastname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>อีเมล *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>เบอร์โทรศัพท์</label>
                            <input type="tel" name="phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>วันที่เริ่มงาน *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">บันทึกข้อมูล</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">รายชื่อพนักงาน</h5>
                    <div>
                        <input type="search" class="form-control form-control-sm" placeholder="ค้นหา..." id="searchInput">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>รหัส</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>แผนก</th>
                                    <th>อีเมล</th>
                                    <th>เบอร์โทร</th>
                                    <th>วันที่เริ่มงาน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $employees = $conn->query("
                                    SELECT e.*, d.dept_name 
                                    FROM employees e 
                                    LEFT JOIN departments d ON e.dept_id = d.id 
                                    WHERE e.status = 'active' 
                                    ORDER BY e.emp_id DESC
                                ");
                                while($emp = $employees->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $emp['emp_id'] ?></td>
                                    <td><?= $emp['firstname'] . ' ' . $emp['lastname'] ?></td>
                                    <td><?= $emp['dept_name'] ?></td>
                                    <td><?= $emp['email'] ?></td>
                                    <td><?= $emp['phone'] ?></td>
                                    <td><?= $emp['start_date'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});
</script>
