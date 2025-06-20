<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener y validar el nombre de la marca
    $marca_nombre = trim($_POST['nombre_marca'] ?? '');
    if (empty($marca_nombre)) {
        throw new Exception('El nombre de la marca es requerido');
    }

    $conn = getDB();

    // Preparar la consulta para verificar si la marca ya existe
    $stmt = $conn->prepare("SELECT id FROM marcas_vehiculo WHERE nombre = ?");
    $stmt->bind_param("s", $marca_nombre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // La marca ya existe, devolver su ID
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'La marca ya existe',
            'id' => $row['id'],
            'text' => $marca_nombre
        ]);
    } else {
        // La marca no existe, insertarla
        $stmt = $conn->prepare("INSERT INTO marcas_vehiculo (nombre) VALUES (?)");
        $stmt->bind_param("s", $marca_nombre);
        
        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Marca guardada exitosamente',
                'id' => $new_id,
                'text' => $marca_nombre
            ]);
        } else {
            throw new Exception('Error al guardar la marca');
        }
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Log del error
    error_log("Error en guardar_marca.php: " . $e->getMessage());
    
    // Devolver respuesta de error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 