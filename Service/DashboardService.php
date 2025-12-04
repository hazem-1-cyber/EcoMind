<?php
// Service/DashboardService.php
class DashboardService {
    private $evenementModel;
    private $inscriptionModel;
    private $propositionModel;

    public function __construct(Evenement $evenementModel, Inscription $inscriptionModel, Proposition $propositionModel) {
        $this->evenementModel = $evenementModel;
        $this->inscriptionModel = $inscriptionModel;
        $this->propositionModel = $propositionModel;
    }

    /**
     * Get dashboard statistics
     * @return array
     */
    public function getStats() {
        return [
            'totalEvents' => $this->evenementModel->count(),
            'totalInscriptions' => $this->inscriptionModel->count(),
            'inscriptionsToday' => $this->inscriptionModel->countToday(),
            'totalPropositions' => $this->propositionModel->count()
        ];
    }

    /**
     * Get all data for dashboard
     * @return array
     */
    public function getDashboardData() {
        return [
            'events' => $this->evenementModel->getAll(),
            'inscriptions' => $this->inscriptionModel->getAll(),
            'propositions' => $this->propositionModel->getAll(),
            'stats' => $this->getStats()
        ];
    }

    /**
     * Get notifications count
     * @return array
     */
    public function getNotifications() {
        $inscriptionsToday = $this->inscriptionModel->countToday();
        $totalPropositions = $this->propositionModel->count();
        
        return [
            'inscriptionsToday' => $inscriptionsToday,
            'totalPropositions' => $totalPropositions,
            'totalNotifications' => $inscriptionsToday + ($totalPropositions > 0 ? 1 : 0)
        ];
    }
}
