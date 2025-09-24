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

try {
    // Total residents
    $residentQuery = "SELECT COUNT(*) as total FROM resident WHERE status = 'active'";
    $residentStmt = $db->prepare($residentQuery);
    $residentStmt->execute();
    $totalResidents = $residentStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total requests
    $requestQuery = "SELECT COUNT(*) as total FROM request";
    $requestStmt = $db->prepare($requestQuery);
    $requestStmt->execute();
    $totalRequests = $requestStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Completed documents
    $completedQuery = "SELECT COUNT(*) as total FROM request WHERE status = 'completed'";
    $completedStmt = $db->prepare($completedQuery);
    $completedStmt->execute();
    $completedDocuments = $completedStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Pending requests
    $pendingQuery = "SELECT COUNT(*) as total FROM request WHERE status = 'pending'";
    $pendingStmt = $db->prepare($pendingQuery);
    $pendingStmt->execute();
    $pendingRequests = $pendingStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Weekly requests data
    $weeklyQuery = "SELECT 
                    DAYNAME(request_date) as day,
                    COUNT(*) as count
                    FROM request 
                    WHERE request_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    GROUP BY DAYNAME(request_date)
                    ORDER BY request_date";
    $weeklyStmt = $db->prepare($weeklyQuery);
    $weeklyStmt->execute();
    $weeklyData = $weeklyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Monthly overview by document type
    $monthlyQuery = "SELECT 
                    dt.name as document_type,
                    COUNT(*) as count
                    FROM request r
                    JOIN document_type dt ON r.document_type_id = dt.type_id
                    WHERE MONTH(r.request_date) = MONTH(NOW())
                    AND YEAR(r.request_date) = YEAR(NOW())
                    GROUP BY dt.name";
    $monthlyStmt = $db->prepare($monthlyQuery);
    $monthlyStmt->execute();
    $monthlyData = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent activities
    $recentQuery = "SELECT 
                   r.request_code,
                   res.full_name,
                   dt.name as document_type,
                   r.status,
                   r.request_date
                   FROM request r
                   JOIN resident res ON r.resident_id = res.resident_id
                   JOIN document_type dt ON r.document_type_id = dt.type_id
                   ORDER BY r.request_date DESC
                   LIMIT 5";
    $recentStmt = $db->prepare($recentQuery);
    $recentStmt->execute();
    $recentActivities = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_residents' => (int)$totalResidents,
            'total_requests' => (int)$totalRequests,
            'completed_documents' => (int)$completedDocuments,
            'pending_requests' => (int)$pendingRequests
        ],
        'weekly_data' => $weeklyData,
        'monthly_data' => $monthlyData,
        'recent_activities' => $recentActivities
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>