<?php
// Incluir la conexión a la base de datos
require_once('../config/db.php');

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    if (empty($_POST['nombre_tipo'])) {
        throw new Exception('El nombre del tipo de vehículo es requerido');
    }

    $nombre_tipo = trim($_POST['nombre_tipo']);
    $conn = getDB();

    // Verificar si el tipo ya existe
    $stmt = $conn->prepare("SELECT id FROM tipos_vehiculo WHERE nombre = ?");
    $stmt->bind_param("s", $nombre_tipo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Tipo de vehículo encontrado',
            'id' => $row['id'],
            'text' => $nombre_tipo
        ]);
    } else {
        // Insertar nuevo tipo
        $stmt = $conn->prepare("INSERT INTO tipos_vehiculo (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre_tipo);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Tipo de vehículo guardado correctamente',
                'id' => $conn->insert_id,
                'text' => $nombre_tipo
            ]);
        } else {
            throw new Exception('Error al guardar el tipo de vehículo');
        }
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en guardar_tipo_vehiculo.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 