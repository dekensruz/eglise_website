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
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis.']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
        exit;
    }
    
    // Préparer le nom complet
    $fullName = $firstName . ' ' . $lastName;
    
    // Insérer le message de contact
    $query = "INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($query);
    $stmt->execute([$fullName, $email, $subject, $message]);
    
    // Si l'utilisateur veut s'inscrire à la newsletter
    if ($newsletter) {
        $newsletter_query = "INSERT IGNORE INTO newsletter_subscribers (email, name, subscribed_at) VALUES (?, ?, NOW())";
        $newsletter_stmt = $db->prepare($newsletter_query);
        $newsletter_stmt->execute([$email, $fullName]);
    }
    
    // Envoyer un email de notification (optionnel)
    $to = 'info@restorationchurch.cd'; // Remplacez par votre email
    $email_subject = 'Nouveau message de contact - ' . $subject;
    $email_body = "
    Nouveau message reçu sur le site web:
    
    Nom: $fullName
    Email: $email
    Téléphone: $phone
    Sujet: $subject
    
    Message:
    $message
    
    Newsletter: " . ($newsletter ? 'Oui' : 'Non') . "
    
    Date: " . date('d/m/Y H:i:s');
    
    $headers = "From: noreply@restorationchurch.cd\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Tentative d'envoi d'email (peut échouer selon la configuration du serveur)
    @mail($to, $email_subject, $email_body, $headers);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons bientôt.'
    ]);
    
} catch (PDOException $e) {
    error_log("Erreur base de données contact: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement. Veuillez réessayer.']);
} catch (Exception $e) {
    error_log("Erreur contact: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}
?>
