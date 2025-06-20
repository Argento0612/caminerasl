<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit();
}

// Incluir conexión a la base de datos
require_once('../config/db.php');

try {
    // Validar y sanitizar los datos del formulario
    $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
    $hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
    $lugar = isset($_POST['lugar']) ? trim($_POST['lugar']) : '';
    $causa = isset($_POST['causa']) ? trim($_POST['causa']) : '';
    $detalle = isset($_POST['detalle']) ? trim($_POST['detalle']) : '';
    $dependencia = isset($_POST['dependencia']) ? trim($_POST['dependencia']) : '';
    
    // Validar campos requeridos
    if (empty($fecha) || empty($hora) || empty($lugar) || empty($causa) || empty($detalle) || empty($dependencia)) {
        throw new Exception('Todos los campos son requeridos');
    }
    
    // Obtener datos del usuario de la sesión
    $nombre_usuario = $_SESSION['username'];
    $departamento = $_SESSION['departamento'];
    $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'efectivo';
    
    // Preparar la consulta SQL
    $sql = "INSERT INTO form_procedimiento (
        fecha_procedimiento,
        hora_procedimiento,
        lugar_procedimiento,
        causa_procedimiento,
        detalle_procedimiento,
        dependencia_cargo,
        nombre_usuario,
        departamento_usuario,
        rol_usuario
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }
    
    // Vincular los parámetros con los tipos correctos
    // s = string, d = decimal/double, i = integer
    $stmt->bind_param("sssssssss", 
        $fecha,
        $hora,
        $lugar,
        $causa,
        $detalle,
        $dependencia,
        $nombre_usuario,
        $departamento,
        $rol
    );
    
    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    // Obtener el ID del registro insertado
    $id_insertado = $stmt->insert_id;
    
    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conn->close();
    
    // Mostrar mensaje de éxito
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Éxito</title>
        <!-- SweetAlert2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                title: '¡Éxito!',
                text: 'El procedimiento se ha guardado correctamente',
                icon: 'success',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'form_procedimiento.php';
                }
            });
        </script>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    // En caso de error, mostrar alerta de error
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <!-- SweetAlert2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                title: 'Error',
                text: 'Error al guardar el procedimiento: <?php echo $e->getMessage(); ?>',
                icon: 'error',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'form_procedimiento.php';
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}
?> 