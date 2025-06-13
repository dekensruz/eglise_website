<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Récupérer et valider les données
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['name'] ?? '');
    
    // Validation
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'L\'adresse email est obligatoire.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
        exit;
    }
    
    // Vérifier si l'email existe déjà
    $check_query = "SELECT id, is_active FROM newsletter_subscribers WHERE email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$email]);
    $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        if ($existing['is_active']) {
            echo json_encode(['success' => false, 'message' => 'Cette adresse email est déjà inscrite à notre newsletter.']);
            exit;
        } else {
            // Réactiver l'abonnement
            $update_query = "UPDATE newsletter_subscribers SET is_active = 1, name = ?, subscribed_at = NOW() WHERE email = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([$name, $email]);
            
            echo json_encode(['success' => true, 'message' => 'Votre abonnement à la newsletter a été réactivé avec succès.']);
            exit;
        }
    }
    
    // Insérer le nouvel abonné
    $query = "INSERT INTO newsletter_subscribers (email, name, subscribed_at, is_active) VALUES (?, ?, NOW(), 1)";
    $stmt = $db->prepare($query);
    $stmt->execute([$email, $name]);
    
    // Envoyer un email de bienvenue (optionnel)
    $to = $email;
    $subject = 'Bienvenue dans notre newsletter - Evangelical Restoration Church Goma';
    $message_body = "
    Cher(e) " . ($name ?: 'ami(e)') . ",
    
    Merci de vous être inscrit(e) à notre newsletter !
    
    Vous recevrez désormais :
    - Les dernières nouvelles de notre église
    - Les annonces d'événements spéciaux
    - Des messages d'encouragement
    - Des ressources spirituelles
    
    Que Dieu vous bénisse !
    
    L'équipe de l'Église Evangelical Restoration Church Goma
    
    ---
    Pour vous désabonner, répondez à cet email avec 'DESABONNER' en objet.
    ";
    
    $headers = "From: newsletter@restorationchurch.cd\r\n";
    $headers .= "Reply-To: info@restorationchurch.cd\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Tentative d'envoi d'email de bienvenue
    @mail($to, $subject, $message_body, $headers);
    
    // Notification à l'admin
    $admin_email = 'info@restorationchurch.cd';
    $admin_subject = 'Nouvel abonné newsletter';
    $admin_message = "Nouvel abonné à la newsletter:\n\nNom: $name\nEmail: $email\nDate: " . date('d/m/Y H:i:s');
    @mail($admin_email, $admin_subject, $admin_message, $headers);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Inscription réussie ! Vous recevrez bientôt nos actualités.'
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur base de données newsletter: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.']);
} catch (Exception $e) {
    error_log("Erreur newsletter: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}
?>
