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
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $gender = htmlspecialchars(trim($_POST['gender']));
        $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
        $address = htmlspecialchars(trim($_POST['address']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $email = htmlspecialchars(trim($_POST['email']));
        $profession = htmlspecialchars(trim($_POST['profession']));
        $join_date = !empty($_POST['join_date']) ? $_POST['join_date'] : null;
        $baptism_date = !empty($_POST['baptism_date']) ? $_POST['baptism_date'] : null;
        $marital_status = htmlspecialchars(trim($_POST['marital_status']));
        $ministry = htmlspecialchars(trim($_POST['ministry']));
        $emergency_contact_name = htmlspecialchars(trim($_POST['emergency_contact_name']));
        $emergency_contact_phone = htmlspecialchars(trim($_POST['emergency_contact_phone']));
        $notes = htmlspecialchars(trim($_POST['notes']));
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        if (empty($first_name) || empty($last_name) || empty($phone)) {
            $error = "Le prénom, le nom et le téléphone sont obligatoires.";
        } else {
            // Insérer dans la base de données
            $query = "INSERT INTO church_members (first_name, last_name, gender, birth_date, address, phone, email, 
                      profession, join_date, baptism_date, marital_status, ministry, emergency_contact_name, 
                      emergency_contact_phone, notes, is_active) 
                      VALUES (:first_name, :last_name, :gender, :birth_date, :address, :phone, :email, 
                      :profession, :join_date, :baptism_date, :marital_status, :ministry, :emergency_contact_name, 
                      :emergency_contact_phone, :notes, :is_active)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':birth_date', $birth_date);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':profession', $profession);
            $stmt->bindParam(':join_date', $join_date);
            $stmt->bindParam(':baptism_date', $baptism_date);
            $stmt->bindParam(':marital_status', $marital_status);
            $stmt->bindParam(':ministry', $ministry);
            $stmt->bindParam(':emergency_contact_name', $emergency_contact_name);
            $stmt->bindParam(':emergency_contact_phone', $emergency_contact_phone);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':is_active', $is_active);
            
            if ($stmt->execute()) {
                $success = "Membre ajouté avec succès.";
            } else {
                $error = "Erreur lors de l'ajout du membre.";
            }
        }
    }
    
    // Modifier un membre
    if (isset($_POST['edit_member'])) {
        $member_id = (int)$_POST['member_id'];
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $gender = htmlspecialchars(trim($_POST['gender']));
        $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
        $address = htmlspecialchars(trim($_POST['address']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $email = htmlspecialchars(trim($_POST['email']));
        $profession = htmlspecialchars(trim($_POST['profession']));
        $join_date = !empty($_POST['join_date']) ? $_POST['join_date'] : null;
        $baptism_date = !empty($_POST['baptism_date']) ? $_POST['baptism_date'] : null;
        $marital_status = htmlspecialchars(trim($_POST['marital_status']));
        $ministry = htmlspecialchars(trim($_POST['ministry']));
        $emergency_contact_name = htmlspecialchars(trim($_POST['emergency_contact_name']));
        $emergency_contact_phone = htmlspecialchars(trim($_POST['emergency_contact_phone']));
        $notes = htmlspecialchars(trim($_POST['notes']));
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validation
        if (empty($first_name) || empty($last_name) || empty($phone)) {
            $error = "Le prénom, le nom et le téléphone sont obligatoires.";
        } else {
            // Mettre à jour dans la base de données
            $query = "UPDATE church_members 
                      SET first_name = :first_name, last_name = :last_name, gender = :gender, 
                      birth_date = :birth_date, address = :address, phone = :phone, email = :email, 
                      profession = :profession, join_date = :join_date, baptism_date = :baptism_date, 
                      marital_status = :marital_status, ministry = :ministry, 
                      emergency_contact_name = :emergency_contact_name, 
                      emergency_contact_phone = :emergency_contact_phone, notes = :notes, 
                      is_active = :is_active 
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':birth_date', $birth_date);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':profession', $profession);
            $stmt->bindParam(':join_date', $join_date);
            $stmt->bindParam(':baptism_date', $baptism_date);
            $stmt->bindParam(':marital_status', $marital_status);
            $stmt->bindParam(':ministry', $ministry);
            $stmt->bindParam(':emergency_contact_name', $emergency_contact_name);
            $stmt->bindParam(':emergency_contact_phone', $emergency_contact_phone);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':is_active', $is_active);
            $stmt->bindParam(':id', $member_id);
            
            if ($stmt->execute()) {
                $success = "Membre mis à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour du membre.";
            }
        }
    }
    
    // Supprimer un membre
    if (isset($_POST['delete_member'])) {
        $member_id = (int)$_POST['member_id'];
        
        // Supprimer de la base de données
        $stmt = $db->prepare("DELETE FROM church_members WHERE id = :id");
        $stmt->bindParam(':id', $member_id);
        
        if ($stmt->execute()) {
            $success = "Membre supprimé avec succès.";
        } else {
            $error = "Erreur lors de la suppression du membre.";
        }
    }
    
    // Envoyer un SMS
    if (isset($_POST['send_sms'])) {
        $recipients = isset($_POST['recipients']) ? $_POST['recipients'] : [];
        $message = htmlspecialchars(trim($_POST['message']));
        
        if (empty($recipients)) {
            $error = "Veuillez sélectionner au moins un destinataire.";
        } elseif (empty($message)) {
            $error = "Le message ne peut pas être vide.";
        } else {
            // Récupérer les numéros de téléphone des membres sélectionnés
            $phone_numbers = [];
            
            if (in_array('all', $recipients)) {
                // Tous les membres actifs
                $stmt = $db->prepare("SELECT phone FROM church_members WHERE is_active = 1");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $phone_numbers[] = $row['phone'];
                }
            } else {
                // Membres spécifiques
                $placeholders = str_repeat('?,', count($recipients) - 1) . '?';
                $stmt = $db->prepare("SELECT phone FROM church_members WHERE id IN ($placeholders) AND is_active = 1");
                $stmt->execute($recipients);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $phone_numbers[] = $row['phone'];
                }
            }
            
            if (empty($phone_numbers)) {
                $error = "Aucun numéro de téléphone valide trouvé pour les destinataires sélectionnés.";
            } else {
                // Ici, vous intégreriez un service d'envoi de SMS
                // Pour l'instant, nous simulons l'envoi
                $success = "SMS envoyé avec succès à " . count($phone_numbers) . " destinataire(s).";
                
                // Exemple de code pour intégrer un service SMS (à adapter selon votre fournisseur)
                /*
                $api_key = 'VOTRE_CLE_API';
                $sender = 'ERC Goma';
                
                foreach ($phone_numbers as $phone) {
                    // Nettoyer le numéro de téléphone
                    $phone = preg_replace('/[^0-9]/', '', $phone);
                    
                    // Appel API
                    $url = 'https://api.service-sms.com/send';
                    $data = [
                        'api_key' => $api_key,
                        'to' => $phone,
                        'from' => $sender,
                        'message' => $message
                    ];
                    
                    $options = [
                        'http' => [
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($data)
                        ]
                    ];
                    
                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                }
                */
            }
        }
    }
}

