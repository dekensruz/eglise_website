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
    // Ajouter un membre
    if (isset($_POST['add_member'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $role = htmlspecialchars(trim($_POST['role']));
        $description = htmlspecialchars(trim($_POST['description']));
        $order_number = (int)$_POST['order_number'];
        $facebook = htmlspecialchars(trim($_POST['facebook']));
        $twitter = htmlspecialchars(trim($_POST['twitter']));
        $instagram = htmlspecialchars(trim($_POST['instagram']));
        $linkedin = htmlspecialchars(trim($_POST['linkedin']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        if (empty($name) || empty($role)) {
            $error = "Le nom et le rôle sont obligatoires.";
        } else {
            // Traitement de l'image
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../images/team/';
                
                // Créer le répertoire s'il n'existe pas
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $file_name;
                
                // Vérifier le type de fichier
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($_FILES['image']['type'], $allowed_types)) {
                    $error = "Seuls les fichiers JPG, PNG, GIF et WEBP sont autorisés.";
                } else {
                    // Déplacer le fichier téléchargé
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image = 'images/team/' . $file_name;
                    } else {
                        $error = "Erreur lors du téléchargement de l'image.";
                    }
                }
            }
            
            if (!isset($error)) {
                // Insérer dans la base de données
                $query = "INSERT INTO team_members (name, role, description, image, order_number, facebook, twitter, instagram, linkedin, email, phone, is_active) 
                          VALUES (:name, :role, :description, :image, :order_number, :facebook, :twitter, :instagram, :linkedin, :email, :phone, :is_active)";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':image', $image);
                $stmt->bindParam(':order_number', $order_number);
                $stmt->bindParam(':facebook', $facebook);
                $stmt->bindParam(':twitter', $twitter);
                $stmt->bindParam(':instagram', $instagram);
                $stmt->bindParam(':linkedin', $linkedin);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':is_active', $is_active);
                
                if ($stmt->execute()) {
                    $success = "Membre ajouté avec succès.";
                } else {
                    $error = "Erreur lors de l'ajout du membre.";
                }
            }
        }
    }
    
    // Modifier un membre
    if (isset($_POST['edit_member'])) {
        $member_id = (int)$_POST['member_id'];
        $name = htmlspecialchars(trim($_POST['name']));
        $role = htmlspecialchars(trim($_POST['role']));
        $description = htmlspecialchars(trim($_POST['description']));
        $order_number = (int)$_POST['order_number'];
        $facebook = htmlspecialchars(trim($_POST['facebook']));
        $twitter = htmlspecialchars(trim($_POST['twitter']));
        $instagram = htmlspecialchars(trim($_POST['instagram']));
        $linkedin = htmlspecialchars(trim($_POST['linkedin']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        if (empty($name) || empty($role)) {
            $error = "Le nom et le rôle sont obligatoires.";
        } else {
            // Récupérer l'image actuelle
            $stmt = $db->prepare("SELECT image FROM team_members WHERE id = :id");
            $stmt->bindParam(':id', $member_id);
            $stmt->execute();
            $current_image = $stmt->fetchColumn();
            
            // Traitement de l'image
            $image = $current_image;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../images/team/';
                
                // Créer le répertoire s'il n'existe pas
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $file_name;
                
                // Vérifier le type de fichier
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($_FILES['image']['type'], $allowed_types)) {
                    $error = "Seuls les fichiers JPG, PNG, GIF et WEBP sont autorisés.";
                } else {
                    // Déplacer le fichier téléchargé
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        $image = 'images/team/' . $file_name;
                        
                        // Supprimer l'ancienne image si elle existe
                        if (!empty($current_image) && file_exists('../' . $current_image)) {
                            unlink('../' . $current_image);
                        }
                    } else {
                        $error = "Erreur lors du téléchargement de l'image.";
                    }
                }
            }
            
            if (!isset($error)) {
                // Mettre à jour dans la base de données
                $query = "UPDATE team_members 
                          SET name = :name, role = :role, description = :description, image = :image, 
                              order_number = :order_number, facebook = :facebook, twitter = :twitter, 
                              instagram = :instagram, linkedin = :linkedin, email = :email, 
                              phone = :phone, is_active = :is_active 
                          WHERE id = :id";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':image', $image);
                $stmt->bindParam(':order_number', $order_number);
                $stmt->bindParam(':facebook', $facebook);
                $stmt->bindParam(':twitter', $twitter);
                $stmt->bindParam(':instagram', $instagram);
                $stmt->bindParam(':linkedin', $linkedin);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':is_active', $is_active);
                $stmt->bindParam(':id', $member_id);
                
                if ($stmt->execute()) {
                    $success = "Membre mis à jour avec succès.";
                } else {
                    $error = "Erreur lors de la mise à jour du membre.";
                }
            }
        }
    }
    
    // Supprimer un membre
    if (isset($_POST['delete_member'])) {
        $member_id = (int)$_POST['member_id'];
        
        // Récupérer l'image
        $stmt = $db->prepare("SELECT image FROM team_members WHERE id = :id");
        $stmt->bindParam(':id', $member_id);
        $stmt->execute();
        $image = $stmt->fetchColumn();
        
        // Supprimer de la base de données
        $stmt = $db->prepare("DELETE FROM team_members WHERE id = :id");
        $stmt->bindParam(':id', $member_id);
        
        if ($stmt->execute()) {
            // Supprimer l'image si elle existe
            if (!empty($image) && file_exists('../' . $image)) {
                unlink('../' . $image);
            }
            $success = "Membre supprimé avec succès.";
        } else {
            $error = "Erreur lors de la suppression du membre.";
        }
    }
}

