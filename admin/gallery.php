<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $image_path = $_POST['existing_image'] ?? '';
        
        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = 'gallery_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'images/gallery/' . $new_filename;
                }
            } else {
                $message = 'Format de fichier non autorisé. Utilisez JPG, PNG ou GIF.';
                $message_type = 'error';
            }
        }
        
        if (empty($message)) {
            try {
                if ($action === 'add') {
                    if (empty($image_path)) {
                        $message = 'Veuillez sélectionner une image.';
                        $message_type = 'error';
                    } else {
                        $query = "INSERT INTO gallery (title, description, image_path, category) VALUES (?, ?, ?, ?)";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$title, $description, $image_path, $category]);
                        $message = 'Image ajoutée avec succès !';
                        $message_type = 'success';
                    }
                } else {
                    $gallery_id = $_POST['gallery_id'];
                    $query = "UPDATE gallery SET title = ?, description = ?, category = ?";
                    $params = [$title, $description, $category];
                    
                    if (!empty($image_path)) {
                        $query .= ", image_path = ?";
                        $params[] = $image_path;
                    }
                    
                    $query .= " WHERE id = ?";
                    $params[] = $gallery_id;
                    
                    $stmt = $db->prepare($query);
                    $stmt->execute($params);
                    $message = 'Image modifiée avec succès !';
                    $message_type = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    
    if ($action === 'delete') {
        $gallery_id = $_POST['gallery_id'];
        try {
            // Récupérer le chemin de l'image avant de la supprimer
            $query = "SELECT image_path FROM gallery WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$gallery_id]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Supprimer l'entrée de la base de données
            $query = "DELETE FROM gallery WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$gallery_id]);
            
            // Supprimer le fichier image si possible
            if ($image && !empty($image['image_path'])) {
                $file_path = '../' . $image['image_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            $message = 'Image supprimée avec succès !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur lors de la suppression : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Récupérer toutes les images de la galerie
try {
    $query = "SELECT * FROM gallery ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $gallery_items = [];
}

// Récupérer les catégories uniques
$categories = [];
foreach ($gallery_items as $item) {
    if (!empty($item['category']) && !in_array($item['category'], $categories)) {
        $categories[] = $item['category'];
    }
}

// Récupérer une image pour modification
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    try {
        $query = "SELECT * FROM gallery WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$edit_id]);
        $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $edit_item = null;
    }
}

$page_title = "Gestion de la Galerie";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-images me-2"></i>Gestion de la Galerie
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#galleryModal">
            <i class="fas fa-plus me-2"></i>Ajouter une Image
        </button>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Images</h6>
                        <h3><?php echo count($gallery_items); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-images fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Catégories</h6>
                        <h3><?php echo count($categories); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Ce Mois</h6>
                        <h3><?php echo count(array_filter($gallery_items, function($i) { return date('Y-m', strtotime($i['created_at'])) === date('Y-m'); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres de catégorie -->
<?php if (!empty($categories)): ?>
<div class="mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>Filtrer par Catégorie
            </h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-primary filter-btn active" data-category="all">
                    Toutes
                </button>
                <?php foreach ($categories as $category): ?>
                    <button type="button" class="btn btn-outline-secondary filter-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Galerie d'images -->
<div class="row" id="galleryContainer">
    <?php foreach ($gallery_items as $item): ?>
        <div class="col-md-4 col-lg-3 mb-4 gallery-item" data-category="<?php echo htmlspecialchars($item['category'] ?? ''); ?>">
            <div class="card h-100">
                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                    <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . (strlen($item['description']) > 100 ? '...' : ''); ?></p>
                    <?php if (!empty($item['category'])): ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($item['category']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editGalleryItem(<?php echo $item['id']; ?>)">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteGalleryItem(<?php echo $item['id']; ?>)">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($gallery_items)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>Aucune image dans la galerie. Cliquez sur "Ajouter une Image" pour commencer.
    </div>
<?php endif; ?>

<!-- Modal pour ajouter/modifier une image -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="galleryModalTitle">Ajouter une Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="galleryForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="gallery_id" id="galleryId">
                    <input type="hidden" name="existing_image" id="existingImage">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie</label>
                                <input type="text" class="form-control" id="category" name="category" list="categoryList">
                                <datalist id="categoryList">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image *</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                        <small class="form-text text-muted">Formats acceptés: JPG, PNG, GIF. Taille max: 5MB</small>
                        <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                            <img id="imagePreview" src="" alt="Aperçu" style="max-width: 100%; max-height: 300px;" class="img-thumbnail">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonction pour éditer une image
function editGalleryItem(itemId) {
    // Récupérer les données de l'image via AJAX ou depuis les données PHP
    <?php if (!empty($gallery_items)): ?>
    const galleryItems = <?php echo json_encode($gallery_items); ?>;
    const item = galleryItems.find(i => i.id == itemId);
    
    if (item) {
        document.getElementById('galleryModalTitle').textContent = 'Modifier l\'Image';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('galleryId').value = item.id;
        document.getElementById('title').value = item.title;
        document.getElementById('description').value = item.description || '';
        document.getElementById('category').value = item.category || '';
        document.getElementById('existingImage').value = item.image_path;
        
        // Afficher l'aperçu de l'image existante
        document.getElementById('imagePreview').src = '../' + item.image_path;
        document.getElementById('imagePreviewContainer').style.display = 'block';
        
        // Rendre le champ d'image facultatif pour l'édition
        document.getElementById('image').removeAttribute('required');
        
        const modal = new bootstrap.Modal(document.getElementById('galleryModal'));
        modal.show();
    }
    <?php endif; ?>
}

// Fonction pour supprimer une image
function deleteGalleryItem(itemId) {
    if (confirmDelete('Êtes-vous sûr de vouloir supprimer cette image ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="gallery_id" value="${itemId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Fonction pour prévisualiser l'image
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const container = document.getElementById('imagePreviewContainer');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Réinitialiser le formulaire quand on ferme la modal
document.getElementById('galleryModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('galleryForm').reset();
    document.getElementById('galleryModalTitle').textContent = 'Ajouter une Image';
    document.getElementById('formAction').value = 'add';
    document.getElementById('galleryId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('image').setAttribute('required', '');
});

// Fonction de confirmation de suppression
function confirmDelete(message) {
    return confirm(message);
}

// Filtrage par catégorie
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mettre à jour les classes actives
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.getAttribute('data-category');
            
            // Filtrer les éléments
            galleryItems.forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>