<?php
require_once('../configs/dbconfig.php');
require_once('../includes/auth.php');

checkAuth();
checkPermission(['admin', 'nurse', 'pharmacist']);

// Function to get next request ID
function getNextRequestId($conn) {
    $sql = "SELECT request_id FROM saline_requests WHERE request_id LIKE 'SR%' ORDER BY request_id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = intval(substr($row['request_id'], 2));
        $nextId = $lastId + 1;
        return 'SR' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
    return 'SR0001';
}

// Get available saline types with detailed stock info
$stock_sql = "SELECT 
                saline_type, 
                SUM(quantity) as total,
                MIN(expiry_date) as nearest_expiry,
                unit_price
              FROM saline_inventory 
              WHERE expiry_date > CURRENT_DATE 
              GROUP BY saline_type, unit_price
              HAVING total > 0";
$stock_result = $conn->query($stock_sql);
$saline_types = [];
while ($row = $stock_result->fetch_assoc()) {
    $saline_types[$row['saline_type']] = [
        'stock' => $row['total'],
        'expiry' => $row['nearest_expiry'],
        'price' => $row['unit_price']
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'approve':
                // Start transaction
                $conn->begin_transaction();
                try {
                    // Update request status
                    $stmt = $conn->prepare("UPDATE saline_requests SET status = 'approved', approved_by = ?, approved_date = NOW() WHERE id = ?");
                    $stmt->bind_param("si", $_SESSION['emp_id'], $_POST['request_id']);
                    $stmt->execute();

                    // Update inventory
                    $stmt = $conn->prepare("UPDATE saline_inventory SET quantity = quantity - ? WHERE saline_type = ? AND quantity >= ? LIMIT 1");
                    $stmt->bind_param("isi", $_POST['quantity'], $_POST['saline_type'], $_POST['quantity']);
                    $stmt->execute();

                    $conn->commit();
                    echo json_encode(['status' => 'success']);
                } catch (Exception $e) {
                    $conn->rollback();
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                exit;
                
            case 'reject':
                $request_id = $_POST['request_id'];
                $sql = "UPDATE saline_requests SET status = 'rejected', 
                        approved_by = ?, approved_date = NOW() 
                        WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $_SESSION['user_id'], $request_id);
                $stmt->execute();
                break;
                
            default:
                // New request
                $request_id = getNextRequestId($conn);
                $emp_id = $_SESSION['emp_id']; // ใช้จาก session
                $department = $_SESSION['department']; // ใช้จาก session
                $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
                $patient_hn = mysqli_real_escape_string($conn, $_POST['patient_hn']);
                $saline_type = mysqli_real_escape_string($conn, $_POST['saline_type']);
                $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
                $notes = mysqli_real_escape_string($conn, $_POST['notes']);
                $ward = mysqli_real_escape_string($conn, $_POST['ward']);
                $room = mysqli_real_escape_string($conn, $_POST['room']);

                // Check stock availability
                $stock_check = "SELECT SUM(quantity) as available 
                               FROM saline_inventory 
                               WHERE saline_type = ? AND expiry_date > CURRENT_DATE";
                $check_stmt = $conn->prepare($stock_check);
                $check_stmt->bind_param("s", $saline_type);
                $check_stmt->execute();
                $stock_result = $check_stmt->get_result()->fetch_assoc();

                if ($stock_result['available'] >= $quantity) {
                    $sql = "INSERT INTO saline_requests (request_id, emp_id, department, patient_name, patient_hn, 
                            saline_type, quantity, notes, ward, room) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssssss", $request_id, $emp_id, $department, $patient_name, 
                                    $patient_hn, $saline_type, $quantity, $notes, $ward, $room);
                    
                    if ($stmt->execute()) {
                        echo "<script>
                            alert('บันทึกคำขอสำเร็จ เลขที่คำขอ: $request_id');
                            window.location.reload();
                        </script>";
                    } else {
                        echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('จำนวนน้ำเกลือในคลังไม่เพียงพอ');</script>";
                }
        }
    }
}

// Fetch active requests
$sql = "SELECT r.*, e.firstname, e.lastname 
        FROM saline_requests r 
        JOIN employees e ON r.emp_id = e.emp_id 
        WHERE r.status = 'pending' 
        ORDER BY r.request_date DESC";
$result = $conn->query($sql);
?>

<!-- Form Section -->
<div class="row">
    <div class="col-12 mb-3">
        <div class="alert alert-info">
            ผู้ขอ: <?php echo $_SESSION['firstname'] . ' ' . $_SESSION['lastname']; ?> 
            (<?php echo $_SESSION['department']; ?>)
        </div>
    </div>
</div>

<div class="row">
    <!-- Request Form -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">แบบฟอร์มขอน้ำเกลือ</h4>
            </div>
            <div class="card-body">
                <form method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="emp_id" value="<?php echo htmlspecialchars($_SESSION['emp_id']); ?>">
                    <input type="hidden" name="department" value="<?php echo htmlspecialchars($_SESSION['department']); ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ผู้ป่วย HN</label>
                            <input type="text" class="form-control" name="patient_hn" required 
                                   pattern="[0-9]{8}" title="กรุณากรอกรหัส HN 8 หลัก">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้ป่วย</label>
                            <input type="text" class="form-control" name="patient_name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">วอร์ด</label>
                            <input type="text" class="form-control" name="ward" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เตียง</label>
                            <input type="text" class="form-control" name="room" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">ประเภทน้ำเกลือ</label>
                            <select class="form-select" name="saline_type" required>
                                <option value="">เลือกประเภท</option>
                                <?php foreach ($saline_types as $type => $details): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>">
                                        <?php echo htmlspecialchars($type); ?> 
                                        (คงเหลือ: <?php echo $details['stock']; ?> ขวด, หมดอายุ: <?php echo $details['expiry']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">จำนวน (ขวด)</label>
                            <input type="number" class="form-control" name="quantity" 
                                   min="1" max="10" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">บันทึกคำขอ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Request List with improved status display -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">รายการขอน้ำเกลือ</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>เลขที่</th>
                                <th>ผู้ป่วย</th>
                                <th>ประเภท</th>
                                <th>จำนวน</th>
                                <th>สถานะ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['saline_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success approve-btn" data-id="<?php echo $row['id']; ?>">อนุมัติ</button>
                                    <button class="btn btn-sm btn-danger reject-btn" data-id="<?php echo $row['id']; ?>">ปฏิเสธ</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Improve approval/rejection handlers
$('.approve-btn, .reject-btn').click(function() {
    const id = $(this).data('id');
    const action = $(this).hasClass('approve-btn') ? 'approve' : 'reject';
    const row = $(this).closest('tr');
    const quantity = row.find('td:eq(3)').text();
    const saline_type = row.find('td:eq(2)').text();

    if (confirm('ยืนยันการ' + (action === 'approve' ? 'อนุมัติ' : 'ปฏิเสธ') + 'คำขอ?')) {
        $.post('saline_requests.php', 
            { 
                action: action, 
                request_id: id,
                quantity: quantity,
                saline_type: saline_type
            }, 
            function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + response.message);
                }
            },
            'json'
        );
    }
});

// Add stock check on quantity input
$('input[name="quantity"]').on('change', function() {
    const salineType = $('select[name="saline_type"]').val();
    const quantity = parseInt($(this).val());
    const stock = parseInt($('select[name="saline_type"] option:selected').text().match(/คงเหลือ: (\d+)/)[1]);
    
    if (quantity > stock) {
        alert('จำนวนที่ขอมากกว่าจำนวนคงเหลือ');
        $(this).val('');
    }
});
</script>
