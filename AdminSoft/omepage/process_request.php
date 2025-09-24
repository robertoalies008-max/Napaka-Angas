<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $documentTypeId = $_POST['documentTypeId'] ?? '';
    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $contactNumber = $_POST['contactNumber'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    
    try {
        // Generate a unique resident code
        $residentCode = 'RES-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Insert resident data
        $stmt = $pdo->prepare("INSERT INTO resident (resident_code, full_name, email, address, contact_number) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$residentCode, $fullName, $email, $address, $contactNumber]);
        $residentId = $pdo->lastInsertId();
        
        // Generate a unique request code
        $requestCode = 'REG-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Insert request data
        $stmt = $pdo->prepare("INSERT INTO request (request_code, resident_id, document_type_id, purpose) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$requestCode, $residentId, $documentTypeId, $purpose]);
        
        // Create notification
        $stmt = $pdo->prepare("INSERT INTO notification (resident_id, request_id, recipient_email, subject, message) 
                              VALUES (?, ?, ?, ?, ?)");
        $subject = "Document Request Received";
        $message = "Dear $fullName, your document request ($requestCode) has been received and is being processed.";
        $stmt->execute([$residentId, $pdo->lastInsertId(), $email, $subject, $message]);
        
        // Success message
        echo "<script>
            alert('Your document request has been submitted successfully! Your request code is: $requestCode');
            window.location.href = 'index.php';
        </script>";
        
    } catch (PDOException $e) {
        error_log("Error processing request: " . $e->getMessage());
        echo "<script>
            alert('There was an error processing your request. Please try again.');
            window.history.back();
        </script>";
    }
} else {
    header('Location: index.php');
    exit;
}
?>