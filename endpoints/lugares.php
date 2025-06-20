<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                throw new Exception("Error de conexión: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("SELECT id, nombre FROM lugares ORDER BY nombre ASC");
            
            if (!$stmt->execute()) {
                throw new Exception("Error al obtener la lista de lugares");
            }

            $result = $stmt->get_result();
            $lugares = [];
            
            while ($row = $result->fetch_assoc()) {
                $lugares[] = $row;
            }

            echo json_encode(['success' => true, 'data' => $lugares]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
        break;

    case 'create':
        if (!isset($_POST['nombre']) || empty($_POST['nombre'])) {
            echo json_encode(['success' => false, 'message' => 'El nombre del lugar es requerido']);
            exit;
        }

        $nombre = trim($_POST['nombre']);

        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                throw new Exception("Error de conexión: " . $conn->connect_error);
            }

            // Verificar si el lugar ya existe
            $stmt = $conn->prepare("SELECT id FROM lugares WHERE nombre = ?");
            $stmt->bind_param("s", $nombre);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al verificar el lugar");
            }

            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'El lugar ya existe']);
                exit;
            }

            // Insertar nuevo lugar
            $stmt = $conn->prepare("INSERT INTO lugares (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombre);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al crear el lugar");
            }

            echo json_encode(['success' => true, 'message' => 'Lugar creado exitosamente']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
} 