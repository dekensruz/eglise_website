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
        $preacher = trim($_POST['preacher'] ?? '');
        $sermon_date = $_POST['sermon_date'] ?? '';
        $video_url = trim($_POST['video_url'] ?? '');
        $audio_url = trim($_POST['audio_url'] ?? '');
        $pdf_url = trim($_POST['pdf_url'] ?? '');
        $thumbnail = $_POST['existing_thumbnail'] ?? '';
        
        // Gestion de l'upload de la miniature
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/sermons/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = 'sermon_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                    $thumbnail = 'images/sermons/' . $new_filename;
                }
            }
        }
        
        try {
            if ($action === 'add') {
                $query = "INSERT INTO sermons (title, description, preacher, sermon_date, video_url, audio_url, pdf_url, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $description, $preacher, $sermon_date, $video_url, $audio_url, $pdf_url, $thumbnail]);
                $message = 'Prédication ajoutée avec succès !';
                $message_type = 'success';
            } else {
                $sermon_id = $_POST['sermon_id'];
                $query = "UPDATE sermons SET title = ?, description = ?, preacher = ?, sermon_date = ?, video_url = ?, audio_url = ?, pdf_url = ?, thumbnail = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $description, $preacher, $sermon_date, $video_url, $audio_url, $pdf_url, $thumbnail, $sermon_id]);
                $message = 'Prédication modifiée avec succès !';
                $message_type = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'delete') {
        $sermon_id = $_POST['sermon_id'];
        try {
            $query = "DELETE FROM sermons WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$sermon_id]);
            $message = 'Prédication supprimée avec succès !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur lors de la suppression : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Récupérer toutes les prédications
try {
    $query = "SELECT * FROM sermons ORDER BY sermon_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $sermons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sermons = [];
}

// Récupérer une prédication pour modification
$edit_sermon = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    try {
        $query = "SELECT * FROM sermons WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$edit_id]);
        $edit_sermon = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $edit_sermon = null;
    }
}

$page_title = "Gestion des Prédications";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-microphone me-2"></i>Gestion des Prédications
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sermonModal">
            <i class="fas fa-plus me-2"></i>Nouvelle Prédication
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
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Prédications</h6>
                        <h3><?php echo count($sermons); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-microphone fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Ce Mois</h6>
                        <h3><?php echo count(array_filter($sermons, function($s) { return date('Y-m', strtotime($s['sermon_date'])) === date('Y-m'); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Avec Vidéo</h6>
                        <h3><?php echo count(array_filter($sermons, function($s) { return !empty($s['video_url']); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-video fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Avec Audio</h6>
                        <h3><?php echo count(array_filter($sermons, function($s) { return !empty($s['audio_url']); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-headphones fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des prédications -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Liste des Prédications
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Prédicateur</th>
                        <th>Date</th>
                        <th>Médias</th>
                        <th>Miniature</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sermons as $sermon): ?>
                        <tr>
                            <td><?php echo $sermon['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($sermon['title']); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars(substr($sermon['description'], 0, 50)) . '...'; ?>
                                </small>
                            </td>
                            <td><?php echo htmlspecialchars($sermon['preacher']); ?></td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($sermon['sermon_date'])); ?>
                            </td>
                            <td>
                                <?php if (!empty($sermon['video_url'])): ?>
                                    <span class="badge bg-primary"><i class="fas fa-video me-1"></i>Vidéo</span>
                                <?php endif; ?>
                                
                                <?php if (!empty($sermon['audio_url'])): ?>
                                    <span class="badge bg-success"><i class="fas fa-headphones me-1"></i>Audio</span>
                                <?php endif; ?>
                                
                                <?php if (!empty($sermon['pdf_url'])): ?>
                                    <span class="badge bg-danger"><i class="fas fa-file-pdf me-1"></i>PDF</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sermon['thumbnail']): ?>
                                    <img src="../<?php echo htmlspecialchars($sermon['thumbnail']); ?>" alt="Miniature" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                <?php else: ?>
                                    <span class="text-muted">Aucune</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editSermon(<?php echo $sermon['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSermon(<?php echo $sermon['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal pour ajouter/modifier une prédication -->
<div class="modal fade" id="sermonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sermonModalTitle">Nouvelle Prédication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sermonForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="sermon_id" id="sermonId">
                    <input type="hidden" name="existing_thumbnail" id="existingThumbnail">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sermon_date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="sermon_date" name="sermon_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preacher" class="form-label">Prédicateur *</label>
                        <input type="text" class="form-control" id="preacher" name="preacher" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="video_url" class="form-label">URL Vidéo</label>
                                <input type="url" class="form-control" id="video_url" name="video_url" placeholder="https://youtube.com/...">
                                <small class="form-text text-muted">YouTube, Vimeo, etc.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="audio_url" class="form-label">URL Audio</label>
                                <input type="url" class="form-control" id="audio_url" name="audio_url" placeholder="https://soundcloud.com/...">
                                <small class="form-text text-muted">SoundCloud, etc.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="pdf_url" class="form-label">URL PDF</label>
                                <input type="url" class="form-control" id="pdf_url" name="pdf_url" placeholder="https://...">
                                <small class="form-text text-muted">Notes ou transcription</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Miniature</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this, 'thumbnailPreview')">
                        <small class="form-text text-muted">Formats acceptés: JPG, PNG, GIF. Taille max: 5MB</small>
                        <img id="thumbnailPreview" src="" alt="Aperçu" style="max-width: 200px; margin-top: 10px; display: none;" class="img-thumbnail">
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
// Fonction pour éditer une prédication
function editSermon(sermonId) {
    // Récupérer les données de la prédication via AJAX ou depuis les données PHP
    <?php if (!empty($sermons)): ?>
    const sermons = <?php echo json_encode($sermons); ?>;
    const sermon = sermons.find(s => s.id == sermonId);
    
    if (sermon) {
        document.getElementById('sermonModalTitle').textContent = 'Modifier la Prédication';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('sermonId').value = sermon.id;
        document.getElementById('title').value = sermon.title;
        document.getElementById('description').value = sermon.description;
        document.getElementById('preacher').value = sermon.preacher;
        document.getElementById('sermon_date').value = sermon.sermon_date;
        document.getElementById('video_url').value = sermon.video_url || '';
        document.getElementById('audio_url').value = sermon.audio_url || '';
        document.getElementById('pdf_url').value = sermon.pdf_url || '';
        document.getElementById('existingThumbnail').value = sermon.thumbnail || '';
        
        if (sermon.thumbnail) {
            document.getElementById('thumbnailPreview').src = '../' + sermon.thumbnail;
            document.getElementById('thumbnailPreview').style.display = 'block';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('sermonModal'));
        modal.show();
    }
    <?php endif; ?>
}

// Fonction pour supprimer une prédication
function deleteSermon(sermonId) {
    if (confirmDelete('Êtes-vous sûr de vouloir supprimer cette prédication ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="sermon_id" value="${sermonId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Fonction pour prévisualiser l'image
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Réinitialiser le formulaire quand on ferme la modal
document.getElementById('sermonModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('sermonForm').reset();
    document.getElementById('sermonModalTitle').textContent = 'Nouvelle Prédication';
    document.getElementById('formAction').value = 'add';
    document.getElementById('sermonId').value = '';
    document.getElementById('existingThumbnail').value = '';
    document.getElementById('thumbnailPreview').style.display = 'none';
});

// Fonction de confirmation de suppression
function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php include 'includes/admin_footer.php'; ?>