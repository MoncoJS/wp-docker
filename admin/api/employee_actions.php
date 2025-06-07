<?php
require_once('../configs/dbconfig.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = $_POST['emp_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $start_date = $_POST['start_date'];

    $sql = "INSERT INTO employees (emp_id, firstname, lastname, email, phone, department, position, start_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $emp_id, $firstname, $lastname, $email, $phone, $department, $position, $start_date);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM employees WHERE status = 'active' ORDER BY emp_id";
    $result = $conn->query($sql);
    $employees = [];
    
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    echo json_encode($employees);
}
