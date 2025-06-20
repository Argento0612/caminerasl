<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método no permitido');
    }

    $marca_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    
    $conn = getDB();
    
    if (empty($query)) {
        $sql = "SELECT id, nombre as text FROM modelos_vehiculo WHERE marca_id = ? ORDER BY nombre ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $marca_id);
    } else {
        $sql = "SELECT id, nombre as text FROM modelos_vehiculo WHERE marca_id = ? AND LOWER(nombre) LIKE LOWER(?) ORDER BY nombre ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("is", $marca_id, $searchTerm);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $modelos = [];
    while ($row = $result->fetch_assoc()) {
        $modelos[] = [
            'id' => $row['id'],
            'text' => $row['text']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'results' => $modelos
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en get_modelos.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar modelos: ' . $e->getMessage()
    ]);
} 