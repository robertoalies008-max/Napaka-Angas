<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/header.php';

// Get approved requests for dropdown
$approved_requests = [];
try {
    $query = "SELECT r.request_id, r.request_code, res.full_name, dt.name as document_type, r.purpose
              FROM request r 
              JOIN resident res ON r.resident_id = res.resident_id 
              JOIN document_type dt ON r.document_type_id = dt.type_id 
              WHERE r.STATUS = 'approved'
              ORDER BY r.request_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $approved_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching approved requests: " . $e->getMessage());
}

// Get recently generated documents
$recent_documents = [];
try {
    $query = "SELECT d.*, r.request_code, dt.name as document_type, res.full_name
              FROM document d
              JOIN request r ON d.request_id = r.request_id
              JOIN document_type dt ON r.document_type_id = dt.type_id
              JOIN resident res ON r.resident_id = res.resident_id
              ORDER BY d.generation_date DESC 
              LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $recent_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching recent documents: " . $e->getMessage());
}
?>
<?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->

    <!-- Documents Content -->
    <div id="documents-content" class="content-section">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Document Generator</h3>
                <p>Generate official barangay documents for approved requests</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <!-- Left Column - Document Generation Form -->
                <div>
                    <h4>Generate Document</h4>
                    
                    <form id="document-form">
                        <div class="form-group">
                            <label for="request-select">Select Approved Request *</label>
                            <select id="request-select" name="request_id" class="form-control" required>
                                <option value="">Choose an approved request...</option>
                                <?php foreach ($approved_requests as $request): ?>
                                    <option value="<?php echo $request['request_id']; ?>" 
                                            data-resident="<?php echo htmlspecialchars($request['full_name']); ?>"
                                            data-document-type="<?php echo htmlspecialchars($request['document_type']); ?>"
                                            data-purpose="<?php echo htmlspecialchars($request['purpose']); ?>">
                                        <?php echo $request['request_code'] . ' - ' . $request['full_name'] . ' - ' . $request['document_type']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Request Details -->
                        <div id="request-details" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;">
                            <h5>Request Details</h5>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 30%; padding: 5px 0;"><strong>Resident:</strong></td>
                                    <td style="padding: 5px 0;" id="detail-resident">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Document Type:</strong></td>
                                    <td style="padding: 5px 0;" id="detail-document-type">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Purpose:</strong></td>
                                    <td style="padding: 5px 0;" id="detail-purpose">-</td>
                                </tr>
                            </table>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="generate-btn">
                            <i class="fas fa-file-download"></i> Generate Document
                        </button>
                    </form>
                </div>
                
                <!-- Right Column - Document Preview -->
                <div>
                    <h4>Document Preview</h4>
                    <div id="document-preview" style="background: #f8f9fa; padding: 20px; border-radius: 5px; min-height: 200px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                        <div style="text-align: center;">
                            <i class="fas fa-file-alt" style="font-size: 48px; margin-bottom: 10px;"></i>
                            <p>Select a request to generate a document</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recently Generated Documents -->
        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h3 class="card-title">Recently Generated</h3>
            </div>
            <div class="table-container">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Document ID</th>
                            <th>Request Code</th>
                            <th>Document Type</th>
                            <th>Resident</th>
                            <th>Generated Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_documents)): ?>
                            <?php foreach ($recent_documents as $doc): ?>
                                <tr>
                                    <td>DOC-<?php echo $doc['document_id']; ?></td>
                                    <td><?php echo $doc['request_code']; ?></td>
                                    <td><?php echo $doc['document_type']; ?></td>
                                    <td><?php echo $doc['full_name']; ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($doc['generation_date'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn action-btn download-document" data-id="<?php echo $doc['document_id']; ?>">
                                                <i class="fas fa-download"></i> Download
                                            </button>
                                            <button class="btn action-btn view-document" data-id="<?php echo $doc['document_id']; ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">
                                    No documents generated yet
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Show request details when a request is selected
document.getElementById('request-select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const detailsDiv = document.getElementById('request-details');
    
    if (this.value) {
        detailsDiv.style.display = 'block';
        document.getElementById('detail-resident').textContent = selectedOption.getAttribute('data-resident');
        document.getElementById('detail-document-type').textContent = selectedOption.getAttribute('data-document-type');
        document.getElementById('detail-purpose').textContent = selectedOption.getAttribute('data-purpose');
        
        // Update preview
        document.getElementById('document-preview').innerHTML = `
            <div style="text-align: center;">
                <h4>${selectedOption.getAttribute('data-document-type')}</h4>
                <p><strong>Resident:</strong> ${selectedOption.getAttribute('data-resident')}</p>
                <p><strong>Purpose:</strong> ${selectedOption.getAttribute('data-purpose')}</p>
                <p style="color: #28a745;"><i class="fas fa-check-circle"></i> Ready to generate</p>
            </div>
        `;
    } else {
        detailsDiv.style.display = 'none';
        document.getElementById('document-preview').innerHTML = `
            <div style="text-align: center;">
                <i class="fas fa-file-alt" style="font-size: 48px; margin-bottom: 10px;"></i>
                <p>Select a request to generate a document</p>
            </div>
        `;
    }
});

// Handle form submission
document.getElementById('document-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const requestId = document.getElementById('request-select').value;
    if (!requestId) {
        alert('Please select an approved request');
        return;
    }
    
    const generateBtn = document.getElementById('generate-btn');
    const originalText = generateBtn.innerHTML;
    
    // Show loading state
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    generateBtn.disabled = true;
    
    // Submit via AJAX
    fetch('../ajax/generate_document.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'request_id=' + requestId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Document generated successfully!');
            location.reload(); // Reload to show new document
        } else {
            alert('Error generating document: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error generating document: ' + error.message);
    })
    .finally(() => {
        generateBtn.innerHTML = originalText;
        generateBtn.disabled = false;
    });
});
</script>

<?php include '../includes/footer.php'; ?>