<?php
// Database connection configuration
$host = 'localhost';
$dbname = 'barangayhub';
$username = 'root'; // Change if needed
$password = ''; // Change if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to get document types from database
function getDocumentTypes($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM document_type");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching document types: " . $e->getMessage());
        return [];
    }
}
?>