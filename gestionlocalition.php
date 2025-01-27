<?php 
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', '', 'reclamations');
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$queryAllReclamations = "SELECT * FROM reclamations";
$result_all = $mysqli->query($queryAllReclamations);
if ($result_all === false) {
    die("Erreur dans l'exécution de la requête SQL pour toutes les réclamations : " . $mysqli->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
            background-color: rgb(0, 0, 0); /* Fond sombre */
            background-color: rgba(0, 0, 0, 0.56); /* Fond sombre avec transparence */
        }

        /* Style du contenu modale */
        .modal-content {
            margin: 2% auto;
            display: block;
            width: 50%; /* Taille de l'image */
            max-width: 500px;
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

        /* Style pour la carte */
        #map {
            height:400px; /* Hauteur de la carte */
            width:90%;   /* Largeur de la carte */
            border: 1px solid #000000;
            display: flex;
            justify-content: center; /* Centre horizontalement dans un conteneur flex */
            align-items: center; 
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
    <title>gestion localication</title>
</head>
<body id="boda">
<div class="container">
    <h2>Carte des Réclamations</h2>
    <center><div id="map"></div></center>
    <h2>Réclamations</h2>
    <table class="table">
        <thead class="theadnoi">
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
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($row = $result_all->fetch_assoc()) {
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
                    <?php if ($row['photo']): ?>
                        <button onclick="voirPhoto('uploads/<?= htmlspecialchars($row['photo']) ?>')" class="voir-photo-btn">👁️ Voir Photo</button>
                    <?php else: ?>
                        Pas de photo
                    <?php endif; ?>
                </td>
                <td><?= isset($row['date']) ? date('d-m-Y H:i:s', strtotime($row['date'])) : 'Non spécifiée' ?></td>
                <td>
                    <?php if ($row['status'] === 'nouveau'): ?>
                        <span class="status-circle status-nouveau"></span> <br> Non Répondu
                    <?php elseif ($row['status'] === 'repondu'): ?>
                        <span class="status-circle status-repondu"></span> Répondu
                    <?php endif; ?>
                </td>
            </tr>
            <?php 
            }
            if ($result_all->num_rows === 0) {
                echo "<tr><td colspan='11'>Aucune réclamation répondue.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal pour afficher la photo -->
<div id="photo-modal" class="modal">
    <span class="close" onclick="fermerModal()">&times;</span>
    <img class="modal-content" id="photo-display">
</div>

<script>
// Initialiser la carte avec Leaflet
var map = L.map('map').setView([36.8094, 10.1815], 6); // Coordonnées de Tunis (peut être modifié selon tes besoins)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Ajouter les marqueurs pour chaque réclamation
<?php 
$result_all = $mysqli->query("SELECT * FROM reclamations");
if ($result_all) {
    while ($row = $result_all->fetch_assoc()) {
        $lat = $row['latitude']; // Latitude
        $lng = $row['longitude']; // Longitude
?>
        L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map)
            .bindPopup("<b>R<?= htmlspecialchars($row['id']) ?></b><br><?= htmlspecialchars($row['nomPrenom']) ?>")
            .openPopup();
<?php
    }
}
?>
</script>
<script>
// Fonction pour afficher la photo dans la modale
function voirPhoto(photoUrl) {
    const modal = document.getElementById('photo-modal');
    const photoDisplay = document.getElementById('photo-display');
    const mapContainer = document.getElementById('map'); // Conteneur de la carte
    
    // Masquer la carte
    mapContainer.style.display = 'none';
    
    // Afficher la photo dans la modale
    photoDisplay.src = photoUrl;
    modal.style.display = 'block';
}

// Fonction pour fermer la modale
function fermerModal() {
    const modal = document.getElementById('photo-modal');
    const mapContainer = document.getElementById('map'); // Conteneur de la carte
    
    // Afficher à nouveau la carte
    mapContainer.style.display = 'block';
    
    // Fermer la modale
    modal.style.display = 'none';
}
</script>

</body>
</html>