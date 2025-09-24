<?php
require_once '../config/database.php';
require_once '../config/auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Request ID is required']);
        exit;
    }
    
    try {
        $query = "SELECT r.*, res.full_name, res.contact_number, res.email, 
                         dt.name as document_type, dt.description,
                         a.username as processed_by_name
                  FROM request r
                  JOIN resident res ON r.resident_id = res.resident_id
                  JOIN document_type dt ON r.document_type_id = dt.type_id
                  LEFT JOIN admin a ON r.processed_by = a.admin_id
                  WHERE r.request_id = :request_id";
                  
        $stmt = $db->prepare($query);
        $stmt->bindValue(':request_id', $_GET['id']);
        $stmt->execute();
        
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Get request history
            $historyQuery = "SELECT rh.*, a.username as admin_name 
                            FROM request_history rh
                            JOIN admin a ON rh.admin_id = a.admin_id
                            WHERE rh.request_id = :request_id
                            ORDER BY rh.change_date DESC";
            $historyStmt = $db->prepare($historyQuery);
            $historyStmt->bindValue(':request_id', $_GET['id']);
            $historyStmt->execute();
            $history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'request' => $request,
                'history' => $history
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>