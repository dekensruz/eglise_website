<?php
$page_title = "Accueil";
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupérer les derniers événements
$database = new Database();
$db = $database->getConnection();

try {
    // Derniers événements
    $events_query = "SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 3";
    $events_stmt = $db->prepare($events_query);
    $events_stmt->execute();
    $upcoming_events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dernières prédications
    $sermons_query = "SELECT * FROM sermons ORDER BY created_at DESC LIMIT 3";
    $sermons_stmt = $db->prepare($sermons_query);
    $sermons_stmt->execute();
    $latest_sermons = $sermons_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Images de la galerie pour le carrousel
    $gallery_query = "SELECT * FROM gallery ORDER BY created_at DESC LIMIT 5";
    $gallery_stmt = $db->prepare($gallery_query);
    $gallery_stmt->execute();
    $gallery_images = $gallery_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $upcoming_events = [];
    $latest_sermons = [];
    $gallery_images = [];
}
?>

<!-- Hero Section avec Carrousel -->
<section class="hero-section">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php if (!empty($gallery_images)): ?>
                <?php foreach ($gallery_images as $index => $image): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="hero-bg" style="background-image: url('<?php echo htmlspecialchars($image['image_path']); ?>')"></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="carousel-item active">
                    <div class="hero-bg" style="background-image: url('images/img1.jpg')"></div>
                </div>
                <div class="carousel-item">
                    <div class="hero-bg" style="background-image: url('images/img3.jpg')"></div>
                </div>
                <div class="carousel-item">
                    <div class="hero-bg" style="background-image: url('images/img5.jpg')"></div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="carousel-indicators">
            <?php $total_images = !empty($gallery_images) ? count($gallery_images) : 3; ?>
            <?php for ($i = 0; $i < $total_images; $i++): ?>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $i; ?>" 
                        <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
            <?php endfor; ?>
        </div>
    </div>
    
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Bienvenue à l'Église Evangelical Restoration Church</h1>
                <p class="hero-subtitle">
                    Une communauté de foi dédiée à la restauration des vies par la puissance de l'Évangile de Jésus-Christ à Goma, RDC.
                </p>
                

                <div class="hero-buttons" >
                    <a href="about.php" class="btn btn-primary btn-lg me-3"  style="margin-bottom: 10px;">
                        <i class="fas fa-church me-2"></i>Découvrir l'Église
                    </a>
                    <a href="events.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-calendar me-2"></i>Nos Événements
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Citation Quotidienne -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Citation Quotidienne</h2>
        </div>
        <?php
        // Récupérer une citation aléatoire active
        $database = new Database();
        $db = $database->getConnection();
        $quote_query = "SELECT * FROM daily_quotes WHERE is_active = 1 ORDER BY RAND() LIMIT 1";
        $quote_stmt = $db->prepare($quote_query);
        $quote_stmt->execute();
        $quote = $quote_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($quote) :
        ?>
        <div class="card border-0 bg-transparent text-center">
            <div class="card-body p-0">
                <blockquote class="blockquote">
                    <p class="mb-2"><i class="fas fa-quote-left me-2"></i><?php echo htmlspecialchars(utf8_encode($quote['quote'])); ?><i class="fas fa-quote-right ms-2"></i></p>
                    <footer class="blockquote-footer">
                        <?php if (!empty($quote['author'])) : ?>
                            <span class="fw-bold"><?php echo htmlspecialchars($quote['author']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($quote['bible_reference'])) : ?>
                            <cite title="Source" class="ms-1"><?php echo htmlspecialchars($quote['bible_reference']); ?></cite>
                        <?php endif; ?>
                        <a href="#" class="btn btn-sm btn-outline-primary ms-2" onclick="shareQuote(event)">
                            <i class="fas fa-share-alt"></i>
                        </a>
                    </footer>
                </blockquote>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Section Bienvenue -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="images/img7.jpg" alt="Notre Église" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="section-title text-start">Notre Mission</h2>
                <p class="section-subtitle text-start">Restaurer les vies par l'amour du Christ</p>
                <p class="mb-4">
                    L'Église Evangelical Restoration Church Goma est une communauté chrétienne évangélique 
                    passionnée par la restauration des vies brisées. Nous croyons en la puissance transformatrice 
                    de l'Évangile de Jésus-Christ et nous nous engageons à partager cet amour avec notre communauté.
                </p>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-heart text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Amour</h6>
                                <small class="text-muted">Aimer Dieu et notre prochain</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hands-helping text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Service</h6>
                                <small class="text-muted">Servir avec compassion</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cross text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Foi</h6>
                                <small class="text-muted">Vivre par la foi en Christ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-1">Communauté</h6>
                                <small class="text-muted">Grandir ensemble</small>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="about.php" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-2"></i>En savoir plus
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section Événements à venir -->
<?php if (!empty($upcoming_events)): ?>
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Événements à Venir</h2>
            <p class="section-subtitle">Rejoignez-nous pour ces moments de communion et de célébration</p>
        </div>
        
        <div class="row">
            <?php foreach ($upcoming_events as $event): ?>
                <div class="col-lg-4 mb-4">
                    <div class="event-card">
                        <div class="event-date">
                            <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                            <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                        </div>
                        <?php if ($event['image']): ?>
                            <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="card-img-top">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?></p>
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
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="events.php" class="btn btn-outline-primary">
                <i class="fas fa-calendar me-2"></i>Voir tous les événements
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section Horaires de Culte -->
<section class="section-padding bg-primary text-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="text-white mb-3">Horaires de Culte</h2>
            <p class="lead">Rejoignez-nous pour adorer ensemble</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="text-center">
                    <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-sun fs-2"></i>
                    </div>
                    <h4>Culte Dominical</h4>
                    <p class="mb-2"><strong>Dimanche 9h00 - 12h00</strong></p>
                    <p>Culte principal avec prédication, louange et communion fraternelle</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="text-center">
                    <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-pray fs-2"></i>
                    </div>
                    <h4>Prière & Étude</h4>
                    <p class="mb-2"><strong>Mercredi 18h00 - 20h00</strong></p>
                    <p>Temps de prière collective et étude approfondie de la Bible</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="text-center">
                    <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-heart fs-2"></i>
                    </div>
                    <h4>Culte de Jeunesse</h4>
                    <p class="mb-2"><strong>Vendredi 18h00 - 20h00</strong></p>
                    <p>Rencontre spéciale pour les jeunes avec louange et enseignement</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="contact.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-map-marker-alt me-2"></i>Nous Localiser
            </a>
        </div>
    </div>
