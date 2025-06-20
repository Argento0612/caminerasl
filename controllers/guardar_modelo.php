<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    if (empty($_POST['modelo_nombre'])) {
        throw new Exception('El nombre del modelo es requerido');
    }

    if (empty($_POST['marca_id'])) {
        throw new Exception('El ID de la marca es requerido');
    }

    $modelo_nombre = trim($_POST['modelo_nombre']);
    $marca_id = (int)$_POST['marca_id'];
    $conn = getDB();

    // Verificar que la marca existe
    $stmt = $conn->prepare("SELECT id FROM marcas_vehiculo WHERE id = ?");
    $stmt->bind_param("i", $marca_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('La marca seleccionada no existe');
    }
    $stmt->close();

    // Verificar si el modelo ya existe para esta marca
    $stmt = $conn->prepare("SELECT id FROM modelos_vehiculo WHERE LOWER(nombre) = LOWER(?) AND marca_id = ?");
    $modelo_lower = strtolower($modelo_nombre);
    $stmt->bind_param("si", $modelo_lower, $marca_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'El modelo ya existe',
            'id' => $row['id'],
            'nombre' => $modelo_nombre
        ]);
    } else {
        // Insertar nuevo modelo
        $stmt = $conn->prepare("INSERT INTO modelos_vehiculo (nombre, marca_id) VALUES (?, ?)");
        $stmt->bind_param("si", $modelo_nombre, $marca_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Modelo guardado correctamente',
                'id' => $conn->insert_id,
                'nombre' => $modelo_nombre
            ]);
        } else {
            throw new Exception('Error al guardar el modelo: ' . $stmt->error);
        }
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error en guardar_modelo.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 