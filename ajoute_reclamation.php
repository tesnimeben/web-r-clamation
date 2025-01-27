<?php
session_start();

// Vérifie que l'utilisateur est connecté et qu'il a un rôle valide (citoyen ou admin_... qui commence par admin_)
if (!isset($_SESSION['role']) || (!in_array($_SESSION['role'], ['citoyen']) && strpos($_SESSION['role'], 'admin_') === false)) {
    header('Location: index.php');
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli('localhost', 'root', '', 'reclamations');
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté
$reclamations = [];

// Exécuter la requête SQL pour récupérer les réclamations de l'utilisateur connecté
$query = $mysqli->prepare("SELECT * FROM reclamations WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
    $reclamations[] = $row;
}

$query->close();

$message = ''; // Initialize $message here

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $_SESSION['user_id'];

    $nomPrenom = trim($_POST['nomPrenom']);
    $email = trim($_POST['email']);
    $cin = trim($_POST['cin']);
    $telephone = trim($_POST['telephone']);
    $titreReclamation = trim($_POST['titreReclamation']);
    $photo = $_FILES['photo']['name']; 
    $service = trim($_POST['service']);
    $typeReclamation = trim($_POST['typeReclamation']);
    $description = trim($_POST['description']);
    
    // Vérification de l'existence de 'lieu' dans $_POST
    $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : ''; 
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);

    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); 
    }

    // Vérification du fichier uploadé (image)
    if ($photo) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Types autorisés

        // Vérifier le type et la taille du fichier
        if (!in_array($fileType, $allowedTypes)) {
            $message = "Seules les images JPEG, PNG et GIF sont autorisées.";
        } elseif ($fileSize > 5000000) { // Limite de taille à 5MB
            $message = "La taille du fichier est trop grande. Maximum : 5 Mo.";
        } else {
            $targetFile = $uploadDir . basename($photo);
            if (!move_uploaded_file($fileTmpPath, $targetFile)) {
                $message = "Erreur lors du téléchargement de la photo.";
            }
        }
    }

    // Si aucune erreur, insertion dans la base de données
    if (!$message) {
        $query = $mysqli->prepare("INSERT INTO reclamations (user_id, nomPrenom, email, cin, telephone, titreReclamation, photo, service, typeReclamation, description, lieu, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'nouveau')");

        if ($query === false) {
            die('Erreur de préparation de la requête : ' . $mysqli->error);
        }

        // Lier les paramètres, y compris user_id
        $query->bind_param("issssssssssss", $user_id, $nomPrenom, $email, $cin, $telephone, $titreReclamation, $photo, $service, $typeReclamation, $description, $lieu, $latitude, $longitude);

        if ($query->execute()) {
            $message = "Réclamation envoyée avec succès.";
        } else {
            $message = "Erreur lors de l'envoi de la réclamation. " . $query->error;
        }

        $query->close();
    }

    $mysqli->close();
}

// Récupération du nom d'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajute reclamation</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height:400px;
            width: 90%;
            border: 1px solid #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
        }
        .form-row label, .form-row input, .form-row select {
            width: 48%;
        }
    </style>
