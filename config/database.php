<?php
/**
 * Configuration de la base de données pour Evangelical Restoration Church Goma
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'restoration_church';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erreur de connexion: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

// Fonction pour créer les tables de base
function createTables() {
    $database = new Database();
    $db = $database->getConnection();
    
    // Table des événements
    $events_table = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        event_date DATETIME NOT NULL,
        location VARCHAR(255),
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    // Table des prédications
    $sermons_table = "CREATE TABLE IF NOT EXISTS sermons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        preacher VARCHAR(255),
        sermon_date DATE,
        video_url VARCHAR(500),
        audio_url VARCHAR(500),
        pdf_url VARCHAR(500),
        thumbnail VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Table des galeries
    $gallery_table = "CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image_path VARCHAR(255) NOT NULL,
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Table des administrateurs
    $admin_table = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255),
        role ENUM('admin', 'editor') DEFAULT 'editor',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Table de la newsletter
    $newsletter_table = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) UNIQUE NOT NULL,
        name VARCHAR(255),
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_active BOOLEAN DEFAULT TRUE
    )";
    
    // Table des messages de contact
    $contact_table = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(255),
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    try {
        $db->exec($events_table);
        $db->exec($sermons_table);
        $db->exec($gallery_table);
        $db->exec($admin_table);
        $db->exec($newsletter_table);
        $db->exec($contact_table);
        
        // Créer un administrateur par défaut
        $default_admin = "INSERT IGNORE INTO admins (username, email, password, full_name, role) 
                         VALUES ('admin', 'admin@restorationchurch.cd', ?, 'Administrateur', 'admin')";
        $stmt = $db->prepare($default_admin);
        $stmt->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
        
        echo "Tables créées avec succès!";
    } catch(PDOException $exception) {
        echo "Erreur lors de la création des tables: " . $exception->getMessage();
    }
}
?>
