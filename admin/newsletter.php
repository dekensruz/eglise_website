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
    
    if ($action === 'delete') {
        $subscriber_id = $_POST['subscriber_id'];
        try {
            $query = "DELETE FROM newsletter_subscribers WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$subscriber_id]);
            $message = 'Abonné supprimé avec succès !';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Erreur lors de la suppression : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'add') {
        $email = trim($_POST['email']);
        $name = trim($_POST['name']);
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Adresse email invalide !';
            $message_type = 'error';
        } else {
            // Vérifier si l'email existe déjà
            $query = "SELECT COUNT(*) FROM newsletter_subscribers WHERE email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $message = 'Cette adresse email est déjà abonnée !';
                $message_type = 'error';
            } else {
                try {
                    $query = "INSERT INTO newsletter_subscribers (name, email, created_at) VALUES (?, ?, NOW())";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$name, $email]);
                    $message = 'Abonné ajouté avec succès !';
                    $message_type = 'success';
                } catch (PDOException $e) {
                    $message = 'Erreur lors de l\'ajout : ' . $e->getMessage();
                    $message_type = 'error';
                }
            }
        }
    }
    
    if ($action === 'export') {
        try {
            $query = "SELECT name, email, created_at FROM newsletter_subscribers ORDER BY created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($subscribers) > 0) {
                // Générer le CSV
                $filename = 'newsletter_subscribers_' . date('Y-m-d') . '.csv';
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $filename);
                
                $output = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($output, ['Nom', 'Email', 'Date d\'inscription']);
                
                // Données
                foreach ($subscribers as $subscriber) {
                    fputcsv($output, [
                        $subscriber['name'],
                        $subscriber['email'],
                        $subscriber['created_at']
                    ]);
                }
                
                fclose($output);
                exit;
            } else {
                $message = 'Aucun abonné à exporter !';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Erreur lors de l\'export : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Récupérer tous les abonnés
try {
    $query = "SELECT * FROM newsletter_subscribers ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $subscribers = [];
}

// Statistiques
$total_subscribers = count($subscribers);

// Abonnés ce mois
$this_month_subscribers = 0;
foreach ($subscribers as $subscriber) {
    if (date('Y-m', strtotime($subscriber['created_at'])) === date('Y-m')) {
        $this_month_subscribers++;
    }
}

// Abonnés par domaine d'email
$email_domains = [];
foreach ($subscribers as $subscriber) {
    $parts = explode('@', $subscriber['email']);
    $domain = $parts[1] ?? 'unknown';
    if (!isset($email_domains[$domain])) {
        $email_domains[$domain] = 0;
    }
    $email_domains[$domain]++;
}

// Trier par nombre d'abonnés
arsort($email_domains);

$page_title = "Gestion de la Newsletter";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-paper-plane me-2"></i>Gestion de la Newsletter</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
            <i class="fas fa-plus me-2"></i>Ajouter un abonné
        </button>
        
        <?php if ($total_subscribers > 0): ?>
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="export">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-export me-2"></i>Exporter (CSV)
                </button>
            </form>
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
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Abonnés</h6>
                        <h3><?php echo $total_subscribers; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
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
                        <h6 class="card-title">Nouveaux ce mois</h6>
                        <h3><?php echo $this_month_subscribers; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-check fa-2x"></i>
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
                        <h6 class="card-title">Domaines d'email</h6>
                        <h3><?php echo count($email_domains); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-at fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des abonnés -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Abonnés
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($subscribers)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Aucun abonné à la newsletter pour le moment.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscribers as $subscriber): ?>
                                    <tr>
                                        <td><?php echo $subscriber['id']; ?></td>
                                        <td><?php echo htmlspecialchars($subscriber['name'] ?: 'Non spécifié'); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($subscriber['email']); ?>">
                                                <?php echo htmlspecialchars($subscriber['email']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($subscriber['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer cet abonné ?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="subscriber_id" value="<?php echo $subscriber['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Domaines d'Email
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($email_domains)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Aucune donnée disponible.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Domaine</th>
                                    <th>Nombre</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($email_domains, 0, 10) as $domain => $count): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($domain); ?></td>
                                        <td><?php echo $count; ?></td>
                                        <td>
                                            <?php echo round(($count / $total_subscribers) * 100, 1); ?>%
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?php echo ($count / $total_subscribers) * 100; ?>%"></div>
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
    </div>
</div>

<!-- Modal pour ajouter un abonné -->
<div class="modal fade" id="addSubscriberModal" tabindex="-1" aria-labelledby="addSubscriberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubscriberModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Ajouter un abonné
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom (optionnel)</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php include 'includes/admin_footer.php'; ?>