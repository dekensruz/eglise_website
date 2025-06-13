<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID non fourni']);
    exit;
}

// Connexion à la base de données
require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les informations du membre
$member_id = (int)$_GET['id'];

$query = "SELECT * FROM team_members WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $member_id);
$stmt->execute();

$member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($member) {
    // Assurer que les données sont correctement encodées pour JSON
    // S'assurer que toutes les chaînes sont en UTF-8
    array_walk_recursive($member, function(&$item) {
        if (is_string($item)) {
            // Vérifier si la chaîne est déjà en UTF-8
            if (!mb_check_encoding($item, 'UTF-8')) {
                $item = utf8_encode($item);
            }
        }
    });
    
    header('Content-Type: application/json; charset=UTF-8');    
    echo json_encode($member, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Membre non trouvé']);
}