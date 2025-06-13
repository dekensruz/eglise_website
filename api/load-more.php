<?php
header('Content-Type: application/json');

// Vérifier les paramètres requis
if (!isset($_GET['type']) || !isset($_GET['page'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Paramètres manquants',
    ]);
    exit;
}

// Récupérer les paramètres
$type = $_GET['type'];
$page = (int)$_GET['page'];
$limit = 5; // Nombre d'éléments à charger par page
$offset = ($page - 1) * $limit;

// Connexion à la base de données
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

try {
    $items = [];
    $html = '';
    $hasMore = false;
    
    // Traitement en fonction du type de contenu
    switch ($type) {
        case 'sermons':
            // Récupérer les prédications
            $query = "SELECT * FROM sermons ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Vérifier s'il y a plus d'éléments
            $countQuery = "SELECT COUNT(*) FROM sermons";
            $countStmt = $db->prepare($countQuery);
            $countStmt->execute();
            $totalItems = $countStmt->fetchColumn();
            $hasMore = ($offset + $limit) < $totalItems;
            
            // Générer le HTML pour les prédications
            foreach ($items as $sermon) {
                $html .= '<div class="col-lg-4 col-md-6 mb-4">';
                $html .= '<div class="card">';
                $html .= '<div class="position-relative">';
                
                if ($sermon['thumbnail']) {
                    $html .= '<img src="' . htmlspecialchars($sermon['thumbnail']) . '" alt="' . htmlspecialchars($sermon['title']) . '" class="card-img-top">';
                } else {
                    $html .= '<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">';
                    $html .= '<i class="fas fa-play-circle text-primary" style="font-size: 3rem;"></i>';
                    $html .= '</div>';
                }
                
                if ($sermon['video_url']) {
                    $html .= '<div class="media-overlay">';
                    $html .= '<button class="play-btn" onclick="openVideoModal(\'' . htmlspecialchars($sermon['video_url']) . '\', \'' . htmlspecialchars($sermon['title']) . '\')">';
                    $html .= '<i class="fas fa-play"></i>';
                    $html .= '</button>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '<div class="card-body">';
                $html .= '<h5 class="card-title">' . htmlspecialchars($sermon['title']) . '</h5>';
                $html .= '<p class="card-text">' . htmlspecialchars(substr($sermon['description'], 0, 100)) . '...</p>';
                
                $html .= '<div class="d-flex justify-content-between align-items-center mb-3">';
                $html .= '<small class="text-muted"><i class="fas fa-user me-1"></i>' . htmlspecialchars($sermon['preacher']) . '</small>';
                $html .= '<small class="text-muted">' . date('d/m/Y', strtotime($sermon['sermon_date'])) . '</small>';
                $html .= '</div>';
                
                $html .= '<div class="btn-group w-100" role="group">';
                
                if ($sermon['video_url']) {
                    $html .= '<button type="button" class="btn btn-primary" onclick="openVideoModal(\'' . htmlspecialchars($sermon['video_url']) . '\', \'' . htmlspecialchars($sermon['title']) . '\')">';
                    $html .= '<i class="fas fa-play me-1"></i>Vidéo';
                    $html .= '</button>';
                }
                
                if ($sermon['audio_url']) {
                    $html .= '<a href="' . htmlspecialchars($sermon['audio_url']) . '" class="btn btn-outline-primary" target="_blank">';
                    $html .= '<i class="fas fa-volume-up me-1"></i>Audio';
                    $html .= '</a>';
                }
                
                if ($sermon['pdf_url']) {
                    $html .= '<a href="' . htmlspecialchars($sermon['pdf_url']) . '" class="btn btn-outline-secondary" target="_blank">';
                    $html .= '<i class="fas fa-file-pdf me-1"></i>PDF';
                    $html .= '</a>';
                }
                
                $html .= '</div>';
                
                $html .= '<div class="mt-2">';
                $html .= '<button class="btn btn-sm btn-outline-primary" onclick="shareSermon(\'' . htmlspecialchars($sermon['title']) . '\', \'' . htmlspecialchars($sermon['video_url']) . '\')">';
                $html .= '<i class="fas fa-share me-1"></i>Partager';
                $html .= '</button>';
                $html .= '</div>';
                
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            break;
            
        case 'gallery':
            // Récupérer les images de la galerie
            $query = "SELECT * FROM gallery ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Vérifier s'il y a plus d'éléments
            $countQuery = "SELECT COUNT(*) FROM gallery";
            $countStmt = $db->prepare($countQuery);
            $countStmt->execute();
            $totalItems = $countStmt->fetchColumn();
            $hasMore = ($offset + $limit) < $totalItems;
            
            // Générer le HTML pour les images de la galerie
            foreach ($items as $image) {
                $html .= '<div class="col-lg-4 col-md-6 mb-4">';
                $html .= '<div class="media-item" onclick="openImageModal(\'' . htmlspecialchars($image['image_path']) . '\', \'' . htmlspecialchars($image['title']) . '\')">';
                $html .= '<img src="' . htmlspecialchars($image['image_path']) . '" alt="' . htmlspecialchars($image['title']) . '">';
                $html .= '<div class="media-overlay">';
                $html .= '<div class="text-center text-white">';
                $html .= '<i class="fas fa-search-plus fa-2x mb-2"></i>';
                $html .= '<h6>' . htmlspecialchars($image['title']) . '</h6>';
                
                if ($image['description']) {
                    $html .= '<p class="small">' . htmlspecialchars(substr($image['description'], 0, 50)) . '...</p>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            break;
            
        case 'past_events':
            // Récupérer les événements passés
            $query = "SELECT * FROM events WHERE event_date < CURDATE() ORDER BY event_date DESC LIMIT :limit OFFSET :offset";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Vérifier s'il y a plus d'éléments
            $countQuery = "SELECT COUNT(*) FROM events WHERE event_date < CURDATE()";
            $countStmt = $db->prepare($countQuery);
            $countStmt->execute();
            $totalItems = $countStmt->fetchColumn();
            $hasMore = ($offset + $limit) < $totalItems;
            
            // Générer le HTML pour les événements passés
            foreach ($items as $event) {
                $html .= '<div class="col-lg-4 col-md-6 mb-4">';
                $html .= '<div class="event-card">';
                
                if ($event['image']) {
                    $html .= '<img src="' . htmlspecialchars($event['image']) . '" alt="' . htmlspecialchars($event['title']) . '" class="event-img">';
                } else {
                    $html .= '<div class="event-img bg-light d-flex align-items-center justify-content-center">';
                    $html .= '<i class="fas fa-calendar-alt text-primary" style="font-size: 3rem;"></i>';
                    $html .= '</div>';
                }
                
                $html .= '<div class="event-date">';
                $html .= '<span class="day">' . date('d', strtotime($event['event_date'])) . '</span>';
                $html .= '<span class="month">' . date('M', strtotime($event['event_date'])) . '</span>';
                $html .= '</div>';
                
                $html .= '<div class="event-content">';
                $html .= '<h5>' . htmlspecialchars($event['title']) . '</h5>';
                $html .= '<p>' . htmlspecialchars(substr($event['description'], 0, 100)) . '...</p>';
                
                $html .= '<div class="event-details">';
                $html .= '<div><i class="fas fa-clock me-2"></i>' . htmlspecialchars($event['time']) . '</div>';
                
                if ($event['location']) {
                    $html .= '<div><i class="fas fa-map-marker-alt me-2"></i>' . htmlspecialchars($event['location']) . '</div>';
                }
                
                $html .= '</div>';
                
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Type de contenu non pris en charge',
            ]);
            exit;
    }
    
    // Retourner les résultats
    echo json_encode([
        'success' => true,
        'items' => $items,
        'html' => $html,
        'hasMore' => $hasMore,
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage(),
    ]);
}