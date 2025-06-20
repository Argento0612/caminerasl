<?php
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

// Verificar si se recibió el número de móvil por POST
if (!isset($_POST['movil']) || empty($_POST['movil'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'El número de móvil es requerido']);
    exit();
}

try {
    // Obtener y limpiar el número de móvil
    $numero = trim($_POST['movil']);

    // Verificar si ya existe
    $stmt = $conn->prepare("SELECT id FROM moviles_operativo WHERE numero = ?");
    $stmt->bind_param("s", $numero);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Este móvil ya está registrado']);
        exit();
    }

    // Insertar nuevo móvil
    $stmt = $conn->prepare("INSERT INTO moviles_operativo (numero, tipo, departamento, activo) VALUES (?, 'No especificado', ?, TRUE)");
    $departamento = $_SESSION['departamento'] ?? 'No especificado';
    $stmt->bind_param("ss", $numero, $departamento);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Retornar éxito
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'movil' => $numero
        ]);
    } else {
        throw new Exception("No se pudo insertar el registro");
    }

} catch (Exception $e) {
    // Registrar error
    error_log("Error en guardar_movil.php: " . $e->getMessage());
    
    // Retornar error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el móvil'
    ]);
}
?> 