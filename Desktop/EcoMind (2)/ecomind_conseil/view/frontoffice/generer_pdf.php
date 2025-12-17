<?php
session_start();

$base = dirname(__DIR__, 2);
require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/reponseConseil_controller.php';

// Vérifier si TCPDF est installé, sinon utiliser une alternative simple
$useTCPDF = file_exists($base . '/vendor/tecnickcom/tcpdf/tcpdf.php');

$controller = new FormulaireController();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Erreur : aucun résultat à afficher.");
}

$reponse = $controller->getReponseById($id);
if (!$reponse) {
    die("Réponse introuvable.");
}

$conseils = $controller->getConseilsAttribues($id);

// Préparer les textes des conseils avec valeurs par défaut
$texteEau = isset($conseils['eau']) && $conseils['eau'] ? $conseils['eau']->getTexteConseil() : "Prenez des douches plus courtes !";
$texteEnergie = isset($conseils['energie']) && $conseils['energie'] ? $conseils['energie']->getTexteConseil() : "Baissez le chauffage d'1°C";
$texteTransport = isset($conseils['transport']) && $conseils['transport'] ? $conseils['transport']->getTexteConseil() : "Prenez le vélo ou les transports en commun";

if ($useTCPDF) {
    // Utiliser TCPDF si disponible
    require_once($base . '/vendor/tecnickcom/tcpdf/tcpdf.php');
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->SetCreator('EcoMind');
    $pdf->SetAuthor('EcoMind');
    $pdf->SetTitle('Résultats Empreinte Écologique');
    $pdf->SetSubject('Conseils personnalisés');
    
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    $pdf->AddPage();
    
    $pdf->SetFont('helvetica', 'B', 24);
    $pdf->Cell(0, 15, 'EcoMind - Vos Résultats', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Conseils personnalisés pour réduire votre empreinte carbone', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Informations du formulaire
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Vos réponses :', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    
    $pdf->Cell(60, 8, 'Nombre de personnes :', 0, 0);
    $pdf->Cell(0, 8, $reponse->getNbPersonne(), 0, 1);
    
    $pdf->Cell(60, 8, 'Fréquence douche/semaine :', 0, 0);
    $pdf->Cell(0, 8, $reponse->getDoucheFreq(), 0, 1);
    
    $pdf->Cell(60, 8, 'Durée douche (min) :', 0, 0);
    $pdf->Cell(0, 8, $reponse->getDureeDouche(), 0, 1);
    
    $pdf->Cell(60, 8, 'Type de chauffage :', 0, 0);
    $pdf->Cell(0, 8, ucfirst($reponse->getChauffageType() ?? 'N/A'), 0, 1);
    
    $pdf->Cell(60, 8, 'Température hiver (°C) :', 0, 0);
    $pdf->Cell(0, 8, $reponse->getTempHiver(), 0, 1);
    
    $pdf->Cell(60, 8, 'Moyen de transport :', 0, 0);
    $pdf->Cell(0, 8, ucfirst($reponse->getTypeTransport() ?? 'N/A'), 0, 1);
    
    $pdf->Cell(60, 8, 'Distance travail (km) :', 0, 0);
    $pdf->Cell(0, 8, $reponse->getDistTravail(), 0, 1);
    
    $pdf->Ln(10);
    
    // Les 3 conseils
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Vos 3 conseils personnalisés :', 0, 1);
    $pdf->Ln(5);
    
    // Conseil EAU
    $pdf->SetFillColor(230, 247, 255);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, '💧 EAU', 0, 1, 'L', true);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 6, $texteEau, 0, 'L');
    $pdf->Ln(5);
    
    // Conseil ÉNERGIE
    $pdf->SetFillColor(255, 245, 230);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, '🔥 ÉNERGIE', 0, 1, 'L', true);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 6, $texteEnergie, 0, 'L');
    $pdf->Ln(5);
    
    // Conseil TRANSPORT
    $pdf->SetFillColor(230, 255, 230);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, '🚗 TRANSPORT', 0, 1, 'L', true);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 6, $texteTransport, 0, 'L');
    
    $pdf->Ln(15);
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->Cell(0, 10, 'Chaque geste compte pour la planète 🌍', 0, 1, 'C');
    
    $pdf->Output('resultats_ecomind.pdf', 'D');
    
} else {
    // Alternative simple avec FPDF ou génération HTML vers PDF
    // Pour une solution sans dépendances, on génère un HTML optimisé pour l'impression
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Résultats EcoMind - PDF</title>
        <style>
            @media print {
                body { margin: 0; padding: 20px; }
                .no-print { display: none; }
            }
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                line-height: 1.6;
            }
            h1 {
                color: #013220;
                text-align: center;
                border-bottom: 3px solid #013220;
                padding-bottom: 10px;
            }
            h2 {
                color: #013220;
                margin-top: 30px;
            }
            .info-section {
                background: #f5f5f5;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
            }
            .info-row {
                display: flex;
                margin: 8px 0;
            }
            .info-label {
                font-weight: bold;
                width: 200px;
            }
            .conseil-card {
                border: 2px solid #013220;
                border-radius: 10px;
                padding: 15px;
                margin: 15px 0;
                background: #f9f9f9;
            }
            .conseil-card h3 {
                color: #013220;
                margin-top: 0;
            }
            .footer {
                text-align: center;
                margin-top: 40px;
                font-style: italic;
                color: #666;
            }
            .print-btn {
                background: #013220;
                color: white;
                padding: 12px 30px;
                border: none;
                border-radius: 25px;
                font-size: 16px;
                cursor: pointer;
                display: block;
                margin: 20px auto;
            }
            .print-btn:hover {
                background: #025230;
            }
        </style>
    </head>
    <body>
        <button class="print-btn no-print" onclick="window.print()">🖨️ Imprimer en PDF</button>
        
        <h1>🌱 EcoMind - Vos Résultats</h1>
        <p style="text-align: center; color: #666;">Conseils personnalisés pour réduire votre empreinte carbone</p>
        
        <div class="info-section">
            <h2>Vos réponses</h2>
            <div class="info-row">
                <span class="info-label">Nombre de personnes :</span>
                <span><?= htmlspecialchars($reponse->getNbPersonne()) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Fréquence douche/semaine :</span>
                <span><?= htmlspecialchars($reponse->getDoucheFreq()) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Durée douche (min) :</span>
                <span><?= htmlspecialchars($reponse->getDureeDouche()) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Type de chauffage :</span>
                <span><?= htmlspecialchars(ucfirst($reponse->getChauffageType() ?? 'N/A')) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Température hiver (°C) :</span>
                <span><?= htmlspecialchars($reponse->getTempHiver()) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Moyen de transport :</span>
                <span><?= htmlspecialchars(ucfirst($reponse->getTypeTransport() ?? 'N/A')) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Distance travail (km) :</span>
                <span><?= htmlspecialchars($reponse->getDistTravail()) ?></span>
            </div>
        </div>
        
        <h2>Vos 3 conseils personnalisés</h2>
        
        <div class="conseil-card">
            <h3>💧 EAU</h3>
            <p><?= htmlspecialchars($texteEau) ?></p>
        </div>
        
        <div class="conseil-card">
            <h3>🔥 ÉNERGIE</h3>
            <p><?= htmlspecialchars($texteEnergie) ?></p>
        </div>
        
        <div class="conseil-card">
            <h3>🚗 TRANSPORT</h3>
            <p><?= htmlspecialchars($texteTransport) ?></p>
        </div>
        
        <div class="footer">
            <p>Chaque geste compte pour la planète 🌍</p>
            <p>Date : <?= date('d/m/Y') ?></p>
        </div>
        
        <button class="print-btn no-print" onclick="window.print()">🖨️ Imprimer en PDF</button>
    </body>
    </html>
    <?php
}
?>