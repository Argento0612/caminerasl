<?php
// Evitar que se muestren errores PHP directamente
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php-error.log');

// Solo iniciar sesión si no hay una activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ajustar la ruta del require según desde dónde se llame al controlador
$config_path = file_exists('../config/db.php') ? '../config/db.php' : 'config/db.php';
require_once $config_path;

// Asegurar que la respuesta sea siempre JSON
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar que el usuario esté autenticado
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Usuario no autenticado');
    }

    $conn = getDB();

    // Debug log
    error_log("POST Data: " . print_r($_POST, true));
    error_log("SESSION Data: " . print_r($_SESSION, true));
    error_log("Valor de hubo_infraccion antes de procesar: " . (isset($_POST['hubo_infraccion']) ? $_POST['hubo_infraccion'] : 'no definido'));
    error_log("Valor de hubo_retencion antes de procesar: " . (isset($_POST['hubo_retencion']) ? $_POST['hubo_retencion'] : 'no definido'));

    // Validar campos obligatorios
    $campos_obligatorios = [
        'fecha', 'hora', 'lugar', 'nombre_identificado', 'dni_identificado',
        'domicilio_identificado', 'sexo_identificado', 'tipo_vehiculo',
        'marca_vehiculo', 'modelo_vehiculo', 'dominio', 'hubo_infraccion'
    ];

    foreach ($campos_obligatorios as $campo) {
        if (empty($_POST[$campo])) {
            $nombre_campo = ucfirst(str_replace('_', ' ', $campo));
            if ($campo === 'tipo_vehiculo') {
                $nombre_campo = 'Tipo de vehículo';
            } else if ($campo === 'marca_vehiculo') {
                $nombre_campo = 'Marca de vehículo';
            } else if ($campo === 'modelo_vehiculo') {
                $nombre_campo = 'Modelo de vehículo';
            }
            throw new Exception("El campo {$nombre_campo} es obligatorio");
        }
    }

    // Validar que hubo_infraccion sea 'si' o 'no'
    if (!in_array($_POST['hubo_infraccion'], ['si', 'no'])) {
        throw new Exception("El campo Hubo infracción debe ser 'si' o 'no'");
    }

    // Obtener el departamento del usuario
    $stmt = $conn->prepare("SELECT departamento FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Obtener o crear el ID del departamento
    $departamento_nombre = $user['departamento'];
    $stmt = $conn->prepare("SELECT id FROM departamentos WHERE nombre = ?");
    $stmt->bind_param("s", $departamento_nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    $departamento = $result->fetch_assoc();
    
    if (!$departamento) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO departamentos (nombre) VALUES (?)");
        $stmt->bind_param("s", $departamento_nombre);
        $stmt->execute();
        $departamento_id = $conn->insert_id;
    } else {
        $departamento_id = $departamento['id'];
    }
    $stmt->close();

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Convertir valores booleanos a formato enum 'si'/'no'
        $hay_menores = isset($_POST['hay_menores']) && $_POST['hay_menores'] === 'si' ? 'si' : 'no';
        $hay_acompanantes = isset($_POST['tiene_acompanantes']) && $_POST['tiene_acompanantes'] === 'si' ? 'si' : 'no';
        $hubo_retencion = isset($_POST['hubo_retencion']) && $_POST['hubo_retencion'] === 'si' ? 'si' : 'no';
        $hubo_infraccion = isset($_POST['hubo_infraccion']) && $_POST['hubo_infraccion'] === 'si' ? 'si' : 'no';

        error_log("Valor de hubo_infraccion después de procesar: " . $hubo_infraccion);
        error_log("Valor de hubo_retencion después de procesar: " . $hubo_retencion);

        // Si hay retención, asegurarnos de que el lugar de retención exista
        $lugar_retencion_id = null;
        if ($hubo_retencion === 'si' && !empty($_POST['lugar_retencion'])) {
            // Primero buscar si ya existe el lugar de retención
            $stmt_lugar = $conn->prepare("SELECT id FROM lugares_retencion WHERE nombre = ?");
            $nombre_lugar_retencion = $_POST['lugar_retencion_texto']; // Usamos el texto en lugar del ID
            $stmt_lugar->bind_param("s", $nombre_lugar_retencion);
            $stmt_lugar->execute();
            $result = $stmt_lugar->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $lugar_retencion_id = $row['id'];
            } else {
                // Si no existe, lo creamos
                $stmt_lugar->close();
                $stmt_lugar = $conn->prepare("INSERT INTO lugares_retencion (nombre) VALUES (?)");
                $stmt_lugar->bind_param("s", $nombre_lugar_retencion);
                $stmt_lugar->execute();
                $lugar_retencion_id = $conn->insert_id;
            }
            $stmt_lugar->close();
        }

        // Procesar campos de procedencia
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];
        
        // Si se seleccionó "Otros", usar el valor del campo de texto
        if ($desde === 'Otros' && !empty($_POST['desde_otros'])) {
            $desde = $_POST['desde_otros'];
        }
        if ($hasta === 'Otros' && !empty($_POST['hasta_otros'])) {
            $hasta = $_POST['hasta_otros'];
        }

        // Preparar el insert principal
        $stmt = $conn->prepare("INSERT INTO operativos_b (
            fecha, hora, lugar_id, departamento_id, nombre_identificado, dni_identificado,
            domicilio_identificado, sexo_identificado, hay_menores, cantidad_menores,
            hay_acompanantes, tipo_vehiculo_id, marca_vehiculo_id, modelo_vehiculo_id,
            dominio, desde, hasta, hubo_infraccion, motivo_infraccion_id,
            numero_acta, hubo_retencion, lugar_retencion_id, alcoholemia, equipo_id,
            prueba_id, resultado_alcoholemia, usuario_id, observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }

        // Preparar los valores
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $lugar = $_POST['lugar'];
        $dep_id = $departamento_id;
        $nombre = $_POST['nombre_identificado'];
        $dni = $_POST['dni_identificado'];
        $domicilio = $_POST['domicilio_identificado'];
        $sexo = $_POST['sexo_identificado'];
        $cantidad_menores = !empty($_POST['cantidad_menores']) ? intval($_POST['cantidad_menores']) : null;
        $tipo_vehiculo_id = intval($_POST['tipo_vehiculo']);
        $marca_vehiculo_id = intval($_POST['marca_vehiculo']);
        $modelo_vehiculo_id = $_POST['modelo_vehiculo'];
        $dominio = $_POST['dominio'];
        $desde_val = $desde; // Usar el valor procesado
        $hasta_val = $hasta; // Usar el valor procesado
        $hubo_infraccion = $_POST['hubo_infraccion'];
        $motivo_infraccion_id = !empty($_POST['motivo_infraccion']) ? intval($_POST['motivo_infraccion']) : null;
        $numero_acta = !empty($_POST['numero_acta']) ? $_POST['numero_acta'] : null;
        $hubo_retencion_val = $hubo_retencion;
        $lugar_retencion_val = $lugar_retencion_id;
        $alcoholemia = $_POST['alcoholemia'];
        $equipo_id = !empty($_POST['equipo']) ? intval($_POST['equipo']) : null;
        $prueba_id = !empty($_POST['prueba_id']) ? $_POST['prueba_id'] : null;
        $resultado_alcoholemia = !empty($_POST['resultado_alcoholemia']) ? $_POST['resultado_alcoholemia'] : null;
        $usuario_id = intval($_SESSION['user_id']);
        $observaciones = null;

        $stmt->bind_param(
            "ssiisssssisiiisssisisiiissis",
            $fecha, $hora, $lugar, $dep_id, $nombre, $dni, $domicilio, $sexo,
            $hay_menores, $cantidad_menores, $hay_acompanantes, $tipo_vehiculo_id,
            $marca_vehiculo_id, $modelo_vehiculo_id, $dominio, $desde_val, $hasta_val,
            $hubo_infraccion, $motivo_infraccion_id, $numero_acta, $hubo_retencion_val,
            $lugar_retencion_val, $alcoholemia, $equipo_id, $prueba_id,
            $resultado_alcoholemia, $usuario_id, $observaciones
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        $operativo_id = $conn->insert_id;
        $stmt->close();

        // Guardar menores si existen
        if ($hay_menores === 'si' && isset($_POST['nombre_menor']) && is_array($_POST['nombre_menor'])) {
            $stmt = $conn->prepare("INSERT INTO menores_operativo (
                operativo_id, nombre, dni, domicilio, observaciones
            ) VALUES (?, ?, ?, ?, ?)");

            $todas_observaciones = []; // Array para almacenar todas las observaciones

            foreach ($_POST['nombre_menor'] as $i => $nombre) {
                if (empty($nombre)) continue;
                
                // Almacenar los valores en variables antes de bind_param
                $menor_operativo_id = $operativo_id;
                $menor_nombre = $nombre;
                $menor_dni = $_POST['dni_menor'][$i] ?? '';
                $menor_domicilio = $_POST['domicilio_menor'][$i] ?? '';
                $menor_observaciones = $_POST['observaciones_menor'][$i] ?? null;

                // Agregar las observaciones al array si existen
                if ($menor_observaciones) {
                    $todas_observaciones[] = "Menor {$menor_nombre}: {$menor_observaciones}";
                }

                $stmt->bind_param("issss",
                    $menor_operativo_id,
                    $menor_nombre,
                    $menor_dni,
                    $menor_domicilio,
                    $menor_observaciones
                );

                if (!$stmt->execute()) {
                    throw new Exception("Error al guardar menor: " . $stmt->error);
                }
            }
            $stmt->close();

            // Actualizar las observaciones en operativos_b si hay observaciones de menores
            if (!empty($todas_observaciones)) {
                $observaciones_completas = implode("\n", $todas_observaciones);
                $stmt = $conn->prepare("UPDATE operativos_b SET observaciones = ? WHERE id = ?");
                $stmt->bind_param("si", $observaciones_completas, $operativo_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error al actualizar observaciones: " . $stmt->error);
                }
                $stmt->close();
            }
        }

        // Guardar acompañantes si existen
        if ($hay_acompanantes === 'si' && isset($_POST['nombre_apellido_acompanante']) && is_array($_POST['nombre_apellido_acompanante'])) {
            $stmt = $conn->prepare("INSERT INTO acompanantes_operativo (
                operativo_id, nombre_apellido, dni, domicilio, sexo
            ) VALUES (?, ?, ?, ?, ?)");

            foreach ($_POST['nombre_apellido_acompanante'] as $i => $nombre) {
                if (empty($nombre)) continue;
                
                // Almacenar los valores en variables antes de bind_param
                $acompanante_operativo_id = $operativo_id;
                $acompanante_nombre = $nombre;
                $acompanante_dni = $_POST['dni_acompanante'][$i] ?? '';
                $acompanante_domicilio = $_POST['domicilio_acompanante'][$i] ?? '';
                $acompanante_sexo = $_POST["sexo_acompanante_" . ($i + 1)] ?? 'M';

                $stmt->bind_param("issss",
                    $acompanante_operativo_id,
                    $acompanante_nombre,
                    $acompanante_dni,
                    $acompanante_domicilio,
                    $acompanante_sexo
                );

                if (!$stmt->execute()) {
                    throw new Exception("Error al guardar acompañante: " . $stmt->error);
                }
            }
            $stmt->close();
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Operativo guardado correctamente',
            'id' => $operativo_id
        ]);

    } catch (Exception $e) {
        error_log("Error in transaction: " . $e->getMessage());
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Error en guardar_formulario_b.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    error_log("Error fatal en guardar_formulario_b.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 