<?php
// Vérifier si l'utilisateur est connecté en tant qu'administrateur
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Accès non autorisé'
    ]);
    exit;
}

// Vérifier si l'ID du membre est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'ID du membre non fourni'
    ]);
    exit;
}

// Connexion à la base de données
require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

try {
    // Récupérer les informations du membre
    $member_id = $_GET['id'];
    $query = "SELECT * FROM church_members WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $member_id);
    $stmt->execute();
    
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($member) {
        // Retourner les données du membre
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => true,
            'member' => $member
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        // Membre non trouvé
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Membre non trouvé'
        ]);
    }
} catch(PDOException $e) {
    // Erreur de base de données
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}