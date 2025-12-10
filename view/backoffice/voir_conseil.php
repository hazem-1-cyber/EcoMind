<?php
session_start();

// L'admin doit être connecté
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php");
    exit;
}

require_once '../../config.php';
require_once '../../model/conseilReponse_model.php';
require_once '../../controller/reponseConseil_controller.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID invalide.");
}

// Récupération de la réponse
$db = Config::getConnexion();
$stmt = $db->prepare("SELECT * FROM reponse_formulaire WHERE id = ?");
$stmt->execute([$id]);
$reponse = $stmt->fetch(PDO::FETCH_OBJ);

if (!$reponse) {
    die("Réponse non trouvée dans la base de données (ID: $id).");
}

// Récupération des conseils attribués à cette réponse (les mêmes que l'utilisateur a vus)
$controller = new FormulaireController();
$conseilsObj = $controller->getConseilsAttribues($id);

// Convertir en tableau simple pour l'affichage
$conseils = [];
foreach ($conseilsObj as $type => $conseil) {
    $conseils[$type] = $conseil->getTexteConseil();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils Admin - Réponse #<?= $id ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--vert:#013220;--mint:#A8E6CF;}
        body{font-family:'Segoe UI',sans-serif;background:#f8f9fa;color:var(--vert);margin:0;padding:40px 20px;}
        .container{max-width:1300px;margin:0 auto;background:white;border-radius:30px;box-shadow:0 25px 70px rgba(1,50,32,0.2);overflow:hidden;}
        header{background:linear-gradient(135deg,var(--vert),#001a10,var(--mint));color:white;padding:50px;text-align:center;}
        h1{font-size:40px;margin:0;}
        .back{display:inline-block;margin:20px;padding:12px 30px;background:rgba(255,255,255,0.3);color:white;border-radius:50px;text-decoration:none;font-weight:bold;}
        .back:hover{background:rgba(255,255,255,0.5);}
        .info{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:25px;padding:50px;background:#f0f8f5;}
        .box{background:white;padding:30px;border-radius:20px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.1);border-top:5px solid var(--mint);}
        .box h3{color:var(--mint);font-size:22px;}
        .box p{font-size:28px;font-weight:900;color:var(--vert);}
        .conseils{display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:40px;padding:60px 40px;background:white;}
        .card{background:white;padding:50px;border-radius:30px;text-align:center;box-shadow:0 20px 50px rgba(1,50,32,0.15);border-top:8px solid var(--mint);transition:0.4s;}
        .card:hover{transform:translateY(-15px);}
        .card i{font-size:70px;color:var(--mint);margin-bottom:20px;}
        .card h2{font-size:30px;color:var(--vert);margin:15px 0;}
        .card p{font-size:19px;line-height:1.8;color:#444;}
        .note{background:#fff3cd;color:#856404;padding:20px;margin:20px 40px;border-radius:15px;text-align:center;font-weight:600;border-left:4px solid #ffc107;}
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Vue Admin – Conseils générés (ID #<?= $id ?>)</h1>
        <a href="index.php" class="back">Retour au backoffice</a>
    </header>

    <div class="note">
        Voici les conseils.
    </div>

    <div class="info">
        <div class="box"><h3>Email</h3><p><?= htmlspecialchars($reponse->email ?? '—') ?></p></div>
        <div class="box"><h3>Personnes</h3><p><?= $reponse->nb_personnes ?></p></div>
        <div class="box"><h3>Douches/sem</h3><p><?= $reponse->douche_freq ?></p></div>
        <div class="box"><h3>Chauffage</h3><p><?= ucfirst($reponse->chauffage ?? '—') ?></p></div>
        <div class="box"><h3>Transport</h3><p><?= htmlspecialchars($reponse->transport_travail ?? '—') ?></p></div>
        <div class="box"><h3>Date</h3><p><?= date('d/m/Y H:i', strtotime($reponse->date_soumission)) ?></p></div>
    </div>

    <div class="conseils">
        <div class="card">
            <i class="fas fa-tint"></i>
            <h2>Eau</h2>
            <p><?= nl2br(htmlspecialchars($conseils['eau'] ?? 'Aucun conseil eau attribué')) ?></p>
        </div>
        <div class="card">
            <i class="fas fa-bolt"></i>
            <h2>Énergie</h2>
            <p><?= nl2br(htmlspecialchars($conseils['energie'] ?? 'Aucun conseil énergie attribué')) ?></p>
        </div>
        <div class="card">
            <i class="fas fa-bicycle"></i>
            <h2>Transport</h2>
            <p><?= nl2br(htmlspecialchars($conseils['transport'] ?? 'Aucun conseil transport attribué')) ?></p>
        </div>
    </div>
</div>
</body>
</html>