</head>
<body id="bore">
<div class="container">
<h1>Ajoute votre réclamation</h1>
    <?php if ($message): ?>
        <div class="message"> 
            <p class="<?= strpos($message, 'succès') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        </div>
    <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-container">
        <div class="form-row">
            <label for="nomPrenom" >Nom et Prénom :</label>
            <input type="text" id="nomPrenom" name="nomPrenom" placeholder="Entrez votre nom et prénom" class="in" required>

            <label for="email" >Email :</label>
            <input type="email" id="email" name="email" placeholder="Entrez votre email" class="in" required>
        </div>

        <div class="form-row">
            <label for="cin" >CIN :</label>
            <input type="text" id="cin" name="cin" placeholder="Entrez votre numéro CIN" required  class="in" maxlength="8">

            <label for="telephone" >Téléphone :</label>
            <input type="text" id="telephone" name="telephone" placeholder="Entrez votre numéro de téléphone" class="in" required>
        </div>

        <div class="form-row">
        <!-- Liste déroulante des services -->
        <label for="service">Service de réclamation :</label>
        <select id="service" name="service" class="sel" required>
            <option value="">-- Choisissez un service </option>
            <option value="protection_civile">Protection Civile / الحماية المدنية</option>
            <option value="police">Police / الأمن الوطني</option>
            <option value="securite_sociale">Sécurité Sociale / الضمان الاجتماعي</option>
            <option value="administration_generale">Administration Générale / الإدارة العامة</option>
            <option value="education">Éducation / وزارة التعليم</option>
            <option value="sante_publique">Santé Publique / وزارة الصحة</option>
            <option value="transport">Ministère des Transports / وزارة النقل</option>
            <option value="municipalite">Municipalité / البلدية</option>
            <option value="steg">STEG (Électricité et Gaz) / الشركة التونسية للكهرباء والغاز</option>
            <option value="sonede">SONEDE (Eaux) / الشركة الوطنية لاستغلال وتوزيع المياه</option>
            <option value="poste">Poste Tunisienne / البريد التونسي</option>
            <option value="sports">Ministère de la Jeunesse et des Sports / وزارة الشباب والرياضة</option>
            <option value="agriculture">Ministère de l'Agriculture / وزارة الفلاحة</option>
            <option value="affaires_sociales">Ministère des Affaires Sociales / وزارة الشؤون الاجتماعية</option>
            <option value="affaires_religieuses">Ministère des Affaires Religieuses / وزارة الشؤون الدينية</option>
            <option value="commerce">Ministère du Commerce / وزارة التجارة وتنمية الصادرات</option>
            <option value="environnement">ANPE (Protection de l'Environnement) / الوكالة الوطنية لحماية المحيط</option>
            <option value="technologie">Ministère des Technologies de l'Information / وزارة تكنولوجيا المعلومات والاقتصاد الرقمي</option>
            <option value="tourisme">Office National du Tourisme Tunisien / الديوان الوطني التونسي للسياحة</option>
            <option value="banques_publiques">Banques Publiques / البنوك العامة</option>
            <option value="autre">Autre</option>
        </select>
        <br><label for="titre">Titre de votre réclamation:</label>
            <select class="sel" input type="text" id="titreReclamation" name="titreReclamation"  required>
                <option value="">-- Sélectionnez le titre de votre réclamation depont le servise </option>
            </select>
            <!-- Champ de texte pour saisir le titre si "Autre" est sélectionné -->
                <div id="autre-container" style="display: none;      ">
                <label for="autre-titre"  style="    background-color:rgba(239, 0, 0, 0.78); color: #ffffff;">▶️Entrez le titre de réclamation:</label>  <input type="text" id="autre-titre" name="autre_titre" placeholder="Saisissez votre titre  " class="in"  />

            </div>
        </div>
        <div class="form-row">
    
                    <label for="photo" >Envoyer photo  du votre ca :</label>
                    <input type="file" id="photo" name="photo" accept="image/*" class="in" >
                    <label for="typeReclamation" >Type de réclamation :</label>
                    <select id="typeReclamation" name="typeReclamation" class="sel" required>
                    <option value=""></option>   
                    <option value="normale">Normale</option>
                    <option value="urgente">Urgente</option>
                    <option value="tres_urgente">Très urgente</option>
                    </select>
        </div>


    </div>
<script>
    const serviceDropdown = document.getElementById("service");
    const titreDropdown = document.getElementById("titreReclamation");
    const autreContainer = document.getElementById("autre-container");
    const autreTitreInput = document.getElementById("autre-titre");

    // Suggestions pour chaque service en français et arabe
    const suggestions = {
        "protection_civile": [
            "Interventions d'urgence / التدخلات الطارئة",
            "Incendies / الحرائق",
            "Secours et assistance / الإسعافات والإنقاذ"
        ],
        "police": [
            "Plainte / شكوى",
            "Signalement de vol / التبليغ عن السرقة",
            "Sécurité publique / الأمن العام"
        ],
        "securite_sociale": [
            "Assurance maladie / التأمين الصحي",
            "Prestations sociales / المساعدات الاجتماعية",
            "Pensions / المعاشات"
        ],
        "administration_generale": [
            "Documents administratifs / الوثائق الإدارية",
            "Plaintes générales / الشكاوي العامة"
        ],
        "education": [
            "Réclamations sur les écoles / شكاوى المدارس",
            "Examens et résultats / الامتحانات والنتائج",
            "Universités / الجامعات"
        ],
        "sante_publique": [
            "Services médicaux / الخدمات الطبية",
            "Hôpitaux / المستشفيات",
            "Cliniques / العيادات"
        ],
        "transport": [
            "Transports publics / النقل العام",
            "Permis de conduire / رخص القيادة",
            "Routes et infrastructures / الطرق والبنية التحتية"
        ],
        "municipalite": [
            "Services municipaux / الخدمات البلدية",
            "Problèmes d'infrastructure / مشاكل البنية التحتية"
        ],
        "steg": [
            "Pannes d'électricité / انقطاع الكهرباء",
            "Fuites de gaz / تسرب الغاز"
        ],
        "sonede": [
            "Pannes d'eau / انقطاع المياه",
            "Fuites d'eau / تسرب المياه"
        ],
        "poste": [
            "Problèmes de courrier / مشاكل البريد",
            "Services postaux / الخدمات البريدية"
        ],
        "sports": [
            "Infrastructures sportives / المنشآت الرياضية",
            "Programmes de sport / برامج الرياضة"
        ],
        "agriculture": [
            "Problèmes agricoles / المشاكل الزراعية",
            "Services de culture / خدمات الزراعة"
        ],
        "affaires_sociales": [
            "Aide sociale / المساعدة الاجتماعية",
            "Pensions / المعاشات"
        ],
        "affaires_religieuses": [
            "Gestion des lieux de culte / إدارة أماكن العبادة",
            "Problèmes liés aux affaires religieuses / مشاكل الشؤون الدينية"
        ],
        "commerce": [
            "Contrôle des prix / مراقبة الأسعار",
            "Problèmes de commerce / مشاكل التجارة"
        ],
        "environnement": [
            "Pollution / التلوث",
            "Problèmes environnementaux / مشاكل بيئية"
        ],
        "technologie": [
            "Services numériques / الخدمات الرقمية",
            "Problèmes technologiques / مشاكل تكنولوجية"
        ],
        "tourisme": [
            "Sites touristiques / المواقع السياحية",
            "Problèmes liés au tourisme / مشاكل السياحة"
        ],
        "banques_publiques": [
            "Problèmes bancaires / مشاكل بنكية",
            "Services financiers / الخدمات المالية"
        ],
        "autre": [] // Pas de suggestions pour "Autre"
    };

    // Met à jour les options du titre ou affiche le champ "autre"
    serviceDropdown.addEventListener("change", function () {
        const selectedService = serviceDropdown.value;

        // Réinitialiser
        titreDropdown.innerHTML = ""; 
        autreContainer.style.display = "none"; 
        autreTitreInput.value = ""; 

        if (selectedService === "autre") {
            // Afficher le champ de texte pour "Autre"
            autreContainer.style.display = "block";
        } else if (suggestions[selectedService]) {
            // Ajouter une option par défaut
            const defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.textContent = "-- Choisissez un titre / اختر عنواناً --";
            titreDropdown.appendChild(defaultOption);

            // Ajouter les suggestions
            suggestions[selectedService].forEach((suggestion) => {
                const option = document.createElement("option");
                option.value = suggestion;
                option.textContent = suggestion;
                titreDropdown.appendChild(option);
            });

            // Afficher la liste déroulante
            titreDropdown.parentElement.style.display = "block";
        } else {
            // Si aucun service valide n'est sélectionné
            const defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.textContent = "-- Sélectionnez un service d'abord / اختر الخدمة أولاً --";
            titreDropdown.appendChild(defaultOption);
        }
    });
</script>


        <label for="description">Description :</label>
        <textarea id="description" name="description" placeholder="Écrire ici votre réclamation..." class="tex" required></textarea>

        <label for="lieu">Localisation votre adresse:</label>
        <center><input type="text" id="lieu" name="lieu" placeholder="Sélectionnez votre lieu sur la carte" required> 
        <br>
        <div id="map"></div>
        <input type="hidden" id="latitude" name="latitude" required>
        <input type="hidden" id="longitude" name="longitude" required></center><br>
        <button type="submit" id="envoye">Envoyer</button>
        <input type="reset" id="annule"value="Réinitialiser">
    </form>
 
</div>

<script>
    const map = L.map('map').setView([35.8095, 10.2266], 6); 

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

let marker;
function getPlaceName(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('API response:', data);
            if (data && data.address) {
                const address = data.address;
                let fullAddress = '';

                if (address.road) {
                    fullAddress += address.road;
                }
                if (address.city) {
                    fullAddress += ', ' + address.city;
                }
                if (address.state) {
                    fullAddress += ', ' + address.state;
                }
                if (address.country) {
                    fullAddress += ', ' + address.country;
                }

                document.getElementById('lieu').value = fullAddress;
            } else {
                alert("Lieu non trouvé pour ces coordonnées.");
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération du nom de lieu :', error);
            alert("Erreur lors de la récupération du lieu.");
        });
}

map.on('click', function(e) {
    const { lat, lng } = e.latlng;

    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    getPlaceName(lat, lng);

    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng]).addTo(map);
    }

    marker.bindPopup(`Coordonnées : ${lat.toFixed(5)}, ${lng.toFixed(5)}`).openPopup();
});
</script>
</body>
</html>
