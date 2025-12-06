<?php
// Test de l'extraction des abréviations des noms d'associations

function getAssociationAbbreviation($nom) {
    // Si le nom commence par une abréviation connue, l'extraire
    if (preg_match('/^([A-Z]{2,})\s*[-–—]\s*/', $nom, $matches)) {
        return $matches[1];
    }
    
    // Sinon, prendre les premières lettres des mots en majuscules
    $words = explode(' ', $nom);
    $abbr = '';
    foreach ($words as $word) {
        if (preg_match('/^[A-Z]/', $word)) {
            $abbr .= $word[0];
        }
    }
    
    // Si on a une abréviation, la retourner, sinon retourner les 4 premiers caractères
    return $abbr ?: substr($nom, 0, 4);
}

echo "<h2>Test d'extraction des abréviations</h2>";

$associations = [
    "AEPD - Association pour l'Environnement et le Patrimoine Durable",
    "ATDD - Association Tunisienne de Développement Durable",
    "Green Tunisia",
    "Tunisie Recyclage",
    "EcoAction Tunisie",
    "Association Tunisienne pour la Protection de la Nature"
];

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>Nom complet</th>";
echo "<th>Abréviation extraite</th>";
echo "<th>Longueur</th>";
echo "<th>Affichage</th>";
echo "</tr>";

foreach ($associations as $nom) {
    $abbr = getAssociationAbbreviation($nom);
    $longueur = strlen($nom);
    $affichage = $longueur > 30 ? $abbr : $nom;
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($nom) . "</td>";
    echo "<td><strong>" . htmlspecialchars($abbr) . "</strong></td>";
    echo "<td>" . $longueur . " caractères</td>";
    echo "<td>" . htmlspecialchars($affichage) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Règles d'extraction :</h3>";
echo "<ol>";
echo "<li>Si le nom commence par une abréviation (ex: AEPD - ...), extraire l'abréviation</li>";
echo "<li>Sinon, prendre les premières lettres des mots en majuscules</li>";
echo "<li>Si aucune abréviation trouvée, prendre les 4 premiers caractères</li>";
echo "</ol>";

echo "<h3>Règles d'affichage :</h3>";
echo "<ul>";
echo "<li>Si le nom fait plus de 30 caractères : afficher l'abréviation</li>";
echo "<li>Sinon : afficher le nom complet</li>";
echo "<li>Le nom complet est toujours disponible au survol (title)</li>";
echo "</ul>";
?>
