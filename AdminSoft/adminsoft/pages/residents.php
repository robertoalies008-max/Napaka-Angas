<?php
require_once '../includes/header.php';

// Get residents data
$residents = [];
try {
    $query = "SELECT * FROM resident ORDER BY registration_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $residents = [];
}
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-section">
    <!-- Residents List Card -->
    <div class="card" id="residents-list-card">
        <div class="card-header">
            <h3 class="card-title">Residents Management</h3>
            <div>
                <button class="btn btn-primary" id="add-resident-button">
                    <i class="fas fa-plus"></i> Add New Resident
                </button>
                <button class="btn btn-outline" id="export-residents">
                    <i class="fas fa-file-export"></i> Export CSV
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <p style="margin-bottom: 20px;">Manage resident information and records</p>
            
            <div style="margin-bottom: 20px;">
                <input type="text" class="form-control" id="resident-search" placeholder="Search residents by name, ID, or address">
            </div>
            
            <div class="table-container">
                <table id="residents-table">
                    <thead>
                        <tr>
                            <th>Resident ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($residents as $resident): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resident['resident_code']); ?></td>
                            <td><?php echo htmlspecialchars($resident['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($resident['address']); ?></td>
                            <td><?php echo htmlspecialchars($resident['contact_number']); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower($resident['status'] ?? 'active'); ?>">
                                    <?php echo ucfirst($resident['status'] ?? 'Active'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn action-btn view-resident" data-id="<?php echo $resident['resident_id']; ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-danger action-btn delete-resident" data-id="<?php echo $resident['resident_id']; ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </body>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Resident Form Card -->
    <div class="card d-none" id="add-resident-form">
        <div class="card-header">
            <h3 class="card-title" id="resident-form-title">Add New Resident</h3>
            <button type="button" class="btn btn-secondary" id="cancel-resident-form">
                <i class="fas fa-arrow-left"></i> Back to List
            </button>
        </div>
        <div class="card-body">
            <form id="resident-form">
                <input type="hidden" id="resident-id" name="resident_id">
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="resident-code">Resident ID *</label>
                        <input type="text" class="form-control" id="resident-code" name="resident_code" required readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="date-registered">Date Registered *</label>
                        <input type="date" class="form-control" id="date-registered" name="registration_date" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="fullname">Full Name *</label>
                        <input type="text" class="form-control" id="fullname" name="full_name" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="birthdate">Birth Date</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="resident-email">Email Address</label>
                        <input type="email" class="form-control" id="resident-email" name="email">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact">Contact Number *</label>
                        <input type="text" class="form-control" id="contact" name="contact_number" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Complete Address *</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="family-count">Family Members</label>
                        <input type="number" class="form-control" id="family-count" name="family_count" min="0" value="0">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="income">Monthly Income (₱)</label>
                        <input type="number" class="form-control" id="income" name="monthly_income" min="0" step="0.01" value="0">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="occupation">Occupation</label>
                        <input type="text" class="form-control" id="occupation" name="occupation">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Voter Status</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="voter_status" id="voter-yes" value="yes">
                                <label class="form-check-label" for="voter-yes">Registered Voter</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="voter_status" id="voter-no" value="no" checked>
                                <label class="form-check-label" for="voter-no">Not Registered</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="resident-status">Status</label>
                        <select class="form-control" id="resident-status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary" id="save-resident-btn">
                        <i class="fas fa-save"></i> Save Resident
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancel-resident-form-2">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Resident Details Modal -->
<div id="details-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Resident Details</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body" id="modal-body">
            <!-- Resident details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="edit-resident-button">
                <i class="fas fa-edit"></i> Edit Resident
            </button>
            <button type="button" class="btn btn-secondary" id="close-modal-button">Close</button>
        </div>
    </div>
</div>

<script src="../js/residents.js"></script>
<?php include '../includes/footer.php'; ?>