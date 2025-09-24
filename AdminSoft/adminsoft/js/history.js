// History specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initHistory();
});

function initHistory() {
    // Filter functionality
    const filterButton = document.getElementById('history-filter-button');
    const statusFilter = document.getElementById('history-status-filter');
    const startDate = document.getElementById('history-start-date');
    const endDate = document.getElementById('history-end-date');
    
    if (filterButton) {
        filterButton.addEventListener('click', filterHistory);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterHistory);
    }
    
    if (startDate && endDate) {
        startDate.addEventListener('change', filterHistory);
        endDate.addEventListener('change', filterHistory);
    }
    
    // Pagination functionality
    initPagination();
}

function filterHistory() {
    const statusFilter = document.getElementById('history-status-filter').value;
    const startDate = document.getElementById('history-start-date').value;
    const endDate = document.getElementById('history-end-date').value;
    const rows = document.querySelectorAll('#history-table tbody tr');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const status = row.dataset.status;
        const dateText = row.cells[5].textContent;
        const rowDate = new Date(dateText);
        
        let matchesStatus = statusFilter === 'all' || status === statusFilter;
        let matchesDate = true;
        
        if (startDate) {
            const start = new Date(startDate);
            matchesDate = matchesDate && rowDate >= start;
        }
        
        if (endDate) {
            const end = new Date(endDate);
            end.setHours(23, 59, 59); // End of the day
            matchesDate = matchesDate && rowDate <= end;
        }
        
        if (matchesStatus && matchesDate) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update showing count
    updateShowingCount(visibleCount);
}

function initPagination() {
    const rowsPerPage = document.getElementById('history-rows');
    const prevButton = document.getElementById('history-prev');
    const nextButton = document.getElementById('history-next');
    const pageButtons = document.querySelectorAll('.history-page');
    
    if (rowsPerPage) {
        rowsPerPage.addEventListener('change', function() {
            applyPagination(parseInt(this.value), 1);
        });
    }
    
    if (prevButton) {
        prevButton.addEventListener('click', function() {
            // Implement previous page logic
            showNotification('Previous page functionality would be implemented here');
        });
    }
    
    if (nextButton) {
        nextButton.addEventListener('click', function() {
            // Implement next page logic
            showNotification('Next page functionality would be implemented here');
        });
    }
    
    pageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            const rows = parseInt(document.getElementById('history-rows').value);
            applyPagination(rows, page);
        });
    });
}

function applyPagination(rowsPerPage, currentPage) {
    const rows = document.querySelectorAll('#history-table tbody tr');
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    
    let visibleCount = 0;
    
    rows.forEach((row, index) => {
        if (index >= startIndex && index < endIndex && row.style.display !== 'none') {
            row.style.display = '';
            visibleCount++;
        } else if (row.style.display !== 'none') {
            row.style.display = 'none';
        }
    });
    
    updateShowingCount(visibleCount, startIndex + 1, Math.min(endIndex, rows.length));
    updatePaginationButtons(currentPage);
}

function updateShowingCount(total, start = 1, end = total) {
    const startElement = document.getElementById('history-start');
    const endElement = document.getElementById('history-end');
    const totalElement = document.getElementById('history-total');
    
    if (startElement) startElement.textContent = start;
    if (endElement) endElement.textContent = end;
    if (totalElement) totalElement.textContent = total;
}

function updatePaginationButtons(currentPage) {
    const pageButtons = document.querySelectorAll('.history-page');
    pageButtons.forEach(button => {
        if (parseInt(button.dataset.page) === currentPage) {
            button.classList.remove('btn-outline');
            button.classList.add('btn-primary');
        } else {
            button.classList.remove('btn-primary');
            button.classList.add('btn-outline');
        }
    });
}

function showNotification(message, type = 'success') {
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
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}