<?php
$page_title = "Toutes les prédications";
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupérer la catégorie demandée
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialiser les variables
$sermons = [];
$categories = [];

try {
    // Récupérer les catégories de prédications
    $cat_query = "SELECT DISTINCT category FROM sermons WHERE category IS NOT NULL AND category != '' ORDER BY category";
    $cat_stmt = $db->prepare($cat_query);
    $cat_stmt->execute();
    $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Construire la requête en fonction de la catégorie
    $query = "SELECT * FROM sermons";
    $params = [];
    
    if (!empty($category)) {
        $query .= " WHERE category = :category";
        $params[':category'] = $category;
    }
    
    $query .= " ORDER BY sermon_date DESC";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $sermons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Gérer l'erreur
    $error_message = "Erreur de base de données: " . $e->getMessage();
}
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh;">
    <div class="hero-bg" style="background-image: url('images/img3.jpg')"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Toutes les prédications</h1>
                <p class="hero-subtitle">
                    Explorez notre bibliothèque de messages inspirants
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Prédications -->
<section class="section-padding">
    <div class="container">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <!-- Filtres par catégorie -->
            <?php if (!empty($categories)): ?>
                <div class="mb-4">
                    <h4 class="mb-3">Filtrer par catégorie</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="all-sermons.php" class="btn <?php echo empty($category) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            Tous
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="all-sermons.php?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $category === $cat ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (empty($sermons)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-play-circle text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Aucune prédication trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                </div>
            <?php else: ?>
                <div class="row">
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
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>