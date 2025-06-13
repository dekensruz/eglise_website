<?php
$page_title = "Événements";
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupérer les événements
$database = new Database();
$db = $database->getConnection();

try {
    // Événements à venir
    $upcoming_query = "SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC";
    $upcoming_stmt = $db->prepare($upcoming_query);
    $upcoming_stmt->execute();
    $upcoming_events = $upcoming_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Événements passés
    $past_query = "SELECT * FROM events WHERE event_date < NOW() ORDER BY event_date DESC LIMIT 5";
    $past_stmt = $db->prepare($past_query);
    $past_stmt->execute();
    $past_events = $past_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $upcoming_events = [];
    $past_events = [];
}
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh;">
    <div class="hero-bg" style="background-image: url('images/img4.jpg')"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Nos Événements</h1>
                <p class="hero-subtitle">
                    Rejoignez-nous pour des moments de communion, d'adoration et de croissance spirituelle
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Événements à venir -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Événements à Venir</h2>
            <p class="section-subtitle">Ne manquez pas ces moments spéciaux</p>
        </div>
        
        <?php if (!empty($upcoming_events)): ?>
            <div class="row">
                <?php foreach ($upcoming_events as $event): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card event-card">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <div class="event-date h-100 d-flex flex-column justify-content-center">
                                        <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                                        <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                                        <small class="mt-1"><?php echo date('Y', strtotime($event['event_date'])); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <?php if ($event['image']): ?>
                                        <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="card-img-top">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('H:i', strtotime($event['event_date'])); ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo htmlspecialchars($event['location']); ?>
                                            </small>
                                        </div>
                                        <div class="mt-3" >
                                            <button class="btn btn-primary btn-sm me-2" onclick="shareEvent('<?php echo htmlspecialchars($event['title']); ?>', '<?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?>')"  style="margin-bottom: 10px;">
                                                <i class="fas fa-share me-1"></i>Partager
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm" onclick="addToCalendar('<?php echo htmlspecialchars($event['title']); ?>', '<?php echo $event['event_date']; ?>', '<?php echo htmlspecialchars($event['location']); ?>')">
                                                <i class="fas fa-calendar-plus me-1"></i>Ajouter au calendrier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php endforeach; ?>
            </div>

            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">Aucun événement programmé pour le moment</h4>
                <p class="text-muted">Revenez bientôt pour découvrir nos prochains événements !</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Section Événements Réguliers -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Événements Réguliers</h2>
            <p class="section-subtitle">Nos rendez-vous hebdomadaires</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-sun fs-2"></i>
                        </div>
                        <h4 class="card-title">Culte Dominical</h4>
                        <p class="text-primary mb-2"><strong>Chaque Dimanche</strong></p>
                        <p class="card-text">
                            <i class="fas fa-clock me-2"></i>9h00 - 12h00<br>
                            <i class="fas fa-map-marker-alt me-2"></i>Sanctuaire Principal
                        </p>
                        <p class="card-text">
                            Culte principal avec prédication, louange, communion fraternelle et école du dimanche pour les enfants.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-pray fs-2"></i>
                        </div>
                        <h4 class="card-title">Prière & Étude</h4>
                        <p class="text-primary mb-2"><strong>Chaque Mercredi</strong></p>
                        <p class="card-text">
                            <i class="fas fa-clock me-2"></i>18h00 - 20h00<br>
                            <i class="fas fa-map-marker-alt me-2"></i>Salle de Prière
                        </p>
                        <p class="card-text">
                            Temps de prière collective, intercession et étude approfondie de la Parole de Dieu.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-users fs-2"></i>
                        </div>
                        <h4 class="card-title">Culte de Jeunesse</h4>
                        <p class="text-primary mb-2"><strong>Chaque Vendredi</strong></p>
                        <p class="card-text">
                            <i class="fas fa-clock me-2"></i>18h00 - 20h00<br>
                            <i class="fas fa-map-marker-alt me-2"></i>Salle de Jeunesse
                        </p>
                        <p class="card-text">
                            Rencontre spéciale pour les jeunes avec louange dynamique, enseignement et activités.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Événements Passés -->
<?php if (!empty($past_events)): ?>
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Événements Passés</h2>
            <p class="section-subtitle">Revivez nos moments de bénédiction</p>
        </div>
        
        <div class="row">
            <?php foreach ($past_events as $event): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <?php if ($event['image']): ?>
                            <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="card-img-top">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-calendar text-primary" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($event['event_date'])); ?>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars($event['location']); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="past-events.php" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt me-2"></i>Voir tous les événements passés
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section Newsletter -->
<section id="newsletter" class="section-padding bg-primary text-white">
    <div class="container" >
        <div class="text-center mb-5" >
            <h2 class="text-white mb-3" >Restez Informé</h2>
            <p class="lead">Ne manquez aucun de nos événements</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="bg-white rounded p-4">
                    <form id="eventNewsletterForm" class="text-dark">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notifications" name="notifications" checked>
                                <label class="form-check-label" for="notifications">
                                    Recevoir les notifications d'événements
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-envelope me-2"></i>S'inscrire à la Newsletter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Fonction pour partager un événement
function shareEvent(title, date) {
    const text = `Rejoignez-nous pour "${title}" le ${date} à l'Église Evangelical Restoration Church Goma`;
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url
        });
    } else {
        // Fallback pour les navigateurs qui ne supportent pas l'API Web Share
        const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}`;
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

// Fonction pour ajouter un événement au calendrier
function addToCalendar(title, date, location) {
    const startDate = new Date(date);
    const endDate = new Date(startDate.getTime() + 2 * 60 * 60 * 1000); // +2 heures
    
    const formatDate = (date) => {
        return date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
    };
    
    const calendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${formatDate(startDate)}/${formatDate(endDate)}&location=${encodeURIComponent(location)}&details=${encodeURIComponent('Événement à l\'Église Evangelical Restoration Church Goma')}`;
    
    window.open(calendarUrl, '_blank');
}

// Gestion du formulaire newsletter
document.getElementById('eventNewsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inscription en cours...';
    submitBtn.disabled = true;
    
    fetch('api/newsletter.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Inscription réussie ! Vous recevrez nos prochaines notifications.');
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
});
</script>

<?php require_once 'includes/footer.php'; ?>
