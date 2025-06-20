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

// Verificar si la conexión está establecida
if (!isset($conn) || $conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Función para mostrar mensajes de error
function mostrarError($mensaje) {
    $_SESSION['error'] = $mensaje;
    header('Location: ../views/form_operativo_a.php');
    exit();
}

// Función para mostrar mensaje de éxito
function mostrarExito($mensaje) {
    $_SESSION['exito'] = $mensaje;
    header('Location: ../views/dashboard.php');
    exit();
}

try {
    // Validar campos requeridos
    if (!isset($_POST['jefe_operativo']) || empty($_POST['jefe_operativo'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'El jefe de operativo es requerido']);
        exit();
    }

    if (!isset($_POST['personal']) || !is_array($_POST['personal']) || empty($_POST['personal'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Debe seleccionar al menos un personal afectado']);
        exit();
    }

    // Procesar personal afectado
    $personal_afectado = implode(', ', array_map('trim', $_POST['personal']));

    // Crear la tabla si no existe
    $sql = "CREATE TABLE IF NOT EXISTS form_operativo_a (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        departamento VARCHAR(100) NOT NULL,
        jefe_operativo VARCHAR(100) NOT NULL,
        personal_afectado TEXT NOT NULL,
        movil VARCHAR(20),
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error al crear la tabla: " . $conn->error);
    }

    // Insertar datos
    $stmt = $conn->prepare("INSERT INTO form_operativo_a (user_id, departamento, jefe_operativo, personal_afectado, movil) VALUES (?, ?, ?, ?, ?)");
    
    // Obtener el departamento de la sesión o usar un valor por defecto
    $departamento = $_SESSION['departamento'] ?? 'No especificado';
    $movil = isset($_POST['movil']) && !empty($_POST['movil']) ? $_POST['movil'] : null;

    $stmt->bind_param(
        "issss",
        $_SESSION['user_id'],
        $departamento,
        $_POST['jefe_operativo'],
        $personal_afectado,
        $movil
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al insertar el registro: " . $stmt->error);
    }

    // Retornar éxito
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Formulario guardado correctamente'
    ]);

} catch (Exception $e) {
    // Registrar error
    error_log("Error en formHandlerA.php: " . $e->getMessage());

    // Retornar error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el formulario: ' . $e->getMessage()
    ]);
}
?> 