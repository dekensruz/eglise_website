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

// Créer le dossier de sauvegarde s'il n'existe pas
$backup_dir = '../backups';
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_backup') {
        // Créer une sauvegarde de la base de données
        $backup_file = $backup_dir . '/backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        try {
            // Récupérer les informations de connexion à la base de données
            $host = 'localhost';
            $dbname = 'restoration_church';
            $username = 'root';
            $password = '';
            
            // Commande pour exporter la base de données
            $command = "mysqldump --host={$host} --user={$username}";
            if ($password) {
                $command .= " --password={$password}";
            }
            $command .= " {$dbname} > {$backup_file}";
            
            // Exécuter la commande
            $output = [];
            $return_var = 0;
            exec($command, $output, $return_var);
            
            if ($return_var === 0) {
                // Créer un fichier zip
                $zip_file = $backup_dir . '/backup_' . date('Y-m-d_H-i-s') . '.zip';
                $zip = new ZipArchive();
                
                if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
                    $zip->addFile($backup_file, basename($backup_file));
                    $zip->close();
                    
                    // Supprimer le fichier SQL
                    unlink($backup_file);
                    
                    $message = 'Sauvegarde créée avec succès : ' . basename($zip_file);
                    $message_type = 'success';
                } else {
                    $message = 'Erreur lors de la création du fichier ZIP';
                    $message_type = 'error';
                }
            } else {
                $message = 'Erreur lors de la création de la sauvegarde';
                $message_type = 'error';
            }
        } catch (Exception $e) {
            $message = 'Erreur : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'delete_backup') {
        $backup_file = $_POST['backup_file'];
        $full_path = $backup_dir . '/' . basename($backup_file);
        
        // Vérifier que le fichier existe et est dans le dossier de sauvegarde
        if (file_exists($full_path) && is_file($full_path) && strpos(realpath($full_path), realpath($backup_dir)) === 0) {
            if (unlink($full_path)) {
                $message = 'Sauvegarde supprimée avec succès';
                $message_type = 'success';
            } else {
                $message = 'Erreur lors de la suppression de la sauvegarde';
                $message_type = 'error';
            }
        } else {
            $message = 'Fichier de sauvegarde invalide';
            $message_type = 'error';
        }
    }
    
    if ($action === 'restore_backup') {
        $backup_file = $_POST['backup_file'];
        $full_path = $backup_dir . '/' . basename($backup_file);
        
        // Vérifier que le fichier existe et est dans le dossier de sauvegarde
        if (file_exists($full_path) && is_file($full_path) && strpos(realpath($full_path), realpath($backup_dir)) === 0) {
            try {
                // Extraire le fichier ZIP
                $zip = new ZipArchive();
                if ($zip->open($full_path) === TRUE) {
                    $extract_dir = $backup_dir . '/temp_' . time();
                    if (!file_exists($extract_dir)) {
                        mkdir($extract_dir, 0755, true);
                    }
                    
                    $zip->extractTo($extract_dir);
                    $zip->close();
                    
                    // Trouver le fichier SQL
                    $sql_files = glob($extract_dir . '/*.sql');
                    if (count($sql_files) > 0) {
                        $sql_file = $sql_files[0];
                        
                        // Récupérer les informations de connexion à la base de données
                        $host = 'localhost';
                        $dbname = 'restoration_church';
                        $username = 'root';
                        $password = '';
                        
                        // Commande pour importer la base de données
                        $command = "mysql --host={$host} --user={$username}";
                        if ($password) {
                            $command .= " --password={$password}";
                        }
                        $command .= " {$dbname} < {$sql_file}";
                        
                        // Exécuter la commande
                        $output = [];
                        $return_var = 0;
                        exec($command, $output, $return_var);
                        
                        if ($return_var === 0) {
                            $message = 'Restauration effectuée avec succès';
                            $message_type = 'success';
                        } else {
                            $message = 'Erreur lors de la restauration de la base de données';
                            $message_type = 'error';
                        }
                    } else {
                        $message = 'Aucun fichier SQL trouvé dans la sauvegarde';
                        $message_type = 'error';
                    }
                    
                    // Nettoyer les fichiers temporaires
                    array_map('unlink', glob($extract_dir . '/*.*'));
                    rmdir($extract_dir);
                } else {
                    $message = 'Erreur lors de l\'ouverture du fichier ZIP';
                    $message_type = 'error';
                }
            } catch (Exception $e) {
                $message = 'Erreur : ' . $e->getMessage();
                $message_type = 'error';
            }
        } else {
            $message = 'Fichier de sauvegarde invalide';
            $message_type = 'error';
        }
    }
    
    if ($action === 'download_backup') {
        $backup_file = $_POST['backup_file'];
        $full_path = $backup_dir . '/' . basename($backup_file);
        
        // Vérifier que le fichier existe et est dans le dossier de sauvegarde
        if (file_exists($full_path) && is_file($full_path) && strpos(realpath($full_path), realpath($backup_dir)) === 0) {
            // Télécharger le fichier
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($full_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));
            readfile($full_path);
            exit;
        } else {
            $message = 'Fichier de sauvegarde invalide';
            $message_type = 'error';
        }
    }
}

