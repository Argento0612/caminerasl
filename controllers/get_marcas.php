<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Método no permitido');
    }

    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $conn = getDB();
    
    if (empty($query)) {
        $sql = "SELECT id, nombre as text FROM marcas_vehiculo ORDER BY nombre ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT id, nombre as text FROM marcas_vehiculo WHERE LOWER(nombre) LIKE LOWER(?) ORDER BY nombre ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("s", $searchTerm);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'text' => $row['text']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'results' => $items
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en get_marcas.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar marcas: ' . $e->getMessage()
    ]);
} 