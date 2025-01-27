<?php 
// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=reclamations';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Démarrage de la session
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès interdit : vous devez vous connecter.");
}

$adminServiceId = $_SESSION['user_id']; // L'ID de l'utilisateur connecté est stocké en session

// Récupération du rôle de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
$stmt->execute(['id' => $adminServiceId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur a le rôle "admin_service"
if (strpos($user['role'], 'admin_') === false) {
    die("Accès interdit : vous n'êtes pas un administrateur de service.");
}

// Récupération des réclamations liées à l'administrateur de service connecté
$stmt = $pdo->prepare("SELECT * FROM reclamations WHERE service = :service");
$stmt->execute(['service' => $user['role']]); // Le rôle de l'administrateur correspond au service dans les réclamations
$reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Réclamations servise </title>
    <link rel="stylesheet" href="style.css">
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
    <h1> Réclamations</h1>
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
                <th>Photo</th> <!-- Colonne Photo -->
            </tr>
        </thead>
        <tbody>
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
                        <?php if ($reclamation['photo']): ?>
                            <!-- Afficher le bouton pour voir la photo -->
                            <button onclick="voirPhoto('uploads/<?= htmlspecialchars($reclamation['photo']) ?>')" class="voir-photo-btn">👁️ Voir Photo</button>
                        <?php else: ?>
                            Pas de photo
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
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
