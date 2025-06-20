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
$nombre_lugar = trim($_POST['nombre_lugar'] ?? '');

if (empty($nombre_lugar)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'El nombre del lugar es requerido'
    ]);
    exit;
}

try {
    // Verificar si el lugar ya existe
    $stmt = $conn->prepare("SELECT id FROM lugares WHERE nombre = ?");
    $stmt->bind_param("s", $nombre_lugar);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Lugar encontrado',
            'id' => $row['id'],
            'text' => $nombre_lugar
        ]);
        exit;
    }
    
    // Si no existe, insertar el nuevo lugar
    $stmt = $conn->prepare("INSERT INTO lugares (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre_lugar);
    
    if ($stmt->execute()) {
        $id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Lugar guardado correctamente',
            'id' => $id,
            'text' => $nombre_lugar
        ]);
    } else {
        throw new Exception("Error al guardar el lugar");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el lugar: ' . $e->getMessage()
    ]);
} 