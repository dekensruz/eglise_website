<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Récupérer les informations de l'administrateur connecté
$admin_id = $_SESSION['admin_id'];
$admin_role = $_SESSION['admin_role'];

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajouter une citation
    if (isset($_POST['add_quote'])) {
        $quote = htmlspecialchars(trim($_POST['quote']));
        $author = htmlspecialchars(trim($_POST['author']));
        $bible_reference = htmlspecialchars(trim($_POST['bible_reference']));
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        if (empty($quote)) {
            $error = "Le texte de la citation est obligatoire.";
        } else {
            // Insérer dans la base de données
            $query = "INSERT INTO daily_quotes (quote, author, bible_reference, is_active, created_by) 
                      VALUES (:quote, :author, :bible_reference, :is_active, :created_by)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':quote', $quote);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':bible_reference', $bible_reference);
            $stmt->bindParam(':is_active', $is_active);
            $stmt->bindParam(':created_by', $admin_id);
            
            if ($stmt->execute()) {
                $success = "Citation ajoutée avec succès.";
            } else {
                $error = "Erreur lors de l'ajout de la citation.";
            }
        }
    }
    
    // Modifier une citation
    if (isset($_POST['edit_quote'])) {
        $quote_id = (int)$_POST['quote_id'];
        $quote = htmlspecialchars(trim($_POST['quote']));
        $author = htmlspecialchars(trim($_POST['author']));
        $bible_reference = htmlspecialchars(trim($_POST['bible_reference']));
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        if (empty($quote)) {
            $error = "Le texte de la citation est obligatoire.";
        } else {
            // Mettre à jour dans la base de données
            $query = "UPDATE daily_quotes 
                      SET quote = :quote, author = :author, bible_reference = :bible_reference, 
                          is_active = :is_active 
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':quote', $quote);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':bible_reference', $bible_reference);
            $stmt->bindParam(':is_active', $is_active);
            $stmt->bindParam(':id', $quote_id);
            
            if ($stmt->execute()) {
                $success = "Citation mise à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour de la citation.";
            }
        }
    }
    
    // Supprimer une citation
    if (isset($_POST['delete_quote'])) {
        $quote_id = (int)$_POST['quote_id'];
        
        // Supprimer de la base de données
        $stmt = $db->prepare("DELETE FROM daily_quotes WHERE id = :id");
        $stmt->bindParam(':id', $quote_id);
        
        if ($stmt->execute()) {
            $success = "Citation supprimée avec succès.";
        } else {
            $error = "Erreur lors de la suppression de la citation.";
        }
    }
}

// Récupérer toutes les citations
$query = "SELECT q.*, a.username as admin_username 
          FROM daily_quotes q 
          LEFT JOIN admins a ON q.created_by = a.id 
          ORDER BY q.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Titre de la page
$page_title = "Citations du Jour";

// Inclure l'en-tête
require_once 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-quote-left me-2"></i>Citations du Jour</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuoteModal">
            <i class="fas fa-plus me-2"></i>Ajouter une Citation
        </button>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total des Citations</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($quotes); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-quote-right fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Citations Actives</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $active_count = 0;
                            foreach ($quotes as $quote) {
                                if ($quote['is_active']) $active_count++;
                            }
                            echo $active_count;
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Citations avec Référence Biblique</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $bible_count = 0;
                            foreach ($quotes as $quote) {
                                if (!empty($quote['bible_reference'])) $bible_count++;
                            }
                            echo $bible_count;
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bible fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des citations -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Liste des Citations</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Citation</th>
                        <th>Auteur</th>
                        <th>Référence Biblique</th>
                        <th>Statut</th>
                        <th>Créé par</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $quote): ?>
                    <tr>
                        <td><?php echo nl2br(htmlspecialchars($quote['quote'])); ?></td>
                        <td><?php echo htmlspecialchars($quote['author']); ?></td>
                        <td><?php echo htmlspecialchars($quote['bible_reference']); ?></td>
                        <td>
                            <?php if ($quote['is_active']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($quote['admin_username'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($quote['created_at'])); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info edit-quote" data-bs-toggle="modal" data-bs-target="#editQuoteModal" data-id="<?php echo $quote['id']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-quote" data-bs-toggle="modal" data-bs-target="#deleteQuoteModal" data-id="<?php echo $quote['id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ajouter une Citation -->
<div class="modal fade" id="addQuoteModal" tabindex="-1" aria-labelledby="addQuoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuoteModalLabel">Ajouter une Citation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quote" class="form-label">Citation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="quote" name="quote" rows="4" required></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="author" class="form-label">Auteur</label>
                            <input type="text" class="form-control" id="author" name="author">
                        </div>
                        <div class="col-md-6">
                            <label for="bible_reference" class="form-label">Référence Biblique</label>
                            <input type="text" class="form-control" id="bible_reference" name="bible_reference" placeholder="Ex: Jean 3:16">
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="add_quote" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier une Citation -->
<div class="modal fade" id="editQuoteModal" tabindex="-1" aria-labelledby="editQuoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editQuoteModalLabel">Modifier une Citation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" id="editQuoteForm">
                <input type="hidden" name="quote_id" id="edit_quote_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_quote" class="form-label">Citation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_quote" name="quote" rows="4" required></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_author" class="form-label">Auteur</label>
                            <input type="text" class="form-control" id="edit_author" name="author">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_bible_reference" class="form-label">Référence Biblique</label>
                            <input type="text" class="form-control" id="edit_bible_reference" name="bible_reference" placeholder="Ex: Jean 3:16">
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="edit_quote" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Supprimer une Citation -->
<div class="modal fade" id="deleteQuoteModal" tabindex="-1" aria-labelledby="deleteQuoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQuoteModalLabel">Confirmer la Suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette citation ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <form action="" method="post">
                    <input type="hidden" name="quote_id" id="delete_quote_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="delete_quote" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Initialiser DataTables
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        }
    });
    
    // Gérer le clic sur le bouton Modifier
    $('.edit-quote').click(function() {
        const quoteId = $(this).data('id');
        
        // Récupérer les données de la citation via AJAX
        $.ajax({
            url: 'ajax/get_quote.php',
            type: 'GET',
            data: { id: quoteId },
            dataType: 'json',
            success: function(data) {
                // Remplir le formulaire avec les données
                $('#edit_quote_id').val(data.id);
                $('#edit_quote').val(data.quote);
                $('#edit_author').val(data.author);
                $('#edit_bible_reference').val(data.bible_reference);
                $('#edit_is_active').prop('checked', data.is_active == 1);
            },
            error: function() {
                alert('Erreur lors de la récupération des données de la citation.');
            }
        });
    });
    
    // Gérer le clic sur le bouton Supprimer
    $('.delete-quote').click(function() {
        const quoteId = $(this).data('id');
        $('#delete_quote_id').val(quoteId);
    });
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>