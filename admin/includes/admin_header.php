<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Administration - ERC Goma</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background: linear-gradient(180deg, #2c5aa0 0%, #1e3a8a 100%);
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #ffc107;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 240px;
            padding-top: 48px;
        }
        
        .navbar {
            background: white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.125);
            font-weight: 600;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .badge {
            font-weight: 500;
        }
        
        /* Styles pour les appareils mobiles */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 56px; /* Hauteur de la navbar */
                left: 0;
                width: 80%; /* Largeur réduite pour un meilleur affichage */
                max-width: 280px; /* Largeur maximale */
                height: calc(100% - 56px);
                z-index: 1030; /* Supérieur à la navbar */
                padding-top: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 10px rgba(0,0,0,0.2); /* Ombre pour mieux distinguer */
                overflow-y: auto; /* Permettre le défilement */
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding-top: 56px;
                width: 100% !important;
            }
            
            .navbar-toggler {
                display: block;
                position: relative;
                left: 0;
                z-index: 1040; /* Supérieur au sidebar */
                margin-right: 10px;
            }
            
            /* Ajuster la position du contenu principal */
            .col-md-9.ms-sm-auto.col-lg-10 {
                width: 100%;
                margin-left: 0 !important;
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
            
            /* Overlay pour fermer le menu en cliquant à l'extérieur */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 1020;
            }
            
            .sidebar.show + .sidebar-overlay {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation principale -->
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="dashboard.php">
            <i class="fas fa-church me-2"></i>ERC Admin
        </a>
        
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" id="sidebarToggler">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <div class="dropdown">
                    <a class="nav-link px-3 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-cog me-2"></i>Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>Voir le site
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteneur principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Overlay pour fermer le menu sur mobile -->
            <div class="sidebar-overlay"></div>
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                Tableau de Bord
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : ''; ?>" href="events.php">
                                <i class="fas fa-calendar"></i>
                                Événements
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sermons.php' ? 'active' : ''; ?>" href="sermons.php">
                                <i class="fas fa-microphone"></i>
                                Prédications
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>" href="gallery.php">
                                <i class="fas fa-images"></i>
                                Galerie
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                                <i class="fas fa-envelope"></i>
                                Messages
                                <?php if (isset($stats['unread_messages']) && $stats['unread_messages'] > 0): ?>
                                    <span class="badge bg-warning ms-2"><?php echo $stats['unread_messages']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? 'active' : ''; ?>" href="newsletter.php">
                                <i class="fas fa-paper-plane"></i>
                                Newsletter
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'team.php' ? 'active' : ''; ?>" href="team.php">
                                <i class="fas fa-users-cog"></i>
                                Équipe Pastorale
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'quotes.php' ? 'active' : ''; ?>" href="quotes.php">
                                <i class="fas fa-quote-left"></i>
                                Citations du Jour
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'members.php' ? 'active' : ''; ?>" href="members.php">
                                <i class="fas fa-user-friends"></i>
                                Membres de l'Église
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                                <i class="fas fa-users"></i>
                                Utilisateurs
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                                <i class="fas fa-cog"></i>
                                Paramètres
                            </a>
                        </li>
                    </ul>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                        <span>Outils</span>
                    </h6>
                    
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="backup.php">
                                <i class="fas fa-download"></i>
                                Sauvegarde
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="analytics.php">
                                <i class="fas fa-chart-bar"></i>
                                Statistiques
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt"></i>
                                Voir le Site
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Alertes -->
                <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<script>
// Ajouter un gestionnaire d'événements pour le bouton du menu sur mobile
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.getElementById('sidebarToggler');
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (navbarToggler && sidebar) {
        // Fonction pour ouvrir/fermer le menu
        function toggleSidebar() {
            sidebar.classList.toggle('show');
        }
        
        // Fonction pour fermer le menu
        function closeSidebar() {
            sidebar.classList.remove('show');
        }
        
        // Ouvrir/fermer le menu quand on clique sur le bouton
        navbarToggler.addEventListener('click', toggleSidebar);
        
        // Fermer le menu quand on clique sur l'overlay
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
        
        // Fermer le menu lorsqu'un lien est cliqué sur mobile
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });
        
        // Fermer le menu quand on redimensionne la fenêtre à une taille desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768 && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        });
    }
});
</script>
