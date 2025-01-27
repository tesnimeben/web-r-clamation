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

    // Mettre à jour le statut dans la base de données
    $updateQuery = "UPDATE reclamations SET status = ? WHERE id = ?";
    $stmt = $mysqli->prepare($updateQuery);
    $stmt->bind_param('si', $new_status, $update_id);
    $stmt->execute();
    $stmt->close();
    
    // Rediriger pour éviter la soumission multiple du formulaire
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$queryAllReclamations = "SELECT * FROM reclamations";
$result_all = $mysqli->query($queryAllReclamations);
if ($result_all === false) {
    die("Erreur dans l'exécution de la requête SQL pour toutes les réclamations : " . $mysqli->error);
}

// Récupération du nom d'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>

       /* Style de la barre de navigation */
       nav { 
            background: linear-gradient(to bottom, rgb(86, 88, 120), rgb(69, 69, 87), rgb(62, 66, 98)); 
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
    <h1>Tableau de Bord Administrateur</h1>
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
       <li><a href="gestionlocalition.php" target="iframe_a">Gestion des localisations</a></li>
         <li><a href="gestionreclamtion.php" target="iframe_a">Gestion des réclamations</a></li>
        <li><a href="gestionservise.php" target="iframe_a">Gestion des services</a></li>
        <li><a href="admin_utilisateurs.php" target="iframe_a">Gestion des Comptes</a></li>
    </ul>
</nav>

<section>
    <iframe src="gestionlocalition.php" name="iframe_a" title="Iframe Example"></iframe>
</section>
</body>
</html>





















