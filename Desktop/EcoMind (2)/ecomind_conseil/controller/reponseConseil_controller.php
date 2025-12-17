<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/conseilReponse_model.php';


class FormulaireController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Config::getConnexion(); 
    }

    
    public function listReponses()
    {
        $sql = "SELECT * FROM reponse_formulaire ORDER BY date_soumission DESC";
        try {
            $stmt = $this->db->query($sql);
            $reponses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reponses[] = new ReponseFormulaire(
                    (int)$row['idformulaire'],
                    $row['email'] ?? null,
                    (int)($row['nb_personnes'] ?? 0),
                    (int)($row['douche_freq'] ?? 0),
                    (int)($row['douche_duree'] ?? 0),
                    $row['chauffage'] ?? null,
                    (int)($row['temp_hiver'] ?? 0),
                    $row['transport_travail'] ?? null,
                    (int)($row['distance_travail'] ?? 0),
                    $row['date_soumission'] ?? null
                );
            }
            return $reponses;
        } catch (Exception $e) {
            die('Erreur lors du chargement des réponses : ' . $e->getMessage());
        }
    }

    // Ajoute une nouvelle réponse (après soumission du formulaire)
    // Dans reponseConseil_controller.php → méthode addReponse()

public function addReponse(ReponseFormulaire $reponse)
{
    $sql = "INSERT INTO reponse_formulaire 
            (email, nb_personnes, douche_freq, douche_duree, chauffage, temp_hiver, transport_travail, distance_travail, date_soumission)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $reponse->getEmail(),
            $reponse->getNbPersonne(),
            $reponse->getDoucheFreq(),
            $reponse->getDureeDouche(),
            $reponse->getChauffageType(),
            $reponse->getTempHiver(),
            $reponse->getTypeTransport(),
            $reponse->getDistTravail()
        ]);

        return (int)$this->db->lastInsertId(); // Retourne l'ID généré
    } catch (Exception $e) {
        // Pour debug : décommente la ligne suivante si tu veux voir l'erreur
        // die("Erreur SQL : " . $e->getMessage());
        error_log("Erreur insertion réponse : " . $e->getMessage());
        return 0;
    }
}
    // Tire un conseil aléatoire pour une catégorie donnée
    private function tirerConseilAleatoire($type)
    {
        $sql = "SELECT idconseil FROM conseil WHERE type = ? ORDER BY RAND() LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$type]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['idconseil'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    // Supprime une réponse (admin)
    public function deleteReponse(int $id): void
    {
        $sql = "DELETE FROM reponse_formulaire WHERE idformulaire = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            die('Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    // Récupère une réponse par ID (pour afficher les conseils)
    public function getReponseById($id)
    {
        // S'assurer que l'ID est un entier
        $id = (int)$id;
        
        $sql = "SELECT * FROM reponse_formulaire WHERE idformulaire = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) return null;

            return new ReponseFormulaire(
                (int)$row['idformulaire'],
                $row['email'],
                (int)$row['nb_personnes'],
                (int)$row['douche_freq'],
                (int)$row['douche_duree'],
                $row['chauffage'],
                (int)$row['temp_hiver'],
                $row['transport_travail'],
                (int)$row['distance_travail'],
                $row['date_soumission']
            );
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Tire 3 conseils aléatoires (1 par catégorie) et les sauvegarde pour cette réponse
    public function getConseilsAleatoires()
    {
        $categories = ['eau', 'energie', 'transport'];
        $conseils = [];

        foreach ($categories as $cat) {
            $sql = "SELECT * FROM conseil WHERE type = :cat ORDER BY RAND() LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cat' => $cat]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $conseils[$cat] = new Conseil(
                    (int)$row['idconseil'],
                    $cat,
                    $row['texte']
                );
            } else {
                // Conseil par défaut
                $conseils[$cat] = new Conseil(
                    null,
                    $cat,
                    "Conseil $cat : soyez éco-responsable !"
                );
            }
        }
        return $conseils;
    }

    // Attribue et sauvegarde 3 conseils aléatoires pour une réponse (stockage en session)
    public function attribuerConseils($reponseId)
    {
        $categories = ['eau', 'energie', 'transport'];
        $conseils = [];
        $conseilsIds = [];

        foreach ($categories as $cat) {
            $sql = "SELECT * FROM conseil WHERE type = :cat ORDER BY RAND() LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cat' => $cat]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $conseilId = (int)$row['idconseil'];
                $conseilsIds[$cat] = $conseilId;

                $conseils[$cat] = new Conseil(
                    $conseilId,
                    $cat,
                    $row['texte']
                );
            } else {
                $conseils[$cat] = new Conseil(
                    null,
                    $cat,
                    "Conseil $cat : soyez éco-responsable !"
                );
            }
        }

        // Stocker les IDs des conseils en session
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['conseils_' . $reponseId] = $conseilsIds;

        return $conseils;
    }

    // Génère des conseils personnalisés avec l'IA
    public function genererConseilsIA($reponseId)
    {
        // S'assurer que l'ID est un entier
        $reponseId = (int)$reponseId;
        
        // Récupérer les données de la réponse
        $reponse = $this->getReponseById($reponseId);
        if (!$reponse) {
            return $this->attribuerConseils($reponseId);
        }

        // Charger le générateur IA
        require_once __DIR__ . '/ia_conseil_generator.php';
        $iaGenerator = new IAConseilGenerator();

        // Générer les conseils avec l'IA
        $conseilsTextes = $iaGenerator->genererConseils($reponse);

        // Créer les objets Conseil
        $conseils = [];
        foreach ($conseilsTextes as $type => $texte) {
            $conseils[$type] = new Conseil(null, $type, $texte);
        }

        // Stocker les textes en session
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['conseils_ia_' . $reponseId] = $conseilsTextes;

        return $conseils;
    }

    // Récupère les conseils IA depuis la session
    public function getConseilsIA($reponseId)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['conseils_ia_' . $reponseId])) {
            $conseilsTextes = $_SESSION['conseils_ia_' . $reponseId];
            $conseils = [];

            foreach ($conseilsTextes as $type => $texte) {
                $conseils[$type] = new Conseil(null, $type, $texte);
            }

            return $conseils;
        }

        return null;
    }

    // Récupère les conseils déjà attribués à une réponse (depuis la session)
    public function getConseilsAttribues($reponseId)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Vérifier si les conseils sont en session
        if (isset($_SESSION['conseils_' . $reponseId])) {
            $conseilsIds = $_SESSION['conseils_' . $reponseId];
            $conseils = [];

            foreach ($conseilsIds as $type => $conseilId) {
                $sql = "SELECT * FROM conseil WHERE idconseil = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$conseilId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $conseils[$type] = new Conseil(
                        (int)$row['idconseil'],
                        $type,
                        $row['texte']
                    );
                }
            }

            if (!empty($conseils)) {
                return $conseils;
            }
        }

        // Si pas en session, générer de nouveaux conseils
        return $this->attribuerConseils($reponseId);
    }
}
?>