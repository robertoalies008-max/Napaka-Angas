<?php
include '../includes/header.php';

// Get requests data
$requests = [];
try {
    $query = "SELECT r.*, res.full_name, dt.name as document_type 
              FROM request r 
              JOIN resident res ON r.resident_id = res.resident_id 
              JOIN document_type dt ON r.document_type_id = dt.type_id 
              ORDER BY r.request_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $requests = [];
}

// Get stats
$stats = [];
try {
    // Approved today
    $query = "SELECT COUNT(*) as count FROM request WHERE DATE(processed_date) = CURDATE() AND STATUS = 'approved'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['approved_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total this month
    $query = "SELECT COUNT(*) as count FROM request WHERE MONTH(request_date) = MONTH(CURDATE()) AND YEAR(request_date) = YEAR(CURDATE())";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['total_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Average processing time (in hours)
    $query = "SELECT AVG(TIMESTAMPDIFF(HOUR, request_date, processed_date)) as avg_time FROM request WHERE STATUS = 'completed'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['avg_time'] = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_time'] ?? 2.5, 1);
} catch (PDOException $e) {
    $stats = ['approved_today' => 0, 'total_month' => 0, 'avg_time' => 2.5];
}
?>
<?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->

    <!-- Requests Content -->
    <div id="requests-content" class="content-section">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Document Requests</h3>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div style="background: #e8f4ff; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 14px; color: var(--gray);">Approved Today</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--primary);" id="approved-today"><?php echo $stats['approved_today']; ?></div>
                </div>
                <div style="background: #e8f4ff; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 14px; color: var(--gray);">Total This Month</div>
                    <div style="font-size: 24px; font-weight:700; color: var(--primary);" id="total-month"><?php echo $stats['total_month']; ?></div>
                </div>
                <div style="background: #e8f4ff; padding: 15px; border-radius: 8px;">
                    <div style="font-size: 14px; color: var(--gray);">Avg. Processing Time</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--primary);" id="processing-time"><?php echo $stats['avg_time']; ?>h</div>
                </div>
            </div>
            
            <div class="card-header">
                <h3 class="card-title">Recent Requests</h3>
                <div style="display: flex; gap: 15px;">
                    <select class="form-control" id="request-status-filter" style="width: auto;">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <input type="text" class="form-control" id="request-search" placeholder="Search..." style="width: 200px;">
                </div>
            </div>
            
            <!-- Recent Requests Table -->
            <div class="table-container">
                <table id="requests-table" class="requests-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Resident</th>
                            <th>Document Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                        <?php
                            // Safe array access for request status
                            $status = $request['STATUS'] ?? 'pending';
                            $status_class = strtolower($status);
                        ?>
                        <tr>
                            <td><?php echo $request['request_code']; ?></td>
                            <td><?php echo $request['full_name']; ?></td>
                            <td><?php echo $request['document_type']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($request['request_date'])); ?></td>
                            <td>
                                <span class="status status-<?php echo $status_class; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($status == 'pending'): ?>
                                        <button class="btn btn-success action-btn approve-request" data-id="<?php echo $request['request_id']; ?>">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger action-btn reject-request" data-id="<?php echo $request['request_id']; ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php elseif ($status == 'approved'): ?>
                                        <button class="btn action-btn complete-request" data-id="<?php echo $request['request_id']; ?>">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn action-btn view-request" data-id="<?php echo $request['request_id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="details-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Request Details</h3>
            <span class="close">&times;</span>
        </div>
        <div id="modal-body">
            <!-- Content will be loaded here dynamically -->
        </div>
    </div>
</div>

<div id="reject-reason-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Reject Request</h3>
            <span class="close" id="close-reject-modal">&times;</span>
        </div>
        <div>
            <label for="reject-reason-select"><strong>Select Reason for Rejection:</strong></label>
            <select id="reject-reason-select" class="form-control" style="margin: 15px 0;">
                <option value="No complete information in the database">No complete information in the database</option>
                <option value="Invalid document type requested">Invalid document type requested</option>
                <option value="Duplicate request">Duplicate request</option>
                <option value="Resident not found">Resident not found</option>
                <option value="Other">Other (please specify below)</option>
            </select>
            <textarea id="reject-reason-other" class="form-control d-none" placeholder="Specify other reason..." rows="2"></textarea>
        </div>
        <div class="modal-footer" style="margin-top: 20px; text-align: right;">
            <button class="btn btn-outline" id="cancel-reject-btn">Cancel</button>
            <button class="btn btn-danger" id="confirm-reject-btn">Confirm Reject</button>
        </div>
    </div>
</div>

<script src="../js/requests.js"></script>
<?php include '../includes/footer.php'; ?>