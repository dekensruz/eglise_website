<?php
$page_title = "Médias";
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupérer les médias
$database = new Database();
$db = $database->getConnection();

try {
    // Dernières prédications
    $sermons_query = "SELECT * FROM sermons ORDER BY created_at DESC LIMIT 12";
    $sermons_stmt = $db->prepare($sermons_query);
    $sermons_stmt->execute();
    $sermons = $sermons_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Images de la galerie
    $gallery_query = "SELECT * FROM gallery ORDER BY created_at DESC LIMIT 12";
    $gallery_stmt = $db->prepare($gallery_query);
    $gallery_stmt->execute();
    $gallery_images = $gallery_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $sermons = [];
    $gallery_images = [];
}
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh;">
    <div class="hero-bg" style="background-image: url('images/img5.jpg')"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Médias & Ressources</h1>
                <p class="hero-subtitle">
                    Prédications, témoignages et moments de bénédiction à partager
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Navigation des sections -->
<section class="py-4 bg-light">
    <div class="container">
        <ul class="nav nav-pills justify-content-center" id="mediaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="sermons-tab" data-bs-toggle="pill" data-bs-target="#sermons" type="button" role="tab">
                    <i class="fas fa-play me-2"></i>Prédications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gallery-tab" data-bs-toggle="pill" data-bs-target="#gallery" type="button" role="tab">
                    <i class="fas fa-images me-2"></i>Galerie Photos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="testimonies-tab" data-bs-toggle="pill" data-bs-target="#testimonies" type="button" role="tab">
                    <i class="fas fa-heart me-2"></i>Témoignages
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="live-tab" data-bs-toggle="pill" data-bs-target="#live" type="button" role="tab">
                    <i class="fas fa-broadcast-tower me-2"></i>En Direct
                </button>
            </li>
        </ul>
    </div>
</section>

