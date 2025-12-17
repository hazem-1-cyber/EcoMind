<?php
require_once 'config.php';
require_once 'model/conseilReponse_model.php';
require_once 'controller/ia_conseil_generator.php';

echo "<h1>Test de personnalisation des conseils IA</h1>";

// Créer différents profils d'utilisateurs pour tester la personnalisation
$profils = [
    [
        'nom' => 'Famille nombreuse, douches longues',
        'reponse' => new ReponseFormulaire(1, 'test1@example.com', 6, 14, 15, 'gaz', 22, 'voiture', 25)
    ],
    [
        'nom' => 'Célibataire éco-responsable',
        'reponse' => new ReponseFormulaire(2, 'test2@example.com', 1, 5, 5, 'electrique', 18, 'velo', 3)
    ],
    [
        'nom' => 'Couple urbain',
        'reponse' => new ReponseFormulaire(3, 'test3@example.com', 2, 10, 8, 'electrique', 20, 'transport_commun', 15)
    ],
    [
        'nom' => 'Famille rurale',
        'reponse' => new ReponseFormulaire(4, 'test4@example.com', 4, 12, 12, 'pompe_a_chaleur', 21, 'voiture', 45)
    ]
];

$generator = new IAConseilGenerator();

foreach ($profils as $profil) {
    echo "<div style='border: 2px solid #A8E6CF; margin: 20px 0; padding: 20px; border-radius: 10px;'>";
    echo "<h2 style='color: #013220;'>👤 " . $profil['nom'] . "</h2>";
    
    $reponse = $profil['reponse'];
    echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<strong>Profil :</strong><br>";
    echo "• Foyer : " . $reponse->getNbPersonne() . " personnes<br>";
    echo "• Douches : " . $reponse->getDoucheFreq() . " fois/semaine de " . $reponse->getDureeDouche() . " minutes<br>";
    echo "• Chauffage : " . $reponse->getChauffageType() . " à " . $reponse->getTempHiver() . "°C<br>";
    echo "• Transport : " . $reponse->getTypeTransport() . " pour " . $reponse->getDistTravail() . " km<br>";
    echo "</div>";
    
    // Générer les conseils
    $conseils = $generator->genererConseils($reponse);
    
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-top: 15px;'>";
    
    foreach ($conseils as $type => $conseil) {
        $couleurs = [
            'eau' => '#4FC3F7',
            'energie' => '#FFD54F', 
            'transport' => '#81C784'
        ];
        $icones = [
            'eau' => '💧',
            'energie' => '⚡',
            'transport' => '🚗'
        ];
        
        echo "<div style='background: white; border-left: 4px solid " . $couleurs[$type] . "; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>";
        echo "<h3 style='margin: 0 0 10px 0; color: #013220;'>" . $icones[$type] . " " . strtoupper($type) . "</h3>";
        echo "<p style='margin: 0; line-height: 1.5; color: #333;'>" . htmlspecialchars($conseil) . "</p>";
        echo "</div>";
    }
    
    echo "</div>";
    echo "</div>";
}

echo "<div style='background: #e8f5e9; padding: 20px; border-radius: 10px; margin: 20px 0; border: 2px solid #4caf50;'>";
echo "<h2 style='color: #2e7d32; margin: 0 0 10px 0;'>✅ Test terminé</h2>";
echo "<p style='margin: 0; color: #333;'>Si les conseils sont identiques pour tous les profils, il y a un problème de personnalisation.<br>";
echo "Si les conseils sont différents et adaptés à chaque profil, la personnalisation fonctionne correctement.</p>";
echo "</div>";

echo "<p style='text-align: center; margin: 30px 0;'>";
echo "<a href='view/frontoffice/formulaire.html' style='background: #A8E6CF; color: #013220; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold;'>🌱 Tester le formulaire</a>";
echo "</p>";
?>