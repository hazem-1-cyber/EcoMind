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

    
    public function listReponses(): array
    {
        $sql = "SELECT * FROM reponse_formulaire ORDER BY date_soumission DESC";
        try {
            $stmt = $this->db->query($sql);
            $reponses = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reponses[] = new ReponseFormulaire(
                    (int)$row['id'],
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
    public function addReponse(ReponseFormulaire $reponse): int
    {
        $sql = "INSERT INTO reponse_formulaire 
                (email, nb_personnes, douche_freq, douche_duree, chauffage, temp_hiver, transport_travail, distance_travail)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

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
            return (int)$this->db->lastInsertId();
        } catch (Exception $e) {
            die('Erreur lors de l\'ajout : ' . $e->getMessage());
        }
    }

    // Tire un conseil aléatoire pour une catégorie donnée
    private function tirerConseilAleatoire(string $type): ?int
    {
        $sql = "SELECT id FROM conseil WHERE type = ? ORDER BY RAND() LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$type]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['id'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    // Supprime une réponse (admin)
    public function deleteReponse(int $id): void
    {
        $sql = "DELETE FROM reponse_formulaire WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            die('Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    // Récupère une réponse par ID (pour afficher les conseils)
    public function getReponseById(int $id): ?ReponseFormulaire
    {
        $sql = "SELECT * FROM reponse_formulaire WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) return null;

            return new ReponseFormulaire(
                (int)$row['id'],
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
    public function getConseilsAleatoires(): array
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
                    (int)$row['id'],
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
    public function attribuerConseils(int $reponseId): array
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
                $conseilId = (int)$row['id'];
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

    // Récupère les conseils déjà attribués à une réponse (depuis la session)
    public function getConseilsAttribues(int $reponseId): array
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Vérifier si les conseils sont en session
        if (isset($_SESSION['conseils_' . $reponseId])) {
            $conseilsIds = $_SESSION['conseils_' . $reponseId];
            $conseils = [];

            foreach ($conseilsIds as $type => $conseilId) {
                $sql = "SELECT * FROM conseil WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$conseilId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $conseils[$type] = new Conseil(
                        (int)$row['id'],
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