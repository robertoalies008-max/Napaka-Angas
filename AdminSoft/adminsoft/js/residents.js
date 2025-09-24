// Residents specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing residents...');
    initResidents();
});

function initResidents() {
    console.log('Initializing residents functionality...');
    
    // Form toggle functionality
    const addResidentButton = document.getElementById('add-resident-button');
    const cancelResidentForm = document.getElementById('cancel-resident-form');
    const cancelResidentForm2 = document.getElementById('cancel-resident-form-2');
    const residentForm = document.getElementById('resident-form');
    
    if (addResidentButton) {
        addResidentButton.addEventListener('click', function() {
            console.log('Add resident button clicked');
            showAddResidentForm();
        });
    } else {
        console.error('Add resident button not found!');
    }
    
    if (cancelResidentForm) {
        cancelResidentForm.addEventListener('click', showResidentsList);
    }
    
    if (cancelResidentForm2) {
        cancelResidentForm2.addEventListener('click', showResidentsList);
    }
    
    if (residentForm) {
        residentForm.addEventListener('submit', handleResidentSubmit);
        console.log('Form submission listener added');
    } else {
        console.error('Resident form not found!');
    }
    
    // Initialize form with default values
    resetResidentForm();
    
    // Search functionality
    const residentSearch = document.getElementById('resident-search');
    if (residentSearch) {
        residentSearch.addEventListener('input', filterResidents);
    }
    
    // Export functionality
    const exportButton = document.getElementById('export-residents');
    if (exportButton) {
        exportButton.addEventListener('click', exportResidents);
    }
    
    // Modal functionality
    initModals();
    
    // Resident actions
    initResidentActions();
    
    console.log('Residents functionality initialized successfully');
}

function showAddResidentForm(editId = null) {
    console.log('Showing add resident form, editId:', editId);
    
    const residentsListCard = document.getElementById('residents-list-card');
    const addResidentForm = document.getElementById('add-resident-form');
    const formTitle = document.getElementById('resident-form-title');
    
    if (residentsListCard && addResidentForm && formTitle) {
        residentsListCard.classList.add('d-none');
        addResidentForm.classList.remove('d-none');
        
        if (editId) {
            formTitle.textContent = 'Edit Resident';
            console.log('Loading data for edit, resident ID:', editId);
            loadResidentData(editId);
        } else {
            formTitle.textContent = 'Add New Resident';
            resetResidentForm();
        }
    } else {
        console.error('Required elements not found:', {
            residentsListCard: !!residentsListCard,
            addResidentForm: !!addResidentForm,
            formTitle: !!formTitle
        });
    }
}

function showResidentsList() {
    console.log('Showing residents list');
    
    const residentsListCard = document.getElementById('residents-list-card');
    const addResidentForm = document.getElementById('add-resident-form');
    
    if (residentsListCard && addResidentForm) {
        residentsListCard.classList.remove('d-none');
        addResidentForm.classList.add('d-none');
    }
}

function resetResidentForm() {
    const form = document.getElementById('resident-form');
    if (form) {
        form.reset();
        // Generate a unique resident code
        document.getElementById('resident-code').value = 'RES-' + String(new Date().getTime()).slice(-6);
        // Set today's date as registration date
        document.getElementById('date-registered').value = new Date().toISOString().split('T')[0];
        document.getElementById('resident-status').value = 'active';
        // Clear hidden resident ID
        document.getElementById('resident-id').value = '';
        
        // Set default voter status to 'no'
        const voterNoRadio = document.getElementById('voter-no');
        if (voterNoRadio) voterNoRadio.checked = true;
        
        console.log('Form reset successfully');
    }
}

