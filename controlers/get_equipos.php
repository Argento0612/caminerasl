<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = isset($_GET['query']) ? $_GET['query'] : '';
    
    $stmt = $conn->prepare("SELECT DISTINCT numero as text, id FROM equipos_alcoholemia WHERE numero LIKE :query ORDER BY numero ASC LIMIT 10");
    $stmt->execute(['query' => "%$query%"]);
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'items' => $equipos
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener equipos de alcoholemia: ' . $e->getMessage()
    ]);
} 