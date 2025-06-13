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
        $event_date = $_POST['event_date'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $image = $_POST['existing_image'] ?? '';
        
        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/events/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = 'event_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'images/events/' . $new_filename;
                }
            }
        }
        
        try {
            if ($action === 'add') {
                $query = "INSERT INTO events (title, description, event_date, location, image) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $description, $event_date, $location, $image]);
                $message = 'Événement ajouté avec succès !';
                $message_type = 'success';
            } else {
                $event_id = $_POST['event_id'];
                $query = "UPDATE events SET title = ?, description = ?, event_date = ?, location = ?, image = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $description, $event_date, $location, $image, $event_id]);
                $message = 'Événement modifié avec succès !';
                $message_type = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'delete') {
        $event_id = $_POST['event_id'];
        try {
            $query = "DELETE FROM events WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$event_id]);
            $message = 'Événement supprimé avec succès !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur lors de la suppression : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Récupérer tous les événements
try {
    $query = "SELECT * FROM events ORDER BY event_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $events = [];
}

// Récupérer un événement pour modification
$edit_event = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    try {
        $query = "SELECT * FROM events WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$edit_id]);
        $edit_event = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $edit_event = null;
    }
}

$page_title = "Gestion des Événements";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar me-2"></i>Gestion des Événements
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal">
            <i class="fas fa-plus me-2"></i>Nouvel Événement
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
                        <h6 class="card-title">Total Événements</h6>
                        <h3><?php echo count($events); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar fa-2x"></i>
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
                        <h6 class="card-title">À Venir</h6>
                        <h3><?php echo count(array_filter($events, function($e) { return strtotime($e['event_date']) >= time(); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
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
                        <h6 class="card-title">Ce Mois</h6>
                        <h3><?php echo count(array_filter($events, function($e) { return date('Y-m', strtotime($e['event_date'])) === date('Y-m'); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-week fa-2x"></i>
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
                        <h6 class="card-title">Passés</h6>
                        <h3><?php echo count(array_filter($events, function($e) { return strtotime($e['event_date']) < time(); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-history fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des événements -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Liste des Événements
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Date & Heure</th>
                        <th>Lieu</th>
                        <th>Statut</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo $event['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars(substr($event['description'], 0, 50)) . '...'; ?>
                                </small>
                            </td>
                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($event['location']); ?></td>
                            <td>
                                <?php if (strtotime($event['event_date']) >= time()): ?>
                                    <span class="badge bg-success">À venir</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Passé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($event['image']): ?>
                                    <img src="../<?php echo htmlspecialchars($event['image']); ?>" alt="Image" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                <?php else: ?>
                                    <span class="text-muted">Aucune</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editEvent(<?php echo $event['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteEvent(<?php echo $event['id']; ?>)">
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

<!-- Modal pour ajouter/modifier un événement -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Nouvel Événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="eventForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="event_id" id="eventId">
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
                                <label for="event_date" class="form-label">Date & Heure *</label>
                                <input type="datetime-local" class="form-control" id="event_date" name="event_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Lieu *</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                        <small class="form-text text-muted">Formats acceptés: JPG, PNG, GIF. Taille max: 5MB</small>
                        <img id="imagePreview" src="" alt="Aperçu" style="max-width: 200px; margin-top: 10px; display: none;" class="img-thumbnail">
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
// Fonction pour éditer un événement
function editEvent(eventId) {
    // Récupérer les données de l'événement via AJAX ou depuis les données PHP
    <?php if (!empty($events)): ?>
    const events = <?php echo json_encode($events); ?>;
    const event = events.find(e => e.id == eventId);
    
    if (event) {
        document.getElementById('eventModalTitle').textContent = 'Modifier l\'Événement';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('eventId').value = event.id;
        document.getElementById('title').value = event.title;
        document.getElementById('description').value = event.description;
        document.getElementById('event_date').value = event.event_date.replace(' ', 'T');
        document.getElementById('location').value = event.location;
        document.getElementById('existingImage').value = event.image || '';
        
        if (event.image) {
            document.getElementById('imagePreview').src = '../' + event.image;
            document.getElementById('imagePreview').style.display = 'block';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        modal.show();
    }
    <?php endif; ?>
}

// Fonction pour supprimer un événement
function deleteEvent(eventId) {
    if (confirmDelete('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="event_id" value="${eventId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Réinitialiser le formulaire quand on ferme la modal
document.getElementById('eventModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('eventForm').reset();
    document.getElementById('eventModalTitle').textContent = 'Nouvel Événement';
    document.getElementById('formAction').value = 'add';
    document.getElementById('eventId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('imagePreview').style.display = 'none';
});
</script>

<?php include 'includes/admin_footer.php'; ?>
