// script.js - Interactive features for Coffee Shop website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize loading spinner
    const spinner = document.createElement('div');
    spinner.className = 'spinner-container';
    spinner.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(spinner);

    // Remove spinner after page load
    window.addEventListener('load', function() {
        setTimeout(function() {
            spinner.style.opacity = '0';
            setTimeout(function() {
                spinner.remove();
            }, 500);
        }, 300);
    });

    // Add page transition class to body
    document.body.classList.add('page-transition');

    // Scroll to top button
    const scrollTopBtn = document.createElement('div');
    scrollTopBtn.className = 'scroll-top';
    scrollTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
    document.body.appendChild(scrollTopBtn);

    // Show/hide scroll to top button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTopBtn.classList.add('active');
        } else {
            scrollTopBtn.classList.remove('active');
        }
    });

    // Scroll to top when button is clicked
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add animation to elements when they come into view
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight - 50) {
                element.classList.add('animate-fade-in');
            }
        });
    };

    // Initial check for elements in view
    animateOnScroll();
    
    // Listen for scroll events to trigger animations
    window.addEventListener('scroll', animateOnScroll);

    // Quantity increment/decrement buttons
    document.querySelectorAll('.quantity-input').forEach(input => {
        const decrementBtn = input.parentElement.querySelector('.decrement');
        const incrementBtn = input.parentElement.querySelector('.increment');
        
        if (decrementBtn && incrementBtn) {
            decrementBtn.addEventListener('click', () => {
                if (input.value > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
            
            incrementBtn.addEventListener('click', () => {
                const max = input.getAttribute('max');
                if (!max || parseInt(input.value) < parseInt(max)) {
                    input.value = parseInt(input.value) + 1;
                }
            });
        }
    });

    // Add to cart animation
    document.querySelectorAll('form[action="add_to_cart.php"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button) {
                button.innerHTML = '<i class="bi bi-check"></i> Added!';
                setTimeout(() => {
                    button.innerHTML = 'Add to Cart';
                }, 1500);
            }
        });
    });

    // Product image zoom effect on hover
    document.querySelectorAll('.product-image').forEach(img => {
        img.addEventListener('mousemove', function(e) {
            const x = e.clientX - this.getBoundingClientRect().left;
            const y = e.clientY - this.getBoundingClientRect().top;
            
            const centerX = this.offsetWidth / 2;
            const centerY = this.offsetHeight / 2;
            
            const moveX = (x - centerX) / 10;
            const moveY = (y - centerY) / 10;
            
            this.style.transform = `scale(1.1) translate(${moveX}px, ${moveY}px)`;
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) translate(0px, 0px)';
        });
    });

    // Toast notifications
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'}"></i>
                <div class="message">
                    <span>${message}</span>
                </div>
            </div>
            <i class="bi bi-x close"></i>
            <div class="progress"></div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('active');
        }, 100);
        
        toast.querySelector('.close').addEventListener('click', () => {
            toast.classList.remove('active');
            setTimeout(() => {
                toast.remove();
            }, 300);
        });
        
        setTimeout(() => {
            toast.classList.remove('active');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }

    // Add custom toast for add to cart success
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('added') === 'true') {
        showToast('Item added to cart successfully!');
    }

    // Menu filter animation
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.product-card').forEach(card => {
                card.style.opacity = '0';
                setTimeout(() => {
                    card.style.opacity = '1';
                }, 300);
            });
        });
    });

    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-shrink');
            } else {
                navbar.classList.remove('navbar-shrink');
            }
        });
    }

    // Form validation visual feedback
    document.querySelectorAll('form').forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        });
    });
});

// Add modification to menu.php to support animations
function updateProductCards() {
    document.querySelectorAll('.col-md-4').forEach((card, index) => {
        card.classList.add('animate-on-scroll');
        card.style.animationDelay = `${index * 0.1}s`;
    });
}

// Add image zoom effect for menu items
function initializeImageZoom() {
    document.querySelectorAll('.card-img-top').forEach(img => {
        img.classList.add('product-image');
    });
}

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        updateProductCards();
        initializeImageZoom();
    });
} else {
    updateProductCards();
    initializeImageZoom();
}