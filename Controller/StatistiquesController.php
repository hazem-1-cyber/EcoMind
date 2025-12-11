<?php
// Controller/StatistiquesController.php
require_once __DIR__ . '/../Model/Database.php';

/**
 * StatistiquesController - Métier Avancé: Statistiques et Analyses
 */
class StatistiquesController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Afficher la page des statistiques
     */
    public function index() {
        // Récupérer toutes les statistiques
        $stats = [
            'tauxParticipation' => $this->getTauxParticipation(),
            'croissanceMensuelle' => $this->getCroissanceMensuelle(),
            'ageMoyen' => $this->getAgeMoyenParticipants(),
            'tauxConversion' => $this->getTauxConversion()
        ];
        
        $chartsData = [
            'evenementsParType' => $this->getEvenementsParType(),
            'topEvenements' => $this->getTop3Evenements()
        ];
        
        require __DIR__ . '/../View/BackOffice/statistiques.php';
    }

    // ========== KPI CALCULATIONS ==========

    /**
     * 1. Taux de Participation - (Total Inscriptions / Total Events) * 100
     */
    private function getTauxParticipation() {
        $sql = "SELECT 
                    COUNT(DISTINCT i.id) as total_inscriptions,
                    COUNT(DISTINCT e.id) as total_events
                FROM evenement e
                LEFT JOIN inscription i ON e.id = i.evenement_id";
        
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        
        if ($result['total_events'] > 0) {
            return round(($result['total_inscriptions'] / $result['total_events']) * 100, 1);
        }
        return 0;
    }

    /**
     * 2. Croissance Mensuelle - % d'augmentation des inscriptions ce mois
     */
    private function getCroissanceMensuelle() {
        // Inscriptions ce mois
        $sqlThisMonth = "SELECT COUNT(*) as count 
                        FROM inscription 
                        WHERE MONTH(date_inscription) = MONTH(CURDATE()) 
                        AND YEAR(date_inscription) = YEAR(CURDATE())";
        
        // Inscriptions le mois dernier
        $sqlLastMonth = "SELECT COUNT(*) as count 
                        FROM inscription 
                        WHERE MONTH(date_inscription) = MONTH(CURDATE() - INTERVAL 1 MONTH) 
                        AND YEAR(date_inscription) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
        
        $thisMonth = $this->pdo->query($sqlThisMonth)->fetch()['count'];
        $lastMonth = $this->pdo->query($sqlLastMonth)->fetch()['count'];
        
        if ($lastMonth > 0) {
            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
        }
        return $thisMonth > 0 ? 100 : 0; // 100% si premier mois avec données
    }

    /**
     * 3. Âge Moyen des Participants
     */
    private function getAgeMoyenParticipants() {
        $sql = "SELECT AVG(age) as age_moyen FROM inscription WHERE age IS NOT NULL AND age > 0";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        
        return $result['age_moyen'] ? round($result['age_moyen'], 1) : 0;
    }

    /**
     * 4. Taux de Conversion - (Événements créés / Total Propositions) * 100
     * Note: Assume qu'un événement créé vient d'une proposition acceptée
     */
    private function getTauxConversion() {
        $sqlEvents = "SELECT COUNT(*) as total_events FROM evenement";
        $sqlPropositions = "SELECT COUNT(*) as total_propositions FROM proposition";
        
        $totalEvents = $this->pdo->query($sqlEvents)->fetch()['total_events'];
        $totalPropositions = $this->pdo->query($sqlPropositions)->fetch()['total_propositions'];
        
        if ($totalPropositions > 0) {
            return round(($totalEvents / $totalPropositions) * 100, 1);
        }
        return 0;
    }

    // ========== CHART DATA ==========

    /**
     * 5. Événements par Type - Pour Pie Chart
     */
    private function getEvenementsParType() {
        $sql = "SELECT 
                    type,
                    COUNT(*) as nombre,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM evenement)), 1) as pourcentage
                FROM evenement 
                GROUP BY type 
                ORDER BY nombre DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * 6. Top 3 Événements - Pour Bar Chart
     */
    private function getTop3Evenements() {
        $sql = "SELECT 
                    e.titre,
                    e.type,
                    COUNT(i.id) as nb_inscriptions
                FROM evenement e
                LEFT JOIN inscription i ON e.id = i.evenement_id
                GROUP BY e.id, e.titre, e.type
                ORDER BY nb_inscriptions DESC
                LIMIT 3";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // ========== ADDITIONAL ANALYTICS ==========

    /**
     * Inscriptions par mois (pour graphique temporel)
     */
    public function getInscriptionsParMois() {
        $sql = "SELECT 
                    MONTH(date_inscription) as mois,
                    MONTHNAME(date_inscription) as nom_mois,
                    COUNT(*) as total
                FROM inscription
                WHERE YEAR(date_inscription) = YEAR(CURDATE())
                GROUP BY MONTH(date_inscription), MONTHNAME(date_inscription)
                ORDER BY mois";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Statistiques détaillées pour tableau
     */
    public function getStatistiquesDetaillees() {
        return [
            'totalEvents' => $this->countTotal('evenement'),
            'totalInscriptions' => $this->countTotal('inscription'),
            'totalPropositions' => $this->countTotal('proposition'),
            'inscriptionsToday' => $this->countInscriptionsToday(),
            'evenementPopulaire' => $this->getEvenementPlusPopulaire()
        ];
    }

    /**
     * Compter total dans une table
     */
    private function countTotal($table) {
        $sql = "SELECT COUNT(*) as total FROM $table";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch()['total'];
    }

    /**
     * Compter inscriptions aujourd'hui
     */
    private function countInscriptionsToday() {
        $sql = "SELECT COUNT(*) as total FROM inscription WHERE DATE(date_inscription) = CURDATE()";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch()['total'];
    }

    /**
     * Événement le plus populaire
     */
    private function getEvenementPlusPopulaire() {
        $sql = "SELECT 
                    e.titre,
                    COUNT(i.id) as nb_inscriptions
                FROM evenement e
                LEFT JOIN inscription i ON e.id = i.evenement_id
                GROUP BY e.id, e.titre
                ORDER BY nb_inscriptions DESC
                LIMIT 1";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch();
    }
}