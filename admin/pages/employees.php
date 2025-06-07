<?php 
require_once('../configs/dbconfig.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input data
    $emp_id = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);

    // Check if emp_id already exists
    $check_sql = "SELECT emp_id FROM employees WHERE emp_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $emp_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('รหัสพนักงานนี้มีอยู่ในระบบแล้ว');</script>";
    } else {
        // Insert new employee
        $sql = "INSERT INTO employees (emp_id, firstname, lastname, email, phone, department, position, start_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $emp_id, $firstname, $lastname, $email, $phone, $department, $position, $start_date);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('บันทึกข้อมูลสำเร็จ');
                window.location.href = 'index.php';
            </script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "');</script>";
        }
    }
}

// Fetch departments for dropdown
$dept_sql = "SELECT DISTINCT department FROM employees WHERE status = 'active'";
$dept_result = $conn->query($dept_sql);
$departments = [];
while ($row = $dept_result->fetch_assoc()) {
    $departments[] = $row['department'];
}

// Fetch employees
$sql = "SELECT * FROM employees WHERE status = 'active' ORDER BY emp_id";
$result = $conn->query($sql);
?>

<div class="row">
    <!-- Form Section -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">เพิ่มข้อมูลพนักงาน</h4>
            </div>
            <div class="card-body">
                <form id="employeeForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">รหัสพนักงาน</label>
                            <input type="text" class="form-control" name="emp_id" pattern="[A-Za-z0-9]+" required>
                            <div class="invalid-feedback">กรุณากรอกรหัสพนักงาน</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">แผนก</label>
                            <select class="form-select" name="department" required>
                                <option value="">เลือกแผนก</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo htmlspecialchars($dept); ?>">
                                        <?php echo htmlspecialchars($dept); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">กรุณาเลือกแผนก</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" name="firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" class="form-control" name="lastname" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">อีเมล</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ตำแหน่ง</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">วันที่เริ่มงาน</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- List Section -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">รายชื่อพนักงาน</h4>
                <div class="d-flex gap-2">
                    <input type="search" class="form-control form-control-sm w-auto" placeholder="ค้นหา..." id="searchEmployee">
                    <select class="form-select form-select-sm w-auto" id="filterDepartment">
                        <option value="">ทุกแผนก</option>
                        <option value="แผนกการพยาบาล">แผนกการพยาบาล</option>
                        <option value="แผนกเภสัชกรรม">แผนกเภสัชกรรม</option>
                        <option value="แผนกห้องปฏิบัติการ">แผนกห้องปฏิบัติการ</option>
                    </select>
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
                                <th>ตำแหน่ง</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="employeeList">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Search functionality
    $('#searchEmployee').on('keyup', function() {
        filterEmployees();
    });

    // Department filter
    $('#filterDepartment').on('change', function() {
        filterEmployees();
    });
});

function filterEmployees() {
    const searchText = $('#searchEmployee').val().toLowerCase();
    const department = $('#filterDepartment').val();
    
    $('#employeeList tr').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const deptMatch = !department || row.find('td:eq(2)').text() === department;
        const textMatch = text.includes(searchText);
        
        row.toggle(textMatch && deptMatch);
    });
}
</script>

<?php
// Display employees table
echo "<script>
    $(document).ready(function() {
        const tbody = $('#employeeList');
        tbody.empty();";

while ($row = $result->fetch_assoc()) {
    echo "
        tbody.append(`
            <tr>
                <td>{$row['emp_id']}</td>
                <td>{$row['firstname']} {$row['lastname']}</td>
                <td>{$row['department']}</td>
                <td>{$row['position']}</td>
                <td>
                    <button class='btn btn-sm btn-warning edit-emp' data-id='{$row['id']}'>แก้ไข</button>
                    <button class='btn btn-sm btn-danger delete-emp' data-id='{$row['id']}'>ลบ</button>
                </td>
            </tr>
        `);";
}

echo "});</script>";
?>

<script>
// Form validation
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
