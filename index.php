<?php 
session_start(); // Garder ici l'appel à session_start()

$mysqli = new mysqli('localhost', 'root', '', 'reclamations');
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$error = ''; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Préparer la requête pour récupérer l'utilisateur par son nom d'utilisateur
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username); // 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Si le mot de passe est correct, enregistrer l'utilisateur dans la session
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id']; // Assurez-vous que le champ 'id' existe dans votre table 'users'
            
            // Vérification du rôle de l'utilisateur
            if ($user['role'] === 'admin') {
                // Redirection vers la page de tableau de bord pour l'admin
                header('Location: dashboard.php');
                exit();
            } elseif (strpos($user['role'], 'admin_') !== false) {
                // Redirection vers la page admin_service.php pour l'admin_sante, admin_transport, etc.
                header('Location: dasbord_admin_servise.php');
                exit();
            } else {
                // Redirection vers la page de réclamation pour les citoyens
                header('Location: reclamation.php');
                exit();
            }
        } else {
            $error = 'Le mot de passe est incorrect.';
        }
    } else {
        $error = 'Nom d\'utilisateur non trouvé.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
     body {
        font-family: Arial, sans-serif;
        background-image: url(rec.png);
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        }
        .login-container {
            background-color: rgba(0, 0, 0, 0.82);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width:350px;
            text-align: center;
        }
        .login-container h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: rgb(255, 255, 255);
        }
        .input-container {
            position: relative;
            margin: 10px 0;
        }
        .input-container .emoji {
            position: absolute;
            top: 50%;
            left: 30px;
            transform: translateY(-50%);
            font-size: 1.2em;
            color: #888;
        }
        .input-container input {
            width: 70%;
            padding: 10px 10px 10px 40px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, rgb(70, 179, 247), rgb(110, 180, 223), rgb(49, 166, 238));
            color: white;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .login-container button:hover {
            background: linear-gradient(135deg, rgb(47, 97, 128), rgb(98, 184, 238), rgb(47, 97, 128));
            transform: scale(1.05);
        }
        .login-container button:active {
            transform: scale(1);
        }
        .register-container {
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }
        .register-container p {
            font-size: 0.9em;
            color: rgb(255, 251, 251);
            margin: 0;
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
    </style>
</head>
<body>
   <div class="login-container">
        <h1>Connexion</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form id="fo" action="" method="POST">
    <div class="input-container">
         <span class="emoji">👤</span>
         <input type="text" name="username" required placeholder="Nom d'utilisateur"></div> 
         <div class="input-container">
         <span class="emoji">🔒</span>
         <input type="password" name="password"  placeholder="Votre mot de passe" required>
         </div> 
        <button type="submit">Se connecter</button>
        <div class="register-container">
            <p>Pas encore inscrit ?</p>
            <a href="register.php">Créer un compte</a>
        </div>
    </form>
 </div>
</body>
</html>
