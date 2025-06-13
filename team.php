<?php
$page_title = "Équipe Pastorale";
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupérer les membres de l'équipe depuis la base de données
$database = new Database();
$db = $database->getConnection();

try {
    // Récupérer les membres actifs, triés par ordre
    $query = "SELECT * FROM team_members WHERE is_active = 1 ORDER BY order_number ASC, name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $team_members = [];
    $error_message = "Erreur de base de données: " . $e->getMessage();
}
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh;">
    <div class="hero-bg" style="background-image: url('images/img10.jpg')"></div>
    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title">Notre Équipe Pastorale</h1>
                <p class="hero-subtitle">
                    Des serviteurs de Dieu dévoués à votre croissance spirituelle
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Pasteur Principal -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 mb-4 mb-lg-0 text-center">
                <img src="images/Pasteure Hadasse.jpg" alt="Pasteure Hadasse" class="team-photo" style="width: 250px; height: 250px;">
            </div>
            <div class="col-lg-8">
                <h2 class="section-title text-start">Pasteure Hadasse</h2>
                <p class="section-subtitle text-start">Pasteure Principale</p>
                <p class="mb-4">
                    La Pasteure Hadasse est une femme de Dieu passionnée par la restauration des vies brisées. 
                    Avec une vision claire pour l'église et un cœur pour les perdus, elle dirige l'Église 
                    Evangelical Restoration Church Goma avec sagesse et compassion.
                </p>
                <p class="mb-4">
                    Dotée d'un don exceptionnel pour l'enseignement et la prédication, elle inspire notre 
                    communauté à grandir dans la foi et à servir Dieu avec excellence. Son ministère est 
                    marqué par la puissance de Dieu et l'amour pour les âmes.
                </p>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-graduation-cap text-primary me-2"></i>Formation</h6>
                        <p class="text-muted">Théologie Pastorale</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar text-primary me-2"></i>Ministère</h6>
                        <p class="text-muted">Plus de 15 ans d'expérience</p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-heart text-primary me-2"></i>Spécialités</h6>
                        <p class="text-muted">Restauration, Guérison, Enseignement</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-book text-primary me-2"></i>Auteure</h6>
                        <p class="text-muted">Plusieurs livres spirituels</p>
                    </div>
                </div>
                
                <blockquote class="blockquote border-start border-primary border-4 ps-3">
                    <p class="mb-2 fst-italic">
                        "Mon désir le plus profond est de voir chaque personne découvrir sa véritable identité 
                        en Christ et marcher dans la destinée que Dieu a préparée pour elle."
                    </p>
                    <footer class="blockquote-footer">Pasteure Hadasse</footer>
                </blockquote>
            </div>
        </div>
    </div>
</section>

<!-- Section Livres de la Pasteure -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Livres de la Pasteure Hadasse</h2>
            <p class="section-subtitle">Des ressources spirituelles pour votre croissance</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <img src="images/livre_la_beaute_de_lepreuve_pasteure_hadassa.jpg" alt="La Beauté de l'Épreuve" class="card-img-top">
                    <div class="card-body text-center">
                        <h5 class="card-title">La Beauté de l'Épreuve</h5>
                        <p class="card-text">Découvrez comment Dieu utilise les épreuves pour façonner notre caractère et révéler sa gloire.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i>Lire Plus
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <img src="images/livre.jpg" alt="Restauration Divine" class="card-img-top">
                    <div class="card-body text-center">
                        <h5 class="card-title">Restauration Divine</h5>
                        <p class="card-text">Comment Dieu restaure les vies brisées et renouvelle notre espérance en Christ.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i>Lire Plus
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <img src="images/livre2.jpg" alt="La Puissance de la Foi" class="card-img-top">
                    <div class="card-body text-center">
                        <h5 class="card-title">La Puissance de la Foi</h5>
                        <p class="card-text">Apprenez à activer votre foi pour voir l'impossible devenir possible dans votre vie.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i>Lire Plus
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <img src="images/livre3.jpg" alt="Femme de Destinée" class="card-img-top">
                    <div class="card-body text-center">
                        <h5 class="card-title">Femme de Destinée</h5>
                        <p class="card-text">Découvrez votre identité et votre appel divin en tant que femme selon le cœur de Dieu.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i>Lire Plus
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <img src="images/livre4.jpg" alt="Prière Transformatrice" class="card-img-top">
                    <div class="card-body text-center">
                        <h5 class="card-title">Prière Transformatrice</h5>
                        <p class="card-text">Principes bibliques pour une vie de prière efficace qui transforme votre vie spirituelle.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i>Lire Plus
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <img src="images/livre6.jpg" alt="Leadership Spirituel" class="card-img-top">
                    <div class="card-body text-center">
                        <h5 class="card-title">Leadership Spirituel</h5>
                        <p class="card-text">Principes bibliques pour diriger avec intégrité et impact dans le Royaume de Dieu.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i>Lire Plus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Équipe de Leadership -->
