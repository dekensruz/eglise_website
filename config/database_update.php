<?php
/**
 * Script de mise à jour de la base de données pour ajouter les nouvelles tables
 * - team_members : pour la gestion des membres de l'équipe de leadership
 * - daily_quotes : pour les citations du jour
 */

require_once 'database.php';

function updateDatabase() {
    $database = new Database();
    $db = $database->getConnection();
    
    // Table des membres de l'équipe
    $team_members_table = "CREATE TABLE IF NOT EXISTS team_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        role VARCHAR(255) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        order_number INT DEFAULT 0,
        facebook VARCHAR(255),
        twitter VARCHAR(255),
        instagram VARCHAR(255),
        linkedin VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(50),
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    // Table des citations du jour
    $daily_quotes_table = "CREATE TABLE IF NOT EXISTS daily_quotes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        quote TEXT NOT NULL,
        author VARCHAR(255),
        bible_reference VARCHAR(255),
        is_active BOOLEAN DEFAULT TRUE,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
    )";
    
    try {
        $db->exec($team_members_table);
        $db->exec($daily_quotes_table);
        
        echo "Tables créées avec succès!";
        return true;
    } catch(PDOException $exception) {
        echo "Erreur lors de la création des tables: " . $exception->getMessage();
        return false;
    }
}

// Exécuter la mise à jour
updateDatabase();

echo "<br><a href='../admin/dashboard.php'>Retour au tableau de bord</a>";
?>