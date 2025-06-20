<?php
require_once '../../config/conexion.php';

header('Content-Type: application/json');

// Verificar si es una petición GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Obtener parámetros de filtrado
        $fecha = $_GET['fecha'] ?? '';
        $hora = $_GET['hora'] ?? '';
        $departamento = $_GET['departamento'] ?? '';

        // Construir la consulta base
        $sql = "SELECT 
                    d.nombre as departamento,
                    p.nombre as puesto,
                    j.nombre as jerarquia,
                    CONCAT(pers.apellido, ', ', pers.nombre) as nombre_completo,
                    f.nombre as funcion,
                    rp.horario,
                    rp.observaciones
                FROM registros_presencialidad rp
                LEFT JOIN departamentos d ON rp.departamento_id = d.id
                LEFT JOIN puestos p ON rp.puesto_id = p.id
                LEFT JOIN jerarquias j ON rp.jerarquia_id = j.id
                LEFT JOIN personal pers ON rp.personal_id = pers.id
                LEFT JOIN funciones f ON rp.funcion_id = f.id
                WHERE 1=1";

        // Aplicar filtros si existen
        if (!empty($fecha)) {
            $sql .= " AND DATE(rp.fecha) = ?";
        }
        if (!empty($hora)) {
            $sql .= " AND TIME(rp.hora) = ?";
        }
        if (!empty($departamento)) {
            $sql .= " AND rp.departamento_id = ?";
        }

        $sql .= " ORDER BY d.nombre, p.nombre, j.orden";

        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare($sql);
        
        // Vincular parámetros si existen
        $types = '';
        $params = [];
        
        if (!empty($fecha)) {
            $types .= 's';
            $params[] = $fecha;
        }
        if (!empty($hora)) {
            $types .= 's';
            $params[] = $hora;
        }
        if (!empty($departamento)) {
            $types .= 'i';
            $params[] = $departamento;
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        
        // Preparar los datos para la respuesta
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Enviar respuesta exitosa
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los datos: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
} 