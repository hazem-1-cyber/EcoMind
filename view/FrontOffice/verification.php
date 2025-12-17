<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../model/DonModel.php";
require_once __DIR__ . "/../../controller/DonController.php";
require_once __DIR__ . "/../../controller/config/SettingsManager.php";

$settingsManager = new SettingsManager();

// Vérifier que le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addDon.php');
    exit;
}

// Récupérer et valider les données du formulaire
$typeDon = trim($_POST['type_don'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$associationId = (int)($_POST['association_id'] ?? 0);

// Validation basique côté serveur
$errors = [];

if (empty($typeDon)) {
    $errors[] = "Le type de don est requis";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide";
}

if ($associationId <= 0) {
    $errors[] = "Association invalide";
}

// Traiter l'upload de l'image si présente
$imagePath = null;
if (isset($_FILES['image_don']) && $_FILES['image_don']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/images/uploads/dons/';
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileExtension = strtolower(pathinfo($_FILES['image_don']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $fileName = 'don_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image_don']['tmp_name'], $targetPath)) {
            $imagePath = 'view/FrontOffice/images/uploads/dons/' . $fileName;
        }
    }
}

// Stocker les données dans la session pour usage ultérieur
$_SESSION['don_data'] = [
    'type_don' => $typeDon,
    'email' => $email,
    'association_id' => $associationId,
    'montant' => floatval($_POST['montant'] ?? 0),
    'livraison' => trim($_POST['livraison'] ?? ''),
    'ville' => trim($_POST['ville'] ?? ''),
    'cp' => trim($_POST['cp'] ?? ''),
    'adresse' => trim($_POST['adresse'] ?? ''),
    'localisation' => trim($_POST['localisation'] ?? ''),
    'tel' => trim($_POST['tel'] ?? ''),
    'description_don' => trim($_POST['description_don'] ?? ''),
    'image_don' => $imagePath
];

// Validation spécifique selon le type de don
if ($typeDon === 'money') {
    $montant = floatval($_POST['montant'] ?? 0);
    $livraison = trim($_POST['livraison'] ?? '');
    $minAmount = $settingsManager->getMinDonationAmount();
    $currency = $settingsManager->getCurrency();
    
    if ($montant < $minAmount) {
        $errors[] = "Le montant minimum est {$minAmount} {$currency}";
    }
    
    // Pour les dons d'argent, toujours rediriger vers paiement (livraison forcée à "en_ligne")
    if (empty($errors)) {
        // Redirection vers la page de paiement
        header('Location: paiement.php');
        exit;
    }
} elseif (in_array($typeDon, ['panneau_solaire', 'materiel', 'autre'])) {
    $ville = trim($_POST['ville'] ?? '');
    $cp = trim($_POST['cp'] ?? '');
    $tel = preg_replace('/[^0-9]/', '', trim($_POST['tel'] ?? '')); // Nettoyer le téléphone
    
    if (empty($ville) || strlen($ville) < 2) {
        $errors[] = "Ville invalide (reçu: '$ville')";
    }
    if (!preg_match('/^\d{4}$/', $cp)) {
        $errors[] = "Code postal invalide (reçu: '$cp')";
    }
    if (!preg_match('/^\d{8}$/', $tel)) {
        $errors[] = "Téléphone invalide (reçu: '$tel')";
    }
    
    if ($typeDon === 'autre') {
        $description = trim($_POST['description_don'] ?? '');
        if (empty($description) || strlen($description) < 10) {
            $errors[] = "Description invalide (minimum 10 caractères)";
        }
    }
}

// Si des erreurs sont détectées, afficher en détail
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    echo "<h3>Erreurs de validation:</h3><ul>";
    foreach ($errors as $err) {
        echo "<li style='color:red;'>$err</li>";
    }
    echo "</ul>";

    echo "<a href='addDon.php'>Retour au formulaire</a>";
    exit;
}

// Créer l'objet Don avec toutes les données
$don = new Don();
$don->setTypeDon($typeDon);
$don->setEmail($email);
$don->setAssociationId($associationId);
$don->setStatut('pending');
$don->setImageDon($imagePath);

// Ajouter les champs selon le type
if ($typeDon === 'money') {
    $don->setMontant(floatval($_POST['montant'] ?? 0));
    $don->setLivraison('en_ligne'); // Toujours en ligne pour les dons d'argent
} else {
    $don->setVille($_POST['ville'] ?? null);
    $don->setCp($_POST['cp'] ?? null);
    $don->setLocalisation($_POST['localisation'] ?? null);
    $don->setTel(preg_replace('/[^0-9]/', '', $_POST['tel'] ?? '')); // Nettoyer le téléphone
    $don->setDescriptionDon($_POST['description_don'] ?? null);
}

// Enregistrer le don dans la base de données
try {
    $donCtrl = new DonController();
    $donCtrl->addDon($don);
    
    // Si validation automatique activée pour les dons money, valider directement
    if ($typeDon === 'money' && $settingsManager->isAutoValidateEnabled()) {
        // Mettre à jour le statut à 'validated'
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("UPDATE dons SET statut = 'validated' WHERE email = ? AND type_don = 'money' ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$email]);
    }
    
    // Conserver l'association_id pour la page merci
    $_SESSION['merci_association_id'] = $associationId;
    
    // Rediriger vers la page de remerciement (la session sera nettoyée dans merci.php)
    header('Location: merci.php');
    exit;
} catch (Exception $e) {
    echo "Erreur lors de l'enregistrement du don: " . $e->getMessage();
    echo "<br><a href='addDon.php'>Retour au formulaire</a>";
}
?>