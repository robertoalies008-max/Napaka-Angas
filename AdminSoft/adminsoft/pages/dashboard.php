<?php 
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// Get real statistics from database with safe fallback
$stats = [
    'total_residents' => 0, 
    'total_requests' => 0, 
    'completed_documents' => 0, 
    'pending_requests' => 0
];

// Only query database if connection exists
if ($db) {
    try {
        // Total residents
     $query = "SELECT COUNT(*) as total FROM resident WHERE status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['total_residents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total requests
       $query = "SELECT COUNT(*) as total FROM request";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['total_requests'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Completed documents
        $query = "SELECT COUNT(*) as total FROM request WHERE status = 'completed'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['completed_documents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pending requests
        $query = "SELECT COUNT(*) as total FROM request WHERE status = 'pending'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['pending_requests'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
    } catch (PDOException $e) {
        // Use demo data if there's an error
        error_log("Dashboard stats error: " . $e->getMessage());
        $stats = [
            'total_residents' => 5, 
            'total_requests' => 5, 
            'completed_documents' => 1, 
            'pending_requests' => 3
        ];
    }
} else {
    // Use demo data if no database connection
    $stats = [
        'total_residents' => 5, 
        'total_requests' => 5, 
        'completed_documents' => 1, 
        'pending_requests' => 3
    ];
}
?>

<!-- Main Content -->

    <!-- Dashboard Content -->
    <div id="dashboard-content" class="content-section">
        <?php if (!$db): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i>
                Database connection failed. Showing demo data.
            </div>
        <?php endif; ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Total Requests</div>
                <div class="stat-value" id="total-requests"><?php echo $stats['total_requests']; ?></div>
                <div style="color: var(--success); font-size: 14px;">
                    <i class="fas fa-arrow-up"></i> <span id="requests-change">12%</span> from last month
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Residents</div>
                <div class="stat-value" id="total-residents"><?php echo $stats['total_residents']; ?></div>
                <div style="color: var(--success); font-size: 14px;">
                    <i class="fas fa-arrow-up"></i> <span id="residents-change">5%</span> from last month
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Documents Issued</div>
                <div class="stat-value" id="total-documents"><?php echo $stats['completed_documents']; ?></div>
                <div style="color: var(--success); font-size: 14px;">
                    <i class="fas fa-arrow-up"></i> <span id="documents-change">8%</span> from last month
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending Requests</div>
                <div class="stat-value" id="pending-requests"><?php echo $stats['pending_requests']; ?></div>
                <div style="color: var(--warning); font-size: 14px;">
                    <i class="fas fa-clock"></i> Need attention
                </div>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-card">
                <h3>Weekly Requests</h3>
                <canvas id="weeklyChart" height="250"></canvas>
            </div>
            
            <div class="chart-card">
                <h3>Monthly Overview</h3>
                <canvas id="monthlyChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/dashboard.js"></script>
<?php include '../includes/footer.php'; ?>