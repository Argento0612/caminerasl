<?php
session_start();
require_once '../../config/db.php';
require_once '../../controllers/LoginController.php';

// Verificar si el usuario está logueado y es admin
$loginController = new LoginController(new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS
));

if (!$loginController->checkPermission('admin')) {
    header('Location: ../login.php');
    exit();
}

// Verificar si se proporcionó un ID de usuario
if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar que el usuario existe y no es el usuario actual
    $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error'] = "Usuario no encontrado.";
        header('Location: users.php');
        exit();
    }

    // Evitar que un administrador se elimine a sí mismo
    if ($user['username'] === $_SESSION['username']) {
        $_SESSION['error'] = "No puedes eliminar tu propio usuario.";
        header('Location: users.php');
        exit();
    }

    // Eliminar el usuario
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        $_SESSION['success'] = "Usuario eliminado exitosamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el usuario.";
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Error de conexión: " . $e->getMessage();
}

header('Location: users.php');
exit();
?> 