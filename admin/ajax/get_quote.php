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
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

// Connexion à la base de données
require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les informations de la citation
$quote_id = (int)$_GET['id'];

$query = "SELECT * FROM daily_quotes WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $quote_id);
$stmt->execute();

$quote = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quote) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Citation non trouvée']);
    exit;
}

// Assurer que les données sont correctement encodées pour JSON
// S'assurer que toutes les chaînes sont en UTF-8
array_walk_recursive($quote, function(&$item) {
    if (is_string($item)) {
        // Vérifier si la chaîne est déjà en UTF-8
        if (!mb_check_encoding($item, 'UTF-8')) {
            $item = utf8_encode($item);
        }
    }
});

// Retourner les données au format JSON
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($quote, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);