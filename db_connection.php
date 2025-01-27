<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "reclamations";

// Connexion à la base de données
$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}
?>
