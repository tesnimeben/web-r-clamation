
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
    <h1>Tableau de Bord Administrateur</h1><H6><?= htmlspecialchars($user['role']); ?></H6>
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
       <li><a href="admin_servise.php" target="iframe_a">Gestion des réclamations</a></li>
        <li><a href="gestionlocalitionse.php" target="iframe_a">Gestion des localisations</a></li>
         <li><a href="ajoute_reclamation.php" target="iframe_a">Ajoutez votre réclamation</a></li>
         <li><a href="mes_reclamations.php" target="iframe_a">L'historique de vos réclamations</a></li>
    </ul>
</nav>

<section>
    <iframe src="admin_servise.php" name="iframe_a" title="Iframe Example"></iframe>
</section>
</body>
</html>
