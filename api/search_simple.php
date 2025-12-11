<?php
// api/search_simple.php - Ultra simple API
header('Content-Type: application/json');

// Disable all error output
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Simple database connection
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=ecomind_events", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    $action = $_GET['action'] ?? '';
    $keyword = $_GET['keyword'] ?? '';
    $sort = $_GET['sort'] ?? 'date_desc';
    
    if ($action === 'events') {
        // Build base query
        $sql = "SELECT *, (SELECT COUNT(*) FROM inscription WHERE evenement_id = evenement.id) as nb_inscriptions 
                FROM evenement";
        $params = [];
        
        // Add WHERE clause if keyword exists
        if (!empty($keyword) && trim($keyword) !== '') {
            $searchTerm = '%' . trim($keyword) . '%';
            $sql .= " WHERE LOWER(titre) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?) OR LOWER(type) LIKE LOWER(?)";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }
        
        // Add ORDER BY clause based on sort parameter
        switch ($sort) {
            case 'title_asc':
                $sql .= " ORDER BY titre ASC";
                break;
            case 'title_desc':
                $sql .= " ORDER BY titre DESC";
                break;
            case 'type_asc':
                $sql .= " ORDER BY type ASC, titre ASC";
                break;
            case 'type_desc':
                $sql .= " ORDER BY type DESC, titre ASC";
                break;
            case 'popularity_desc':
                $sql .= " ORDER BY nb_inscriptions DESC, titre ASC";
                break;
            case 'popularity_asc':
                $sql .= " ORDER BY nb_inscriptions ASC, titre ASC";
                break;
            case 'date_asc':
                $sql .= " ORDER BY date_creation ASC";
                break;
            case 'date_desc':
            default:
                $sql .= " ORDER BY date_creation DESC";
                break;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $results = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'total' => count($results),
            'keyword' => $keyword,
            'sort' => $sort,
            'debug' => [
                'keyword_received' => $keyword,
                'keyword_trimmed' => trim($keyword),
                'keyword_empty' => empty($keyword),
                'sort_received' => $sort,
                'sql_used' => !empty($keyword) ? 'with_keyword' : 'all_events',
                'params_count' => count($params)
            ]
        ]);
        
    } elseif ($action === 'inscriptions') {
        // Build base query
        $sql = "SELECT i.*, e.titre as evenement_titre 
                FROM inscription i 
                JOIN evenement e ON i.evenement_id = e.id";
        $params = [];
        $conditions = [];
        
        // Add WHERE clause if keyword exists
        if (!empty($keyword) && trim($keyword) !== '') {
            $searchTerm = '%' . trim($keyword) . '%';
            $conditions[] = "(LOWER(i.nom) LIKE LOWER(?) OR LOWER(i.prenom) LIKE LOWER(?) OR LOWER(i.email) LIKE LOWER(?) OR LOWER(e.titre) LIKE LOWER(?))";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        // Add event filter if specified
        $evenement_id = $_GET['evenement_id'] ?? '';
        if (!empty($evenement_id) && $evenement_id !== '') {
            $conditions[] = "i.evenement_id = ?";
            $params[] = $evenement_id;
        }
        
        // Add age filters if specified
        $age_min = $_GET['age_min'] ?? '';
        $age_max = $_GET['age_max'] ?? '';
        if (!empty($age_min) && $age_min !== '') {
            $conditions[] = "CAST(COALESCE(i.age, 0) AS UNSIGNED) >= ?";
            $params[] = intval($age_min);
        }
        if (!empty($age_max) && $age_max !== '') {
            $conditions[] = "CAST(COALESCE(i.age, 0) AS UNSIGNED) <= ?";
            $params[] = intval($age_max);
        }
        
        // Add date filters if specified
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';
        if (!empty($date_from) && $date_from !== '') {
            $conditions[] = "DATE(i.date_inscription) >= ?";
            $params[] = $date_from;
        }
        if (!empty($date_to) && $date_to !== '') {
            $conditions[] = "DATE(i.date_inscription) <= ?";
            $params[] = $date_to;
        }
        
        // Add WHERE clause if conditions exist
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Add ORDER BY clause based on sort parameter
        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY i.nom ASC, i.prenom ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY i.nom DESC, i.prenom DESC";
                break;
            case 'age_asc':
                $sql .= " ORDER BY CAST(COALESCE(i.age, 0) AS UNSIGNED) ASC, i.nom ASC";
                break;
            case 'age_desc':
                $sql .= " ORDER BY CAST(COALESCE(i.age, 0) AS UNSIGNED) DESC, i.nom ASC";
                break;
            case 'event_asc':
                $sql .= " ORDER BY e.titre ASC, i.nom ASC";
                break;
            case 'event_desc':
                $sql .= " ORDER BY e.titre DESC, i.nom ASC";
                break;
            case 'date_asc':
                $sql .= " ORDER BY i.date_inscription ASC";
                break;
            case 'date_desc':
            default:
                $sql .= " ORDER BY i.date_inscription DESC";
                break;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $results = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'total' => count($results),
            'keyword' => $keyword,
            'sort' => $sort,
            'debug' => [
                'keyword_received' => $keyword,
                'sort_received' => $sort,
                'results_found' => count($results),
                'sql_final' => $sql,
                'params_used' => $params
            ]
        ]);
        
    } elseif ($action === 'propositions') {
        // Build base query
        $sql = "SELECT * FROM proposition";
        $params = [];
        
        // Add WHERE clause if keyword exists
        if (!empty($keyword) && trim($keyword) !== '') {
            $searchTerm = '%' . trim($keyword) . '%';
            $sql .= " WHERE LOWER(association_nom) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?) OR LOWER(email_contact) LIKE LOWER(?) OR LOWER(type) LIKE LOWER(?)";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        // Add ORDER BY clause based on sort parameter
        switch ($sort) {
            case 'association_asc':
                $sql .= " ORDER BY association_nom ASC";
                break;
            case 'association_desc':
                $sql .= " ORDER BY association_nom DESC";
                break;
            case 'type_asc':
                $sql .= " ORDER BY type ASC, association_nom ASC";
                break;
            case 'type_desc':
                $sql .= " ORDER BY type DESC, association_nom ASC";
                break;
            case 'date_asc':
                $sql .= " ORDER BY date_proposition ASC";
                break;
            case 'date_desc':
            default:
                $sql .= " ORDER BY date_proposition DESC";
                break;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $results = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'total' => count($results),
            'keyword' => $keyword,
            'debug' => [
                'keyword_received' => $keyword,
                'results_found' => count($results)
            ]
        ]);
        
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error'
    ]);
}
?>