<?php
require_once '../config/conexion.php';

header('Content-Type: application/json');

// Verificar el método de la petición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener los datos del formulario
        $fecha = $_POST['fecha'] ?? '';
        $hora = $_POST['hora'] ?? '';
        $personal = $_POST['personal'] ?? '';
        $turno = $_POST['turno'] ?? '';
        $actividades = $_POST['actividades'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        $incidencias = $_POST['incidencias'] ?? '';

        // Validar datos requeridos
        if (empty($fecha) || empty($hora) || empty($personal) || empty($turno) || empty($actividades)) {
            throw new Exception('Todos los campos marcados como requeridos deben ser completados');
        }

        // Preparar la consulta SQL
        $sql = "INSERT INTO registro_accidentes (fecha, hora, personal, turno, actividades, observaciones, incidencias) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $fecha, $hora, $personal, $turno, $actividades, $observaciones, $incidencias);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Registro de accidente guardado exitosamente'
            ]);
        } else {
            throw new Exception('Error al guardar el registro');
        }

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
} 