<?php
/**
 * Script de mise à jour de la base de données pour ajouter les colonnes 'category' aux tables events et sermons
 */

require_once 'database.php';

function updateDatabaseCategories() {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        // Ajouter la colonne category à la table events si elle n'existe pas déjà
        $check_events_query = "SHOW COLUMNS FROM events LIKE 'category'";
        $check_events_stmt = $db->prepare($check_events_query);
        $check_events_stmt->execute();
        
        if ($check_events_stmt->rowCount() == 0) {
            $events_update = "ALTER TABLE events ADD COLUMN category VARCHAR(100) AFTER location";
            $db->exec($events_update);
            echo "Colonne 'category' ajoutée à la table 'events' avec succès!<br>";
        } else {
            echo "La colonne 'category' existe déjà dans la table 'events'.<br>";
        }
        
        // Ajouter la colonne category à la table sermons si elle n'existe pas déjà
        $check_sermons_query = "SHOW COLUMNS FROM sermons LIKE 'category'";
        $check_sermons_stmt = $db->prepare($check_sermons_query);
        $check_sermons_stmt->execute();
        
        if ($check_sermons_stmt->rowCount() == 0) {
            $sermons_update = "ALTER TABLE sermons ADD COLUMN category VARCHAR(100) AFTER preacher";
            $db->exec($sermons_update);
            echo "Colonne 'category' ajoutée à la table 'sermons' avec succès!<br>";
        } else {
            echo "La colonne 'category' existe déjà dans la table 'sermons'.<br>";
        }
        
        return true;
    } catch(PDOException $exception) {
        echo "Erreur lors de la mise à jour des tables: " . $exception->getMessage() . "<br>";
        return false;
    }
}

// Exécuter la mise à jour si le script est appelé directement
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    updateDatabaseCategories();
    echo "<br><a href='../admin/dashboard.php'>Retour au tableau de bord</a>";
}
?>