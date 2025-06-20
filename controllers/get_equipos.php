<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método no permitido');
    }

    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $conn = getDB();
    
    error_log("Buscando equipos con término: " . $query);
    
    if (empty($query)) {
        $sql = "SELECT id, numero as text FROM equipos_alcoholemia ORDER BY numero ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT id, numero as text FROM equipos_alcoholemia WHERE LOWER(numero) LIKE LOWER(?) ORDER BY numero ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("s", $searchTerm);
    }
    
    error_log("SQL: " . $sql);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'text' => $row['text']
        ];
    }
    
    error_log("Equipos encontrados: " . print_r($items, true));
    
    echo json_encode([
        'success' => true,
        'results' => $items
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en get_equipos.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar equipos: ' . $e->getMessage()
    ]);
} 