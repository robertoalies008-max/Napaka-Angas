<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Set execution time limit
set_time_limit(30);

// Start debug logging
error_log("=== ADD RESIDENT START ===");
error_log("Time: " . date('Y-m-d H:i:s'));
error_log("POST data: " . print_r($_POST, true));

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    error_log("Database connection failed");
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing POST request");
    
    // Check if we're receiving the form data
    if (empty($_POST)) {
        error_log("No POST data received");
        echo json_encode(['success' => false, 'message' => 'No form data received']);
        exit;
    }
    
    // Validate required fields
    $required_fields = ['full_name', 'address', 'contact_number'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        error_log("Missing required fields: " . implode(', ', $missing_fields));
        echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing_fields)]);
        exit;
    }
    
    try {
        error_log("Starting database operation");
        
        // Generate resident code if not provided
        $resident_code = $_POST['resident_code'] ?? 'RES-' . date('YmdHis');
        
        $query = "INSERT INTO resident SET 
                  resident_code = :resident_code,
                  full_name = :full_name,
                  email = :email,
                  birthdate = :birthdate,
                  address = :address,
                  contact_number = :contact_number,
                  family_count = :family_count,
                  voter_status = :voter_status,
                  monthly_income = :monthly_income,
                  occupation = :occupation,
                  registration_date = :registration_date,
                  status = :status,
                  created_at = NOW()";
                  
        error_log("SQL Query: " . $query);
        
        $stmt = $db->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare statement failed");
            echo json_encode(['success' => false, 'message' => 'Database prepare failed']);
            exit;
        }
        
        // Bind parameters with logging
        $params = [
            ':resident_code' => $resident_code,
            ':full_name' => trim($_POST['full_name']),
            ':email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            ':birthdate' => !empty($_POST['birthdate']) ? $_POST['birthdate'] : null,
            ':address' => trim($_POST['address']),
            ':contact_number' => trim($_POST['contact_number']),
            ':family_count' => isset($_POST['family_count']) ? intval($_POST['family_count']) : 0,
            ':voter_status' => $_POST['voter_status'] ?? 'no',
            ':monthly_income' => isset($_POST['monthly_income']) ? floatval($_POST['monthly_income']) : 0,
            ':occupation' => !empty($_POST['occupation']) ? trim($_POST['occupation']) : null,
            ':registration_date' => $_POST['registration_date'] ?? date('Y-m-d H:i:s'),
            ':status' => $_POST['status'] ?? 'active'
        ];
        
        error_log("Parameters: " . print_r($params, true));
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        error_log("Executing query...");
        $result = $stmt->execute();
        
        if ($result) {
            $last_id = $db->lastInsertId();
            error_log("Resident added successfully. ID: " . $last_id);
            echo json_encode([
                'success' => true, 
                'message' => 'Resident added successfully',
                'resident_id' => $last_id
            ]);
        } else {
            error_log("Execute returned false");
            $errorInfo = $stmt->errorInfo();
            error_log("PDO error info: " . print_r($errorInfo, true));
            echo json_encode(['success' => false, 'message' => 'Failed to add resident - ' . ($errorInfo[2] ?? 'execute failed')]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

error_log("=== ADD RESIDENT END ===");
?>