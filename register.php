<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'reclamations');
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'citoyen';  

    if (!empty($username) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Préparer la requête SQL pour insérer un nouvel utilisateur
        $stmt = $mysqli->prepare("INSERT INTO users (username, password,role) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $hashed_password, $role);
        if ($stmt->execute()) {
            header('Location: index.php'); // Rediriger vers la page de connexion
            exit();
        } else {
            $error = "deja cree cette compet  ";
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <style>
     body {
        font-family: Arial, sans-serif;
        background-image: url(rec.png);
        background-size: cover;
        background-position: center; /* Centre l'image */
        background-attachment: fixed; /* L'image reste fixe lorsque tu fais défiler la page */
        background-repeat: no-repeat; /* Ne pas répéter l'image si elle est plus petite que l'écran */
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        }
        /* Conteneur */
        .login-container {
            background-color:  rgba(0, 0, 0, 0.54);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width:350px;
            text-align: center;
        }
        /* Titre */
        .login-container h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: rgb(255, 255, 255) ;
        }
        .input-container {
             position: relative;
             margin: 10px 0;
        }
        .input-container .emoji {
            position: absolute;
            top: 50%;
            left: 30px; /* L'emoji est aligné à gauche */
            transform: translateY(-50%);
            font-size: 1.2em;
            color: #888;
        }
        .input-container input {
            width: 70%;
            padding: 10px 10px 10px 40px; /* Espace pour l'emoji */
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        /* Bouton */
        .login-container button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg,rgb(70, 179, 247),rgb(110, 180, 223),rgb(49, 166, 238));
            color: white;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .login-container button:hover {
            background: linear-gradient(135deg,rgb(47, 97, 128),rgb(98, 184, 238),rgb(47, 97, 128));
            transform: scale(1.05);
        }

        .login-container button:active {
            transform: scale(1);
        }
        .register-container {
        display: flex; /* Aligner les éléments sur la même ligne */
        gap: 5px; /* Espacement entre le texte et le lien */
        align-items: center; /* Centrer verticalement */
        justify-content: center; /* Centrer horizontalement dans le parent */
        margin-top: 10px;
}

.register-container p {
    font-size: 0.9em;
    color: rgb(255, 251, 251);
    margin: 0; /* Supprime les marges par défaut */
}

.register-container a {
    font-size: 0.9em;
    color: rgb(23, 176, 237);
    text-decoration: none;
    font-weight: bold; 
}

.register-container a:hover {
    text-decoration: underline;
}
.input-container input [type=email]{
        background-color:  rgba(183, 178, 178, 0.54);
            width: 70%;
            padding: 10px 10px 10px 40px; /* Espace pour l'emoji */
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

    </style>
</head>
<body id="bo">

<div class="login-container">
    <h1>Créer un compte</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form id="fo" action="" method="POST">
        <div class="input-container">
        <span class="emoji">👤</span>
        <input type="text" name="username" required placeholder="entrez votre nom d'utilist">
        </div>
        <br>
        <div class="input-container">
        <span class="emoji">📧</span>
        <input type="email" name="email" placeholder="Entrez votre email">
        </div>
        <br>
        <div class="input-container">
        <span class="emoji">🔒</span>
        <input type="password" name="password" required>
        </div>
        <br>
        <button id="bu" type="submit">Créer un compte</button>
        <button ><a href="index.php">j'ai deja un compte </a></button>
    </form>
</div>
</body>
</html>
