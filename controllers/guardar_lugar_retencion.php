<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    if (empty($_POST['nombre'])) {
        throw new Exception('El nombre del lugar de retención es requerido');
    }

    $nombre = trim($_POST['nombre']);
    $conn = getDB();

    // Verificar si el lugar ya existe
    $stmt = $conn->prepare("SELECT id FROM lugares_retencion WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Lugar encontrado',
            'id' => $row['id'],
            'text' => $nombre
        ]);
    } else {
        // Insertar nuevo lugar
        $stmt = $conn->prepare("INSERT INTO lugares_retencion (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Lugar de retención guardado correctamente',
                'id' => $conn->insert_id,
                'text' => $nombre
            ]);
        } else {
            throw new Exception('Error al guardar el lugar de retención');
        }
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en guardar_lugar_retencion.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 