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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-color: #ccff00;
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.95);
            border-right: 1px solid var(--neon-color);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: 5px 0 15px rgba(204, 255, 0, 0.1);
        }

        .logo-container {
            padding: 1rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            max-width: 180px;
            height: auto;
            margin: 0 auto;
            filter: drop-shadow(0 0 10px var(--neon-color));
        }

        .admin-info {
            text-align: center;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .admin-title {
            color: var(--neon-color);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 10px var(--neon-color);
        }

        .admin-subtitle {
            color: var(--text-color);
            font-size: 1rem;
            opacity: 0.8;
        }

        .nav-menu {
            padding: 0 1rem;
        }

        .nav-item {
            margin-bottom: 1rem;
        }

        .nav-link {
            color: var(--text-color);
            text-decoration: none;
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-radius: 5px;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background: rgba(204, 255, 0, 0.1);
            color: var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        .logout-btn {
            margin: 1rem;
            padding: 0.8rem;
            background: transparent;
            border: 1px solid var(--neon-color);
            color: var(--neon-color);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            background: var(--neon-color);
            color: var(--bg-color);
            box-shadow: 0 0 15px var(--neon-color);
        }

        .logout-btn i {
            margin-right: 10px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .welcome-message {
            text-align: center;
        }

        .welcome-title {
            font-size: 2.5rem;
            color: var(--neon-color);
            margin-bottom: 1.5rem;
            text-shadow: 0 0 15px var(--neon-color);
        }

        .welcome-text {
            font-size: 1.2rem;
            color: var(--text-color);
        }

        .welcome-text span {
            color: var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
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
        <div class="logo-container">
            <img src="../../public/img/logo.png" alt="Logo" class="logo">
        </div>
        
        <div class="admin-info">
            <div class="admin-title">admin</div>
            <div class="admin-subtitle">Administración</div>
        </div>

        <nav class="nav-menu">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    Inicio
                </a>
            </div>
            <div class="nav-item">
                <a href="/caminerasl/views/admin/form_presencialidad.php" class="nav-link">
                    <i class="fas fa-calendar-check"></i>
                    Presencialidad
                </a>
            </div>
            <div class="nav-item">
                <a href="create_user.php" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    Crear Usuario
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#estadisticasSubmenu">
                    <i class="fas fa-chart-bar"></i>
                    Estadísticas
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="estadisticasSubmenu">
                    <div class="nav-item">
                        <a href="estadisticas/generales.php" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            Estadísticas Generales
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="estadisticas/departamentos.php" class="nav-link">
                            <i class="fas fa-building"></i>
                            Estadísticas por Departamento
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="estadisticas/conductores.php" class="nav-link">
                            <i class="fas fa-user"></i>
                            Estadísticas de Conductores
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="estadisticas/vehiculos.php" class="nav-link">
                            <i class="fas fa-car"></i>
                            Estadísticas de Vehículos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="estadisticas/infracciones.php" class="nav-link">
                            <i class="fas fa-exclamation-triangle"></i>
                            Estadísticas de Infracciones
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="estadisticas/alcoholemia.php" class="nav-link">
                            <i class="fas fa-wine-bottle"></i>
                            Estadísticas de Alcoholemia
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="estadisticas/reportes.php" class="nav-link">
                            <i class="fas fa-file-alt"></i>
                            Reportes Personalizados
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Cerrar Sesión
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="welcome-title">Panel de Administración</h1>
        <p>Bienvenido al panel de administración. Aquí podrás gestionar usuarios, ver estadísticas y más.</p>
        
        <!-- Nueva sección de Presencialidad -->
        <div class="card mt-4">
            <div class="card-header">
                <h2>Registro de Personal</h2>
            </div>
            <div class="card-body">
                <form class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha">
                        </div>
                        <div class="col-md-4">
                            <label for="personal" class="form-label">Personal de Guardia</label>
                            <input type="text" class="form-control" id="personal" name="personal" placeholder="Buscar por nombre...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Departamento</th>
                                <th>Lugar de Servicio</th>
                                <th>Personal de Guardia</th>
                                <th>Modalidad de Trabajo</th>
                                <th>Personal Ausente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se cargarán los datos dinámicamente -->
                            <tr>
                                <td>2023-10-01 08:00</td>
                                <td>Departamento A</td>
                                <td>Lugar 1</td>
                                <td>Juan Pérez</td>
                                <td>Presencial</td>
                                <td>No</td>
                            </tr>
                            <tr>
                                <td>2023-10-01 09:00</td>
                                <td>Departamento B</td>
                                <td>Lugar 2</td>
                                <td>María López</td>
                                <td>Remoto</td>
                                <td>Sí</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 