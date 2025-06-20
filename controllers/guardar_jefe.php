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

// Función para normalizar texto (quitar tildes y convertir a minúsculas)
function normalizarTexto($texto) {
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    $texto = preg_replace('/[áàäâã]/u', 'a', $texto);
    $texto = preg_replace('/[éèëê]/u', 'e', $texto);
    $texto = preg_replace('/[íìïî]/u', 'i', $texto);
    $texto = preg_replace('/[óòöôõ]/u', 'o', $texto);
    $texto = preg_replace('/[úùüû]/u', 'u', $texto);
    $texto = preg_replace('/[ñ]/u', 'n', $texto);
    return $texto;
}

// Función para capitalizar texto
function capitalizarTexto($texto) {
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    $palabras = explode(' ', $texto);
    $palabras = array_map('ucfirst', $palabras);
    return implode(' ', $palabras);
}

try {
    $nombre = trim($_POST['nombre']);
    $apellido = '';
    $dni = '';
    $departamento = $_SESSION['departamento'] ?? 'No especificado';
    $activo = 1;

    // Verificar si ya existe
    $stmt = $conn->prepare("SELECT id FROM jefes_operativo WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Este jefe ya está registrado']);
        exit();
    }

    // Insertar nuevo jefe
    $stmt = $conn->prepare("INSERT INTO jefes_operativo (nombre, apellido, dni, departamento, activo) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Error en prepare: " . $conn->error);
    }
    $stmt->bind_param("ssssi", $nombre, $apellido, $dni, $departamento, $activo);
    if (!$stmt->execute()) {
        throw new Exception("Error en execute: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        // Retornar éxito
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'nombre' => $nombre
        ]);
    } else {
        throw new Exception("No se pudo insertar el registro");
    }

} catch (Exception $e) {
    // Registrar error
    error_log("Error en guardar_jefe.php: " . $e->getMessage());
    
    // Retornar error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el jefe: ' . $e->getMessage()
    ]);
}
?> 