<?php if (!empty($team_members)): ?>
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Équipe de Leadership</h2>
            <p class="section-subtitle">Des leaders dévoués au service de Dieu et de l'église</p>
        </div>
        
        <div class="row">
            <?php foreach ($team_members as $member): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <?php if (!empty($member['image'])): ?>
                            <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="team-photo">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; margin: 0 auto;">
                                <i class="fas fa-user text-primary" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <h5><?php echo htmlspecialchars($member['name']); ?></h5>
                        <p class="text-primary mb-2"><?php echo htmlspecialchars($member['role']); ?></p>
                        <p class="text-muted">
                            <?php echo htmlspecialchars($member['description']); ?>
                        </p>
                        <div class="social-links">
                            <?php if (!empty($member['facebook'])): ?>
                                <a href="<?php echo htmlspecialchars($member['facebook']); ?>" target="_blank" class="text-primary me-2"><i class="fab fa-facebook"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($member['twitter'])): ?>
                                <a href="<?php echo htmlspecialchars($member['twitter']); ?>" target="_blank" class="text-primary me-2"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($member['instagram'])): ?>
                                <a href="<?php echo htmlspecialchars($member['instagram']); ?>" target="_blank" class="text-primary me-2"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($member['linkedin'])): ?>
                                <a href="<?php echo htmlspecialchars($member['linkedin']); ?>" target="_blank" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($member['email'])): ?>
                                <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="text-primary"><i class="fas fa-envelope"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section Conseil d'Administration -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Conseil d'Administration</h2>
            <p class="section-subtitle">Gouvernance et supervision de l'église</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-gavel fs-2"></i>
                    </div>
                    <h5>Président</h5>
                    <p class="text-muted">Supervision générale et direction stratégique</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-calculator fs-2"></i>
                    </div>
                    <h5>Trésorier</h5>
                    <p class="text-muted">Gestion financière et comptabilité</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-pen fs-2"></i>
                    </div>
                    <h5>Secrétaire</h5>
                    <p class="text-muted">Documentation et communication</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-users fs-2"></i>
                    </div>
                    <h5>Conseillers</h5>
                    <p class="text-muted">Conseil et orientation spirituelle</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Appel au Service -->
<section class="section-padding bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-3">Rejoignez Notre Équipe</h2>
        <p class="lead mb-4">
            Dieu appelle chaque croyant à servir dans son royaume. Découvrez comment vous pouvez 
            utiliser vos dons et talents pour l'avancement de l'Évangile.
        </p>
        <div class="row justify-content-center">
            <div class="col-auto"  style="margin-bottom: 20px;">
                <a href="contact.php" class="btn btn-outline-light btn-lg me-3">
                    <i class="fas fa-hands-helping me-2"></i>Servir avec Nous
                </a>
            </div>
            <div class="col-auto">
                <a href="about.php" class="btn btn-light btn-lg">
                    <i class="fas fa-info-circle me-2"></i>Nos Ministères
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
