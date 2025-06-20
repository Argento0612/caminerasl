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

// Inicializar variables
$error = '';
$success = '';
$user = null;

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

    // Obtener datos del usuario
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: users.php');
        exit();
    }

    // Procesar el formulario cuando se envía
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $dni = trim($_POST['dni']);
        $email = trim($_POST['email']);
        $departamento = trim($_POST['departamento']);
        $rol = trim($_POST['rol']);
        $new_password = trim($_POST['new_password']);

        // Verificar si el nombre de usuario o email ya existe (excluyendo el usuario actual)
        $checkStmt = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ? OR dni = ?) AND id != ?");
        $checkStmt->execute([$username, $email, $dni, $_GET['id']]);
        
        if ($checkStmt->rowCount() > 0) {
            $error = 'El usuario, correo electrónico o DNI ya está registrado';
        } else {
            // Preparar la consulta base
            $sql = "UPDATE users SET username = ?, dni = ?, email = ?, departamento = ?, rol = ?";
            $params = [$username, $dni, $email, $departamento, $rol];

            // Si se proporcionó una nueva contraseña, actualizarla
            if (!empty($new_password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = ?";
            $params[] = $_GET['id'];

            // Ejecutar la actualización
            $updateStmt = $db->prepare($sql);
            if ($updateStmt->execute($params)) {
                $success = 'Usuario actualizado exitosamente';
                // Actualizar los datos del usuario para mostrar los cambios
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Error al actualizar el usuario';
            }
        }
    }
} catch (PDOException $e) {
    $error = "Error de conexión: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Panel de Administración</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --neon-color: #ccff00;
            --neon-color-alt: #d4f300;
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --accent-color: rgba(255, 255, 255, 0.2);
            --sidebar-width: 280px;
            --neon-blue: #00f7ff;
            --neon-purple: #bc13fe;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: rgba(26, 26, 26, 0.95);
            border-right: 1px solid var(--neon-color);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 5px 0 15px rgba(204, 255, 0, 0.1);
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            position: relative;
        }

        .form-container {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 20px var(--neon-blue),
                        inset 0 0 20px var(--neon-purple);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: var(--neon-blue);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
            text-shadow: 0 0 5px var(--neon-blue);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--neon-purple);
            border-radius: 8px;
            color: #ffffff;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--neon-blue);
            box-shadow: 0 0 10px var(--neon-blue);
            outline: none;
            color: #ffffff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        select.form-control {
            appearance: none;
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid var(--neon-blue);
            color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2300f7ff' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        .btn-neon {
            background: transparent;
            border: 2px solid var(--neon-blue);
            color: var(--neon-blue);
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .btn-neon:hover {
            background: var(--neon-blue);
            color: var(--bg-color);
            box-shadow: 0 0 20px var(--neon-blue);
        }

        .alert {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .alert-success {
            background: rgba(0, 255, 0, 0.1);
            border: 2px solid #00ff00;
            color: #00ff00;
            text-shadow: 0 0 5px #00ff00;
        }

        .alert-danger {
            background: rgba(255, 0, 0, 0.1);
            border: 2px solid #ff0000;
            color: #ff0000;
            text-shadow: 0 0 5px #ff0000;
        }

        /* Reutilizar los estilos del sidebar */
        .sidebar-header {
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .logo-container {
            padding: 0.5rem;
            text-align: left;
        }

        .logo {
            max-width: 250px;
            height: auto;
            margin: 0 auto;
            filter: drop-shadow(0 0 10px var(--neon-color));
        }

        .nav-item {
            padding: 0.5rem 1.5rem;
            margin-bottom: 0.5rem;
        }

        .nav-link {
            color: var(--text-color);
            text-decoration: none;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--neon-color);
            color: #000;
            box-shadow: 0 0 20px rgba(204, 255, 0, 0.3);
        }

        .nav-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .user-info {
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(204, 255, 0, 0.2);
        }

        .user-info h4 {
            color: var(--neon-color);
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .user-info p {
            color: var(--text-color);
            font-size: 0.9rem;
            margin: 0;
            opacity: 0.8;
        }

        .password-group {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--neon-blue);
            cursor: pointer;
            padding: 0;
            font-size: 1.2rem;
        }

        .toggle-password:hover {
            color: var(--neon-purple);
            text-shadow: 0 0 5px var(--neon-purple);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="../../public/img/logo.png" alt="Logo" class="logo">
            </div>
        </div>
        
        <div class="user-info">
            <h4><?php echo $_SESSION['username']; ?></h4>
            <p><?php echo isset($_SESSION['rol']) ? $_SESSION['rol'] : ''; ?></p>
        </div>

        <nav>
            <div class="nav-item">
                <a href="../dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a href="users.php" class="nav-link">
                    <i class="fas fa-users"></i>
                    Usuarios
                </a>
            </div>
            <div class="nav-item">
                <a href="create_user.php" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    Crear Usuario
                </a>
            </div>
            <div class="nav-item">
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h1>Editar Usuario</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="dni">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" value="<?php echo htmlspecialchars($user['dni']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                            <div class="password-group">
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="departamento">Departamento</label>
                            <select class="form-control" id="departamento" name="departamento" required>
                                <option value="">Seleccione un departamento</option>
                                <option value="Departamento de Seguridad Vial N° 1" <?php echo $user['departamento'] === 'Departamento de Seguridad Vial N° 1' ? 'selected' : ''; ?>>Departamento de Seguridad Vial N° 1</option>
                                <option value="Departamento de Seguridad Vial N° 2" <?php echo $user['departamento'] === 'Departamento de Seguridad Vial N° 2' ? 'selected' : ''; ?>>Departamento de Seguridad Vial N° 2</option>
                                <option value="Departamento de Seguridad Vial N° 3" <?php echo $user['departamento'] === 'Departamento de Seguridad Vial N° 3' ? 'selected' : ''; ?>>Departamento de Seguridad Vial N° 3</option>
                                <option value="Departamento de Seguridad Vial N° 4" <?php echo $user['departamento'] === 'Departamento de Seguridad Vial N° 4' ? 'selected' : ''; ?>>Departamento de Seguridad Vial N° 4</option>
                                <option value="Departamento de Seguridad Vial N° 5" <?php echo $user['departamento'] === 'Departamento de Seguridad Vial N° 5' ? 'selected' : ''; ?>>Departamento de Seguridad Vial N° 5</option>
                                <option value="Departamento de Tránsito" <?php echo $user['departamento'] === 'Departamento de Tránsito' ? 'selected' : ''; ?>>Departamento de Tránsito</option>
                                <option value="Brigada Especiales" <?php echo $user['departamento'] === 'Brigada Especiales' ? 'selected' : ''; ?>>Brigada Especiales</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rol">Rol</label>
                            <select class="form-control" id="rol" name="rol" required>
                                <option value="">Seleccione un rol</option>
                                <option value="admin" <?php echo $user['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                <option value="user" <?php echo $user['rol'] === 'user' ? 'selected' : ''; ?>>Usuario</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn-neon">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 