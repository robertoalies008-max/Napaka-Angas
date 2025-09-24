<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Resident ID is required']);
        exit;
    }
    
    $resident_id = $_GET['id'];
    
    try {
        $query = "SELECT * FROM resident WHERE resident_id = :resident_id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':resident_id', $resident_id);
        $stmt->execute();
        
        $resident = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resident) {
            echo json_encode([
                'success' => true,
                'resident' => $resident
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Resident not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>