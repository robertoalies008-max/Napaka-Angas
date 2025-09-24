<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <h1><i class="fas fa-home"></i> BarangayHub</h1>
    </div>
    
    <ul class="menu">
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li><a href="requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i> Requests</a></li>
        <li><a href="residents.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'residents.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Residents</a></li>
        <li><a href="documents.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>"><i class="fas fa-file-pdf"></i> Documents</a></li>
        <li><a href="history.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>"><i class="fas fa-history"></i> Request History</a></li>
        <li><a href="../pages/login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>