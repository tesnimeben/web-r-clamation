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
$id_reclamation = isset($_POST['id']) ? $_POST['id'] : null;
$reponse = isset($_POST['reponse']) ? $_POST['reponse'] : null;
if ($id_reclamation && $reponse) {
    $query = "UPDATE reclamations SET reponse = ?, status = 'repondu' WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        die("Erreur dans la préparation de la requête SQL : " . $mysqli->error);
    }
    $stmt->bind_param("si", $reponse, $id_reclamation);

    if ($stmt->execute()) {
       echo "Réclamation mise à jour avec succès.";
        header("Refresh: 0.5; URL=dashboard.php");
        
        exit();
    } else {
        die("Erreur dans l'exécution de la requête SQL : " . $stmt->error);
    }
    $stmt->close();
} else {
    die("Données manquantes.");
}

$mysqli->close();
?>





