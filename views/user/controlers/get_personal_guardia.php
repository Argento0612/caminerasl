<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit();
}

require_once(__DIR__ . '/../../../config/db.php');

try {
    $sql = "SELECT DISTINCT TRIM(personal_guardia) as nombre FROM registros_presencialidad WHERE personal_guardia IS NOT NULL AND personal_guardia != '' ORDER BY nombre ASC";
    $result = $conn->query($sql);

    $personal = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $nombres = array_map('trim', explode(',', $row['nombre']));
            foreach ($nombres as $nombre) {
                if ($nombre !== '') {
                    $personal[$nombre] = true;
                }
            }
        }
        $personal = array_keys($personal);
        sort($personal);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'results' => $personal
        ]);
    } else {
        throw new Exception("Error al consultar personal de guardia: " . $conn->error);
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