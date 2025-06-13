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
    
    if ($action === 'mark_read') {
        $message_id = $_POST['message_id'];
        try {
            $query = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$message_id]);
            $message = 'Message marqué comme lu !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'mark_unread') {
        $message_id = $_POST['message_id'];
        try {
            $query = "UPDATE contact_messages SET is_read = 0 WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$message_id]);
            $message = 'Message marqué comme non lu !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'delete') {
        $message_id = $_POST['message_id'];
        try {
            $query = "DELETE FROM contact_messages WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$message_id]);
            $message = 'Message supprimé avec succès !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur lors de la suppression : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'mark_all_read') {
        try {
            $query = "UPDATE contact_messages SET is_read = 1 WHERE is_read = 0";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $message = 'Tous les messages ont été marqués comme lus !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Récupérer un message spécifique si demandé
$view_message = null;
if (isset($_GET['view'])) {
    $view_id = $_GET['view'];
    try {
        // Marquer comme lu automatiquement
        $query = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$view_id]);
        
        // Récupérer le message
        $query = "SELECT * FROM contact_messages WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$view_id]);
        $view_message = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $view_message = null;
    }
}

// Récupérer tous les messages
try {
    $query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $contact_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $contact_messages = [];
}

// Compter les messages non lus
$unread_count = 0;
foreach ($contact_messages as $msg) {
    if ($msg['is_read'] == 0) {
        $unread_count++;
    }
}

$page_title = "Gestion des Messages";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-envelope me-2"></i>Gestion des Messages
        <?php if ($unread_count > 0): ?>
            <span class="badge bg-danger"><?php echo $unread_count; ?> non lu<?php echo $unread_count > 1 ? 's' : ''; ?></span>
        <?php endif; ?>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <?php if ($unread_count > 0): ?>
            <form method="POST" class="me-2">
                <input type="hidden" name="action" value="mark_all_read">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="fas fa-check-double me-2"></i>Tout marquer comme lu
                </button>
            </form>
        <?php endif; ?>
        
        <?php if ($view_message): ?>
            <a href="messages.php" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        <?php endif; ?>
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
                        <h6 class="card-title">Total Messages</h6>
                        <h3><?php echo count($contact_messages); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Non Lus</h6>
                        <h3><?php echo $unread_count; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-envelope-open fa-2x"></i>
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
                        <h6 class="card-title">Lus</h6>
                        <h3><?php echo count($contact_messages) - $unread_count; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
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
                        <h3><?php echo count(array_filter($contact_messages, function($m) { return date('Y-m', strtotime($m['created_at'])) === date('Y-m'); })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($view_message): ?>
<!-- Affichage d'un message spécifique -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-envelope-open me-2"></i><?php echo htmlspecialchars($view_message['subject'] ?: 'Sans objet'); ?>
        </h5>
        <div>
            <span class="badge bg-secondary">
                <?php echo date('d/m/Y H:i', strtotime($view_message['created_at'])); ?>
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>De :</strong> <?php echo htmlspecialchars($view_message['name']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Email :</strong> <a href="mailto:<?php echo htmlspecialchars($view_message['email']); ?>"><?php echo htmlspecialchars($view_message['email']); ?></a></p>
            </div>
        </div>
        
        <div class="message-content p-3 bg-light rounded">
            <?php echo nl2br(htmlspecialchars($view_message['message'])); ?>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between">
        <div>
            <a href="messages.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="mailto:<?php echo htmlspecialchars($view_message['email']); ?>?subject=Re: <?php echo htmlspecialchars($view_message['subject'] ?: 'Votre message'); ?>" class="btn btn-primary ms-2">
                <i class="fas fa-reply me-2"></i>Répondre
            </a>
        </div>
        <form method="POST" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer ce message ?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="message_id" value="<?php echo $view_message['id']; ?>">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i>Supprimer
            </button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Liste des messages -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Liste des Messages
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($contact_messages)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Aucun message reçu pour le moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Statut</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Objet</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contact_messages as $msg): ?>
                            <tr class="<?php echo $msg['is_read'] == 0 ? 'table-light fw-bold' : ''; ?>">
                                <td><?php echo $msg['id']; ?></td>
                                <td>
                                    <?php if ($msg['is_read'] == 0): ?>
                                        <span class="badge bg-danger">Non lu</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Lu</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>">
                                        <?php echo htmlspecialchars($msg['email']); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="messages.php?view=<?php echo $msg['id']; ?>">
                                        <?php echo htmlspecialchars($msg['subject'] ?: 'Sans objet'); ?>
                                    </a>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="messages.php?view=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($msg['is_read'] == 0): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="mark_read">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="mark_unread">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php include 'includes/admin_footer.php'; ?>