<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Récupérer les statistiques
$database = new Database();
$db = $database->getConnection();

try {
    // Statistiques générales
    $stats = [];
    
    // Nombre d'événements à venir
    $events_query = "SELECT COUNT(*) as count FROM events WHERE event_date >= NOW()";
    $events_stmt = $db->prepare($events_query);
    $events_stmt->execute();
    $stats['upcoming_events'] = $events_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre de prédications
    $sermons_query = "SELECT COUNT(*) as count FROM sermons";
    $sermons_stmt = $db->prepare($sermons_query);
    $sermons_stmt->execute();
    $stats['sermons'] = $sermons_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Nombre d'abonnés newsletter
    $newsletter_query = "SELECT COUNT(*) as count FROM newsletter_subscribers WHERE is_active = 1";
    $newsletter_stmt = $db->prepare($newsletter_query);
    $newsletter_stmt->execute();
    $stats['newsletter_subscribers'] = $newsletter_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Messages non lus
    $messages_query = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
    $messages_stmt = $db->prepare($messages_query);
    $messages_stmt->execute();
    $stats['unread_messages'] = $messages_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Images dans la galerie
    $gallery_query = "SELECT COUNT(*) as count FROM gallery";
    $gallery_stmt = $db->prepare($gallery_query);
    $gallery_stmt->execute();
    $stats['gallery_images'] = $gallery_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Derniers messages de contact
    $recent_messages_query = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5";
    $recent_messages_stmt = $db->prepare($recent_messages_query);
    $recent_messages_stmt->execute();
    $recent_messages = $recent_messages_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Prochains événements
    $upcoming_events_query = "SELECT * FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 5";
    $upcoming_events_stmt = $db->prepare($upcoming_events_query);
    $upcoming_events_stmt->execute();
    $upcoming_events = $upcoming_events_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $stats = [
        'upcoming_events' => 0,
        'sermons' => 0,
        'newsletter_subscribers' => 0,
        'unread_messages' => 0,
        'gallery_images' => 0
    ];
    $recent_messages = [];
    $upcoming_events = [];
}

$page_title = "Tableau de Bord";
include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Exporter
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if (isset($_GET['error']) && $_GET['error'] === 'restricted'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Accès refusé!</strong> Vous n'avez pas les droits nécessaires pour accéder à cette page.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Cartes de statistiques -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Événements à Venir
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['upcoming_events']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Prédications
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['sermons']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-play fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Abonnés Newsletter
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['newsletter_subscribers']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Messages Non Lus
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['unread_messages']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques et contenus -->
            <div class="row">
                <!-- Prochains événements -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar me-2"></i>Prochains Événements
                            </h6>
                            <a href="events.php" class="btn btn-sm btn-primary">Gérer</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($upcoming_events)): ?>
                                <?php foreach ($upcoming_events as $event): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <div class="bg-primary text-white rounded text-center p-2" style="min-width: 50px;">
                                                <div class="fw-bold"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                                <small><?php echo date('M', strtotime($event['event_date'])); ?></small>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('H:i', strtotime($event['event_date'])); ?>
                                                <i class="fas fa-map-marker-alt ms-2 me-1"></i>
                                                <?php echo htmlspecialchars($event['location']); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">Aucun événement programmé</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Messages récents -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-comments me-2"></i>Messages Récents
                            </h6>
                            <a href="messages.php" class="btn btn-sm btn-primary">Voir Tous</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_messages)): ?>
                                <?php foreach ($recent_messages as $message): ?>
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="me-3">
                                            <div class="bg-<?php echo $message['is_read'] ? 'secondary' : 'warning'; ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <?php echo htmlspecialchars($message['name']); ?>
                                                <?php if (!$message['is_read']): ?>
                                                    <span class="badge bg-warning">Nouveau</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1 small"><?php echo htmlspecialchars(substr($message['message'], 0, 80)) . '...'; ?></p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">Aucun message</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bolt me-2"></i>Actions Rapides
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="events.php?action=add" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-plus me-2"></i>Nouvel Événement
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="sermons.php?action=add" class="btn btn-outline-success w-100">
                                        <i class="fas fa-microphone me-2"></i>Nouvelle Prédication
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="gallery.php?action=add" class="btn btn-outline-info w-100">
                                        <i class="fas fa-images me-2"></i>Ajouter Photos
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="newsletter.php?action=send" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Envoyer Newsletter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-history me-2"></i>Activité Récente
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Connexion administrateur</h6>
                                        <p class="mb-1 small text-muted">Vous vous êtes connecté au panneau d'administration</p>
                                        <small class="text-muted">Il y a quelques instants</small>
                                    </div>
                                </div>
                                
                                <?php if (!empty($recent_messages)): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-warning"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Nouveau message reçu</h6>
                                            <p class="mb-1 small text-muted">
                                                Message de <?php echo htmlspecialchars($recent_messages[0]['name']); ?>
                                            </p>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($recent_messages[0]['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Site web mis à jour</h6>
                                        <p class="mb-1 small text-muted">Le contenu du site a été actualisé</p>
                                        <small class="text-muted">Aujourd'hui</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #2c5aa0 !important;
}
.border-left-success {
    border-left: 4px solid #28a745 !important;
}
.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}
.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #2c5aa0;
}
</style>

<?php include 'includes/admin_footer.php'; ?>
