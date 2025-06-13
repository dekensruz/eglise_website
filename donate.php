<?php
$page_title = "Contact";
require_once 'includes/header.php';
?>


    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-gift fa-4x text-primary mb-4"></i>
                        <h2 class="mb-4">Faire un don</h2>
                        <p class="lead mb-4">Soutenez notre mission  en faisant un don via Airtel Money</p>
                        
                        <div class="donation-info bg-light p-4 rounded mb-4">
                            <h4 class="mb-3">Numéro Airtel Money</h4>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <span class="h3 mb-0">+243 979 451 322</span>
                                <button class="btn btn-sm btn-outline-primary" onclick="copyNumber()" title="Copier le numéro">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <p class="text-muted mb-4">
                            Après avoir effectué votre don, n'hésitez pas à nous contacter pour nous en informer.
                        </p>

                        <a href="contact.php" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i> Nous contacter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function copyNumber() {
        navigator.clipboard.writeText('+243 979 451 322').then(() => {
            alert('Numéro copié !');
        });
    }
    </script>
</body>
</html>
