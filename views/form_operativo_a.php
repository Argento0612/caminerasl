<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once('../config/db.php');

// Obtener jefes de operativo
$jefes = [];
try {
    $stmt = $conn->prepare("SELECT nombre FROM jefes_operativo ORDER BY nombre ASC");
    if (!$stmt) {
        die('Error en la consulta SQL (jefes): ' . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $jefes = [];
    while ($row = $result->fetch_assoc()) {
        $jefes[] = $row['nombre'];
    }
} catch (Exception $e) {
    error_log("Error al cargar jefes: " . $e->getMessage());
    $jefes = []; // Asegurar que sea un array vacío
}

// Obtener personal operativo
$personal = [];
try {
    $stmt = $conn->prepare("SELECT nombre FROM personal_operativo ORDER BY nombre ASC");
    if (!$stmt) {
        die('Error en la consulta SQL (personal): ' . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $personal = [];
    while ($row = $result->fetch_assoc()) {
        $personal[] = $row['nombre'];
    }
} catch (Exception $e) {
    error_log("Error al cargar personal: " . $e->getMessage());
    $personal = []; // Asegurar que sea un array vacío
}

// Obtener móviles
$moviles = [];
try {
    $stmt = $conn->prepare("SELECT numero FROM moviles_operativo WHERE activo = TRUE ORDER BY numero ASC");
    if (
        $stmt && $stmt->execute()
    ) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $moviles[] = $row['numero'];
        }
    }
} catch (Exception $e) {
    error_log("Error al cargar móviles: " . $e->getMessage());
    $moviles = []; // Asegurar que sea un array vacío
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inicio de Operatividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: #0a0a0a;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1200px;
            padding: 2rem;
        }

        .form-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.5rem;
            font-weight: 600;
            color: #c8ff00;
            text-shadow: 0 0 10px rgba(200, 255, 0, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
        }

        .form-container {
            background: rgba(20, 20, 20, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(200, 255, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(200, 255, 0, 0.1);
        }

        .form-section {
            background: rgba(30, 30, 30, 0.5);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(200, 255, 0, 0.1);
        }

        .form-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(200, 255, 0, 0.2);
        }

        .section-title {
            color: #c8ff00;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 5px rgba(200, 255, 0, 0.3);
        }

        select, .form-control {
            background: rgba(15, 15, 15, 0.9) !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 0.75rem !important;
            transition: all 0.3s ease !important;
        }

        select:focus, .form-control:focus {
            border-color: #c8ff00 !important;
            box-shadow: 0 0 10px rgba(200, 255, 0, 0.3) !important;
            background: rgba(20, 20, 20, 0.9) !important;
        }

        .checkbox-container {
            max-height: 300px;
            overflow-y: auto;
            padding: 1rem;
            border-radius: 10px;
            background: rgba(25, 25, 25, 0.5);
            scrollbar-width: thin;
            scrollbar-color: #c8ff00 #1a1a1a;
        }

        .checkbox-container::-webkit-scrollbar {
            width: 6px;
        }

        .checkbox-container::-webkit-scrollbar-track {
            background: #1a1a1a;
            border-radius: 3px;
        }

        .checkbox-container::-webkit-scrollbar-thumb {
            background: #c8ff00;
            border-radius: 3px;
        }

        .form-check {
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .form-check:hover {
            background: rgba(200, 255, 0, 0.1);
        }

        .form-check-input {
            border-color: rgba(200, 255, 0, 0.5);
        }

        .form-check-input:checked {
            background-color: #c8ff00;
            border-color: #c8ff00;
        }

        .btn-add {
            background: transparent;
            border: 1px solid #c8ff00;
            color: #c8ff00;
            padding: 0.5rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-top: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-add:hover {
            background: #c8ff00;
            color: #000;
            box-shadow: 0 0 15px rgba(200, 255, 0, 0.4);
            transform: translateY(-2px);
        }

        .btn-submit {
            background: #c8ff00;
            color: #000;
            padding: 1rem 3rem;
            border-radius: 15px;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            margin-top: 2rem;
            width: auto;
            display: block;
            margin: 2rem auto 0;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(200, 255, 0, 0.5);
            background: #d4ff33;
        }

        .help-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: center;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 5px rgba(200, 255, 0, 0.5);
            }
            to {
                text-shadow: 0 0 20px rgba(200, 255, 0, 0.8);
            }
        }

        /* Animaciones para elementos al cargar */
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { 
                transform: translateY(30px);
                opacity: 0;
            }
            to { 
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Estilos para los botones de navegación */
        .nav-button {
            position: fixed;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(20, 20, 20, 0.9);
            border: 1px solid rgba(200, 255, 0, 0.3);
            color: #c8ff00;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            z-index: 1050;
        }

        .nav-button:hover, .nav-button:focus {
            background: rgba(200, 255, 0, 0.2);
            border-color: #c8ff00;
            box-shadow: 0 0 15px rgba(200, 255, 0, 0.3);
            color: #c8ff00;
            text-decoration: none;
        }

        /* Botón de cerrar - siempre visible en la esquina superior derecha */
        .close-button {
            top: 10px;
            right: 10px;
        }

        /* Botón de menú - solo visible en móvil/tablet, esquina superior izquierda */
        .menu-button {
            top: 10px;
            left: 10px;
            display: none;
        }

        /* Ajustes responsive */
        @media (max-width: 991px) {
            .container {
                padding-top: 4rem;
            }

            .menu-button {
                display: flex;
            }

            .form-title {
                font-size: 1.8rem;
                padding-top: 1rem;
            }
        }

        @media (min-width: 992px) {
            .container {
                padding-top: 4rem;
            }
        }

        /* Estilos para el menú lateral */
        .offcanvas {
            background: rgba(15, 15, 15, 0.98);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(200, 255, 0, 0.1);
        }

        .offcanvas-header {
            border-bottom: 1px solid rgba(200, 255, 0, 0.1);
            padding: 1.5rem;
        }

        .offcanvas-title {
            color: #c8ff00;
            font-size: 1.5rem;
            text-shadow: 0 0 10px rgba(200, 255, 0, 0.3);
        }

        .offcanvas-body {
            padding: 1.5rem;
        }

        .menu-item {
            color: #fff;
            text-decoration: none;
            padding: 1rem;
            margin: 0.5rem 0;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            background: rgba(30, 30, 30, 0.5);
            border: 1px solid rgba(200, 255, 0, 0.1);
        }

        .menu-item:hover {
            background: rgba(200, 255, 0, 0.1);
            color: #c8ff00;
            transform: translateX(5px);
            box-shadow: 0 0 15px rgba(200, 255, 0, 0.2);
        }

        .menu-item i {
            width: 25px;
            text-align: center;
        }

        /* Animación para los items del menú */
        .offcanvas.show .menu-item {
            animation: slideInLeft 0.3s ease forwards;
            opacity: 0;
            transform: translateX(-20px);
        }

        .offcanvas.show .menu-item:nth-child(1) { animation-delay: 0.1s; }
        .offcanvas.show .menu-item:nth-child(2) { animation-delay: 0.2s; }
        .offcanvas.show .menu-item:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Ajuste para el botón de cerrar del offcanvas */
        .offcanvas .btn-close-white {
            filter: invert(1) brightness(200%) sepia(100%) saturate(1000%) hue-rotate(90deg);
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .offcanvas .btn-close-white:hover {
            opacity: 1;
            transform: rotate(90deg);
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
            background-color: rgba(0, 0, 0, 0.95);
            border-right: 2px solid #c8ff00;
            box-shadow: 0 0 20px rgba(200, 255, 0, 0.2);
        }

        .offcanvas-header {
            position: relative;
            border-bottom: 1px solid rgba(200, 255, 0, 0.2);
            padding: 1.5rem;
            min-height: 80px;
        }

        .logo-container {
            padding: 0.5rem 0;
            text-align: center;
            margin-right: 40px;
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
            background-color: transparent;
            border: none;
            color: #c8ff00;
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

        .btn-close::before {
            content: '×';
            font-size: 2rem;
            line-height: 1;
            color: #c8ff00;
            text-shadow: 0 0 10px #c8ff00;
        }

        .btn-close:hover {
            background-color: transparent;
            transform: rotate(90deg);
            box-shadow: 0 0 15px #c8ff00;
        }

        .btn-close:hover::before {
            color: #c8ff00;
            text-shadow: 0 0 15px #c8ff00;
        }

        .user-info {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(200, 255, 0, 0.2);
        }

        .user-info h4 {
            color: #c8ff00;
            margin-bottom: 10px;
        }

        .user-info p {
            color: #00ffff !important;
            margin-bottom: 0;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }

        .department-text {
            color: #00ffff !important;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
            font-size: 1.1rem;
            font-weight: 500;
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
            color: #c8ff00;
            background-color: rgba(200, 255, 0, 0.1);
            border-left: 3px solid #c8ff00;
            text-shadow: 0 0 10px #c8ff00;
        }

        .nav-link i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .logout-btn {
            margin: 20px;
            background-color: transparent;
            border: 2px solid #c8ff00;
            color: #c8ff00;
            padding: 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            width: calc(100% - 40px);
        }

        .logout-btn:hover {
            background-color: #c8ff00;
            color: #1a1a1a;
            box-shadow: 0 0 20px #c8ff00;
        }

        /* Backdrop blur effect */
        .offcanvas-backdrop.show {
            backdrop-filter: blur(5px);
            background-color: rgba(0, 0, 0, 0.7);
        }

        @media (max-width: 991px) {
            .container {
                padding-top: 4rem;
            }

            .menu-btn {
                display: flex;
            }

            .form-title {
                font-size: 1.8rem;
                padding-top: 1rem;
            }

            .logout-btn {
                padding: 20px;
                margin: 15px;
                font-size: 1.2rem;
                width: calc(100% - 30px);
            }
        }
    </style>
</head>
<body>
    <!-- Hamburger Menu Button -->
    <button class="menu-btn d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
        <div class="offcanvas-header">
            <div class="logo-container">
                <img src="../public/img/logo.png" alt="Logo Institucional" class="logo">
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="user-info">
                <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                <p class="department-text"><?php echo htmlspecialchars($_SESSION['departamento']); ?></p>
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </div>
                <div class="nav-item">
                    <a href="../views/dashboard.php" class="nav-link">
                        <i class="fas fa-file-alt"></i> Formularios
                    </a>
                </div>
                
            </nav>

            <form action="../controllers/logout.php" method="POST" class="m-0">
                <button type="submit" class="logout-btn w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- Botón de cerrar (siempre visible) -->
    <a href="../views/dashboard.php" class="nav-button close-button" title="Volver al Dashboard">
        <i class="fas fa-times"></i>
    </a>

    <div class="container">
        <h1 class="form-title animate-fade-in">
            <i class="fas fa-clipboard-list me-2"></i>
            Formulario de Inicio de Operatividad
        </h1>
        
        <div class="form-container animate-slide-up">
            <form id="formOperativoA" action="../controllers/formHandlerA.php" method="POST">
                <div class="row g-4">
                    <!-- Jefe de Operativo -->
                    <div class="col-12 col-md-4">
                        <div class="form-section h-100">
                            <h3 class="section-title">
                                <i class="fas fa-user-tie me-2"></i>
                                Jefe de Operativo
                            </h3>
                            <select name="jefe_operativo" class="form-select mb-3" required>
                                <option value="" disabled selected>Seleccionar Jefe</option>
                                <?php foreach ($jefes as $jefe): ?>
                                    <option value="<?php echo htmlspecialchars($jefe); ?>">
                                        <?php echo htmlspecialchars($jefe); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalAgregarJefe">
                                <i class="fas fa-plus me-2"></i>
                                Agregar Jefe
                            </button>
                        </div>
                    </div>

                    <!-- Móvil -->
                    <div class="col-12 col-md-4">
                        <div class="form-section h-100">
                            <h3 class="section-title">
                                <i class="fas fa-car me-2"></i>
                                Móvil
                            </h3>
                            <select name="movil" class="form-select mb-3">
                                <option value="" disabled selected>Seleccionar Móvil</option>
                                <?php foreach ($moviles as $movil): ?>
                                    <option value="<?php echo htmlspecialchars($movil); ?>">
                                        <?php echo htmlspecialchars($movil); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalAgregarMovil">
                                <i class="fas fa-plus me-2"></i>
                                Agregar Móvil
                            </button>
                        </div>
                    </div>

                    <!-- Personal Afectado -->
                    <div class="col-12 col-md-4">
                        <div class="form-section h-100">
                            <h3 class="section-title">
                                <i class="fas fa-users me-2"></i>
                                Personal Afectado
                            </h3>
                            <div class="checkbox-container mb-3">
                                <?php foreach ($personal as $persona): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="personal[]" 
                                               value="<?php echo htmlspecialchars($persona); ?>"
                                               id="personal_<?php echo htmlspecialchars($persona); ?>">
                                        <label class="form-check-label" 
                                               for="personal_<?php echo htmlspecialchars($persona); ?>">
                                            <?php echo htmlspecialchars($persona); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($personal)): ?>
                                    <div class="text-center text-muted">No hay personal registrado</div>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalAgregarPersonal">
                                <i class="fas fa-plus me-2"></i>
                                Agregar Personal
                            </button>
                            <div class="help-text">
                                Selecciona uno o más miembros del personal marcando las casillas
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit mt-4">
                    <i class="fas fa-paper-plane me-2"></i>
                    Enviar Formulario
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Agregar Jefe -->
    <div class="modal fade" id="modalAgregarJefe" tabindex="-1" aria-labelledby="modalAgregarJefeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-light" id="modalAgregarJefeLabel">Agregar Nuevo Jefe</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarJefe">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreJefe" class="form-label text-light">Nombre del Jefe</label>
                            <input type="text" class="form-control" id="nombreJefe" name="nombre" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Móvil -->
    <div class="modal fade" id="modalAgregarMovil" tabindex="-1" aria-labelledby="modalAgregarMovilLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-light" id="modalAgregarMovilLabel">Agregar Nuevo Móvil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarMovil">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="numeroMovil" class="form-label text-light">Número de Móvil</label>
                            <input type="text" class="form-control" id="numeroMovil" name="movil" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Personal -->
    <div class="modal fade" id="modalAgregarPersonal" tabindex="-1" aria-labelledby="modalAgregarPersonalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-light" id="modalAgregarPersonalLabel">Agregar Nuevo Personal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarPersonal">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombrePersonal" class="form-label text-light">Nombre del Personal</label>
                            <input type="text" class="form-control" id="nombrePersonal" name="nombre" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para manejar el envío de formularios modales
        function handleModalForm(formId, url, selectId) {
            document.getElementById(formId).addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cerrar el modal
                        bootstrap.Modal.getInstance(document.querySelector(`#modal${selectId}`)).hide();
                        
                        // Actualizar el select correspondiente
                        if (selectId === 'AgregarJefe') {
                            const select = document.querySelector('select[name="jefe_operativo"]');
                            if (select) {
                                const option = new Option(data.nombre, data.nombre);
                                select.add(option);
                                select.value = data.nombre;
                            }
                        } else if (selectId === 'AgregarMovil') {
                            const select = document.querySelector('select[name="movil"]');
                            if (select) {
                                const option = new Option(data.movil, data.movil);
                                select.add(option);
                                select.value = data.movil;
                            }
                        } else if (selectId === 'AgregarPersonal') {
                            const container = document.querySelector('.checkbox-container');
                            // Remover mensaje de "No hay personal registrado" si existe
                            const emptyMessage = container.querySelector('.text-muted');
                            if (emptyMessage) {
                                emptyMessage.remove();
                            }
                            
                            const div = document.createElement('div');
                            div.className = 'form-check animate-fade-in';
                            div.innerHTML = `
                                <input class="form-check-input" type="checkbox" 
                                       name="personal[]" 
                                       value="${data.nombre}"
                                       id="personal_${data.nombre}"
                                       checked>
                                <label class="form-check-label" 
                                       for="personal_${data.nombre}">
                                    ${data.nombre}
                                </label>
                            `;
                            container.appendChild(div);
                        }

                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Registro agregado correctamente',
                            timer: 2000,
                            showConfirmButton: false,
                            background: '#1a1a1a',
                            color: '#fff',
                            iconColor: '#c8ff00'
                        });

                        // Limpiar el formulario modal
                        this.reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al guardar el registro',
                            background: '#1a1a1a',
                            color: '#fff',
                            confirmButtonColor: '#c8ff00'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la solicitud',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#c8ff00'
                    });
                });
            });
        }

        // Configurar manejadores para cada formulario modal
        handleModalForm('formAgregarJefe', '../controllers/guardar_jefe.php', 'AgregarJefe');
        handleModalForm('formAgregarMovil', '../controllers/guardar_movil.php', 'AgregarMovil');
        handleModalForm('formAgregarPersonal', '../controllers/guardar_personal.php', 'AgregarPersonal');

        // Código existente del formulario principal
        document.getElementById('formOperativoA').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const personalSeleccionado = document.querySelectorAll('input[name="personal[]"]:checked');
            if (personalSeleccionado.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe seleccionar al menos un personal afectado',
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#c8ff00'
                });
                return;
            }

            const formData = new FormData(this);

            fetch('../controllers/formHandlerA.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#1a1a1a',
                        color: '#fff',
                        iconColor: '#c8ff00'
                    }).then(() => {
                        this.reset();
                        document.querySelectorAll('input[name="personal[]"]').forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        document.querySelector('select[name="jefe_operativo"]').selectedIndex = 0;
                        document.querySelector('select[name="movil"]').selectedIndex = 0;
                        window.scrollTo(0, 0);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#c8ff00'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al procesar el formulario',
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#c8ff00'
                });
            });
        });

        // Agregar animación al abrir el menú
        const offcanvasMenu = document.getElementById('sideMenu');
        if (offcanvasMenu) {
            offcanvasMenu.addEventListener('show.bs.offcanvas', function () {
                const menuItems = this.querySelectorAll('.nav-item');
                menuItems.forEach((item, index) => {
                    item.style.animationDelay = `${(index + 1) * 0.1}s`;
                });
            });
        }
    </script>
</body>
</html> 