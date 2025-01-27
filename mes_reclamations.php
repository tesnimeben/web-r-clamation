<?php 
session_start();

// Vérifie que l'utilisateur est connecté et qu'il a un rôle valide (citoyen ou admin_... qui commence par admin_)
if (!isset($_SESSION['role']) || (!in_array($_SESSION['role'], ['citoyen']) && strpos($_SESSION['role'], 'admin_') === false)) {
    header('Location: index.php');
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli('localhost', 'root', '', 'reclamations');
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Récupère les réclamations pour l'utilisateur
$query = "SELECT id, titreReclamation, description, date, status, nomPrenom, email, cin, lieu, typeReclamation, service, photo FROM reclamations WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reclamations = [];
while ($row = $result->fetch_assoc()) {
    $reclamations[] = $row;
}

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="style.css">
    <title>Mes Réclamations</title>
    <style>
        /* Style pour la fenêtre modale */
        .modal {
            display: none; /* Caché par défaut */
            position: fixed; /* Rester en place */
            z-index: 1; /* Au-dessus des autres éléments */
            left: 0;
            top: 0;
            width: 100%; /* Largeur de l'écran */
            height: 100%; /* Hauteur de l'écran */
            overflow: auto; /* Activer le défilement si nécessaire */
            background-color: rgba(0, 0, 0, 0.8); /* Fond sombre avec transparence */
        }

        /* Style du contenu modale */
        .modal-content {
            margin: 2% auto;
            display: block;
            max-width: 90%; /* Taille de l'image dynamique */
            max-height: 90%; /* Ajuste la hauteur */
        }

        /* Le bouton de fermeture */
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
            background-color: rgb(255, 11, 11); /* Fond sombre */
        }
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
<h1>Mes Réclamations</h1>
<table class="table">
    <thead class="theadnoi">
        <tr>
            <th>ID</th>
            <th>Nom et Prénom</th>
            <th>Email</th>
            <th>CIN</th>
            <th>Lieu</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Date</th>
            <th>Type</th>
            <th>Service</th>
            <th>Statut</th>
            <th>Photo</th> <!-- Colonne Photo -->
        </tr>
    </thead>
    <tbody>
    <?php if (empty($reclamations)): ?>
        <tr>
            <td colspan="12">Aucune réclamation répondue.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($reclamations as $reclamation): ?>
            <tr>
                <td>R<?= htmlspecialchars($reclamation['id']); ?></td>
                <td><?= htmlspecialchars($reclamation['nomPrenom']); ?></td>
                <td><?= htmlspecialchars($reclamation['email']); ?></td>
                <td><?= htmlspecialchars($reclamation['cin']); ?></td>
                <td><?= htmlspecialchars($reclamation['lieu']); ?></td>
                <td><?= htmlspecialchars($reclamation['titreReclamation']); ?></td>
                <td><?= htmlspecialchars($reclamation['description']); ?></td>
                <td><?= date('d-m-Y H:i:s', strtotime($reclamation['date'])); ?></td>
                <td><?= htmlspecialchars($reclamation['typeReclamation']); ?></td>
                <td><?= htmlspecialchars($reclamation['service']); ?></td>
                <td>
                    <?php if ($reclamation['status'] === 'nouveau'): ?>
                        <span class="status-circle status-nouveau"></span> <br> Non Répondu
                    <?php elseif ($reclamation['status'] === 'repondu'): ?>
                        <span class="status-circle status-repondu"></span> Répondu
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($reclamation['photo']): ?>
                        <!-- Afficher le bouton pour voir la photo -->
                        <button onclick="voirPhoto('uploads/<?= htmlspecialchars($reclamation['photo']) ?>')" class="voir-photo-btn">👁️ Voir Photo</button>
                    <?php else: ?>
                        Pas de photo
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- Modal pour afficher la photo -->
<div id="photo-modal" class="modal">
    <span class="close" onclick="fermerModal()">&times;</span>
    <img class="modal-content" id="photo-display">
</div>

<script>
// Fonction pour afficher la photo dans la modale
function voirPhoto(photoUrl) {
    const modal = document.getElementById('photo-modal');
    const photoDisplay = document.getElementById('photo-display');

    // Afficher la photo dans la modale
    photoDisplay.src = photoUrl;
    modal.style.display = 'block';  // Afficher la modale
}

// Fonction pour fermer la modale
function fermerModal() {
    const modal = document.getElementById('photo-modal');
    modal.style.display = 'none';  // Masquer la modale
}
</script>

</body>
</html>
