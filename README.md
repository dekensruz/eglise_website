# Site Web - Evangelical Restoration Church Goma

Site web moderne et dynamique pour l'Église Evangelical Restoration Church Goma, développé avec PHP, MySQL, CSS et JavaScript.

## 🌟 Fonctionnalités

### Site Public
- **Page d'accueil dynamique** avec carrousel d'images
- **Présentation de l'église** (mission, vision, valeurs, histoire)
- **Équipe pastorale** avec photos et biographies
- **Calendrier d'événements** avec système de partage
- **Section multimédia** (prédications vidéo/audio, galeries photos)
- **Formulaire de contact** avec gestion des demandes
- **Newsletter** avec système d'abonnement
- **Design responsive** compatible mobile et desktop
- **Intégration réseaux sociaux** (Facebook, YouTube, WhatsApp)

### Panneau d'Administration
- **Tableau de bord** avec statistiques en temps réel
- **Gestion des événements** (ajout, modification, suppression)
- **Gestion des prédications** (upload vidéo/audio/PDF)
- **Gestion de la galerie photos**
- **Gestion des messages de contact**
- **Gestion de la newsletter** et des abonnés
- **Système d'authentification sécurisé**
- **Interface intuitive** avec DataTables et graphiques

## 🛠️ Technologies Utilisées

- **Backend :** PHP 8+ avec PDO
- **Base de données :** MySQL/MariaDB
- **Frontend :** HTML5, CSS3, JavaScript (Vanilla)
- **Framework CSS :** Bootstrap 5
- **Icônes :** Font Awesome 6
- **Polices :** Google Fonts (Poppins)
- **Outils admin :** DataTables, Chart.js

## 📋 Prérequis

- **Serveur web** : Apache/Nginx avec PHP 8.0+
- **Base de données** : MySQL 5.7+ ou MariaDB 10.3+
- **Extensions PHP** : PDO, PDO_MySQL, GD (pour les images)
- **XAMPP/WAMP/LAMP** recommandé pour le développement local

## 🚀 Installation

### 1. Téléchargement
```bash
# Cloner ou télécharger les fichiers dans votre dossier web
# Par exemple : C:\xampp\htdocs\restorationchurch\
```

### 2. Configuration de la base de données
1. Démarrez MySQL/MariaDB
2. Accédez à `http://localhost/restorationchurch/install.php`
3. Le script créera automatiquement :
   - La base de données `restoration_church`
   - Toutes les tables nécessaires
   - Un compte administrateur par défaut
   - Des données d'exemple

### 3. Configuration (optionnelle)
Modifiez `config/database.php` si nécessaire :
```php
private $host = 'localhost';
private $db_name = 'restoration_church';
private $username = 'root';
private $password = '';
```

### 4. Accès au site
- **Site public :** `http://localhost/restorationchurch/`
- **Administration :** `http://localhost/restorationchurch/admin/`
- **Identifiants par défaut :** admin / admin123

## 📁 Structure du Projet

```
restorationchurch/
├── config/
│   └── database.php          # Configuration base de données
├── includes/
│   ├── header.php           # En-tête du site
│   └── footer.php           # Pied de page du site
├── admin/
│   ├── includes/
│   │   ├── admin_header.php # En-tête admin
│   │   └── admin_footer.php # Pied de page admin
│   ├── login.php           # Connexion admin
│   ├── dashboard.php       # Tableau de bord
│   ├── events.php          # Gestion événements
│   └── logout.php          # Déconnexion
├── api/
│   ├── contact.php         # API formulaire contact
│   └── newsletter.php      # API newsletter
├── css/
│   └── style.css           # Styles personnalisés
├── js/
│   └── main.js             # Scripts JavaScript
├── images/                 # Images du site
├── index.php               # Page d'accueil
├── about.php               # À propos
├── team.php                # Équipe pastorale
├── events.php              # Événements
├── media.php               # Médias
├── contact.php             # Contact
├── install.php             # Script d'installation
└── README.md               # Documentation
```

## 🎯 Utilisation

### Administration
1. Connectez-vous à `/admin/` avec admin/admin123
2. **Changez immédiatement le mot de passe** par défaut
3. Ajoutez vos événements, prédications et photos
4. Personnalisez le contenu selon vos besoins

### Gestion du Contenu
- **Événements :** Ajoutez date, heure, lieu et description
- **Prédications :** Uploadez vidéos, audios et documents PDF
- **Galerie :** Organisez vos photos par catégories
- **Messages :** Répondez aux demandes de contact
- **Newsletter :** Gérez les abonnés et envoyez des actualités

### Personnalisation
- Modifiez les couleurs dans `css/style.css`
- Remplacez le logo dans `images/logo.png`
- Adaptez les textes dans les fichiers PHP
- Configurez les liens réseaux sociaux

## 🔧 Configuration Avancée

### Email (optionnel)
Pour activer l'envoi d'emails :
1. Configurez PHP mail() ou SMTP
2. Modifiez les adresses dans `api/contact.php` et `api/newsletter.php`

### Réseaux Sociaux
Mettez à jour les liens dans `includes/footer.php` :
- Facebook : https://web.facebook.com/evangelicalrestorationchurchgoma
- YouTube, Instagram, WhatsApp selon vos comptes

### Sécurité
- Changez les mots de passe par défaut
- Configurez HTTPS en production
- Limitez l'accès au dossier `/admin/`
- Sauvegardez régulièrement la base de données

## 📱 Responsive Design

Le site est entièrement responsive et s'adapte à :
- **Desktop** : Expérience complète avec sidebar
- **Tablette** : Navigation adaptée
- **Mobile** : Menu hamburger et contenu optimisé

## 🎨 Personnalisation des Couleurs

Variables CSS principales dans `css/style.css` :
```css
:root {
    --primary-color: #2c5aa0;    /* Bleu principal */
    --secondary-color: #f8f9fa;  /* Gris clair */
    --accent-color: #ffc107;     /* Jaune accent */
    --text-dark: #333;           /* Texte foncé */
    --text-light: #666;          /* Texte clair */
}
```

## 🔒 Sécurité

- Authentification par session PHP
- Protection CSRF sur les formulaires
- Validation et échappement des données
- Hashage sécurisé des mots de passe
- Protection contre l'injection SQL avec PDO

## 📊 Base de Données

### Tables principales :
- `events` : Événements de l'église
- `sermons` : Prédications et médias
- `gallery` : Photos et images
- `contact_messages` : Messages de contact
- `newsletter_subscribers` : Abonnés newsletter
- `admins` : Comptes administrateurs

## 🆘 Support et Dépannage

### Problèmes courants :
1. **Erreur de connexion DB :** Vérifiez MySQL et les paramètres
2. **Page blanche :** Activez l'affichage des erreurs PHP
3. **Images non affichées :** Vérifiez les permissions des dossiers
4. **Admin inaccessible :** Réinitialisez via `install.php`

### Logs :
- Erreurs PHP : Consultez les logs du serveur
- Erreurs MySQL : Vérifiez les logs de la base de données

## 🔄 Mises à Jour

Pour mettre à jour le site :
1. Sauvegardez la base de données
2. Sauvegardez les fichiers personnalisés
3. Remplacez les fichiers système
4. Testez toutes les fonctionnalités

## 📞 Contact Technique

Pour le support technique ou les personnalisations :
- Consultez la documentation en ligne
- Vérifiez les issues GitHub
- Contactez l'équipe de développement

## 📄 Licence

Ce projet est développé spécifiquement pour l'Église Evangelical Restoration Church Goma.

---

**Développé avec ❤️ pour la gloire de Dieu et l'avancement de Son royaume.**
