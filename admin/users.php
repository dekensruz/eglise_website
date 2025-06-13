<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Vérifier si l'utilisateur a les droits d'administrateur
if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] !== 'admin') {
    // Rediriger les éditeurs vers le tableau de bord
    header('Location: dashboard.php?error=restricted');
    exit;
}

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

// Récupérer l'ID de l'administrateur connecté
$current_admin_id = $_SESSION['admin_id'];

// Récupérer les informations de l'administrateur connecté
try {
    $query = "SELECT * FROM admins WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$current_admin_id]);
    $current_admin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $current_admin = null;
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $role = trim($_POST['role']);
        
        // Validation
        if (empty($username) || empty($password) || empty($email)) {
            $message = 'Tous les champs obligatoires doivent être remplis !';
            $message_type = 'error';
        } elseif ($password !== $confirm_password) {
            $message = 'Les mots de passe ne correspondent pas !';
            $message_type = 'error';
        } elseif (strlen($password) < 6) {
            $message = 'Le mot de passe doit contenir au moins 6 caractères !';
            $message_type = 'error';
        } else {
            // Vérifier si le nom d'utilisateur existe déjà
            $query = "SELECT COUNT(*) FROM admins WHERE username = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$username]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $message = 'Ce nom d\'utilisateur existe déjà !';
                $message_type = 'error';
            } else {
                // Vérifier si l'email existe déjà
                $query = "SELECT COUNT(*) FROM admins WHERE email = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$email]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $message = 'Cette adresse email est déjà utilisée !';
                    $message_type = 'error';
                } else {
                    try {
                        // Hachage du mot de passe
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $query = "INSERT INTO admins (username, password, email, full_name, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$username, $hashed_password, $email, $full_name, $role]);
                        
                        $message = 'Utilisateur ajouté avec succès !';
                        $message_type = 'success';
                    } catch (PDOException $e) {
                        $message = 'Erreur lors de l\'ajout : ' . $e->getMessage();
                        $message_type = 'error';
                    }
                }
            }
        }
    }
    
    if ($action === 'edit') {
        $admin_id = $_POST['admin_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $role = trim($_POST['role']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        // Validation
        if (empty($username) || empty($email)) {
            $message = 'Le nom d\'utilisateur et l\'email sont obligatoires !';
            $message_type = 'error';
        } else {
            // Vérifier si le nom d'utilisateur existe déjà pour un autre utilisateur
            $query = "SELECT COUNT(*) FROM admins WHERE username = ? AND id != ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$username, $admin_id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $message = 'Ce nom d\'utilisateur existe déjà !';
                $message_type = 'error';
            } else {
                // Vérifier si l'email existe déjà pour un autre utilisateur
                $query = "SELECT COUNT(*) FROM admins WHERE email = ? AND id != ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$email, $admin_id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $message = 'Cette adresse email est déjà utilisée !';
                    $message_type = 'error';
                } else {
                    try {
                        // Si un nouveau mot de passe est fourni
                        if (!empty($password)) {
                            if ($password !== $confirm_password) {
                                $message = 'Les mots de passe ne correspondent pas !';
                                $message_type = 'error';
                            } elseif (strlen($password) < 6) {
                                $message = 'Le mot de passe doit contenir au moins 6 caractères !';
                                $message_type = 'error';
                            } else {
                                // Hachage du nouveau mot de passe
                                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                
                                $query = "UPDATE admins SET username = ?, password = ?, email = ?, full_name = ?, role = ? WHERE id = ?";
                                $stmt = $db->prepare($query);
                                $stmt->execute([$username, $hashed_password, $email, $full_name, $role, $admin_id]);
                            }
                        } else {
                            // Mise à jour sans changer le mot de passe
                            $query = "UPDATE admins SET username = ?, email = ?, full_name = ?, role = ? WHERE id = ?";
                            $stmt = $db->prepare($query);
                            $stmt->execute([$username, $email, $full_name, $role, $admin_id]);
                        }
                        
                        if ($message_type !== 'error') {
                            $message = 'Utilisateur mis à jour avec succès !';
                            $message_type = 'success';
                            
                            // Si l'utilisateur modifie son propre compte, mettre à jour les informations de session
                            if ($admin_id == $current_admin_id) {
                                $_SESSION['admin_username'] = $username;
                            }
                        }
                    } catch (PDOException $e) {
                        $message = 'Erreur lors de la mise à jour : ' . $e->getMessage();
                        $message_type = 'error';
                    }
                }
            }
        }
    }
    
    if ($action === 'delete') {
        $admin_id = $_POST['admin_id'];
        
        // Empêcher la suppression de son propre compte
        if ($admin_id == $current_admin_id) {
            $message = 'Vous ne pouvez pas supprimer votre propre compte !';
            $message_type = 'error';
        } else {
            try {
                $query = "DELETE FROM admins WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$admin_id]);
                
                $message = 'Utilisateur supprimé avec succès !';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Erreur lors de la suppression : ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Récupérer tous les administrateurs
try {
    $query = "SELECT * FROM admins ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $admins = [];
}

$page_title = "Gestion des Utilisateurs";
include 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users-cog me-2"></i>Gestion des Utilisateurs</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-2"></i>Ajouter un utilisateur
        </button>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Utilisateurs</h6>
                        <h3><?php echo count($admins); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Administrateurs</h6>
                        <h3><?php echo count(array_filter($admins, function($a) { return $a['role'] === 'admin'; })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Éditeurs</h6>
                        <h3><?php echo count(array_filter($admins, function($a) { return $a['role'] === 'editor'; })); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-edit fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des utilisateurs -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Liste des Utilisateurs
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($admins)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Aucun utilisateur trouvé.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom d'utilisateur</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><?php echo $admin['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($admin['username']); ?>
                                    <?php if ($admin['id'] == $current_admin_id): ?>
                                        <span class="badge bg-primary">Vous</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($admin['full_name'] ?: 'Non spécifié'); ?></td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($admin['email']); ?>">
                                        <?php echo htmlspecialchars($admin['email']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($admin['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Administrateur</span>
                                    <?php elseif ($admin['role'] === 'editor'): ?>
                                        <span class="badge bg-success">Éditeur</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($admin['role']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($admin['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUser(<?php echo htmlspecialchars(json_encode($admin)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <?php if ($admin['id'] != $current_admin_id): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal pour ajouter un utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Ajouter un utilisateur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="full_name" name="full_name">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin">Administrateur</option>
                            <option value="editor">Éditeur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier un utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="admin_id" id="edit_admin_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Modifier l'utilisateur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name">
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Rôle <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="admin">Administrateur</option>
                            <option value="editor">Éditeur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <div class="form-text">Laissez vide pour conserver le mot de passe actuel.</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="edit_confirm_password" name="confirm_password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(message) {
    return confirm(message);
}

function editUser(admin) {
    document.getElementById('edit_admin_id').value = admin.id;
    document.getElementById('edit_username').value = admin.username;
    document.getElementById('edit_email').value = admin.email;
    document.getElementById('edit_full_name').value = admin.full_name || '';
    document.getElementById('edit_role').value = admin.role;
    
    // Réinitialiser les champs de mot de passe
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_confirm_password').value = '';
    
    // Afficher le modal
    var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
}
</script>

<?php include 'includes/admin_footer.php'; ?>