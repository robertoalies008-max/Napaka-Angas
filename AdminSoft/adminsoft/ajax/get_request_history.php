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
    $statusFilter = $_GET['status'] ?? 'all';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 10;
    $offset = ($page - 1) * $limit;
    
    try {
        $whereConditions = [];
        $params = [];
        
        if ($statusFilter !== 'all') {
            $whereConditions[] = 'rh.new_status = :status';
            $params[':status'] = $statusFilter;
        }
        
        if (!empty($startDate)) {
            $whereConditions[] = 'DATE(rh.change_date) >= :start_date';
            $params[':start_date'] = $startDate;
        }
        
        if (!empty($endDate)) {
            $whereConditions[] = 'DATE(rh.change_date) <= :end_date';
            $params[':end_date'] = $endDate;
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $query = "SELECT rh.*, r.request_code, a.username as admin_name, 
                         res.full_name as resident_name, dt.name as document_type
                  FROM request_history rh
                  JOIN request r ON rh.request_id = r.request_id
                  JOIN admin a ON rh.admin_id = a.admin_id
                  JOIN resident res ON r.resident_id = res.resident_id
                  JOIN document_type dt ON r.document_type_id = dt.type_id
                  $whereClause
                  ORDER BY rh.change_date DESC
                  LIMIT :limit OFFSET :offset";
                  
        $countQuery = "SELECT COUNT(*) as total
                      FROM request_history rh
                      JOIN request r ON rh.request_id = r.request_id
                      $whereClause";
        
        $stmt = $db->prepare($query);
        $countStmt = $db->prepare($countQuery);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
            $countStmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $countStmt->execute();
        
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode([
            'success' => true,
            'history' => $history,
            'pagination' => [
                'page' => (int)$page,
                'limit' => (int)$limit,
                'total' => (int)$total,
                'pages' => ceil($total / $limit)
            ]
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>