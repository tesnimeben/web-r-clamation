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

// Suppression d'un utilisateur
if (isset($_POST['delete'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
}

// Changement de rôle
if (isset($_POST['change_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute(['role' => $newRole, 'id' => $userId]);
}

// Récupération de la liste des utilisateurs
$stmt = $pdo->query("SELECT id, username, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Comptes</title>
    <link rel="stylesheet" href="style.css">
  
</head>
<body id="boda">
<div class="container">
    <h1>Gestion des Comptes Utilisateurs</h1>
    <table class="table">
        <thead class="theadnoi">
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <div class="action-buttons">
                            <!-- Formulaire pour supprimer un utilisateur -->
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button class="login-btn" type="submit" name="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                            </form>

                            <!-- Formulaire pour changer le rôle -->
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="new_role" required class="selec">
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="citoyen" <?= $user['role'] === 'citoyen' ? 'selected' : '' ?>>Citoyen</option>
                                    <!-- Autres rôles admin de services -->
                                    <option value="admin_police" <?= $user['role'] === 'admin_police' ? 'selected' : '' ?>>Admin Police</option>
                                    <option value="admin_sante" <?= $user['role'] === 'admin_sante' ? 'selected' : '' ?>>Admin Santé</option>
                                    <option value="admin_education" <?= $user['role'] === 'admin_education' ? 'selected' : '' ?>>Admin Éducation</option>
                                    <option value="admin_transport" <?= $user['role'] === 'admin_transport' ? 'selected' : '' ?>>Admin Transport</option>
                                    <option value="admin_protection_civile" <?= $user['role'] === 'admin_protection_civile' ? 'selected' : '' ?>>Admin Protection Civile</option>
                                    <option value="admin_securite_sociale" <?= $user['role'] === 'admin_securite_sociale' ? 'selected' : '' ?>>Admin Sécurité Sociale</option>
                                    <option value="admin_administration_generale" <?= $user['role'] === 'admin_administration_generale' ? 'selected' : '' ?>>Admin Administration Générale</option>
                                    <option value="admin_municipalite" <?= $user['role'] === 'admin_municipalite' ? 'selected' : '' ?>>Admin Municipalité</option>
                                    <option value="admin_steg" <?= $user['role'] === 'admin_steg' ? 'selected' : '' ?>>Admin STEG</option>
                                    <option value="admin_sonede" <?= $user['role'] === 'admin_sonede' ? 'selected' : '' ?>>Admin SONED</option>
                                    <option value="admin_poste" <?= $user['role'] === 'admin_poste' ? 'selected' : '' ?>>Admin Poste</option>
                                    <option value="admin_sports" <?= $user['role'] === 'admin_sports' ? 'selected' : '' ?>>Admin Sports</option>
                                    <option value="admin_agriculture" <?= $user['role'] === 'admin_agriculture' ? 'selected' : '' ?>>Admin Agriculture</option>
                                    <option value="admin_affaires_sociales" <?= $user['role'] === 'admin_affaires_sociales' ? 'selected' : '' ?>>Admin Affaires Sociales</option>
                                    <option value="admin_affaires_religieuses" <?= $user['role'] === 'admin_affaires_religieuses' ? 'selected' : '' ?>>Admin Affaires Religieuses</option>
                                    <option value="admin_affaires_commerce" <?= $user['role'] === 'admin_affaires_commerce' ? 'selected' : '' ?>>Admin Affaires Commerce</option>
                                    <option value="admin_environnement" <?= $user['role'] === 'admin_environnement' ? 'selected' : '' ?>>Admin Environnement</option>
                                    <option value="admin_technologie" <?= $user['role'] === 'admin_technologie' ? 'selected' : '' ?>>Admin Technologie</option>
                                    <option value="admin_tourisme" <?= $user['role'] === 'admin_tourisme' ? 'selected' : '' ?>>Admin Tourisme</option>
                                    <option value="admin_banques_publiques" <?= $user['role'] === 'admin_banques_publiques' ? 'selected' : '' ?>>Admin Banques Publiques</option>
                                </select>
                                <button class="logout-btn"type="submit" name="change_role">Modifier</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
            </div>
</body>
</html>

