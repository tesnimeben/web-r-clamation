<?php
session_start();

// Vérification si l'utilisateur est connecté et a le rôle 'citoyen'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'citoyen' || !isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Rediriger vers la page de connexion si non connecté
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mysqli = new mysqli('localhost', 'root', '', 'reclamations');
    if ($mysqli->connect_error) {
        die("Erreur de connexion : " . $mysqli->connect_error);
    }
    // Ajoutez ici le traitement des données POST si nécessaire.
}

// Récupération du nom d'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamation</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Style de la barre de navigation */
        nav { 
            background: linear-gradient(to bottom, rgb(64, 67, 119), rgb(53, 53, 131), rgb(53, 60, 108)); 
            color: white;
            padding: 15px 0;
            position:sticky; 
            width: 100%;
            top:100px; 
            left: 0; 
            z-index: 1000; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            transition: all 0.3s ease; 
        }

        /* Liste de la navigation */
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        /* Items du menu */
        nav ul li {
            margin: 0 20px;
        }

        /* Liens de navigation */
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        nav ul li a:focus {
            background-color: #066fe0; /* Fond bleu */
            color: white; /* Texte en blanc */
            border: 10px solid #066fe0; /* Bordure bleue */
        }
        nav ul li a:active {
            border: 10px  #066fe0; /* Bordure bleue lors du clic */
            background-color: #066fe0; /* Fond bleu lors du clic */
            color: white; /* Texte en blanc lors du clic */
        }

        /* Si la page contient une iframe, on évite que le flash ou l'animation ne soit déclenché en permanence */
        iframe {
    width: 100%;
    height: 80vh; /* 80% de la hauteur de la fenêtre */
    border: none;
    margin-top: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

        /* Ajout d'une ombre subtile sur l'iframe lors du survol */
        iframe:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        #map {
            height: 500px;
            width: 100%;
            border: 1px solid #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 5px;
        }
    </style>
</head>
<body id="boda">

<div class="header">
    <div>
            <h6>Bienvenue, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong> !</h6>
            <p>Sur la plateforme dédiée à la gestion citoyenne, vous pouvez soumettre vos réclamations et contribuer activement à l'amélioration de notre pays.</p>
    </div>
    <div class="btn-container">
        <button class="login-btn" onclick="alert('Nom d\'utilisateur : <?= addslashes(htmlspecialchars($username)) ?>')">
            <?= htmlspecialchars($username); ?>
        </button>
        <button class="logout-btn" onclick="window.location.href='logout.php';">
            Déconnecter
        </button>
    </div>
</div>
<nav>
    <ul>
         <li><a href="ajoute_reclamation.php" target="iframe_a">Ajoutez votre réclamation</a></li>
         <li><a href="mes_reclamations.php" target="iframe_a">L'historique de vos réclamations</a></li>
    </ul>
</nav>

<section>
    <iframe src="ajoute_reclamation.php" name="iframe_a" title="Iframe Example"></iframe>
</section>
</body>
</html>
