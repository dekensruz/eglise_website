<?php
/**
 * Script de mise à jour de la base de données pour ajouter la table des membres de l'église
 */

require_once 'database.php';

function updateDatabaseMembers() {
    $database = new Database();
    $db = $database->getConnection();
    
    // Table des membres de l'église
    $church_members_table = "CREATE TABLE IF NOT EXISTS church_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        gender ENUM('M', 'F') NOT NULL,
        birth_date DATE,
        address TEXT,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(255),
        profession VARCHAR(255),
        join_date DATE,
        baptism_date DATE,
        marital_status ENUM('single', 'married', 'divorced', 'widowed') DEFAULT 'single',
        ministry VARCHAR(255),
        emergency_contact_name VARCHAR(255),
        emergency_contact_phone VARCHAR(50),
        notes TEXT,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    try {
        $db->exec($church_members_table);
        
        echo "Table des membres de l'église créée avec succès!";
        return true;
    } catch(PDOException $exception) {
        echo "Erreur lors de la création de la table des membres: " . $exception->getMessage();
        return false;
    }
}

// Exécuter la mise à jour si le script est appelé directement
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    updateDatabaseMembers();
}
?>