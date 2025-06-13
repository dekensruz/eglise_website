<?php
$page_title = "Galerie complète";
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupérer la catégorie demandée
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Initialiser les variables
$gallery_images = [];
$categories = [];

try {
    // Récupérer les catégories d'images
    $cat_query = "SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != '' ORDER BY category";
    $cat_stmt = $db->prepare($cat_query);
    $cat_stmt->execute();
    $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Construire la requête en fonction de la catégorie
    $query = "SELECT * FROM gallery";
    $params = [];
    
    if (!empty($category)) {
        $query .= " WHERE category = :category";
        $params[':category'] = $category;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $gallery_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Gérer l'erreur
    $error_message = "Erreur de base de données: " . $e->getMessage();
}
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh;">
    <div class="hero-bg" style="background-image: url('images/img1.jpg')"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Galerie complète</h1>
                <p class="hero-subtitle">
                    Tous les moments capturés de notre communauté
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Galerie -->
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
                        <a href="all-gallery.php" class="btn <?php echo empty($category) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            Tous
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="all-gallery.php?category=<?php echo urlencode($cat); ?>" class="btn <?php echo $category === $cat ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (empty($gallery_images)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-images text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Aucune image trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                </div>
            <?php else: ?>
                <div class="row">
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
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

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


</script>



<?php require_once 'includes/footer.php'; ?>




