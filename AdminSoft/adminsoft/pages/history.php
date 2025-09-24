<?php
include '../includes/header.php';

// Get request history
$history = [];
try {
    $query = "SELECT rh.*, r.request_code, a.username as admin_name, 
                     res.full_name as resident_name, dt.name as document_type
              FROM request_history rh
              JOIN request r ON rh.request_id = r.request_id
              JOIN admin a ON rh.admin_id = a.admin_id
              JOIN resident res ON r.resident_id = res.resident_id
              JOIN document_type dt ON r.document_type_id = dt.type_id
              ORDER BY rh.change_date DESC
              LIMIT 50";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $history = [];
}

// Get total count for pagination
$totalCount = count($history);
?>
<?php include '../includes/sidebar.php'; ?>

<!-- Main Content -->

    
    <!-- History Content -->
    <div id="history-content" class="content-section">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Request History</h3>
                <p>Track and manage all system requests</p>
            </div>
            
            <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
                <select class="form-control" id="history-status-filter" style="width: auto;">
                    <option value="all">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending</option>
                </select>
                
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span>Date Range:</span>
                    <input type="date" class="form-control" id="history-start-date" style="width: auto;">
                    <span>to</span>
                    <input type="date" class="form-control" id="history-end-date" style="width: auto;">
                </div>
                
                <button class="btn btn-primary" id="history-filter-button">Filter</button>
            </div>
            
            <div style="margin-bottom: 15px; color: var(--gray);">
                Showing <span id="history-start">1</span>-<span id="history-end"><?php echo min(10, $totalCount); ?></span> of <span id="history-total"><?php echo $totalCount; ?></span> requests
            </div>
            
            <div class="table-container">
                <table id="history-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Document Type</th>
                            <th>Resident</th>
                            <th>Action</th>
                            <th>Admin</th>
                            <th>Date</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $record): ?>
                        <tr data-status="<?php echo $record['new_status']; ?>">
                            <td><?php echo $record['request_code']; ?></td>
                            <td><?php echo $record['document_type']; ?></td>
                            <td>
                                <div><?php echo $record['resident_name']; ?></div>
                            </td>
                            <td>
                                <span class="status status-<?php echo strtolower($record['new_status'] ?? 'pending'); ?>">
                                    <?php echo ucfirst($record['action'] ?? 'Action'); ?>: <?php echo ucfirst($record['new_status'] ?? 'Pending'); ?>
                                </span>
                            </td>
                            <td><?php echo $record['admin_name']; ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($record['change_date'])); ?></td>
                            <td><?php echo $record['notes'] ?: 'No notes'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                <div style="color: var(--gray);">
                    Rows per page: 
                    <select id="history-rows" style="padding: 5px; border-radius: 4px; border: 1px solid var(--light-gray);">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="btn btn-outline" id="history-prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="btn btn-primary history-page" data-page="1">1</button>
                    <button class="btn btn-outline history-page" data-page="2">2</button>
                    <button class="btn btn-outline history-page" data-page="3">3</button>
                    <button class="btn btn-outline" id="history-next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/history.js"></script>
<?php include '../includes/footer.php'; ?>