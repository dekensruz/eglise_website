<?php
$page_title = "Événements Passés";
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupérer la catégorie demandée
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialiser les variables
$past_events = [];
$categories = [];

try {
    // Récupérer les catégories d'événements
    $cat_query = "SELECT DISTINCT category FROM events WHERE category IS NOT NULL AND category != '' ORDER BY category";
    $cat_stmt = $db->prepare($cat_query);
    $cat_stmt->execute();
    $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Construire la requête en fonction de la catégorie
    $query = "SELECT * FROM events WHERE event_date < NOW()";
    $params = [];
    
    if (!empty($category)) {
        $query .= " AND category = :category";
        $params[':category'] = $category;
    }
    
    $query .= " ORDER BY event_date DESC";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $past_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Gérer l'erreur
    $error_message = "Erreur de base de données: " . $e->getMessage();
}
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh;">
    <div class="hero-bg" style="background-image: url('images/img4.jpg')"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Événements Passés</h1>
                <p class="hero-subtitle">
                    Revivez nos moments de bénédiction et de communion fraternelle
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Événements Passés -->
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
                        <a href="past-events.php" class="btn <?php echo empty($category) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            Tous
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="past-events.php?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $category === $cat ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (empty($past_events)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Aucun événement passé trouvé</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                </div>
            <?php else: ?>
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
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>