// Récupérer tous les membres
$query = "SELECT * FROM church_members ORDER BY last_name, first_name";
$stmt = $db->prepare($query);
$stmt->execute();
$church_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Titre de la page
$page_title = "Gestion des Membres";

// Inclure l'en-tête
require_once 'includes/admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users me-2"></i>Gestion des Membres de l'Église</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#sendSmsModal">
            <i class="fas fa-sms me-2"></i>Envoyer SMS
        </button>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($church_members); ?></div>
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
                            foreach ($church_members as $member) {
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
                            <?php echo count($church_members) - $active_count; ?>
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
        <h6 class="m-0 font-weight-bold text-primary">Liste des Membres</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="membersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Genre</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Ministère</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($church_members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['last_name'] . ' ' . $member['first_name']); ?></td>
                            <td><?php echo $member['gender'] === 'M' ? 'Homme' : 'Femme'; ?></td>
                            <td><?php echo htmlspecialchars($member['phone']); ?></td>
                            <td><?php echo htmlspecialchars($member['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($member['ministry'] ?? '-'); ?></td>
                            <td>
                                <?php if ($member['is_active']): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info view-member" data-id="<?php echo $member['id']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary edit-member" data-id="<?php echo $member['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-member" data-id="<?php echo $member['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $member['phone']); ?>" target="_blank" class="btn btn-sm btn-success">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
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
            <form method="post" action="">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Prénom *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Nom *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Genre *</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Sélectionner</option>
                                <option value="M">Homme</option>
                                <option value="F">Femme</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="profession" class="form-label">Profession</label>
                            <input type="text" class="form-control" id="profession" name="profession">
                        </div>
                        <div class="col-md-6">
                            <label for="marital_status" class="form-label">État civil</label>
                            <select class="form-control" id="marital_status" name="marital_status">
                                <option value="single">Célibataire</option>
                                <option value="married">Marié(e)</option>
                                <option value="divorced">Divorcé(e)</option>
                                <option value="widowed">Veuf/Veuve</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="join_date" class="form-label">Date d'adhésion</label>
                            <input type="date" class="form-control" id="join_date" name="join_date">
                        </div>
                        <div class="col-md-6">
                            <label for="baptism_date" class="form-label">Date de baptême</label>
                            <input type="date" class="form-control" id="baptism_date" name="baptism_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ministry" class="form-label">Ministère</label>
                        <input type="text" class="form-control" id="ministry" name="ministry">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="emergency_contact_name" class="form-label">Contact d'urgence (Nom)</label>
                            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name">
                        </div>
                        <div class="col-md-6">
                            <label for="emergency_contact_phone" class="form-label">Contact d'urgence (Téléphone)</label>
                            <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Membre actif</label>
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
            <form method="post" action="" id="editMemberForm">
                <input type="hidden" name="member_id" id="edit_member_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_first_name" class="form-label">Prénom *</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_last_name" class="form-label">Nom *</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_gender" class="form-label">Genre *</label>
                            <select class="form-control" id="edit_gender" name="gender" required>
                                <option value="">Sélectionner</option>
                                <option value="M">Homme</option>
                                <option value="F">Femme</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_birth_date" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="edit_birth_date" name="birth_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Adresse</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_profession" class="form-label">Profession</label>
                            <input type="text" class="form-control" id="edit_profession" name="profession">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_marital_status" class="form-label">État civil</label>
                            <select class="form-control" id="edit_marital_status" name="marital_status">
                                <option value="single">Célibataire</option>
                                <option value="married">Marié(e)</option>
                                <option value="divorced">Divorcé(e)</option>
                                <option value="widowed">Veuf/Veuve</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_join_date" class="form-label">Date d'adhésion</label>
                            <input type="date" class="form-control" id="edit_join_date" name="join_date">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_baptism_date" class="form-label">Date de baptême</label>
                            <input type="date" class="form-control" id="edit_baptism_date" name="baptism_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_ministry" class="form-label">Ministère</label>
                        <input type="text" class="form-control" id="edit_ministry" name="ministry">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_emergency_contact_name" class="form-label">Contact d'urgence (Nom)</label>
                            <input type="text" class="form-control" id="edit_emergency_contact_name" name="emergency_contact_name">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_emergency_contact_phone" class="form-label">Contact d'urgence (Téléphone)</label>
                            <input type="tel" class="form-control" id="edit_emergency_contact_phone" name="emergency_contact_phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">Membre actif</label>
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

<!-- Modal Voir un Membre -->
<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-labelledby="viewMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMemberModalLabel">Détails du Membre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nom complet:</strong> <span id="view_full_name"></span></p>
                        <p><strong>Genre:</strong> <span id="view_gender"></span></p>
                        <p><strong>Date de naissance:</strong> <span id="view_birth_date"></span></p>
                        <p><strong>Adresse:</strong> <span id="view_address"></span></p>
                        <p><strong>Téléphone:</strong> <span id="view_phone"></span></p>
                        <p><strong>Email:</strong> <span id="view_email"></span></p>
                        <p><strong>Profession:</strong> <span id="view_profession"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>État civil:</strong> <span id="view_marital_status"></span></p>
                        <p><strong>Date d'adhésion:</strong> <span id="view_join_date"></span></p>
                        <p><strong>Date de baptême:</strong> <span id="view_baptism_date"></span></p>
                        <p><strong>Ministère:</strong> <span id="view_ministry"></span></p>
                        <p><strong>Contact d'urgence:</strong> <span id="view_emergency_contact"></span></p>
                        <p><strong>Statut:</strong> <span id="view_status"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes:</h6>
                        <p id="view_notes"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" id="view_whatsapp_link" target="_blank" class="btn btn-success">
                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Supprimer un Membre -->
<div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMemberModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce membre? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="">
                    <input type="hidden" name="member_id" id="delete_member_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="delete_member" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Envoyer SMS -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendSmsModalLabel">Envoyer un SMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipients" class="form-label">Destinataires *</label>
                        <select class="form-control" id="recipients" name="recipients[]" multiple required>
                            <option value="all">Tous les membres actifs</option>
                            <?php foreach ($church_members as $member): ?>
                                <?php if ($member['is_active']): ?>
                                    <option value="<?php echo $member['id']; ?>">
                                        <?php echo htmlspecialchars($member['last_name'] . ' ' . $member['first_name'] . ' (' . $member['phone'] . ')'); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs membres.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message *</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        <small class="form-text text-muted">
                            <span id="charCount">0</span>/160 caractères
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="send_sms" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour la gestion des membres -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser DataTables
        $('#membersTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            order: [[0, 'asc']]
        });
        
        // Compteur de caractères pour le SMS
        document.getElementById('message').addEventListener('input', function() {
            document.getElementById('charCount').textContent = this.value.length;
        });
        
        // Gestion du modal d'édition
        const editButtons = document.querySelectorAll('.edit-member');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const memberId = this.getAttribute('data-id');
                getMemberData(memberId, 'edit');
            });
        });
        
        // Gestion du modal de visualisation
        const viewButtons = document.querySelectorAll('.view-member');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const memberId = this.getAttribute('data-id');
                getMemberData(memberId, 'view');
            });
        });
        
        // Gestion du modal de suppression
        const deleteButtons = document.querySelectorAll('.delete-member');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const memberId = this.getAttribute('data-id');
                document.getElementById('delete_member_id').value = memberId;
                $('#deleteMemberModal').modal('show');
            });
        });
        
        // Fonction pour récupérer les données d'un membre
        function getMemberData(memberId, action) {
            // Simuler une requête AJAX (à remplacer par une vraie requête)
            // Dans un environnement réel, vous utiliseriez fetch ou XMLHttpRequest
            
            // Exemple avec fetch:
            fetch('ajax/get_church_member.php?id=' + memberId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (action === 'edit') {
                            fillEditForm(data.member);
                        } else if (action === 'view') {
                            fillViewModal(data.member);
                        }
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la récupération des données du membre.');
                });
        }
        
        // Remplir le formulaire d'édition
        function fillEditForm(member) {
            document.getElementById('edit_member_id').value = member.id;
            document.getElementById('edit_first_name').value = member.first_name;
            document.getElementById('edit_last_name').value = member.last_name;
            document.getElementById('edit_gender').value = member.gender;
            document.getElementById('edit_birth_date').value = member.birth_date;
            document.getElementById('edit_address').value = member.address;
            document.getElementById('edit_phone').value = member.phone;
            document.getElementById('edit_email').value = member.email;
            document.getElementById('edit_profession').value = member.profession;
            document.getElementById('edit_join_date').value = member.join_date;
            document.getElementById('edit_baptism_date').value = member.baptism_date;
            document.getElementById('edit_marital_status').value = member.marital_status;
            document.getElementById('edit_ministry').value = member.ministry;
            document.getElementById('edit_emergency_contact_name').value = member.emergency_contact_name;
            document.getElementById('edit_emergency_contact_phone').value = member.emergency_contact_phone;
            document.getElementById('edit_notes').value = member.notes;
            document.getElementById('edit_is_active').checked = member.is_active == 1;
            
            $('#editMemberModal').modal('show');
        }
        
        // Remplir le modal de visualisation
        function fillViewModal(member) {
            document.getElementById('view_full_name').textContent = member.last_name + ' ' + member.first_name;
            document.getElementById('view_gender').textContent = member.gender === 'M' ? 'Homme' : 'Femme';
            document.getElementById('view_birth_date').textContent = member.birth_date || '-';
            document.getElementById('view_address').textContent = member.address || '-';
            document.getElementById('view_phone').textContent = member.phone;
            document.getElementById('view_email').textContent = member.email || '-';
            document.getElementById('view_profession').textContent = member.profession || '-';
            
            let maritalStatus = '-';
            switch(member.marital_status) {
                case 'single': maritalStatus = 'Célibataire'; break;
                case 'married': maritalStatus = 'Marié(e)'; break;
                case 'divorced': maritalStatus = 'Divorcé(e)'; break;
                case 'widowed': maritalStatus = 'Veuf/Veuve'; break;
            }
            document.getElementById('view_marital_status').textContent = maritalStatus;
            
            document.getElementById('view_join_date').textContent = member.join_date || '-';
            document.getElementById('view_baptism_date').textContent = member.baptism_date || '-';
            document.getElementById('view_ministry').textContent = member.ministry || '-';
            
            let emergencyContact = '-';
            if (member.emergency_contact_name) {
                emergencyContact = member.emergency_contact_name;
                if (member.emergency_contact_phone) {
                    emergencyContact += ' (' + member.emergency_contact_phone + ')';
                }
            }
            document.getElementById('view_emergency_contact').textContent = emergencyContact;
            
            document.getElementById('view_status').innerHTML = member.is_active == 1 ? 
                '<span class="badge bg-success">Actif</span>' : 
                '<span class="badge bg-secondary">Inactif</span>';
            
            document.getElementById('view_notes').textContent = member.notes || '-';
            
            // Lien WhatsApp
            const phoneNumber = member.phone.replace(/[^0-9]/g, '');
            document.getElementById('view_whatsapp_link').href = 'https://wa.me/' + phoneNumber;
            
            $('#viewMemberModal').modal('show');
        }
    });
</script>

<?php require_once 'includes/admin_footer.php'; ?>