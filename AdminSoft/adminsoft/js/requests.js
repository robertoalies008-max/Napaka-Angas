// Requests specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initRequests();
});

function initRequests() {
    // Filter functionality
    const requestSearch = document.getElementById('request-search');
    const statusFilter = document.getElementById('request-status-filter');
    
    if (requestSearch && statusFilter) {
        requestSearch.addEventListener('input', filterRequests);
        statusFilter.addEventListener('change', filterRequests);
    }
    
    // Modal functionality
    initModals();
    
    // Request actions
    initRequestActions();
}

function filterRequests() {
    const searchTerm = document.getElementById('request-search').value.toLowerCase();
    const statusFilter = document.getElementById('request-status-filter').value;
    const rows = document.querySelectorAll('#requests-table tbody tr');
    
    rows.forEach(row => {
        const resident = row.cells[1].textContent.toLowerCase();
        const documentType = row.cells[2].textContent.toLowerCase();
        const status = row.dataset.status;
        
        const matchesSearch = resident.includes(searchTerm) || documentType.includes(searchTerm);
        const matchesStatus = statusFilter === 'all' || status === statusFilter;
        
        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function initModals() {
    // Modal close functionality
    const closeModal = document.querySelector('.close');
    const modal = document.getElementById('details-modal');
    
    if (closeModal && modal) {
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    // Reject modal functionality
    const rejectModal = document.getElementById('reject-reason-modal');
    const closeRejectModal = document.getElementById('close-reject-modal');
    const cancelRejectBtn = document.getElementById('cancel-reject-btn');
    
    if (rejectModal && closeRejectModal && cancelRejectBtn) {
        closeRejectModal.addEventListener('click', closeRejectReasonModal);
        cancelRejectBtn.addEventListener('click', closeRejectReasonModal);
        
        window.addEventListener('click', (e) => {
            if (e.target === rejectModal) closeRejectReasonModal();
        });
    }
    
    // Reject reason select change
    const rejectReasonSelect = document.getElementById('reject-reason-select');
    const rejectReasonOther = document.getElementById('reject-reason-other');
    
    if (rejectReasonSelect && rejectReasonOther) {
        rejectReasonSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                rejectReasonOther.classList.remove('d-none');
            } else {
                rejectReasonOther.classList.add('d-none');
            }
        });
    }
}

function initRequestActions() {
    // Delegated event listeners for dynamic content
    document.addEventListener('click', (e) => {
        // Approve request
        if (e.target.closest('.approve-request')) {
            const button = e.target.closest('.approve-request');
            const requestId = button.dataset.id;
            updateRequestStatus(requestId, 'approved');
        }
        
        // Reject request
        else if (e.target.closest('.reject-request')) {
            const button = e.target.closest('.reject-request');
            const requestId = button.dataset.id;
            openRejectModal(requestId);
        }
        
        // Complete request
        else if (e.target.closest('.complete-request')) {
            const button = e.target.closest('.complete-request');
            const requestId = button.dataset.id;
            updateRequestStatus(requestId, 'completed');
        }
        
        // View request
        else if (e.target.closest('.view-request')) {
            const button = e.target.closest('.view-request');
            const requestId = button.dataset.id;
            viewRequest(requestId);
        }
    });
    
    // Confirm reject button
    const confirmRejectBtn = document.getElementById('confirm-reject-btn');
    if (confirmRejectBtn) {
        confirmRejectBtn.addEventListener('click', confirmReject);
    }
}

let pendingRejectRequestId = null;

function openRejectModal(requestId) {
    pendingRejectRequestId = requestId;
    const modal = document.getElementById('reject-reason-modal');
    const rejectReasonSelect = document.getElementById('reject-reason-select');
    const rejectReasonOther = document.getElementById('reject-reason-other');
    
    if (modal && rejectReasonSelect && rejectReasonOther) {
        rejectReasonSelect.value = rejectReasonSelect.options[0].value;
        rejectReasonOther.classList.add('d-none');
        rejectReasonOther.value = '';
        modal.style.display = 'flex';
    }
}

function closeRejectReasonModal() {
    const modal = document.getElementById('reject-reason-modal');
    if (modal) {
        modal.style.display = 'none';
    }
    pendingRejectRequestId = null;
}

function confirmReject() {
    const rejectReasonSelect = document.getElementById('reject-reason-select');
    const rejectReasonOther = document.getElementById('reject-reason-other');
    
    let reason = rejectReasonSelect.value;
    if (reason === 'Other') {
        reason = rejectReasonOther.value.trim() || 'No reason specified';
    }
    
    if (pendingRejectRequestId) {
        updateRequestStatus(pendingRejectRequestId, 'rejected', reason);
    }
    closeRejectReasonModal();
}

function updateRequestStatus(requestId, newStatus, reason = '') {
    // Send AJAX request to update status
    const formData = new FormData();
    formData.append('request_id', requestId);
    formData.append('status', newStatus);
    formData.append('reason', reason);
    
    fetch('../ajax/update_request.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Request ${requestId} has been ${newStatus}`, 'success');
            // Reload the page to reflect changes
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification('Error updating request: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error updating request', 'error');
        console.error('Error:', error);
    });
}

function viewRequest(requestId) {
    // Send AJAX request to get request details
    fetch(`../ajax/get_request.php?id=${requestId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = document.getElementById('details-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalBody = document.getElementById('modal-body');
            
            modalTitle.textContent = `Request Details: ${data.request.request_code}`;
            modalBody.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4>Request Information</h4>
                        <p><strong>Request ID:</strong> ${data.request.request_code}</p>
                        <p><strong>Resident:</strong> ${data.request.full_name}</p>
                        <p><strong>Document Type:</strong> ${data.request.document_type}</p>
                        <p><strong>Purpose:</strong> ${data.request.purpose}</p>
                        <p><strong>Date:</strong> ${new Date(data.request.request_date).toLocaleDateString()}</p>
                        <p><strong>Status:</strong> <span class="status status-${data.request.status}">${data.request.status.charAt(0).toUpperCase() + data.request.status.slice(1)}</span></p>
                    </div>
                    <div>
                        <h4>Actions</h4>
                        <div style="margin-top: 20px;">
                            <button class="btn btn-primary"><i class="fas fa-print"></i> Print Document</button>
                            ${data.request.status === 'pending' ? `
                                <button class="btn btn-success approve-request" data-id="${data.request.request_id}"><i class="fas fa-check"></i> Approve</button>
                                <button class="btn btn-danger reject-request" data-id="${data.request.request_id}"><i class="fas fa-times"></i> Reject</button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
            modal.style.display = 'flex';
        } else {
            showNotification('Error loading request details', 'error');
        }
    })
    .catch(error => {
        showNotification('Error loading request details', 'error');
        console.error('Error:', error);
    });
}

function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');
    
    if (notification && notificationMessage) {
        notification.className = `notification ${type} show`;
        notificationMessage.textContent = message;
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
}