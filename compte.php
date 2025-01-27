<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirection vers la page de connexion
    exit();
}

$username = $_SESSION['username']; // Récupération du nom d'utilisateur
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?= htmlspecialchars($username) ?></h1> <!-- Affiche le nom d'utilisateur -->

        <h2>Mes Informations</h2>
        <p>Nom d'utilisateur : <?= htmlspecialchars($username) ?></p>
        <!-- Autres informations du compte peuvent être récupérées et affichées ici -->

        <a href="modifier_compte.php">Modifier mes informations</a> <!-- Lien vers la page de modification -->
    </div>
</body>
</html>
