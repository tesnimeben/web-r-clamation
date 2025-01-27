<?php 
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli('localhost', 'root', '', 'reclamations');
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// Récupérer les réclamations avec statut 'nouveau'
$queryNouvelles = "SELECT * FROM reclamations WHERE status = 'nouveau'";
$result_nouvelles = $mysqli->query($queryNouvelles);
if ($result_nouvelles === false) {
    die("Erreur dans l'exécution de la requête SQL pour nouvelles réclamations : " . $mysqli->error);
}

// Récupérer les réclamations avec statut 'repondu'
$queryRepondues = "SELECT * FROM reclamations WHERE status = 'repondu'";
$result_repondues = $mysqli->query($queryRepondues);
if ($result_repondues === false) {
    die("Erreur dans l'exécution de la requête SQL pour réclamations répondues : " . $mysqli->error);
}

// Vérification si une réclamation doit être mise à jour
if (isset($_POST['update_status_id'])) {
    $update_id = $_POST['update_status_id'];
    $new_status = $_POST['new_status'];
    $service_assigned = $_POST['service_assigned']; // Nouveau champ pour spécifier le service concerné

    // Mettre à jour le statut et le service dans la base de données
    $updateQuery = "UPDATE reclamations SET status = ?, service = ? WHERE id = ?";
    $stmt = $mysqli->prepare($updateQuery);
    $stmt->bind_param('ssi', $new_status, $service_assigned, $update_id);
    $stmt->execute();
    $stmt->close();
    
  
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réclamations</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style pour la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.56);
        }

        .modal-content {
            margin: 2% auto;
            display: block;
            width: 50%;
            max-width: 500px;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            background-color: transparent;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body id="boda">
    <div class="container">
        <h2>Nouvelles Réclamations</h2>
        <table class="table table-red">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom et Prénom</th>
                    <th>Email</th>
                    <th>CIN</th>
                    <th>Lieu</th>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Service</th>
                    <th>Photo</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result_nouvelles->num_rows > 0) {
                    while ($row = $result_nouvelles->fetch_assoc()):
                ?>
                <tr>
                    <td>R<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nomPrenom']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['cin'] ?></td>
                    <td><?= htmlspecialchars($row['lieu']) ?></td>
                    <td><?= htmlspecialchars($row['titreReclamation']) ?></td>
                    <td><?= htmlspecialchars($row['typeReclamation']) ?></td>
                    <td><?= htmlspecialchars($row['service']) ?></td>
                    <td>
                        <?php if (!empty($row['photo'])): ?>
                            <button onclick="voirPhoto('uploads/<?= htmlspecialchars($row['photo']) ?>')" class="voir-photo-btn">
                                👁️ Voir Photo
                            </button>
                        <?php else: ?>
                            Pas de photo
                        <?php endif; ?>
                    </td>
                    <td><?= isset($row['date']) ? date('d-m-Y H:i:s', strtotime($row['date'])) : 'Non spécifiée' ?></td>
                    <td>
                        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="update_status_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="new_status" value="repondu">
                            <!-- Choisir le service auquel envoyer la réclamation -->
                            <select name="service_assigned" class="selec" required>
                                <option value="admin_sante" <?= ($row['service'] === 'admin_sante') ? 'selected' : '' ?>>Admin_sante</option>
                                <option value="admin_education" <?= ($row['service'] === 'admin_education') ? 'selected' : '' ?>>Admin_Éducation</option>
                                <option value="admin_transport" <?= ($row['service'] === 'admin_transport') ? 'selected' : '' ?>>Admin_Transport</option>
                                <!-- Ajouter d'autres options ici pour chaque service -->
                            </select>
                            <button type="submit">Marquer comme répondu</button>
                        </form>
                    </td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='11'>Aucune nouvelle réclamation.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Réclamations Répondue</h2>
        <table class="table table-green">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom et Prénom</th>
                    <th>Email</th>
                    <th>CIN</th>
                    <th>Lieu</th>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Service</th>
                    <th>Photo</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result_repondues->num_rows > 0) {
                    while ($row = $result_repondues->fetch_assoc()):
                ?>
                <tr>
                    <td>R<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nomPrenom']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['cin'] ?></td>
                    <td><?= htmlspecialchars($row['lieu']) ?></td>
                    <td><?= htmlspecialchars($row['titreReclamation']) ?></td>
                    <td><?= htmlspecialchars($row['typeReclamation']) ?></td>
                    <td><?= htmlspecialchars($row['service']) ?></td>
                    <td>
                        <?php if (!empty($row['photo'])): ?>
                            <button onclick="voirPhoto('uploads/<?= htmlspecialchars($row['photo']) ?>')" class="voir-photo-btn">
                                👁️ Voir Photo
                            </button>
                        <?php else: ?>
                            Pas de photo
                        <?php endif; ?>
                    </td>
                    <td><?= $row['date'] ?></td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='10'>Aucune réclamation répondue.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div id="photo-modal" class="modal">
            <span class="close" onclick="fermerModal()">&times;</span>
            <img class="modal-content" id="photo-display">
        </div>
    </div>

    <script>
    function voirPhoto(photoUrl) {
        const modal = document.getElementById('photo-modal');
        const photoDisplay = document.getElementById('photo-display');
        
        // Affiche la photo dans la modale
        photoDisplay.src = photoUrl;
        modal.style.display = 'block';
    }

    function fermerModal() {
        const modal = document.getElementById('photo-modal');
        modal.style.display = 'none'; // Ferme la modale
    }
    </script>
</body>
</html>