function loadResidentData(residentId) {
    console.log('Loading resident data for ID:', residentId);
    
    const saveButton = document.getElementById('save-resident-btn');
    if (!saveButton) {
        console.error('Save button not found!');
        return;
    }
    
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    saveButton.disabled = true;

    const url = `../ajax/get_resident.php?id=${residentId}`;
    console.log('Fetching from:', url);
    
    fetch(url)
    .then(response => {
        console.log('Response status:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data received:', data);
        
        if (data.success && data.resident) {
            const resident = data.resident;
            console.log('Filling form with resident data:', resident);
            
            // Fill form fields
            document.getElementById('resident-id').value = resident.resident_id || '';
            document.getElementById('resident-code').value = resident.resident_code || '';
            document.getElementById('fullname').value = resident.full_name || '';
            document.getElementById('resident-email').value = resident.email || '';
            
            // Format dates properly
            if (resident.birthdate && resident.birthdate !== '0000-00-00') {
                document.getElementById('birthdate').value = resident.birthdate;
            }
            
            if (resident.registration_date) {
                const regDate = resident.registration_date.split(' ')[0];
                document.getElementById('date-registered').value = regDate;
            }
            
            document.getElementById('resident-status').value = resident.status || 'active';
            document.getElementById('address').value = resident.address || '';
            document.getElementById('contact').value = resident.contact_number || '';
            document.getElementById('family-count').value = resident.family_count || '';
            
            // Set voter status
            const voterStatus = resident.voter_status || 'no';
            document.querySelectorAll('input[name="voter_status"]').forEach(radio => {
                radio.checked = (radio.value === voterStatus);
            });
            
            document.getElementById('income').value = resident.monthly_income || '';
            document.getElementById('occupation').value = resident.occupation || '';
            
            console.log('Form filled successfully');
            showNotification('Resident data loaded successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to load resident data');
        }
    })
    .catch(error => {
        console.error('Error loading resident data:', error);
        showNotification('Error loading resident: ' + error.message, 'error');
    })
    .finally(() => {
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

function handleResidentSubmit(e) {
    e.preventDefault();
    console.log('Form submission started');
    
    const submitButton = e.target.querySelector('#save-resident-btn');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitButton.disabled = true;
    
    // Convert form data to URL-encoded format instead of FormData
    const formData = new FormData(e.target);
    const urlEncodedData = new URLSearchParams(formData).toString();
    
    // DEBUG: Check ALL form fields
    console.log('=== FORM DATA DEBUG ===');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    console.log('=== END FORM DATA ===');
    
    const residentId = formData.get('resident_id');
    const isEdit = residentId && residentId !== '';
    const url = isEdit ? '../ajax/update_resident.php' : '../ajax/add_resident.php';
    
    console.log('Submitting to:', url, 'isEdit:', isEdit, 'residentId:', residentId);
    
    // Set timeout for request
    const timeoutPromise = new Promise((_, reject) => {
        setTimeout(() => reject(new Error('Request timeout after 10 seconds')), 10000);
    });
    
    const fetchPromise = fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: urlEncodedData
    });
    
    // Race between fetch and timeout
    Promise.race([fetchPromise, timeoutPromise])
    .then(response => {
        console.log('Response status:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification(isEdit ? 'Resident updated successfully' : 'Resident added successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Server returned unsuccessful response');
        }
    })
    .catch(error => {
        console.error('Error submitting form:', error);
        showNotification('Error: ' + error.message, 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

function viewResident(residentId) {
    console.log('Viewing resident:', residentId);
    
    const viewButton = document.querySelector(`.view-resident[data-id="${residentId}"]`);
    if (!viewButton) {
        console.error('View button not found for resident:', residentId);
        return;
    }
    
    const originalText = viewButton.innerHTML;
    viewButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    viewButton.disabled = true;

    const url = `../ajax/get_resident.php?id=${residentId}`;
    console.log('Fetching resident details from:', url);
    
    fetch(url)
    .then(response => {
        console.log('Response status:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data received');
        if (data.success) {
            const resident = data.resident;
            const modal = document.getElementById('details-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalBody = document.getElementById('modal-body');
            const editButton = document.getElementById('edit-resident-button');
            
            if (!modal || !modalTitle || !modalBody) {
                throw new Error('Modal elements not found');
            }
            
            modalTitle.textContent = `Resident Details: ${resident.full_name}`;
            
            const birthdate = resident.birthdate && resident.birthdate !== '0000-00-00' 
                ? resident.birthdate 
                : 'Not provided';
                
            const registrationDate = resident.registration_date 
                ? resident.registration_date.split(' ')[0]
                : 'Not provided';
            
            modalBody.innerHTML = `
                <div class="resident-details">
                    <div class="detail-section">
                        <h4>Basic Information</h4>
                        <div class="detail-group">
                            <div class="detail-label">Resident ID</div>
                            <div>${resident.resident_code || 'N/A'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Full Name</div>
                            <div>${resident.full_name || 'N/A'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Email Address</div>
                            <div>${resident.email || 'Not provided'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Birth Date</div>
                            <div>${birthdate}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Occupation</div>
                            <div>${resident.occupation || 'Not specified'}</div>
                        </div>
                    </div>
                    <div class="detail-section">
                        <h4>Address & Contact Information</h4>
                        <div class="detail-group">
                            <div class="detail-label">Complete Address</div>
                            <div>${resident.address || 'N/A'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Contact Number</div>
                            <div>${resident.contact_number || 'N/A'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Family Members</div>
                            <div>${resident.family_count || '0'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Monthly Income</div>
                            <div>₱${parseFloat(resident.monthly_income || 0).toLocaleString()}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Voter Status</div>
                            <div>${resident.voter_status === 'yes' ? 'Registered Voter' : 'Not Registered'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Registration Date</div>
                            <div>${registrationDate}</div>
                        </div>
                    </div>
                </div>
            `;
            
            if (editButton) {
                editButton.dataset.id = residentId;
                editButton.style.display = 'inline-block';
                console.log('Edit button set with resident ID:', residentId);
            } else {
                console.error('Edit button not found in modal!');
            }
            
            modal.style.display = 'flex';
            console.log('Modal displayed successfully');
        } else {
            throw new Error(data.message || 'Failed to load resident details');
        }
    })
    .catch(error => {
        console.error('Error loading resident details:', error);
        showNotification('Error: ' + error.message, 'error');
    })
    .finally(() => {
        viewButton.innerHTML = originalText;
        viewButton.disabled = false;
    });
}

function deleteResident(residentId) {
    if (confirm('Are you sure you want to delete this resident? This action cannot be undone.')) {
        const deleteButton = document.querySelector(`.delete-resident[data-id="${residentId}"]`);
        const originalText = deleteButton.innerHTML;
        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        deleteButton.disabled = true;
        
        const url = '../ajax/delete_resident.php';
        console.log('Deleting resident via:', url);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `resident_id=${residentId}`
        })
        .then(response => {
            console.log('Delete response status:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data);
            if (data.success) {
                showNotification('Resident deleted successfully', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to delete resident');
            }
        })
        .catch(error => {
            console.error('Error deleting resident:', error);
            showNotification('Error: ' + error.message, 'error');
            deleteButton.innerHTML = originalText;
            deleteButton.disabled = false;
        });
    }
}

function initModals() {
    const closeModal = document.querySelector('.close');
    const closeModalButton = document.getElementById('close-modal-button');
    const modal = document.getElementById('details-modal');
    
    if (closeModal && modal) {
        closeModal.addEventListener('click', closeModalFunc);
    }
    
    if (closeModalButton && modal) {
        closeModalButton.addEventListener('click', closeModalFunc);
    }
    
    if (modal) {
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModalFunc();
            }
        });
    }
    
    const editResidentButton = document.getElementById('edit-resident-button');
    if (editResidentButton) {
        editResidentButton.addEventListener('click', function() {
            console.log('Edit resident button clicked in modal');
            editResidentFromModal();
        });
    } else {
        console.error('Edit resident button not found in modal!');
    }
}

function initResidentActions() {
    console.log('Initializing resident actions...');
    
    // Test if buttons exist
    const viewButtons = document.querySelectorAll('.view-resident');
    const deleteButtons = document.querySelectorAll('.delete-resident');
    
    console.log('Found view buttons:', viewButtons.length);
    console.log('Found delete buttons:', deleteButtons.length);
    
    document.addEventListener('click', (e) => {
        console.log('Click event on:', e.target);
        
        if (e.target.closest('.view-resident')) {
            const button = e.target.closest('.view-resident');
            const residentId = button.dataset.id;
            console.log('View button clicked for resident:', residentId);
            viewResident(residentId);
        }
        else if (e.target.closest('.delete-resident')) {
            const button = e.target.closest('.delete-resident');
            const residentId = button.dataset.id;
            console.log('Delete button clicked for resident:', residentId);
            deleteResident(residentId);
        }
        else if (e.target.closest('#edit-resident-button')) {
            const button = e.target.closest('#edit-resident-button');
            const residentId = button.dataset.id;
            console.log('Edit from modal clicked for resident:', residentId);
            editResidentFromModal();
        }
    });
}

function editResidentFromModal() {
    const editButton = document.getElementById('edit-resident-button');
    if (editButton && editButton.dataset.id) {
        const residentId = editButton.dataset.id;
        console.log('Editing resident from modal, ID:', residentId);
        closeModalFunc();
        // Small delay to ensure modal is closed
        setTimeout(() => {
            showAddResidentForm(residentId);
        }, 100);
    } else {
        console.error('Edit button or resident ID not found for modal edit');
        showNotification('Error: Cannot edit resident', 'error');
    }
}

function closeModalFunc() {
    const modal = document.getElementById('details-modal');
    if (modal) {
        modal.style.display = 'none';
        console.log('Modal closed');
    }
}

function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    });
    
    const notification = document.createElement('div');
    notification.className = `notification ${type} show`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Rest of functions...
function filterResidents() {
    const searchTerm = document.getElementById('resident-search').value.toLowerCase();
    const rows = document.querySelectorAll('#residents-table tbody tr');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const id = row.cells[0].textContent.toLowerCase();
        const address = row.cells[2].textContent.toLowerCase();
        
        if (name.includes(searchTerm) || id.includes(searchTerm) || address.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
}

function exportResidents() {
    let csv = 'Resident ID,Name,Address,Contact,Status\n';
    const rows = document.querySelectorAll('#residents-table tbody tr');
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cells = row.cells;
            csv += `"${cells[0].textContent}","${cells[1].textContent}","${cells[2].textContent}","${cells[3].textContent}","${cells[4].textContent}"\n`;
        }
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'residents.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}