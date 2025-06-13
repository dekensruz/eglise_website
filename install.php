<?php
/**
 * Script d'installation pour Evangelical Restoration Church Goma
 * Ce script crée la base de données et les tables nécessaires
 */

// Configuration de la base de données
$host = 'localhost';
$db_name = 'restoration_church';
$username = 'root';
$password = '';

try {
    // Connexion à MySQL (sans spécifier de base de données)
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "✓ Base de données '$db_name' créée ou existe déjà.<br>";
    
    // Se connecter à la base de données
    $pdo->exec("USE `$db_name`");
    
    // Créer les tables
    
    // Table des événements
    $events_table = "
    CREATE TABLE IF NOT EXISTS `events` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text,
        `event_date` datetime NOT NULL,
        `location` varchar(255) DEFAULT NULL,
        `image` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $pdo->exec($events_table);
    echo "✓ Table 'events' créée.<br>";
    
    // Table des prédications
    $sermons_table = "
    CREATE TABLE IF NOT EXISTS `sermons` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text,
        `preacher` varchar(255) DEFAULT NULL,
        `sermon_date` date DEFAULT NULL,
        `video_url` varchar(500) DEFAULT NULL,
        `audio_url` varchar(500) DEFAULT NULL,
        `pdf_url` varchar(500) DEFAULT NULL,
        `thumbnail` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $pdo->exec($sermons_table);
    echo "✓ Table 'sermons' créée.<br>";
    
    // Table de la galerie
    $gallery_table = "
    CREATE TABLE IF NOT EXISTS `gallery` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text,
        `image_path` varchar(255) NOT NULL,
        `category` varchar(100) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $pdo->exec($gallery_table);
    echo "✓ Table 'gallery' créée.<br>";
    
    // Table des administrateurs
    $admin_table = "
    CREATE TABLE IF NOT EXISTS `admins` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `full_name` varchar(255) DEFAULT NULL,
        `role` enum('admin','editor') NOT NULL DEFAULT 'editor',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $pdo->exec($admin_table);
    echo "✓ Table 'admins' créée.<br>";
    
    // Table de la newsletter
    $newsletter_table = "
    CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(100) NOT NULL,
        `name` varchar(255) DEFAULT NULL,
        `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `is_active` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $pdo->exec($newsletter_table);
    echo "✓ Table 'newsletter_subscribers' créée.<br>";
    
    // Table des messages de contact
    $contact_table = "
    CREATE TABLE IF NOT EXISTS `contact_messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(100) NOT NULL,
        `subject` varchar(255) DEFAULT NULL,
        `message` text NOT NULL,
        `is_read` tinyint(1) NOT NULL DEFAULT '0',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";
    $pdo->exec($contact_table);
    echo "✓ Table 'contact_messages' créée.<br>";
    
    // Créer l'administrateur par défaut
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $admin_insert = "
    INSERT IGNORE INTO `admins` (`username`, `email`, `password`, `full_name`, `role`) 
    VALUES ('admin', 'admin@restorationchurch.cd', ?, 'Administrateur Principal', 'admin')
    ";
    $stmt = $pdo->prepare($admin_insert);
    $stmt->execute([$admin_password]);
    echo "✓ Administrateur par défaut créé (admin / admin123).<br>";
    
    // Insérer quelques données d'exemple
    
    // Événements d'exemple
    $sample_events = [
        [
            'Culte de Réveil Spirituel',
            'Un temps spécial de réveil et de restauration spirituelle pour toute la communauté.',
            date('Y-m-d H:i:s', strtotime('+1 week Sunday 9:00')),
            'Sanctuaire Principal, Goma'
        ],
        [
            'Conférence de Jeunesse',
            'Trois jours de formation et d\'inspiration pour les jeunes de notre église.',
            date('Y-m-d H:i:s', strtotime('+2 weeks Friday 18:00')),
            'Centre de Conférence, Goma'
        ],
        [
            'Journée de Prière et Jeûne',
            'Une journée consacrée à la prière collective et au jeûne pour notre nation.',
            date('Y-m-d H:i:s', strtotime('+3 weeks Saturday 6:00')),
            'Église ERC Goma'
        ]
    ];
    
    foreach ($sample_events as $event) {
        $event_insert = "INSERT IGNORE INTO events (title, description, event_date, location) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($event_insert);
        $stmt->execute($event);
    }
    echo "✓ Événements d'exemple ajoutés.<br>";
    
    // Prédications d'exemple
    $sample_sermons = [
        [
            'La Puissance de la Restauration',
            'Message sur la capacité de Dieu à restaurer ce qui était brisé dans nos vies.',
            'Pasteure Hadasse',
            date('Y-m-d', strtotime('-1 week')),
            'https://www.youtube.com/watch?v=example1',
            null,
            null
        ],
        [
            'Marcher dans sa Destinée',
            'Découvrir et accomplir le plan de Dieu pour votre vie.',
            'Pasteure Hadasse',
            date('Y-m-d', strtotime('-2 weeks')),
            'https://www.youtube.com/watch?v=example2',
            null,
            null
        ],
        [
            'La Foi qui Déplace les Montagnes',
            'Comprendre et exercer une foi puissante qui transforme les situations.',
            'Pasteur Jean-Baptiste',
            date('Y-m-d', strtotime('-3 weeks')),
            'https://www.youtube.com/watch?v=example3',
            null,
            null
        ]
    ];
    
    foreach ($sample_sermons as $sermon) {
        $sermon_insert = "INSERT IGNORE INTO sermons (title, description, preacher, sermon_date, video_url, audio_url, pdf_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sermon_insert);
        $stmt->execute($sermon);
    }
    echo "✓ Prédications d'exemple ajoutées.<br>";
    
    // Images de galerie d'exemple (utilisant les images existantes)
    $sample_gallery = [
        ['Culte Dominical', 'Moment de louange et d\'adoration', 'images/img1.jpg', 'Cultes'],
        ['Prière Collective', 'Temps de prière en communauté', 'images/img3.jpg', 'Prière'],
        ['Jeunesse en Action', 'Activités du ministère jeunesse', 'images/img5.jpg', 'Jeunesse'],
        ['Communion Fraternelle', 'Moments de partage entre frères et sœurs', 'images/img7.jpg', 'Communion'],
        ['Baptême', 'Cérémonie de baptême', 'images/img8.jpg', 'Sacrements'],
        ['Formation Biblique', 'Sessions d\'étude de la Bible', 'images/img9.jpg', 'Formation']
    ];
    
    foreach ($sample_gallery as $image) {
        $gallery_insert = "INSERT IGNORE INTO gallery (title, description, image_path, category) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($gallery_insert);
        $stmt->execute($image);
    }
    echo "✓ Images de galerie d'exemple ajoutées.<br>";
    
    echo "<br><strong>🎉 Installation terminée avec succès !</strong><br><br>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Informations importantes :</h3>";
    echo "<ul>";
    echo "<li><strong>Base de données :</strong> $db_name</li>";
    echo "<li><strong>Utilisateur admin :</strong> admin</li>";
    echo "<li><strong>Mot de passe admin :</strong> admin123</li>";
    echo "<li><strong>URL d'administration :</strong> <a href='admin/login.php'>admin/login.php</a></li>";
    echo "<li><strong>Site web :</strong> <a href='index.php'>index.php</a></li>";
    echo "</ul>";
    echo "<p><strong>⚠️ Important :</strong> Changez le mot de passe administrateur après la première connexion !</p>";
    echo "</div>";
    
    echo "<p><a href='index.php' class='btn btn-primary'>Voir le Site Web</a> ";
    echo "<a href='admin/login.php' class='btn btn-success'>Administration</a></p>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Erreur d'installation :</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>Vérifiez :</strong></p>";
    echo "<ul>";
    echo "<li>Que MySQL/MariaDB est démarré</li>";
    echo "<li>Que les paramètres de connexion sont corrects</li>";
    echo "<li>Que l'utilisateur a les permissions nécessaires</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Evangelical Restoration Church Goma</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <h1>🏛️ Installation - Evangelical Restoration Church Goma</h1>
    <hr>
</body>
</html>
