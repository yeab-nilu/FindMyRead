// FindMyRead - Main JavaScript File

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation Toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
        
        // Close menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            });
        });
    }
    
    // Search functionality
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const searchInput = form.querySelector('input[name="q"]');
            if (searchInput && searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    });
    
    // Book card hover effects
    const bookCards = document.querySelectorAll('.book-card');
    bookCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (form.hasAttribute('data-skip-validation')) {
                return;
            }
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger-color)';
                    
                    // Remove error styling on input
                    field.addEventListener('input', function() {
                        this.style.borderColor = 'var(--border-color)';
                    });
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields.', 'error');
            }
        });
    });
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Optional auto-loading states for submit buttons (opt-in via data-auto-loading)
    const buttons = document.querySelectorAll('.btn[data-auto-loading]');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.type === 'submit' && this.form && this.form.checkValidity()) {
                const original = this.innerHTML;
                this.dataset.originalText = original;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                this.disabled = true;
                // Safety: revert if still on page after 5s (e.g., prevented submit)
                setTimeout(() => {
                    if (document.body.contains(this)) {
                        this.innerHTML = this.dataset.originalText || original;
                        this.disabled = false;
                    }
                }, 5000);
            }
        });
    });
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize lazy loading for images
    initializeLazyLoading();
});

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        ${message}
    `;
    
    // Add to top of main content
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.insertBefore(notification, mainContent.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
}

    // AJAX helper function
    function makeRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const config = { ...defaultOptions, ...options };
    
        return fetch(url, config)
        .then(response => {
                if (!response.ok) {
                    // Try to parse JSON error; if fails, throw generic
                    return response.text().then(text => {
                        let data;
                        try { data = JSON.parse(text); } catch (_) {}
                        const msg = (data && (data.message || data.error)) || `Request failed with status ${response.status}`;
                        throw new Error(msg);
                    });
                }
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text();
        })
        .catch(error => {
            console.error('Request failed:', error);
            showNotification(error.message || 'Request failed. Please try again.', 'error');
            throw error;
        });
}

// Add to reading list function
function addToList(bookId, listType) {
    const button = event.target.closest('.btn');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;
    
    makeRequest(`actions/add-to-list.php?book_id=${bookId}&list_type=${listType}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            button.innerHTML = '<i class="fas fa-check"></i> Added';
            button.classList.remove('btn-outline');
            button.classList.add('btn-success');
        } else {
            showNotification(data.message, 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Remove from reading list function
function removeFromList(bookId, listType) {
    const button = event.target.closest('.btn');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';
    button.disabled = true;
    
    makeRequest(`actions/remove-from-list.php?book_id=${bookId}&list_type=${listType}`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            button.innerHTML = '<i class="fas fa-plus"></i> Add to List';
            button.classList.remove('btn-danger');
            button.classList.add('btn-outline');
        } else {
            showNotification(data.message, 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Rate book function
function rateBook(bookId, rating) {
    const button = event.target.closest('.btn');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rating...';
    button.disabled = true;
    
    makeRequest(`actions/rate-book.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            book_id: bookId,
            rating: rating
        })
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Update rating display
            updateRatingDisplay(bookId, rating);
            // Restore button state
            button.innerHTML = '<i class="fas fa-check"></i> Rated';
            button.disabled = false;
            setTimeout(() => {
                if (document.body.contains(button)) {
                    button.innerHTML = originalText;
                }
            }, 1500);
        } else {
            showNotification(data.message, 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Update rating display
function updateRatingDisplay(bookId, rating) {
    const ratingContainer = document.querySelector(`[data-book-id="${bookId}"] .stars`);
    if (ratingContainer) {
        ratingContainer.innerHTML = generateStarsHTML(rating);
    }
}

// Generate stars HTML
function generateStarsHTML(rating) {
    let stars = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = (rating - fullStars) >= 0.5;
    
    for (let i = 1; i <= 5; i++) {
        if (i <= fullStars) {
            stars += '<span class="star filled">★</span>';
        } else if (i === fullStars + 1 && hasHalfStar) {
            stars += '<span class="star half">★</span>';
        } else {
            stars += '<span class="star empty">☆</span>';
        }
    }
    
    return stars;
}

// Initialize tooltips
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.cssText = `
                position: absolute;
                background: var(--bg-dark);
                color: white;
                padding: 0.5rem;
                border-radius: var(--radius-sm);
                font-size: 0.8rem;
                z-index: 1000;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s ease;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            
            setTimeout(() => {
                tooltip.style.opacity = '1';
            }, 100);
            
            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            });
        });
    });
}

// Initialize lazy loading
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    }
}

// Search suggestions
function initializeSearchSuggestions() {
    const searchInput = document.querySelector('input[name="q"]');
    if (!searchInput) return;
    
    let searchTimeout;
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'search-suggestions';
    suggestionsContainer.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        display: none;
    `;
    
    searchInput.parentNode.style.position = 'relative';
    searchInput.parentNode.appendChild(suggestionsContainer);
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            makeRequest(`api/search-suggestions.php?q=${encodeURIComponent(query)}`)
                .then(suggestions => {
                    if (suggestions.length > 0) {
                        suggestionsContainer.innerHTML = suggestions.map(suggestion => `
                            <div class="suggestion-item" style="padding: 0.75rem; cursor: pointer; border-bottom: 1px solid var(--border-light);">
                                <strong>${suggestion.title}</strong><br>
                                <small style="color: var(--text-secondary);">by ${suggestion.author}</small>
                            </div>
                        `).join('');
                        suggestionsContainer.style.display = 'block';
                    } else {
                        suggestionsContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    suggestionsContainer.style.display = 'none';
                });
        }, 300);
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });
    
    // Handle suggestion clicks
    suggestionsContainer.addEventListener('click', function(e) {
        const suggestionItem = e.target.closest('.suggestion-item');
        if (suggestionItem) {
            const title = suggestionItem.querySelector('strong').textContent;
            searchInput.value = title;
            suggestionsContainer.style.display = 'none';
            searchInput.form.submit();
        }
    });
}

// Initialize search suggestions if on search page
if (window.location.pathname.includes('search') || window.location.pathname.includes('books')) {
    initializeSearchSuggestions();
}
