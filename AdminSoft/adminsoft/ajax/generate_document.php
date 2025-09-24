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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['request_id']) || !isset($_POST['document_type_id'])) {
        echo json_encode(['success' => false, 'message' => 'Request ID and document type are required']);
        exit;
    }
    
    try {
        // Get request details
        $requestQuery = "SELECT r.*, res.*, dt.name as document_type
                        FROM request r
                        JOIN resident res ON r.resident_id = res.resident_id
                        JOIN document_type dt ON r.document_type_id = dt.type_id
                        WHERE r.request_id = :request_id";
        $requestStmt = $db->prepare($requestQuery);
        $requestStmt->bindValue(':request_id', $_POST['request_id']);
        $requestStmt->execute();
        $request = $requestStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$request) {
            echo json_encode(['success' => false, 'message' => 'Request not found']);
            exit;
        }
        
        // Generate PDF document (this is a simplified version)
        $filename = 'document_' . $request['request_code'] . '_' . date('YmdHis') . '.pdf';
        $filepath = '../documents/' . $filename;
        
        // Create documents directory if it doesn't exist
        if (!is_dir('../documents')) {
            mkdir('../documents', 0755, true);
        }
        
        // For now, we'll just create a placeholder file
        // In a real application, you would use a PDF library like TCPDF or Dompdf
        $documentContent = "BARANGAY DOCUMENT\n\n";
        $documentContent .= "Request Code: " . $request['request_code'] . "\n";
        $documentContent .= "Resident: " . $request['full_name'] . "\n";
        $documentContent .= "Document Type: " . $request['document_type'] . "\n";
        $documentContent .= "Purpose: " . $request['purpose'] . "\n";
        $documentContent .= "Date Issued: " . date('Y-m-d H:i:s') . "\n";
        $documentContent .= "Issued By: " . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . "\n";
        
        file_put_contents($filepath, $documentContent);
        
        // Update request status to completed
        $updateQuery = "UPDATE request SET 
                       status = 'completed',
                       processed_by = :admin_id,
                       processed_date = NOW()
                       WHERE request_id = :request_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindValue(':request_id', $_POST['request_id']);
        $updateStmt->bindValue(':admin_id', $_SESSION['admin_id']);
        $updateStmt->execute();
        
        // Create document record
        $documentQuery = "INSERT INTO document SET 
                         request_id = :request_id,
                         file_name = :file_name,
                         file_path = :file_path,
                         file_size = :file_size,
                         generated_by = :admin_id";
        $documentStmt = $db->prepare($documentQuery);
        $documentStmt->bindValue(':request_id', $_POST['request_id']);
        $documentStmt->bindValue(':file_name', $filename);
        $documentStmt->bindValue(':file_path', $filepath);
        $documentStmt->bindValue(':file_size', filesize($filepath));
        $documentStmt->bindValue(':admin_id', $_SESSION['admin_id']);
        $documentStmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Document generated successfully',
            'file_path' => $filepath,
            'file_name' => $filename
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>