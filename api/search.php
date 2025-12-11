<?php
// api/search.php - Clean API for search
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Disable error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(0);

try {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../Controller/SearchController.php';
    
    $searchController = new SearchController();
    $action = $_GET['action'] ?? '';
    
    if ($action === 'events') {
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'type' => $_GET['type'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_desc',
            'limit' => $_GET['limit'] ?? 20,
            'offset' => $_GET['offset'] ?? 0
        ];
        
        $results = $searchController->searchEvents($filters);
        $total = $searchController->countSearchResults('events', $filters);
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'total' => $total,
            'filters' => $filters,
            'debug' => [
                'keyword_received' => $filters['keyword'],
                'keyword_length' => strlen($filters['keyword']),
                'results_count' => count($results)
            ]
        ]);
        
    } elseif ($action === 'inscriptions') {
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'evenement_id' => $_GET['evenement_id'] ?? '',
            'age_min' => $_GET['age_min'] ?? '',
            'age_max' => $_GET['age_max'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_desc',
            'limit' => $_GET['limit'] ?? 20,
            'offset' => $_GET['offset'] ?? 0
        ];
        
        $results = $searchController->searchInscriptions($filters);
        $total = $searchController->countSearchResults('inscriptions', $filters);
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'total' => $total,
            'filters' => $filters
        ]);
        
    } elseif ($action === 'propositions') {
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'type' => $_GET['type'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_desc'
        ];
        
        $results = $searchController->searchPropositions($filters);
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'filters' => $filters
        ]);
        
    } elseif ($action === 'autocomplete') {
        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? 'events';
        
        if (strlen($query) < 2) {
            echo json_encode(['success' => true, 'suggestions' => []]);
            exit;
        }
        
        if ($type === 'events') {
            $suggestions = $searchController->getEventSuggestions($query);
        } else {
            $suggestions = $searchController->getInscriptionSuggestions($query);
        }
        
        echo json_encode([
            'success' => true,
            'suggestions' => $suggestions
        ]);
        
    } elseif ($action === 'filters') {
        $filterType = $_GET['type'] ?? '';
        
        if ($filterType === 'event_types') {
            $types = $searchController->getEventTypes();
            echo json_encode(['success' => true, 'types' => $types]);
        } elseif ($filterType === 'events_list') {
            $events = $searchController->getEventsForFilter();
            echo json_encode(['success' => true, 'events' => $events]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Type de filtre non reconnu']);
        }
        
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Action non reconnue: ' . $action
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>