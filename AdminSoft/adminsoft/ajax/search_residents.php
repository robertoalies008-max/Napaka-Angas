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
    $searchTerm = $_GET['q'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = $_GET['limit'] ?? 10;
    $offset = ($page - 1) * $limit;
    
    try {
        $query = "SELECT * FROM resident 
                 WHERE (full_name LIKE :search OR resident_code LIKE :search OR address LIKE :search)
                 AND status = 'active'
                 ORDER BY full_name ASC
                 LIMIT :limit OFFSET :offset";
                 
        $countQuery = "SELECT COUNT(*) as total FROM resident 
                      WHERE (full_name LIKE :search OR resident_code LIKE :search OR address LIKE :search)
                      AND status = 'active'";
        
        $stmt = $db->prepare($query);
        $countStmt = $db->prepare($countQuery);
        
        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindValue(':search', $searchParam);
        $countStmt->bindValue(':search', $searchParam);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $countStmt->execute();
        
        $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode([
            'success' => true,
            'residents' => $residents,
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