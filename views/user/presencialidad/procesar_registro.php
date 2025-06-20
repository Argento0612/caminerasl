<?php
// Desactivar la visualización de errores
error_reporting(0);
ini_set('display_errors', 0);

// Asegurarnos de que no haya salida antes del header
ob_start();

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit();
}

// Incluir archivo de conexión
require_once(__DIR__ . '/../../../config/db.php');

// Verificar si se recibieron datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del formulario
        $usuario_id = $_SESSION['user_id'];
        $usuario_nombre = $_SESSION['username'] ?? 'SIN NOMBRE';
        $fecha = $_POST['fecha_registro'];
        $hora = $_POST['hora_registro'];
        $departamento = $_POST['departamento'];
        $lugar_servicio = $_POST['lugar_servicio'];
        $personal_guardia = $_POST['personal_guardia'] ?? '';
        $modalidad = $_POST['modalidad'] ?? '';
        
        // Log para depuración de modalidad
        error_log("POST completo: " . print_r($_POST, true));
        error_log("Modalidad recibida: " . $modalidad);
        
        // Validar que la modalidad sea una de las permitidas
        $modalidades_permitidas = ['24 HS', '48 HS', 'OFICINA'];
        if (!in_array($modalidad, $modalidades_permitidas)) {
            error_log("Modalidad inválida: " . $modalidad);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error: Modalidad no válida. Por favor seleccione una opción válida.'
            ]);
            exit();
        }
        $hay_ausentes = isset($_POST['hay_ausentes']) ? 1 : 0;
        $personal_ausente = $_POST['personal_ausente'] ?? NULL;
        $motivo_ausencia = $_POST['motivo_ausencia'] ?? NULL;
        $personal_horas_ip = isset($_POST['personal_horas_ip']) && trim($_POST['personal_horas_ip']) !== '' ? $_POST['personal_horas_ip'] : NULL;

        // Verificar si ya existe un registro para este usuario en esta fecha
        $sql_check = "SELECT id, modalidad FROM registros_presencialidad WHERE usuario_id = ? AND fecha = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("is", $usuario_id, $fecha);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $existing_record = $result_check->fetch_assoc();
            error_log("Ya existe un registro para el usuario $usuario_id en la fecha $fecha con modalidad: " . ($existing_record['modalidad'] ?? 'NO ESPECIFICADA'));
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Ya existe un registro para esta fecha. No se puede registrar más de una vez por día.'
            ]);
            exit();
        }

        // Log antes de la inserción
        error_log("Intentando insertar registro con modalidad: " . $modalidad);

        // Preparar la consulta SQL
        $sql = "INSERT INTO registros_presencialidad 
                (usuario_id, usuario_nombre, fecha, hora, departamento, lugar_servicio, personal_guardia, 
                modalidad, hay_ausentes, personal_ausente, motivo_ausencia, personal_horas_ip)
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        error_log("SQL a ejecutar: " . $sql);
        error_log("Parámetros: usuario_id=$usuario_id, usuario_nombre=$usuario_nombre, fecha=$fecha, hora=$hora, departamento=$departamento, lugar_servicio=$lugar_servicio, personal_guardia=$personal_guardia, modalidad=$modalidad, hay_ausentes=$hay_ausentes, personal_ausente=$personal_ausente, motivo_ausencia=$motivo_ausencia, personal_horas_ip=$personal_horas_ip");

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Error en la preparación de la consulta: " . $conn->error);
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("isssssssssss", 
            $usuario_id, 
            $usuario_nombre, 
            $fecha, 
            $hora, 
            $departamento, 
            $lugar_servicio, 
            $personal_guardia, 
            $modalidad, 
            $hay_ausentes, 
            $personal_ausente, 
            $motivo_ausencia,
            $personal_horas_ip
        );

        // Log antes de ejecutar la consulta
        error_log("Ejecutando consulta con modalidad: " . $modalidad);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            error_log("Registro guardado exitosamente con modalidad: " . $modalidad);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => '¡Registro guardado exitosamente!'
            ]);
        } else {
            error_log("Error al guardar el registro: " . $stmt->error);
            throw new Exception("Error al guardar el registro: " . $stmt->error);
        }

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    } finally {
        if (isset($stmt_check)) {
            $stmt_check->close();
        }
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error: Método no permitido'
    ]);
}
?>