<!-- Contenu des onglets -->
<section class="section-padding">
    <div class="container">
        <div class="tab-content" id="mediaTabsContent">
            
            <!-- Onglet Prédications -->
            <div class="tab-pane fade show active" id="sermons" role="tabpanel">
                <div class="text-center mb-5">
                    <h2 class="section-title">Prédications</h2>
                    <p class="section-subtitle">Messages inspirants pour votre croissance spirituelle</p>
                </div>
                
                <?php if (!empty($sermons)): ?>
                    <div class="row" id="sermonsContainer">
                        <?php foreach ($sermons as $sermon): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card">
                                    <div class="position-relative">
                                        <?php if ($sermon['thumbnail']): ?>
                                            <img src="<?php echo htmlspecialchars($sermon['thumbnail']); ?>" alt="<?php echo htmlspecialchars($sermon['title']); ?>" class="card-img-top">
                                        <?php else: ?>
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-play-circle text-primary" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($sermon['video_url']): ?>
                                            <div class="media-overlay">
                                                <button class="play-btn" onclick="openVideoModal('<?php echo htmlspecialchars($sermon['video_url']); ?>', '<?php echo htmlspecialchars($sermon['title']); ?>')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($sermon['title']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars(substr($sermon['description'], 0, 100)) . '...'; ?></p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($sermon['preacher']); ?>
                                            </small>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($sermon['sermon_date'])); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="btn-group w-100" role="group">
                                            <?php if ($sermon['video_url']): ?>
                                                <button type="button" class="btn btn-primary" onclick="openVideoModal('<?php echo htmlspecialchars($sermon['video_url']); ?>', '<?php echo htmlspecialchars($sermon['title']); ?>')">
                                                    <i class="fas fa-play me-1"></i>Vidéo
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($sermon['audio_url']): ?>
                                                <a href="<?php echo htmlspecialchars($sermon['audio_url']); ?>" class="btn btn-outline-primary" target="_blank">
                                                    <i class="fas fa-volume-up me-1"></i>Audio
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($sermon['pdf_url']): ?>
                                                <a href="<?php echo htmlspecialchars($sermon['pdf_url']); ?>" class="btn btn-outline-secondary" target="_blank">
                                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-primary" onclick="shareSermon('<?php echo htmlspecialchars($sermon['title']); ?>', '<?php echo htmlspecialchars($sermon['video_url']); ?>')">
                                                <i class="fas fa-share me-1"></i>Partager
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="all-sermons.php" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Voir toutes les prédications
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-play-circle text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Aucune prédication disponible</h4>
                        <p class="text-muted">Les prédications seront bientôt disponibles !</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Onglet Galerie Photos -->
            <div class="tab-pane fade" id="gallery" role="tabpanel">
                <div class="text-center mb-5">
                    <h2 class="section-title">Galerie Photos</h2>
                    <p class="section-subtitle">Moments de bénédiction et de communion</p>
                </div>
                
                <?php if (!empty($gallery_images)): ?>
                    <div class="row" id="galleryContainer">
                        <?php foreach ($gallery_images as $image): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="media-item" onclick="openImageModal('<?php echo htmlspecialchars($image['image_path']); ?>', '<?php echo htmlspecialchars($image['title']); ?>')">
                                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                                    <div class="media-overlay">
                                        <div class="text-center text-white">
                                            <i class="fas fa-search-plus fa-2x mb-2"></i>
                                            <h6><?php echo htmlspecialchars($image['title']); ?></h6>
                                            <?php if ($image['description']): ?>
                                                <p class="small"><?php echo htmlspecialchars(substr($image['description'], 0, 50)) . '...'; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                  
                    <div class="text-center mt-4">
                        <a href="all-gallery.php" class="btn btn-outline-primary">
                            <i class="fas fa-images me-2"></i>Voir toute la galerie
                        </a>
                    </div>
                <?php else: ?>
                   
                         
           
              
                <?php endif; ?>
            </div>
            
            <!-- Onglet Témoignages -->
            <div class="tab-pane fade" id="testimonies" role="tabpanel">
                <div class="text-center mb-5">
                    <h2 class="section-title">Témoignages</h2>
                    <p class="section-subtitle">Dieu transforme des vies dans notre église</p>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="images/img11.jpg" alt="Témoignage" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1">Marie Kabila</h6>
                                        <small class="text-muted">Membre depuis 2020</small>
                                    </div>
                                </div>
                                <p class="card-text fst-italic">
                                    "Grâce aux prières et à l'accompagnement de cette église, j'ai retrouvé l'espoir après une période très difficile. 
                                    Dieu a restauré ma famille et ma vie professionnelle."
                                </p>
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="images/img12.jpg" alt="Témoignage" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1">Jean-Baptiste Mukendi</h6>
                                        <small class="text-muted">Membre depuis 2018</small>
                                    </div>
                                </div>
                                <p class="card-text fst-italic">
                                    "Cette église m'a aidé à découvrir ma véritable identité en Christ. Les enseignements sont profonds 
                                    et pratiques. Ma vie spirituelle a été transformée."
                                </p>
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-heart me-2"></i>Partager votre témoignage
                    </a>
                </div>
            </div>
            
            <!-- Onglet En Direct -->
            <div class="tab-pane fade" id="live" role="tabpanel">
                <div class="text-center mb-5">
                    <h2 class="section-title">Cultes en Direct</h2>
                    <p class="section-subtitle">Rejoignez-nous en ligne pour nos cultes</p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="mb-4">
                                    <i class="fas fa-broadcast-tower text-primary" style="font-size: 4rem;"></i>
                                </div>
                                <h4>Prochaine Diffusion</h4>
                                <p class="lead">Dimanche à 9h00 (Heure de Goma)</p>
                                <p class="text-muted mb-4">
                                    Suivez nos cultes en direct sur Facebook Live. Vous pouvez également revoir 
                                    les cultes précédents sur notre page Facebook.
                                </p>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                    <a href="https://web.facebook.com/evangelicalrestorationchurchgoma" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="fab fa-facebook me-2"></i>Facebook Live
                                    </a>
                                    <a href="#" class="btn btn-outline-primary btn-lg">
                                        <i class="fab fa-youtube me-2"></i>YouTube Live
                                    </a>
                                </div>
                                
                                <div class="mt-4">
                                    <h6>Horaires des Diffusions</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Dimanche :</strong> 9h00 - 12h00 (Culte principal)</li>
                                        <li><strong>Mercredi :</strong> 18h00 - 20h00 (Prière & Étude)</li>
                                        <li><strong>Vendredi :</strong> 18h00 - 20h00 (Jeunesse)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal pour les vidéos -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
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

<!-- Modal pour les images -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <!-- L'image sera injectée ici par JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour ouvrir une image dans la modal
function openImageModal(imageSrc, title) {
    const modal = document.getElementById('imageModal');
    const modalTitle = modal.querySelector('.modal-title');
    const modalBody = modal.querySelector('.modal-body');
    
    modalTitle.textContent = title;
    modalBody.innerHTML = `<img src="${imageSrc}" alt="${title}" class="img-fluid">`;
    
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

// Fonction pour partager une prédication
function shareSermon(title, videoUrl) {
    const text = `Écoutez cette prédication inspirante: "${title}" - Église Evangelical Restoration Church Goma`;
    const url = videoUrl || window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url
        });
    } else {
        const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}`;
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
