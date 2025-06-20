<?php
// Desactivar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

try {
    // Consultar lugares de servicio
    $sql = "SELECT id, nombre as text FROM lugares_servicio ORDER BY nombre ASC";
    $result = $conn->query($sql);

    if ($result) {
        $lugares = [];
        while ($row = $result->fetch_assoc()) {
            $lugares[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'results' => $lugares
        ]);
    } else {
        throw new Exception("Error al consultar lugares: " . $conn->error);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 