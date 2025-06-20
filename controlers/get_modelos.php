<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = isset($_GET['query']) ? $_GET['query'] : '';
    $marca_id = isset($_GET['marca_id']) ? $_GET['marca_id'] : null;
    
    $sql = "SELECT DISTINCT m.nombre as text, m.id, m.marca_id 
            FROM modelos_vehiculo m 
            WHERE m.nombre LIKE :query";
    
    if ($marca_id) {
        $sql .= " AND m.marca_id = :marca_id";
    }
    
    $sql .= " ORDER BY m.nombre ASC LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $params = ['query' => "%$query%"];
    
    if ($marca_id) {
        $params['marca_id'] = $marca_id;
    }
    
    $stmt->execute($params);
    $modelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'items' => $modelos
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener modelos de vehículo: ' . $e->getMessage()
    ]);
} 