// Récupérer la liste des sauvegardes
$backups = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'zip') {
            $backups[] = [
                'name' => $file,
                'size' => filesize($backup_dir . '/' . $file),
                'date' => filemtime($backup_dir . '/' . $file)
            ];
        }
    }
    
    // Trier par date (plus récent en premier)
    usort($backups, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}

$page_title = "Sauvegarde et Restauration";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-database me-2"></i>Sauvegarde et Restauration</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <form method="POST" onsubmit="return confirmBackup('Êtes-vous sûr de vouloir créer une nouvelle sauvegarde ?')">
            <input type="hidden" name="action" value="create_backup">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-download me-2"></i>Créer une Sauvegarde
            </button>
        </form>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Informations sur la sauvegarde -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Informations sur la Sauvegarde</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <p><i class="fas fa-lightbulb me-2"></i><strong>Conseils pour la sauvegarde :</strong></p>
            <ul>
                <li>Créez régulièrement des sauvegardes de votre base de données.</li>
                <li>Téléchargez les sauvegardes sur votre ordinateur pour plus de sécurité.</li>
                <li>Avant de restaurer une sauvegarde, assurez-vous de comprendre que cela remplacera toutes les données actuelles.</li>
            </ul>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Sauvegardes</h6>
                                <h3><?php echo count($backups); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-save fa-2x"></i>
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
                                <h6 class="card-title">Dernière Sauvegarde</h6>
                                <h3><?php echo count($backups) > 0 ? date('d/m/Y', $backups[0]['date']) : 'Aucune'; ?></h3>
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
                                <h6 class="card-title">Espace Utilisé</h6>
                                <h3>
                                    <?php 
                                    $total_size = 0;
                                    foreach ($backups as $backup) {
                                        $total_size += $backup['size'];
                                    }
                                    echo round($total_size / (1024 * 1024), 2) . ' MB'; 
                                    ?>
                                </h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hdd fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des sauvegardes -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Liste des Sauvegardes</h5>
    </div>
    <div class="card-body">
        <?php if (empty($backups)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Aucune sauvegarde disponible. Cliquez sur "Créer une Sauvegarde" pour commencer.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom du fichier</th>
                            <th>Date</th>
                            <th>Taille</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($backup['name']); ?></td>
                                <td><?php echo date('d/m/Y H:i:s', $backup['date']); ?></td>
                                <td><?php echo round($backup['size'] / 1024, 2) . ' KB'; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="download_backup">
                                            <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($backup['name']); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Télécharger">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirmRestore('Êtes-vous sûr de vouloir restaurer cette sauvegarde ? Toutes les données actuelles seront remplacées.')">
                                            <input type="hidden" name="action" value="restore_backup">
                                            <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($backup['name']); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restaurer">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer cette sauvegarde ?')">
                                            <input type="hidden" name="action" value="delete_backup">
                                            <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($backup['name']); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
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

<script>
function confirmBackup(message) {
    return confirm(message);
}

function confirmRestore(message) {
    return confirm(message);
}

function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php include 'includes/admin_footer.php'; ?>