// Récupérer tous les membres
$query = "SELECT * FROM team_members ORDER BY order_number, name";
$stmt = $db->prepare($query);
$stmt->execute();
$team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Titre de la page
$page_title = "Gestion de l'Équipe";

// Inclure l'en-tête
require_once 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users-cog me-2"></i>Gestion de l'Équipe</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="fas fa-plus me-2"></i>Ajouter un Membre
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
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total des Membres</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($team_members); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Membres Actifs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $active_count = 0;
                            foreach ($team_members as $member) {
                                if ($member['is_active']) $active_count++;
                            }
                            echo $active_count;
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Membres Inactifs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count($team_members) - $active_count; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des membres -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Liste des Membres de l'Équipe</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Rôle</th>
                        <th>Ordre</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($team_members as $member): ?>
                    <tr>
                        <td class="text-center">
                            <?php if (!empty($member['image']) && file_exists('../' . $member['image'])): ?>
                                <img src="../<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $member['name']; ?></td>
                        <td><?php echo $member['role']; ?></td>
                        <td><?php echo $member['order_number']; ?></td>
                        <td>
                            <?php if ($member['is_active']): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info edit-member" data-bs-toggle="modal" data-bs-target="#editMemberModal" data-id="<?php echo $member['id']; ?> ">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-member" data-bs-toggle="modal" data-bs-target="#deleteMemberModal" data-id="<?php echo $member['id']; ?>" data-name="<?php echo $member['name']; ?>">
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

<!-- Modal Ajouter un Membre -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel">Ajouter un Membre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="role" name="role" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <small class="text-muted">Formats acceptés: JPG, PNG, GIF, WEBP</small>
                        </div>
                        <div class="col-md-6">
                            <label for="order_number" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="order_number" name="order_number" value="0" min="0">
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Réseaux Sociaux</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="facebook" class="form-label">Facebook</label>
                            <input type="text" class="form-control" id="facebook" name="facebook" placeholder="URL Facebook">
                        </div>
                        <div class="col-md-6">
                            <label for="twitter" class="form-label">Twitter</label>
                            <input type="text" class="form-control" id="twitter" name="twitter" placeholder="URL Twitter">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="instagram" class="form-label">Instagram</label>
                            <input type="text" class="form-control" id="instagram" name="instagram" placeholder="URL Instagram">
                        </div>
                        <div class="col-md-6">
                            <label for="linkedin" class="form-label">LinkedIn</label>
                            <input type="text" class="form-control" id="linkedin" name="linkedin" placeholder="URL LinkedIn">
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Coordonnées</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Téléphone">
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Actif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="add_member" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier un Membre -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMemberModalLabel">Modifier un Membre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data" id="editMemberForm">
                <input type="hidden" name="member_id" id="edit_member_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role" class="form-label">Rôle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_role" name="role" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image">
                            <small class="text-muted">Formats acceptés: JPG, PNG, GIF, WEBP</small>
                            <div id="current_image_container" class="mt-2"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_order_number" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="edit_order_number" name="order_number" min="0">
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Réseaux Sociaux</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_facebook" class="form-label">Facebook</label>
                            <input type="text" class="form-control" id="edit_facebook" name="facebook" placeholder="URL Facebook">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_twitter" class="form-label">Twitter</label>
                            <input type="text" class="form-control" id="edit_twitter" name="twitter" placeholder="URL Twitter">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_instagram" class="form-label">Instagram</label>
                            <input type="text" class="form-control" id="edit_instagram" name="instagram" placeholder="URL Instagram">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_linkedin" class="form-label">LinkedIn</label>
                            <input type="text" class="form-control" id="edit_linkedin" name="linkedin" placeholder="URL LinkedIn">
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Coordonnées</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" placeholder="Email">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone" placeholder="Téléphone">
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">Actif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="edit_member" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Supprimer un Membre -->
<div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMemberModalLabel">Confirmer la Suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le membre <strong id="delete_member_name"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <form action="" method="post">
                    <input type="hidden" name="member_id" id="delete_member_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="delete_member" class="btn btn-danger">Supprimer</button>
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
    $('.edit-member').click(function() {
        const memberId = $(this).data('id');
        
        // Récupérer les données du membre via AJAX
        $.ajax({
            url: 'ajax/get_team_member.php',
            type: 'GET',
            data: { id: memberId },
            dataType: 'json',
            success: function(data) {
                // Remplir le formulaire avec les données
                $('#edit_member_id').val(data.id);
                $('#edit_name').val(data.name);
                $('#edit_role').val(data.role);
                $('#edit_description').val(data.description);
                $('#edit_order_number').val(data.order_number);
                $('#edit_facebook').val(data.facebook);
                $('#edit_twitter').val(data.twitter);
                $('#edit_instagram').val(data.instagram);
                $('#edit_linkedin').val(data.linkedin);
                $('#edit_email').val(data.email);
                $('#edit_phone').val(data.phone);
                $('#edit_is_active').prop('checked', data.is_active == 1);
                
                // Afficher l'image actuelle
                if (data.image) {
                    $('#current_image_container').html(`<img src="../${data.image}" alt="${data.name}" class="img-thumbnail" style="max-height: 100px;">`)
                } else {
                    $('#current_image_container').html('<p class="text-muted">Aucune image</p>');
                }
            },
            error: function() {
                alert('Erreur lors de la récupération des données du membre.');
            }
        });
    });
    
    // Gérer le clic sur le bouton Supprimer
    $('.delete-member').click(function() {
        const memberId = $(this).data('id');
        const memberName = $(this).data('name');
        
        $('#delete_member_id').val(memberId);
        $('#delete_member_name').text(memberName);
    });
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>