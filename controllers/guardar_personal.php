<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Incluir conexión a la base de datos
require_once('../config/db.php');

// Verificar si se recibió el nombre por POST
if (!isset($_POST['nombre']) || empty($_POST['nombre'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
    exit();
}

try {
    $nombre = trim($_POST['nombre']);
    $apellido = '';
    $dni = '';
    $departamento = $_SESSION['departamento'] ?? 'No especificado';
    $activo = 1;

    // Verificar si ya existe
    $stmt = $conn->prepare("SELECT id FROM personal_operativo WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Este personal ya está registrado']);
        exit();
    }

    // Insertar nuevo personal
    $stmt = $conn->prepare("INSERT INTO personal_operativo (nombre, apellido, dni, departamento, activo) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error en prepare: " . $conn->error);
    }
    $stmt->bind_param("ssssi", $nombre, $apellido, $dni, $departamento, $activo);
    if (!$stmt->execute()) {
        throw new Exception("Error en execute: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'nombre' => $nombre]);
    } else {
        throw new Exception("No se pudo insertar el registro");
    }
} catch (Exception $e) {
    // Registrar error
    error_log("Error en guardar_personal.php: " . $e->getMessage());
    
    // Retornar error
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al guardar el personal: ' . $e->getMessage()]);
}
?> 