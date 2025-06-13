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

// Vérifier si la table settings existe, sinon la créer
try {
    $query = "SHOW TABLES LIKE 'settings'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Créer la table settings
        $query = "CREATE TABLE settings (
            id INT(11) NOT NULL AUTO_INCREMENT,
            setting_key VARCHAR(255) NOT NULL,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY (setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        // Insérer les paramètres par défaut
        $default_settings = [
            ['site_title', 'Restoration Church'],
            ['site_description', 'Église de la Restauration - Foi, Espérance et Amour'],
            ['church_address', '123 Rue de la Paix, 75000 Paris, France'],
            ['church_phone', '+33 1 23 45 67 89'],
            ['church_email', 'contact@restorationchurch.org'],
            ['service_times', 'Dimanche: 10h00 - 12h00\nMercredi: 19h00 - 21h00'],
            ['facebook_url', 'https://facebook.com/restorationchurch'],
            ['twitter_url', 'https://twitter.com/restorationchurch'],
            ['instagram_url', 'https://instagram.com/restorationchurch'],
            ['youtube_url', 'https://youtube.com/restorationchurch'],
            ['about_church', 'Nous sommes une église dynamique dédiée à la restauration spirituelle et à l\'épanouissement de chaque individu.'],
            ['pastor_name', 'Pasteur Jean Dupont'],
            ['pastor_message', 'Bienvenue dans notre église. Nous sommes ravis de vous accueillir parmi nous.'],
            ['google_maps_embed', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937595!2d2.292292615509614!3d48.85836507928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1621536234051!5m2!1sfr!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'],
            ['footer_text', '© 2023 Restoration Church. Tous droits réservés.'],
            ['analytics_code', '<!-- Google Analytics Code -->']
        ];
        
        foreach ($default_settings as $setting) {
            $query = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute($setting);
        }
    }
} catch (PDOException $e) {
    $message = 'Erreur lors de la vérification/création de la table settings : ' . $e->getMessage();
    $message_type = 'error';
}

// Récupérer tous les paramètres
try {
    $query = "SELECT * FROM settings ORDER BY setting_key";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convertir en tableau associatif pour un accès plus facile
    $settings_array = [];
    foreach ($settings as $setting) {
        $settings_array[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (PDOException $e) {
    $settings = [];
    $settings_array = [];
    $message = 'Erreur lors de la récupération des paramètres : ' . $e->getMessage();
    $message_type = 'error';
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_general') {
        try {
            // Mettre à jour les paramètres généraux
            $site_title = trim($_POST['site_title']);
            $site_description = trim($_POST['site_description']);
            $footer_text = trim($_POST['footer_text']);
            
            $updates = [
                ['site_title', $site_title],
                ['site_description', $site_description],
                ['footer_text', $footer_text]
            ];
            
            foreach ($updates as $update) {
                $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$update[1], $update[0]]);
            }
            
            $message = 'Paramètres généraux mis à jour avec succès !';
            $message_type = 'success';
            
            // Mettre à jour le tableau des paramètres
            $settings_array['site_title'] = $site_title;
            $settings_array['site_description'] = $site_description;
            $settings_array['footer_text'] = $footer_text;
        } catch (PDOException $e) {
            $message = 'Erreur lors de la mise à jour des paramètres généraux : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'update_contact') {
        try {
            // Mettre à jour les informations de contact
            $church_address = trim($_POST['church_address']);
            $church_phone = trim($_POST['church_phone']);
            $church_email = trim($_POST['church_email']);
            $service_times = trim($_POST['service_times']);
            $google_maps_embed = trim($_POST['google_maps_embed']);
            
            $updates = [
                ['church_address', $church_address],
                ['church_phone', $church_phone],
                ['church_email', $church_email],
                ['service_times', $service_times],
                ['google_maps_embed', $google_maps_embed]
            ];
            
            foreach ($updates as $update) {
                $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$update[1], $update[0]]);
            }
            
            $message = 'Informations de contact mises à jour avec succès !';
            $message_type = 'success';
            
            // Mettre à jour le tableau des paramètres
            $settings_array['church_address'] = $church_address;
            $settings_array['church_phone'] = $church_phone;
            $settings_array['church_email'] = $church_email;
            $settings_array['service_times'] = $service_times;
            $settings_array['google_maps_embed'] = $google_maps_embed;
        } catch (PDOException $e) {
            $message = 'Erreur lors de la mise à jour des informations de contact : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'update_social') {
        try {
            // Mettre à jour les liens sociaux
            $facebook_url = trim($_POST['facebook_url']);
            $twitter_url = trim($_POST['twitter_url']);
            $instagram_url = trim($_POST['instagram_url']);
            $youtube_url = trim($_POST['youtube_url']);
            
            $updates = [
                ['facebook_url', $facebook_url],
                ['twitter_url', $twitter_url],
                ['instagram_url', $instagram_url],
                ['youtube_url', $youtube_url]
            ];
            
            foreach ($updates as $update) {
                $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$update[1], $update[0]]);
            }
            
            $message = 'Liens sociaux mis à jour avec succès !';
            $message_type = 'success';
            
            // Mettre à jour le tableau des paramètres
            $settings_array['facebook_url'] = $facebook_url;
            $settings_array['twitter_url'] = $twitter_url;
            $settings_array['instagram_url'] = $instagram_url;
            $settings_array['youtube_url'] = $youtube_url;
        } catch (PDOException $e) {
            $message = 'Erreur lors de la mise à jour des liens sociaux : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'update_about') {
        try {
            // Mettre à jour les informations sur l'église
            $about_church = trim($_POST['about_church']);
            $pastor_name = trim($_POST['pastor_name']);
            $pastor_message = trim($_POST['pastor_message']);
            
            $updates = [
                ['about_church', $about_church],
                ['pastor_name', $pastor_name],
                ['pastor_message', $pastor_message]
            ];
            
            foreach ($updates as $update) {
                $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$update[1], $update[0]]);
            }
            
            $message = 'Informations sur l\'église mises à jour avec succès !';
            $message_type = 'success';
            
            // Mettre à jour le tableau des paramètres
            $settings_array['about_church'] = $about_church;
            $settings_array['pastor_name'] = $pastor_name;
            $settings_array['pastor_message'] = $pastor_message;
        } catch (PDOException $e) {
            $message = 'Erreur lors de la mise à jour des informations sur l\'église : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if ($action === 'update_analytics') {
        try {
            // Mettre à jour le code d'analyse
            $analytics_code = trim($_POST['analytics_code']);
            
            $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$analytics_code, 'analytics_code']);
            
            $message = 'Code d\'analyse mis à jour avec succès !';
            $message_type = 'success';
            
            // Mettre à jour le tableau des paramètres
            $settings_array['analytics_code'] = $analytics_code;
        } catch (PDOException $e) {
            $message = 'Erreur lors de la mise à jour du code d\'analyse : ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

$page_title = "Paramètres du Site";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-cogs me-2"></i>Paramètres du Site</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group" id="settings-tabs" role="tablist">
            <a class="list-group-item list-group-item-action active" id="general-tab" data-bs-toggle="list" href="#general" role="tab" aria-controls="general">
                <i class="fas fa-globe me-2"></i>Général
            </a>
            <a class="list-group-item list-group-item-action" id="contact-tab" data-bs-toggle="list" href="#contact" role="tab" aria-controls="contact">
                <i class="fas fa-address-card me-2"></i>Contact
            </a>
            <a class="list-group-item list-group-item-action" id="social-tab" data-bs-toggle="list" href="#social" role="tab" aria-controls="social">
                <i class="fas fa-share-alt me-2"></i>Réseaux Sociaux
            </a>
            <a class="list-group-item list-group-item-action" id="about-tab" data-bs-toggle="list" href="#about" role="tab" aria-controls="about">
                <i class="fas fa-church me-2"></i>À Propos
            </a>
            <a class="list-group-item list-group-item-action" id="analytics-tab" data-bs-toggle="list" href="#analytics" role="tab" aria-controls="analytics">
                <i class="fas fa-chart-line me-2"></i>Analytique
            </a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="tab-content" id="settings-tabContent">
            <!-- Paramètres généraux -->
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-globe me-2"></i>Paramètres Généraux</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_general">
                            
                            <div class="mb-3">
                                <label for="site_title" class="form-label">Titre du Site</label>
                                <input type="text" class="form-control" id="site_title" name="site_title" value="<?php echo htmlspecialchars($settings_array['site_title'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Description du Site</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings_array['site_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="footer_text" class="form-label">Texte du Pied de Page</label>
                                <input type="text" class="form-control" id="footer_text" name="footer_text" value="<?php echo htmlspecialchars($settings_array['footer_text'] ?? ''); ?>">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Paramètres de contact -->
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-address-card me-2"></i>Informations de Contact</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_contact">
                            
                            <div class="mb-3">
                                <label for="church_address" class="form-label">Adresse de l'Église</label>
                                <textarea class="form-control" id="church_address" name="church_address" rows="2"><?php echo htmlspecialchars($settings_array['church_address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="church_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="church_phone" name="church_phone" value="<?php echo htmlspecialchars($settings_array['church_phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="church_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="church_email" name="church_email" value="<?php echo htmlspecialchars($settings_array['church_email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="service_times" class="form-label">Horaires des Services</label>
                                <textarea class="form-control" id="service_times" name="service_times" rows="3"><?php echo htmlspecialchars($settings_array['service_times'] ?? ''); ?></textarea>
                                <div class="form-text">Utilisez une nouvelle ligne pour chaque horaire.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="google_maps_embed" class="form-label">Code d'intégration Google Maps</label>
                                <textarea class="form-control" id="google_maps_embed" name="google_maps_embed" rows="4"><?php echo htmlspecialchars($settings_array['google_maps_embed'] ?? ''); ?></textarea>
                                <div class="form-text">Collez ici le code iframe de Google Maps.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Paramètres des réseaux sociaux -->
            <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-share-alt me-2"></i>Réseaux Sociaux</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_social">
                            
                            <div class="mb-3">
                                <label for="facebook_url" class="form-label"><i class="fab fa-facebook me-2"></i>Facebook</label>
                                <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($settings_array['facebook_url'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="twitter_url" class="form-label"><i class="fab fa-twitter me-2"></i>Twitter</label>
                                <input type="url" class="form-control" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($settings_array['twitter_url'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="instagram_url" class="form-label"><i class="fab fa-instagram me-2"></i>Instagram</label>
                                <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($settings_array['instagram_url'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="youtube_url" class="form-label"><i class="fab fa-youtube me-2"></i>YouTube</label>
                                <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($settings_array['youtube_url'] ?? ''); ?>">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Paramètres à propos -->
            <div class="tab-pane fade" id="about" role="tabpanel" aria-labelledby="about-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-church me-2"></i>À Propos de l'Église</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_about">
                            
                            <div class="mb-3">
                                <label for="about_church" class="form-label">À Propos de l'Église</label>
                                <textarea class="form-control" id="about_church" name="about_church" rows="4"><?php echo htmlspecialchars($settings_array['about_church'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pastor_name" class="form-label">Nom du Pasteur</label>
                                <input type="text" class="form-control" id="pastor_name" name="pastor_name" value="<?php echo htmlspecialchars($settings_array['pastor_name'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="pastor_message" class="form-label">Message du Pasteur</label>
                                <textarea class="form-control" id="pastor_message" name="pastor_message" rows="4"><?php echo htmlspecialchars($settings_array['pastor_message'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Paramètres d'analytique -->
            <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Code d'Analytique</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_analytics">
                            
                            <div class="mb-3">
                                <label for="analytics_code" class="form-label">Code d'Analytique (Google Analytics, etc.)</label>
                                <textarea class="form-control" id="analytics_code" name="analytics_code" rows="6"><?php echo htmlspecialchars($settings_array['analytics_code'] ?? ''); ?></textarea>
                                <div class="form-text">Collez ici le code de suivi fourni par votre service d'analytique.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>