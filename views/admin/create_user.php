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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $dni = trim($_POST['dni']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $departamento = trim($_POST['departamento']);
    $rol = trim($_POST['rol']);

    try {
        $db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        
        // Verificar si el usuario o DNI ya existe
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR dni = ?");
        $stmt->execute([$username, $email, $dni]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'El usuario, correo electrónico o DNI ya está registrado';
        } else {
            // Insertar nuevo usuario
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("
                INSERT INTO users (username, dni, email, password, departamento, rol) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$username, $dni, $email, $hashed_password, $departamento, $rol])) {
                $success = 'Usuario creado exitosamente';
            } else {
                $error = 'Error al crear el usuario';
            }
        }
    } catch (PDOException $e) {
        $error = "Error de conexión: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario - Panel de Administración</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reutilizar los estilos base del dashboard */
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

        /* Sidebar Styles */
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
            transition: all 0.3s ease;
        }

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
            max-width: 180px;
            height: auto;
            margin: 0 auto;
            filter: drop-shadow(0 0 10px var(--neon-color));
            transition: all 0.3s ease;
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

        .nav-link:hover {
            background: rgba(204, 255, 0, 0.1);
            color: var(--neon-color);
            transform: translateX(5px);
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.1);
        }

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

        /* User Info */
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

        /* Estilos específicos del formulario */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            width: calc(100% - var(--sidebar-width));
        }

        .content-header {
            margin-bottom: 2rem;
            text-align: left;
            width: 100%;
            max-width: 100px;
            margin-left: 50px;
        }

        .content-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
            margin-bottom: 1rem;
        }

        .form-container {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 20px var(--neon-blue),
                        inset 0 0 20px var(--neon-purple);
            margin-bottom: 2rem;
            max-width: 700px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
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
            color: rgba(255, 255, 255, 0.8);
        }

        /* Asegurarnos de que funcione en todos los navegadores */
        .form-control::-webkit-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .form-control::-moz-placeholder {
            color: rgba(255, 255, 255, 0.8);
            opacity: 1;
        }

        .form-control:-ms-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .form-control::-ms-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Estilo para las opciones por defecto de los select */
        select.form-control option[value=""] {
            color: rgba(255, 255, 255, 0.8);
        }

        select.form-control {
            appearance: none;
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid var(--neon-blue);
            color: #ffffff;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2300f7ff' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            box-shadow: 0 0 10px rgba(0, 247, 255, 0.2);
        }

        select.form-control:focus {
            background: rgba(0, 0, 0, 0.9);
            border-color: var(--neon-blue);
            box-shadow: 0 0 15px var(--neon-blue);
            outline: none;
        }

        select.form-control option {
            background-color: rgba(0, 0, 0, 0.9);
            color: #ffffff;
            padding: 10px;
        }

        select.form-control:hover {
            box-shadow: 0 0 20px var(--neon-blue);
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
            transform: translateY(-2px);
        }

        .btn-neon:active {
            transform: translateY(0);
        }

        .alert {
            width: 100%;
            max-width: 700px;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
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

        /* Reutilizar otros estilos necesarios del dashboard */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 5rem;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .content-header {
            margin-bottom: 2rem;
            text-align: center;
            width: 100%;
            max-width: 1000px;
        }

        .content-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
            margin-bottom: 1rem;
        }

        .form-container {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 20px var(--neon-blue),
                        inset 0 0 20px var(--neon-purple);
            margin-bottom: 2rem;
            max-width: 2000px;
            width: 1000px;
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
            color: rgba(255, 255, 255, 0.8);
        }

        /* Asegurarnos de que funcione en todos los navegadores */
        .form-control::-webkit-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .form-control::-moz-placeholder {
            color: rgba(255, 255, 255, 0.8);
            opacity: 1;
        }

        .form-control:-ms-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .form-control::-ms-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Estilo para las opciones por defecto de los select */
        select.form-control option[value=""] {
            color: rgba(255, 255, 255, 0.8);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
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
            transform: translateY(-2px);
        }

        .btn-neon:active {
            transform: translateY(0);
        }

        .alert {
            width: 100%;
            max-width: 1000px;
            text-align: center;
        }

        /* Resto de estilos del sidebar y responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
        }

        /* Estilos responsive */
        @media (max-width: 1200px) {
            .form-container {
                max-width: 90%;
            }
            
            .content-header {
                max-width: 90%;
            }
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .form-container {
                padding: 1.5rem;
            }

            .content-header h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .form-container {
                padding: 1rem;
                margin: 1rem;
            }

            .row {
                margin: 0;
            }

            .col-md-6 {
                padding: 0;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .content-header h1 {
                font-size: 1.5rem;
            }

            .btn-neon {
                width: 100%;
                margin-top: 1rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 0.5rem;
            }

            .form-container {
                padding: 1rem;
                margin: 0.5rem;
            }

            .content-header {
                margin-bottom: 1rem;
            }

            .content-header h1 {
                font-size: 1.2rem;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .form-control {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }

            .btn-neon {
                padding: 0.5rem 1.5rem;
                font-size: 1rem;
            }

            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }

        /* Agregar botón para mostrar/ocultar sidebar en móvil */
        .toggle-sidebar {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: transparent;
            border: 2px solid var(--neon-color);
            color: var(--neon-color);
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        @media (max-width: 992px) {
            .toggle-sidebar {
                display: block;
            }

            .toggle-sidebar:hover {
                background: var(--neon-color);
                color: var(--bg-color);
                box-shadow: 0 0 15px var(--neon-color);
            }
        }

        /* Overlay para cuando el sidebar está activo en móvil */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media (max-width: 992px) {
            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    
    <!-- Botón Toggle Sidebar -->
    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay para sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
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
                <a href="create_user.php" class="nav-link active">
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
            <h1>Crear Nuevo Usuario</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert">
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
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="dni">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="departamento">Departamento</label>
                            <select class="form-control" id="departamento" name="departamento" required>
                                <option value="">Seleccione un departamento</option>
                                <option value="Departamento de Seguridad Vial N° 1">Departamento de Seguridad Vial N° 1</option>
                                <option value="Departamento de Seguridad Vial N° 2">Departamento de Seguridad Vial N° 2</option>
                                <option value="Departamento de Seguridad Vial N° 3">Departamento de Seguridad Vial N° 3</option>
                                <option value="Departamento de Seguridad Vial N° 4">Departamento de Seguridad Vial N° 4</option>
                                <option value="Departamento de Seguridad Vial N° 5">Departamento de Seguridad Vial N° 5</option>
                                <option value="Departamento de Tránsito">Departamento de Tránsito</option>
                                <option value="Brigada Especiales">Brigada Especiales</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rol">Rol</label>
                            <select class="form-control" id="rol" name="rol" required>
                                <option value="">Seleccione un rol</option>
                                <option value="admin">Administrador</option>
                                <option value="user">Usuario</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn-neon">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Particles.js Config
        particlesJS('particles-js', {
            particles: {
                number: { value: 50, density: { enable: true, value_area: 800 } },
                color: { value: '#ccff00' },
                shape: { type: 'circle' },
                opacity: {
                    value: 0.5,
                    random: true,
                    animation: { enable: true, speed: 1, minimumValue: 0.1, sync: false }
                },
                size: {
                    value: 3,
                    random: true,
                    animation: { enable: true, speed: 2, minimumValue: 0.1, sync: false }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#ccff00',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'repulse' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                }
            },
            retina_detect: true
        });

        // Funcionalidad del sidebar responsive
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleSidebar');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            toggleBtn.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            // Cerrar sidebar al cambiar el tamaño de la ventana
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html> 