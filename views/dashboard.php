<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Definir la ruta base del proyecto
$base_path = dirname(__DIR__);

// Incluir archivos necesarios
require_once $base_path . '/config/db.php';
require_once $base_path . '/controllers/LoginController.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts - Orbitron -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-color: #ccff00;
            --bg-dark: #1a1a1a;
            --sidebar-dark: rgba(0, 0, 0, 0.95);
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background-color: var(--bg-dark);
            color: white;
            min-height: 100vh;
            position: relative;
        }

        /* Hamburger Menu Button */
        .menu-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1040;
            background-color: transparent;
            border: 2px solid var(--neon-color);
            color: var(--neon-color);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .menu-btn:hover {
            background-color: var(--neon-color);
            color: var(--bg-dark);
            box-shadow: 0 0 15px var(--neon-color);
        }

        .menu-btn i {
            font-size: 1.5rem;
        }

        /* Offcanvas Styles */
        .offcanvas {
            background-color: var(--sidebar-dark);
            border-right: 2px solid var(--neon-color);
            box-shadow: 0 0 20px rgba(204, 255, 0, 0.2);
        }

        .offcanvas-header {
            position: relative;
            border-bottom: 1px solid rgba(204, 255, 0, 0.2);
            padding: 1.5rem;
            min-height: 80px;
        }

        .logo-container {
            padding: 0.5rem 0;
            text-align: center;
            width: 100%; /* Ajustado para ocupar todo el ancho */
            margin-right: 0; /* Eliminado el margen derecho */
        }

        .logo {
            max-width: 100px;
            height: auto;
            margin: 0 auto;
        }

        /* Close Button */
        .btn-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none !important;
            border: none;
            color: var(--neon-color) !important;
            font-size: 24px;
            width: 40px;
            height: 40px;
            padding: 0;
            opacity: 1 !important;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            background-image: none !important;
        }

        .btn-close::before {
            content: '×';
            font-size: 2rem;
            line-height: 1;
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
        }

        .btn-close:hover,
        .btn-close:focus,
        .btn-close:active {
            opacity: 1 !important;
            background: none !important;
            background-image: none !important;
            transform: rotate(90deg);
            box-shadow: 0 0 15px var(--neon-color);
            outline: none !important;
        }

        .user-info {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(204, 255, 0, 0.2);
        }

        .user-info h4 {
            color: var(--neon-color);
            margin-bottom: 10px;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            margin: 10px 0;
        }

        .nav-link {
            color: white;
            padding: 12px 20px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: var(--neon-color);
            background-color: rgba(204, 255, 0, 0.1);
            border-left: 3px solid var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
        }

        .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        /* Submenu styles */
        .submenu {
            padding-left: 20px;
            display: none;
        }

        .submenu.active {
            display: block;
        }

        .submenu .nav-link {
            padding: 8px 20px;
            font-size: 0.9em;
        }

        .nav-item.has-submenu > .nav-link::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .nav-item.has-submenu.active > .nav-link::after {
            transform: rotate(180deg);
        }

        .logout-btn {
            margin: 20px;
            background-color: transparent;
            border: 2px solid var(--neon-color);
            color: var(--neon-color);
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            background-color: var(--neon-color);
            color: var(--bg-dark);
            box-shadow: 0 0 20px var(--neon-color);
        }

        /* Main Content */
        .main-content {
            padding: 80px 30px 30px;
        }

        .welcome-title {
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
            margin-bottom: 30px;
        }

        .form-card {
            background-color: var(--sidebar-dark);
            border: 2px solid var(--neon-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 0 20px rgba(204, 255, 0, 0.1);
        }

        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(204, 255, 0, 0.2);
        }

        .form-card h3 {
            color: var(--neon-color);
            margin-bottom: 15px;
        }

        .form-card p {
            color: #ccc;
            margin-bottom: 20px;
        }

        .form-btn {
            background-color: transparent;
            border: 2px solid var(--neon-color);
            color: var(--neon-color);
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .form-btn:hover {
            background-color: var(--neon-color);
            color: var(--bg-dark);
            box-shadow: 0 0 20px var(--neon-color);
        }

        /* Backdrop blur effect */
        .offcanvas-backdrop.show {
            backdrop-filter: blur(5px);
            background-color: rgba(0, 0, 0, 0.7);
        }

        /* Form Buttons Container */
        .form-buttons-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(26, 26, 26, 0.98);
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .scrollable-content {
            min-height: 100vh;
            width: 100%;
            overflow-y: auto;
            padding: 2rem 1rem 80px;
            display: flex;
            flex-direction: column;
        }

        .form-buttons-wrapper {
            flex: 1;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .forms-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            margin: 2rem auto;
            width: 100%;
        }

        .form-card {
            flex: 1 1 350px;
            max-width: 350px;
            min-height: 220px;
            background: linear-gradient(145deg, rgba(26, 26, 26, 0.9), rgba(0, 0, 0, 0.95));
            border: 2px solid var(--neon-color);
            border-radius: 12px;
            padding: 2rem;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(204, 255, 0, 0.1), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .form-card:hover::before {
            transform: translateX(100%);
        }

        .form-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 0 30px rgba(204, 255, 0, 0.2);
            border-color: #fff;
        }

        .form-card i {
            font-size: 2.8rem;
            color: var(--neon-color);
            margin-bottom: 1.2rem;
            transition: all 0.3s ease;
        }

        .form-card:hover i {
            transform: scale(1.1);
            color: #fff;
        }

        .form-card h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: var(--neon-color);
            transition: all 0.3s ease;
            text-align: center;
        }

        .form-card:hover h3 {
            color: #fff;
        }

        .form-card p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            text-align: center;
            line-height: 1.5;
        }

        .form-card .status-badge {
            position: absolute;
            top: 0.8rem;
            right: 0.8rem;
            padding: 0.25rem 0.6rem;
            border-radius: 15px;
            font-size: 0.75rem;
            background: rgba(204, 255, 0, 0.2);
            color: var(--neon-color);
            border: 1px solid var(--neon-color);
        }

        /* Form Header Styles */
        .form-header {
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(-20px);
            animation: slideDownFade 0.8s ease forwards;
            text-align: center;
            padding: 0 1rem;
        }

        .form-title {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: #ffffff;
            font-size: 2rem;
            letter-spacing: 1px;
        }

        .form-title i {
            color: var(--neon-color);
            margin-right: 0.8rem;
            font-size: 2rem;
        }

        .form-divider {
            width: 60%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--neon-color), transparent);
            margin: 1rem auto;
            animation: pulseBorder 2s infinite;
        }

        .form-description {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            line-height: 1.5;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        .close-forms {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            color: var(--neon-color);
            font-size: 24px;
            width: 40px;
            height: 40px;
            padding: 0;
            opacity: 1;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .close-forms i {
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
        }

        .close-forms:hover {
            background-color: transparent;
            transform: rotate(90deg);
            box-shadow: 0 0 15px var(--neon-color);
        }

        .close-forms:hover i {
            color: var(--neon-color);
            text-shadow: 0 0 15px var(--neon-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .menu-btn {
                top: 15px;
                left: 15px;
                z-index: 1050;
            }

            .form-header {
                margin-top: 60px;
                padding: 0 15px;
            }

            .form-title {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }

            .form-description {
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }

            .form-buttons-container {
                padding: 15px;
            }

            .form-card {
                margin-bottom: 15px;
            }
        }

        /* Sidebar Styles */
        .sidebar-fixed {
            background-color: var(--sidebar-dark);
            border-right: 2px solid var(--neon-color);
            box-shadow: 0 0 20px rgba(204, 255, 0, 0.2);
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1030;
        }

        /* Main Content adjustments */
        @media (min-width: 768px) {
            .main-content {
                margin-left: 280px;
            }
        }

        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0;
                padding-top: 80px;
            }
        }

        /* Welcome Message Styles */
        .welcome-container {
            min-height: calc(100vh - 60px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        .welcome-message {
            max-width: 800px;
        }

        .welcome-message h1 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: white;
        }

        .welcome-message h1 span {
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
        }

        .welcome-message p {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #ffffff;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.8s ease-out forwards;
            animation-delay: 0.5s;
        }

        .welcome-message p span {
            color: var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .welcome-message h1 {
                font-size: 2rem;
            }

            .welcome-message p {
                font-size: 1rem;
            }
        }

        /* Particle effect styles */
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background-color: var(--neon-color);
            opacity: 0.3;
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            50% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-100vh) scale(1);
                opacity: 0;
            }
        }

        /* Text animation enhancements */
        .welcome-message {
            position: relative;
            z-index: 1;
        }

        .welcome-message h1 {
            animation: glow 2s ease-in-out infinite alternate;
        }

        .welcome-message p span {
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 5px var(--neon-color),
                           0 0 10px var(--neon-color),
                           0 0 15px var(--neon-color);
            }
            to {
                text-shadow: 0 0 10px var(--neon-color),
                           0 0 20px var(--neon-color),
                           0 0 30px var(--neon-color);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Grid effect */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(var(--neon-color) 1px, transparent 1px),
                            linear-gradient(90deg, var(--neon-color) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.05;
            z-index: 0;
        }

        /* Main content z-index adjustment */
        .main-content {
            position: relative;
            z-index: 1;
        }

        @keyframes slideDownFade {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseBorder {
            0% {
                opacity: 0.5;
                box-shadow: 0 0 5px var(--neon-color);
            }
            50% {
                opacity: 1;
                box-shadow: 0 0 15px var(--neon-color);
            }
            100% {
                opacity: 0.5;
                box-shadow: 0 0 5px var(--neon-color);
            }
        }
    </style>
</head>
<body>
    <!-- Grid Overlay -->
    <div class="grid-overlay"></div>

    <!-- Particles Container -->
    <div class="particles-container" id="particlesContainer"></div>

    <!-- Hamburger Menu Button (visible only on mobile) -->
    <button class="menu-btn d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Fixed Sidebar (visible only on desktop) -->
    <div class="sidebar-fixed d-none d-md-block">
        <div class="logo-container">
            <img src="../public/img/logo.png" alt="Logo Institucional" class="logo">
        </div>
        
        <div class="user-info">
            <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
            <p><?php echo htmlspecialchars($_SESSION['departamento']); ?></p>
        </div>

        <nav class="nav-menu">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </div>
            <div class="nav-item">
                <a href="/caminerasl/views/user/presencialidad/registro_personal.php" class="nav-link">
                    <i class="fas fa-calendar-check"></i> Presencialidad
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link" id="formulariosLinkDesktop">
                    <i class="fas fa-file-alt"></i> Formularios
                </a>
            </div>
        </nav>

        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>

    <!-- Offcanvas Sidebar (visible only on mobile) -->
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
        <div class="offcanvas-header">
            <div class="logo-container">
                <img src="../public/img/logo.png" alt="Logo Institucional" class="logo">
            </div>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
                <span aria-hidden="true"></span>
            </button>
        </div>
        <div class="offcanvas-body">
            <div class="user-info">
                <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                <p><?php echo htmlspecialchars($_SESSION['departamento']); ?></p>
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/caminerasl/views/user/presencialidad/registro_personal.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i> Presencialidad
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link" id="formulariosLinkMobile">
                        <i class="fas fa-file-alt"></i> Formularios
                    </a>
                </div>
            </nav>

            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="welcome-container">
            <div class="welcome-message">
                <h1><span>Bienvenido</span>, <?php echo htmlspecialchars($_SESSION['username']); ?>.</h1>
                <p>Muy pronto verás en esta sección <span>nuevas funciones</span> y <span>novedades</span> del sistema.</p>
            </div>
        </div>
    </div>

    <!-- Form Buttons Container -->
    <div class="form-buttons-container" id="formButtonsContainer">
        <div class="scrollable-content">
            <button class="close-forms" id="closeFormsBtn">
                <i class="fas fa-times"></i>
            </button>
            <div class="form-buttons-wrapper">
                <!-- Form Header -->
                <div class="form-header">
                    <h2 class="form-title">
                        <i class="fas fa-folder-open"></i>
                        Gestión de Formularios Operativos
                    </h2>
                    <div class="form-divider"></div>
                    <p class="form-description">
                        Seleccioná uno de los formularios disponibles para registrar tus actividades operativas del día.
                    </p>
                </div>

                <div class="container-fluid p-0">
                    <div class="row g-0 justify-content-center">
                        <div class="col-12 col-md-10">
                            <div class="forms-grid">
                                <a href="form_operativo_a.php" class="form-card col-12 mb-4">
                                    <i class="fas fa-play-circle"></i>
                                    <h3>Inicio de Operatividad</h3>
                                    <p>Registra el inicio de actividades y personal asignado</p>
                                </a>
                                
                                <a href="form_operativo_b.php" class="form-card col-12 mb-4">
                                    <i class="fas fa-tasks"></i>
                                    <h3>Operatividad</h3>
                                    <p>Gestiona y actualiza las operaciones en curso</p>
                                </a>

                                <a href="form_procedimiento.php" class="form-card col-12 mb-4">
                                    <i class="fas fa-clipboard-list"></i>
                                    <h3>PROCEDIMIENTO</h3>
                                    <p>Gestiona y registra los procedimientos operativos</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formButtonsContainer = document.getElementById('formButtonsContainer');
            const mainContent = document.getElementById('mainContent');
            const closeFormsBtn = document.getElementById('closeFormsBtn');
            
            // Particle effect
            const particlesContainer = document.getElementById('particlesContainer');
            const numberOfParticles = 50;

            function createParticle() {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random position
                particle.style.left = Math.random() * 100 + '%';
                
                // Random size
                const size = Math.random() * 3 + 1;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                
                // Random animation duration
                const duration = Math.random() * 10 + 5;
                particle.style.animation = `float ${duration}s infinite linear`;
                
                // Random delay
                particle.style.animationDelay = -Math.random() * duration + 's';
                
                return particle;
            }

            // Create particles
            for (let i = 0; i < numberOfParticles; i++) {
                particlesContainer.appendChild(createParticle());
            }

            // Función para mostrar los botones de formularios
            function showFormButtons() {
                formButtonsContainer.style.display = 'flex';
                mainContent.style.opacity = '0.1';
            }

            // Botón Formularios en escritorio
            const formulariosLinkDesktop = document.getElementById('formulariosLinkDesktop');
            formulariosLinkDesktop.addEventListener('click', function(e) {
                e.preventDefault();
                showFormButtons();
            });

            // Botón Formularios en móvil
            const formulariosLinkMobile = document.getElementById('formulariosLinkMobile');
            formulariosLinkMobile.addEventListener('click', function(e) {
                e.preventDefault();
                showFormButtons();
                // Cerrar el menú lateral en móviles
                const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('sideMenu'));
                if (offcanvas) {
                    offcanvas.hide();
                }
            });

            // Cerrar los botones de formularios
            closeFormsBtn.addEventListener('click', function() {
                formButtonsContainer.style.display = 'none';
                mainContent.style.opacity = '1';
            });

            // Text animation for spans
            const spans = document.querySelectorAll('.welcome-message p span');
            spans.forEach((span, index) => {
                span.style.animationDelay = `${index * 0.2}s`;
            });
        });

        // Submenu functionality
        document.querySelectorAll('.nav-item.has-submenu > .nav-link').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const submenu = parent.querySelector('.submenu');
                
                // Toggle active class on parent
                parent.classList.toggle('active');
                
                // Toggle submenu visibility
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';
                } else {
                    submenu.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html> 