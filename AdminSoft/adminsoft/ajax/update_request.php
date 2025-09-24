<?php
session_start(); // Add this at the top
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

// TEMPORARILY REMOVE AUTHENTICATION CHECK
// if (!isset($_SESSION['admin_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Not authenticated']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['request_id']) || !isset($_POST['status'])) {
        echo json_encode(['success' => false, 'message' => 'Request ID and status are required']);
        exit;
    }
    
    try {
        // Get current request status
        $currentQuery = "SELECT status FROM request WHERE request_id = :request_id";
        $currentStmt = $db->prepare($currentQuery);
        $currentStmt->bindValue(':request_id', $_POST['request_id']);
        $currentStmt->execute();
        $currentRequest = $currentStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentRequest) {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
            exit;
        }
        
        // Update request
        $query = "UPDATE request SET 
                  status = :status,
                  processed_by = :admin_id,
                  processed_date = NOW(),
                  updated_at = NOW()
                  WHERE request_id = :request_id";
                  
        $stmt = $db->prepare($query);
        
        $stmt->bindValue(':request_id', $_POST['request_id']);
        $stmt->bindValue(':status', $_POST['status']);
        $stmt->bindValue(':admin_id', $_SESSION['admin_id']);
        
        if ($stmt->execute()) {
            // Log the status change
            $historyQuery = "INSERT INTO request_history SET 
                            request_id = :request_id,
                            admin_id = :admin_id,
                            action = 'status_change',
                            old_status = :old_status,
                            new_status = :new_status,
                            notes = :notes";
            
            $historyStmt = $db->prepare($historyQuery);
            $historyStmt->bindValue(':request_id', $_POST['request_id']);
            $historyStmt->bindValue(':admin_id', $_SESSION['admin_id']);
            $historyStmt->bindValue(':old_status', $currentRequest['status']);
            $historyStmt->bindValue(':new_status', $_POST['status']);
            $historyStmt->bindValue(':notes', $_POST['reason'] ?? 'Status updated by admin');
            $historyStmt->execute();
            
            // If status is completed, create a document record
            if ($_POST['status'] === 'completed') {
                $documentQuery = "INSERT INTO document SET 
                                request_id = :request_id,
                                file_name = :file_name,
                                file_path = :file_path,
                                generated_by = :admin_id";
                
                $documentStmt = $db->prepare($documentQuery);
                $documentStmt->bindValue(':request_id', $_POST['request_id']);
                $documentStmt->bindValue(':file_name', 'document_' . $_POST['request_id'] . '.pdf');
                $documentStmt->bindValue(':file_path', '/documents/document_' . $_POST['request_id'] . '.pdf');
                $documentStmt->bindValue(':admin_id', $_SESSION['admin_id']);
                $documentStmt->execute();
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Request updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update request']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>