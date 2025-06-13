<?php
$page_title = "Contact";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh;">
    <div class="hero-bg" style="background-image: url('images/img17.jpg')"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Contactez-Nous</h1>
                <p class="hero-subtitle">
                    Nous sommes là pour vous accompagner dans votre parcours spirituel
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Contact -->
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <h2 class="section-title">Contactez-Nous</h2>
                <p class="section-subtitle">Nous sommes à votre écoute et répondrons à votre message dans les plus brefs délais</p>
            </div>
        </div>
        
        <div class="row">
            <!-- Formulaire de contact -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 hover-lift">
                    <div class="card-body p-4">
                        <form class="contact-form" id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg bg-light" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg bg-light" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-medium">Téléphone</label>
                                    <input type="tel" class="form-control form-control-lg bg-light" id="phone" name="phone">
                                </div>
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-medium">Sujet <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg bg-light" id="subject" name="subject" required>
                                        <option value="" selected disabled>Choisir un sujet</option>
                                        <option value="Question générale">Question générale</option>
                                        <option value="Demande de prière">Demande de prière</option>
                                        <option value="Événements">Événements</option>
                                        <option value="Bénévolat">Bénévolat</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-medium">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control form-control-lg bg-light" id="message" name="message" rows="5" required></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                        <label class="form-check-label" for="newsletter">
                                            Je souhaite recevoir la newsletter de l'église
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Envoyer le message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Informations de contact -->
            <div class="col-lg-4">
                <div class="card shadow border-0 h-100 hover-lift">
                    <div class="card-body p-4">
                    <h5 class="card-title mb-4 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informations de Contact
                    </h5>
                    
                    <div class="contact-info">
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-primary text-white" style="border-radius : 8px">
                                        <i class="fas fa-map-marker-alt" style="padding: 8px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1">Adresse</h6>
                                    <p class="text-muted mb-0">
                                        Goma, Nord-Kivu<br>
                                        République Démocratique du Congo
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center" >
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-primary text-white" style="border-radius : 8px">
                                        <i class="fas fa-phone"style="padding: 8px;"  ></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1">Téléphone</h6>
                                    <p class="text-muted mb-0">
                                        <a href="tel:+243000000000" class="text-decoration-none text-muted hover-primary">+243 XXX XXX XXX</a><br>
                                        <a href="tel:+243000000001" class="text-decoration-none text-muted hover-primary">+243 XXX XXX XXX</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-primary text-white" style="border-radius : 8px">
                                        <i class="fas fa-envelope" style="padding: 8px" ></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1">Email</h6>
                                    <p class="text-muted mb-0">
                                        <a href="mailto:info@restorationchurch.cd" class="text-decoration-none text-muted hover-primary">info@restorationchurch.cd</a><br>
                                        <a href="mailto:pasteur@restorationchurch.cd" class="text-decoration-none text-muted hover-primary">pasteur@restorationchurch.cd</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="contact-item mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-primary text-white" style="border-radius : 8px">
                                        <i class="fab fa-whatsapp" style="padding: 8px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1">WhatsApp</h6>
                                    <p class="text-muted mb-0">
                                        <a href="https://wa.me/243000000000" target="_blank" class="text-decoration-none text-muted hover-primary">+243 XXX XXX XXX</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="schedule-info mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-primary text-white" style="border-radius : 8px;">
                                    <i class="fas fa-clock" style="padding: 8px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-3">Horaires de Bureau</h6>
                                <ul class="list-unstyled schedule-list mb-0">
                                    <li class="d-flex justify-content-between py-2 border-bottom">
                                        <span><i class="fas fa-angle-right text-primary me-2"></i>Lundi - Vendredi</span>
                                        <span class="badge bg-light text-primary">8h00 - 17h00</span>
                                    </li>
                                    <li class="d-flex justify-content-between py-2 border-bottom">
                                        <span><i class="fas fa-angle-right text-primary me-2"></i>Samedi</span>
                                        <span class="badge bg-light text-primary">9h00 - 15h00</span>
                                    </li>
                                    <li class="d-flex justify-content-between py-2">
                                        <span><i class="fas fa-angle-right text-primary me-2"></i>Dimanche</span>
                                        <span class="badge bg-light text-primary">Après le culte</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="schedule-info">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-primary text-white" style="border-radius : 8px;">
                                    <i class="fas fa-calendar" style="padding : 8px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-3">Horaires de Culte</h6>
                                <ul class="list-unstyled schedule-list mb-0">
                                    <li class="d-flex justify-content-between py-2 border-bottom">
                                        <span><i class="fas fa-church text-primary me-2"></i>Dimanche</span>
                                        <span class="badge bg-light text-primary">9h00 - 12h00</span>
                                    </li>
                                    <li class="d-flex justify-content-between py-2 border-bottom">
                                        <span><i class="fas fa-pray text-primary me-2"></i>Mercredi</span>
                                        <span class="badge bg-light text-primary">18h00 - 20h00</span>
                                    </li>
                                    <li class="d-flex justify-content-between py-2">
                                        <span><i class="fas fa-bible text-primary me-2"></i>Vendredi</span>
                                        <span class="badge bg-light text-primary">18h00 - 20h00</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

         <!-- Réseux sociaux  -->
            <div class="social-links">
                   
                        <a href="https://web.facebook.com/evangelicalrestorationchurchgoma" target="_blank" class="text-light me-3">
                            <i class="fab fa-facebook-f fa-lg"></i>
                        </a>
                        <a href="#" class="text-light me-3">
                            <i class="fab fa-youtube fa-lg"></i>
                        </a>
                        <a href="#" class="text-light me-3">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                        <a href="#" class="text-light">
                            <i class="fab fa-whatsapp fa-lg"></i>
                        </a>
                    </div>
