<?php

$mysqli = new mysqli('localhost', 'root', '', 'reclamations');

if ($mysqli->connect_error) {
    die("Échec de connexion à la base de données : " . $mysqli->connect_error);
}

$nom = $_POST['nom'];
$email = $_POST['email'];
$cin = $_POST['cin'];
$lieu = $_POST['lieu'];
$type = $_POST['type'];
$description = $_POST['description'];
$sql = "INSERT INTO reclamations (nom, email, cin, lieu, type, description, statut) 
        VALUES ('$nom', '$email', '$cin', '$lieu', '$type', '$description', 'nouveau')";

if ($mysqli->query($sql)) {
    echo "Réclamation ajoutée avec succès !";
    header("Location: reclamation.php");
} else {
    echo "Erreur : " . $mysqli->error;
}

$mysqli->close();
?>
