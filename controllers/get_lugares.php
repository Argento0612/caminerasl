<?php
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
require_once('../config/db.php');

// Obtener el término de búsqueda
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

try {
    // Preparar la consulta SQL
    if (empty($query)) {
        // Si no hay término de búsqueda, mostrar todos los lugares
        $sql = "SELECT id, nombre as text FROM lugares ORDER BY nombre ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
        error_log("SQL sin query: " . $sql);
    } else {
        // Si hay término de búsqueda, buscar coincidencias
        $sql = "SELECT id, nombre as text FROM lugares WHERE LOWER(nombre) LIKE LOWER(?) ORDER BY nombre ASC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bind_param("s", $searchTerm);
        error_log("SQL con query: " . $sql . " - Parámetro: " . $searchTerm);
    }
    
    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Obtener los resultados
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'text' => $row['text']
        ];
    }
    
    error_log("Resultados encontrados: " . count($items));
    
    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'results' => $items
    ]);

} catch (Exception $e) {
    // Devolver error
    error_log("Error en get_lugares.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar lugares: ' . $e->getMessage()
    ]);
} 