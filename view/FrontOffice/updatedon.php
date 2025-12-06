<?php
require_once __DIR__ . "/../../Model/Don.php";
require_once __DIR__ . "/../../Controller/DonController.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: don.html');
    exit;
}

$don = new Don(
    null,
    $_POST['type_don'],
    $_POST['type_don'] === 'money' ? (float)$_POST['montant'] : null,
    $_POST['livraison'] ?? null,
    $_POST['ville'] ?? $_POST['ville_autre'] ?? null,
    $_POST['cp'] ?? $_POST['cp_autre'] ?? null,
    $_POST['adresse'] ?? null,
    $_POST['localisation'] ?? $_POST['localisation_autre'] ?? null,
    $_POST['tel'] ?? $_POST['tel_autre'] ?? null,
    $_POST['description_don'] ?? null,
    $_POST['association'],
    $_POST['email']
);

echo "<pre style='background:#f4f4f4;padding:15px;margin:20px;border-radius:8px;'>";
echo "<h2>var_dump() :</h2>";
var_dump($don);
echo "</pre>";

$ctrl = new DonController();
$ctrl->showDon($don);
$ctrl->addDon($don);

header('Location: merci.php');
exit;
?>