</section>

<!-- Section Carte -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Notre Localisation</h2>
            <p class="section-subtitle">Venez nous rendre visite à Goma</p>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow border-0 hover-lift">
                    <div class="card-body p-0">
                        <!-- Carte Google Maps (remplacez par vos coordonnées) -->
                        <div id="map" style="height: 400px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); display: flex; align-items: center; justify-content: center; border-radius: 15px; overflow: hidden;">
                            <div class="text-center p-4">
                                <div class="icon-circle bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; border-radius : 15px">
                                    <i class="fas fa-map-marker-alt" style="font-size: 2rem; padding : 20px"></i>
                                </div>
                                <h4 class="mt-3 text-primary fw-bold">Carte Interactive</h4>
                                <p class="text-muted mb-4">Goma, République Démocratique du Congo</p>
                                <a href="https://maps.app.goo.gl/rZLBy3hJyNNFD93q8" target="_blank" class="btn btn-primary btn-lg">
                                    <i class="fas fa-directions me-2"></i>Obtenir l'itinéraire
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section FAQ -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Questions Fréquentes</h2>
            <p class="section-subtitle">Trouvez rapidement les réponses à vos questions</p>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fas fa-question-circle text-primary me-2"></i>Quels sont les horaires de culte?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body bg-light">
                                        <p>Nos cultes ont lieu chaque dimanche de 9h00 à 12h00. Nous avons également des réunions de prière le mercredi et le vendredi de 18h00 à 20h00. Tous sont les bienvenus!</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="fas fa-question-circle text-primary me-2"></i>Comment puis-je me faire baptiser?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body bg-light">
                                        <p>Pour recevoir le baptême, vous devez d'abord suivre notre cours de préparation au baptême qui a lieu tous les trimestres. Veuillez contacter le bureau de l'église pour vous inscrire au prochain cours.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <i class="fas fa-question-circle text-primary me-2"></i>Quels ministères sont disponibles dans l'église?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body bg-light">
                                        <p>Notre église propose plusieurs ministères, notamment: la louange, l'école du dimanche, le ministère des jeunes, le ministère des femmes, le ministère des hommes, l'évangélisation, et bien d'autres. Vous pouvez vous impliquer selon vos dons et talents.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        <i class="fas fa-question-circle text-primary me-2"></i>Y a-t-il des activités pour les enfants?
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body bg-light">
                                        <p>Oui, nous avons une école du dimanche pour les enfants de tous âges pendant le culte dominical. Nous organisons également des activités spéciales pour les enfants pendant les vacances scolaires.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        <i class="fas fa-question-circle text-primary me-2"></i>Comment puis-je faire un don à l'église?
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body bg-light">
                                        <p>Vous pouvez faire un don lors de nos cultes ou par transfert mobile. Pour plus d'informations sur les dons en ligne ou les virements bancaires, veuillez contacter notre bureau administratif.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Urgence -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="icon-circle bg-danger text-white mx-auto mb-3" style="width: 80px; height: 80px; border-radius : 15px">
                                <i class="fas fa-pray" style="font-size: 2rem; padding : 20px"></i>
                            </div>
                            <h3 class="text-danger fw-bold">Besoin de Prière Urgente?</h3>
                            <p class="lead mb-4">Notre équipe pastorale est disponible pour vous soutenir dans les moments difficiles</p>
                            
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <a href="https://wa.me/243000000000" target="_blank" class="btn btn-success btn-lg mb-2">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                                <a href="tel:+243000000000" class="btn btn-primary btn-lg mb-2">
                                    <i class="fas fa-phone-alt me-2"></i>Appeler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Gestion du formulaire de contact
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Afficher le loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    submitBtn.disabled = true;
    
    // Simuler l'envoi (remplacez par votre logique d'envoi)
    setTimeout(() => {
        showAlert('success', 'Message envoyé avec succès! Nous vous répondrons bientôt.');
        e.target.reset();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
    
    /* 
    // Code réel pour l'envoi
    fetch('api/contact.php', {
        method: 'POST',
        body: formData
    })vitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
    */
});

// Fonction pour partager une citation
function shareQuote(event) {
    event.preventDefault();
    
    // Récupérer le texte de la citation et l'auteur
    const quoteElement = event.target.closest('.card-body').querySelector('.blockquote p');
    const authorElement = event.target.closest('.card-body').querySelector('.blockquote-footer');
    
    const quoteText = quoteElement ? quoteElement.innerText : '';
    const authorText = authorElement ? authorElement.innerText : '';
    
    // Créer le texte à partager
    const shareText = `"${quoteText}" - ${authorText} | Evangelical Restoration Church Goma`;
    
    // Vérifier si l'API de partage est disponible
    if (navigator.share) {
        navigator.share({
            title: 'Citation inspirante',
            text: shareText,
            url: window.location.href
        })
        .then(() => console.log('Partage réussi'))
        .catch((error) => console.log('Erreur de partage', error));
    } else {
        // Fallback pour les navigateurs qui ne supportent pas l'API de partage
        // Créer un élément temporaire pour copier le texte
        const tempInput = document.createElement('textarea');
        tempInput.value = shareText;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        // Afficher un message de confirmation
        alert('Citation copiée dans le presse-papier. Vous pouvez maintenant la partager.');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
