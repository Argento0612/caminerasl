<?php
session_start();
header('Content-Type: application/json');

require_once('../config/db.php');

try {
    $conn = getDB();
    
    $stmt = $conn->prepare("SELECT id, numero as text, tipo, departamento FROM moviles_operativo WHERE activo = TRUE ORDER BY numero");
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }
    
    $result = $stmt->get_result();
    $moviles = [];
    
    while ($row = $result->fetch_assoc()) {
        $moviles[] = [
            'id' => $row['id'],
            'text' => $row['text'],
            'tipo' => $row['tipo'],
            'departamento' => $row['departamento']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'results' => $moviles
    ]);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener móviles: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 