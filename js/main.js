// JavaScript principal pour Evangelical Restoration Church Goma

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des composants
    initializeNavbar();
    initializeCarousel();
    initializeForms();
    initializeAnimations();
    initializeModal();
});

// Navigation active
function initializeNavbar() {
    // Smooth scrolling pour les liens d'ancrage
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar background change on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// Carrousel d'images
function initializeCarousel() {
    const carousel = document.querySelector('#heroCarousel');
    if (carousel) {
        // Auto-play carousel
        const carouselInstance = new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true
        });
    }
}

// Gestion des formulaires
function initializeForms() {
    // Formulaire de contact
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactForm);
    }

    // Formulaire newsletter
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', handleNewsletterForm);
    }
}

// Gestion du formulaire de contact
function handleContactForm(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Afficher le loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    submitBtn.disabled = true;
    
    fetch('api/contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Message envoyé avec succès! Nous vous répondrons bientôt.');
            e.target.reset();
        } else {
            showAlert('error', data.message || 'Erreur lors de l\'envoi du message.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('error', 'Erreur de connexion. Veuillez réessayer.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Gestion du formulaire newsletter
function handleNewsletterForm(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitBtn.disabled = true;
    
    fetch('api/newsletter.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Inscription réussie à la newsletter!');
            e.target.reset();
        } else {
            showAlert('error', data.message || 'Erreur lors de l\'inscription.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('error', 'Erreur de connexion. Veuillez réessayer.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Affichage des alertes
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Créer le conteneur d'alertes s'il n'existe pas
function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Animations au scroll
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observer les éléments à animer
    document.querySelectorAll('.card, .team-card, .event-card').forEach(el => {
        observer.observe(el);
    });
}

// Modal pour les médias
function initializeModal() {
    // Modal pour les vidéos
    const videoModal = document.getElementById('videoModal');
    if (videoModal) {
        videoModal.addEventListener('hidden.bs.modal', function () {
            const iframe = this.querySelector('iframe');
            if (iframe) {
                iframe.src = iframe.src; // Reset video
            }
        });
    }
}

// Fonction pour ouvrir une vidéo ou audio dans un nouvel onglet
function openVideoModal(mediaUrl, title) {
    // Vérifier si l'URL est valide
    if (!mediaUrl || mediaUrl.trim() === '') {
        showAlert('error', 'URL du média non valide ou manquante');
        return;
    }
    
    try {
        // Rediriger directement vers l'URL du média dans un nouvel onglet
        window.open(mediaUrl, '_blank');
    } catch (error) {
        console.error('Erreur lors de l\'ouverture de l\'URL:', error);
        showAlert('error', 'Impossible d\'ouvrir le média. Veuillez vérifier l\'URL.');
    }
}

// Fonction pour partager sur les réseaux sociaux
function shareOnSocial(platform, url, title) {
    const encodedUrl = encodeURIComponent(url);
    const encodedTitle = encodeURIComponent(title);
    
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodedTitle} ${encodedUrl}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

// Fonction pour copier le lien
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        showAlert('success', 'Lien copié dans le presse-papiers!');
    }).catch(() => {
        showAlert('error', 'Impossible de copier le lien.');
    });
}

// Lazy loading pour les images
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
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
}

// Fonction utilitaire pour formater les dates
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Fonction pour charger plus de contenu (pagination)
function loadMore(type, page) {
    const loadMoreBtn = document.querySelector(`#loadMore${type}`);
    const originalText = loadMoreBtn.innerHTML;
    
    loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Chargement...';
    loadMoreBtn.disabled = true;
    
    fetch(`api/load-more.php?type=${type}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items.length > 0) {
                const container = document.querySelector(`#${type}Container`);
                container.insertAdjacentHTML('beforeend', data.html);
                
                if (!data.hasMore) {
                    loadMoreBtn.style.display = 'none';
                } else {
                    loadMoreBtn.dataset.page = page + 1;
                }
            } else {
                loadMoreBtn.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('error', 'Erreur lors du chargement.');
        })
        .finally(() => {
            loadMoreBtn.innerHTML = originalText;
            loadMoreBtn.disabled = false;
        });
}

document.getElementById('themeToggle').addEventListener('click', function() {
    document.body.classList.toggle('light-mode');
    const theme = document.body.classList.contains('light-mode') ? 'light' : 'dark';
    localStorage.setItem('theme', theme);
});

// Apply saved theme on load
window.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        document.body.classList.add('light-mode');
    }
});
