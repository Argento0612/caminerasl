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
$nombre_causa = trim($_POST['nombre_causa'] ?? '');

if (empty($nombre_causa)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'El nombre de la causa es requerido'
    ]);
    exit;
}

try {
    // Verificar si la causa ya existe
    $stmt = $conn->prepare("SELECT id FROM causas WHERE LOWER(nombre) = LOWER(?)");
    $stmt->bind_param("s", $nombre_causa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Causa encontrada',
            'id' => $row['id'],
            'text' => $nombre_causa
        ]);
        exit;
    }
    
    // Si no existe, insertar la nueva causa
    $stmt = $conn->prepare("INSERT INTO causas (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre_causa);
    
    if ($stmt->execute()) {
        $id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Causa guardada correctamente',
            'id' => $id,
            'text' => $nombre_causa
        ]);
    } else {
        throw new Exception("Error al guardar la causa");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la causa: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
} 