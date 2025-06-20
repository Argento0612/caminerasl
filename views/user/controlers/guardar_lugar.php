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

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit();
}

// Verificar si se recibió el nombre del lugar
if (!isset($_POST['nombre_lugar']) || empty($_POST['nombre_lugar'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'El nombre del lugar es requerido'
    ]);
    exit();
}

// Incluir archivo de conexión
require_once(__DIR__ . '/../../../config/db.php');

try {
    $nombre_lugar = $conn->real_escape_string($_POST['nombre_lugar']);

    // Verificar si el lugar ya existe
    $check_sql = "SELECT id FROM lugares_servicio WHERE nombre = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $nombre_lugar);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // El lugar ya existe, retornar éxito
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'El lugar ya existe'
        ]);
        exit();
    }

    // Insertar nuevo lugar
    $insert_sql = "INSERT INTO lugares_servicio (nombre) VALUES (?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("s", $nombre_lugar);

    if ($insert_stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Lugar guardado exitosamente'
        ]);
    } else {
        throw new Exception("Error al guardar el lugar: " . $conn->error);
    }

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($check_stmt)) {
        $check_stmt->close();
    }
    if (isset($insert_stmt)) {
        $insert_stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 