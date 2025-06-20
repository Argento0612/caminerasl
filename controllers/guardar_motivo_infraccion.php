<?php
header('Content-Type: application/json');

// Incluir la conexión a la base de datos
require_once('../config/db.php');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener y validar los datos
$nombre_motivo = trim($_POST['nombre_motivo'] ?? '');

if (empty($nombre_motivo)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'El nombre del motivo es requerido'
    ]);
    exit;
}

try {
    // Verificar si el motivo ya existe
    $stmt = $conn->prepare("SELECT id FROM motivos_infraccion WHERE nombre = ?");
    $stmt->bind_param("s", $nombre_motivo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Motivo encontrado',
            'id' => $row['id'],
            'text' => $nombre_motivo
        ]);
        exit;
    }
    
    // Si no existe, insertar el nuevo motivo
    $stmt = $conn->prepare("INSERT INTO motivos_infraccion (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre_motivo);
    
    if ($stmt->execute()) {
        $id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Motivo guardado correctamente',
            'id' => $id,
            'text' => $nombre_motivo
        ]);
    } else {
        throw new Exception("Error al guardar el motivo");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el motivo: ' . $e->getMessage()
    ]);
} 