</section>

<!-- Section Dernières Prédications -->
<?php if (!empty($latest_sermons)): ?>
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Dernières Prédications</h2>
            <p class="section-subtitle">Écoutez les messages inspirants de nos pasteurs</p>
        </div>
        
        <div class="row">
            <?php foreach ($latest_sermons as $sermon): ?>
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <?php if ($sermon['thumbnail']): ?>
                            <img src="<?php echo htmlspecialchars($sermon['thumbnail']); ?>" alt="<?php echo htmlspecialchars($sermon['title']); ?>" class="card-img-top">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-play-circle text-primary" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($sermon['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($sermon['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo htmlspecialchars($sermon['preacher']); ?>
                                </small>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y', strtotime($sermon['sermon_date'])); ?>
                                </small>
                            </div>
                            <div class="mt-3">
                                <?php if ($sermon['video_url']): ?>
                                    <a href="#" onclick="openVideoModal('<?php echo htmlspecialchars($sermon['video_url']); ?>', '<?php echo htmlspecialchars($sermon['title']); ?>')" class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-play me-1"></i>Vidéo
                                    </a>
                                <?php endif; ?>
                                <?php if ($sermon['audio_url']): ?>
                                    <a href="<?php echo htmlspecialchars($sermon['audio_url']); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="fas fa-volume-up me-1"></i>Audio
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="media.php" class="btn btn-outline-primary">
                <i class="fas fa-play me-2"></i>Voir toutes les prédications
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Modal pour les vidéos -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Prédication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Le contenu vidéo sera injecté ici par JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour partager une citation
function shareQuote(event) {
    event.preventDefault();
    
    // Récupérer le texte de la citation
    const quoteElement = event.target.closest('.blockquote');
    const quoteText = quoteElement.querySelector('p').textContent.trim();
    const quoteFooter = quoteElement.querySelector('.blockquote-footer').textContent.trim();
    const shareText = `"${quoteText}" - ${quoteFooter}`;
    
    // Vérifier si l'API Web Share est disponible
    if (navigator.share) {
        navigator.share({
            title: 'Citation inspirante',
            text: shareText,
            url: window.location.href
        })
        .then(() => console.log('Citation partagée avec succès'))
        .catch((error) => console.log('Erreur lors du partage:', error));
    } else {
        // Fallback: copier dans le presse-papiers
        navigator.clipboard.writeText(shareText)
            .then(() => {
                alert('Citation copiée dans le presse-papiers!');
            })
            .catch(err => {
                console.error('Erreur lors de la copie:', err);
            });
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
