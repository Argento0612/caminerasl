<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    if (empty($_POST['numero_equipo'])) {
        throw new Exception('El número del equipo es requerido');
    }

    $numero_equipo = trim($_POST['numero_equipo']);
    $conn = getDB();

    // Verificar si el equipo ya existe
    $stmt = $conn->prepare("SELECT id FROM equipos_alcoholemia WHERE numero = ?");
    $stmt->bind_param("s", $numero_equipo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Equipo encontrado',
            'id' => $row['id'],
            'text' => $numero_equipo
        ]);
    } else {
        // Insertar nuevo equipo
        $stmt = $conn->prepare("INSERT INTO equipos_alcoholemia (numero) VALUES (?)");
        $stmt->bind_param("s", $numero_equipo);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Equipo guardado correctamente',
                'id' => $conn->insert_id,
                'text' => $numero_equipo
            ]);
        } else {
            throw new Exception('Error al guardar el equipo');
        }
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en guardar_equipo.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 