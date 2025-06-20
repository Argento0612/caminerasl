<?php
session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Incluir la conexión a la base de datos
require_once('../config/db.php');

// Obtener y validar los datos
$nombre_dependencia = isset($_POST['nombre_dependencia']) ? trim($_POST['nombre_dependencia']) : '';

if (empty($nombre_dependencia)) {
    echo json_encode(['success' => false, 'message' => 'El nombre de la dependencia es requerido']);
    exit();
}

try {
    // Verificar si ya existe la dependencia
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM dependencias WHERE LOWER(nombre) = LOWER(?)");
    $stmt->bind_param("s", $nombre_dependencia);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Esta dependencia ya existe']);
        exit();
    }

    // Insertar nueva dependencia
    $stmt = $conn->prepare("INSERT INTO dependencias (nombre) VALUES (?)");
    $stmt->bind_param("s", $nombre_dependencia);
    
    if ($stmt->execute()) {
        $id = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Dependencia guardada correctamente',
            'id' => $id,
            'nombre' => $nombre_dependencia
        ]);
    } else {
        throw new Exception("Error al guardar la dependencia");
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la dependencia: ' . $e->getMessage()
    ]);
} 