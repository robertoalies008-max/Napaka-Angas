// Show request form when PROCEED buttons are clicked
document.querySelectorAll('.proceed-btn').forEach(button => {
    button.addEventListener('click', function() {
        const documentType = this.getAttribute('data-document');
        const documentTypeId = this.getAttribute('data-document-id');
        document.getElementById('documentType').value = documentType;
        document.getElementById('documentTypeId').value = documentTypeId;
        document.getElementById('requestForm').style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent scrolling when form is open
    });
});

// Close form when X button is clicked
document.getElementById('closeForm').addEventListener('click', function() {
    document.getElementById('requestForm').style.display = 'none';
    document.body.style.overflow = 'auto'; // Re-enable scrolling
});

// Close form when clicking outside the form
document.getElementById('requestForm').addEventListener('click', function(e) {
    if (e.target === this) {
        document.getElementById('requestForm').style.display = 'none';
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }
});

// Form submission
document.getElementById('documentRequestForm').addEventListener('submit', function(e) {
    // Form will be submitted to process_request.php
    // The alert will be shown after successful submission in process_request.php
});

// Smooth scrolling for navigation links
document.querySelectorAll('nav a').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Show info box when nav item is clicked
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Hide all info boxes
        document.querySelectorAll('.info-box').forEach(box => {
            box.style.display = 'none';
        });
        
        // Show the clicked info box
        const infoId = this.getAttribute('href').substring(1) + '-info';
        const infoBox = document.getElementById(infoId);
        if (infoBox) {
            infoBox.style.display = 'block';
            
            // Hide the info box after 3 seconds
            setTimeout(() => {
                infoBox.style.display = 'none';
            }, 3000);
        }
        
        // Still scroll to the section
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Hide info boxes when clicking elsewhere
document.addEventListener('click', function(e) {
    if (!e.target.matches('.nav-link')) {
        document.querySelectorAll('.info-box').forEach(box => {
            box.style.display = 'none';
        });
    }
});