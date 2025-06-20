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

// Obtener la conexión a la base de datos
$db = getDB();

// Procesar la búsqueda
$where = "1=1";
$params = [];
$types = "";

if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $where .= " AND DATE(fecha_hora) = ?";
    $params[] = $_GET['fecha'];
    $types .= "s";
}

if (isset($_GET['personal']) && !empty($_GET['personal'])) {
    $where .= " AND personal_guardia LIKE ?";
    $params[] = "%" . $_GET['personal'] . "%";
    $types .= "s";
}

// Consulta SQL para obtener los registros
$sql = "SELECT * FROM registros_presencialidad WHERE $where ORDER BY fecha DESC";
$stmt = $db->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$registros = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Presencialidad - Panel de Administración</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-cyan: #00ffff;
            --neon-fuchsia: #ff00ff;
            --bg-dark: #0a0a0a;
            --bg-darker: #050505;
            --text-color: #ffffff;
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-color);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Grid Overlay */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(var(--neon-cyan) 1px, transparent 1px),
                linear-gradient(90deg, var(--neon-cyan) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.05;
            z-index: 0;
        }

        /* Card Styles */
        .card {
            background: rgba(5, 5, 5, 0.95);
            border: 1px solid var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .card-header {
            background: rgba(0, 255, 255, 0.1);
            border-bottom: 1px solid var(--neon-cyan);
            color: var(--neon-cyan);
            text-shadow: 0 0 10px var(--neon-cyan);
        }

        /* Form Controls */
        .form-control {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--neon-cyan);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.7);
            border-color: var(--neon-fuchsia);
            box-shadow: 0 0 15px rgba(255, 0, 255, 0.3);
            color: var(--text-color);
        }

        .form-label {
            color: var(--neon-cyan);
            text-shadow: 0 0 5px var(--neon-cyan);
        }

        /* Button Styles */
        .btn-primary {
            background: transparent;
            border: 1px solid var(--neon-cyan);
            color: var(--neon-cyan);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--neon-cyan);
            color: var(--bg-darker);
            box-shadow: 0 0 20px var(--neon-cyan);
        }

        /* Table Styles */
        .table {
            color: var(--text-color);
            border-color: rgba(0, 255, 255, 0.1);
        }

        .table thead th {
            background: rgba(0, 255, 255, 0.1);
            color: var(--neon-cyan);
            border-bottom: 2px solid var(--neon-cyan);
            text-shadow: 0 0 5px var(--neon-cyan);
        }

        .table tbody td {
            border-color: rgba(0, 255, 255, 0.1);
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(0, 255, 255, 0.05);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.1);
        }

        /* No Results Message */
        .table tbody tr td.text-center {
            background: rgba(5, 5, 5, 0.95);
            color: var(--neon-cyan);
            text-shadow: 0 0 5px var(--neon-cyan);
            padding: 2rem;
            font-size: 1.1em;
            letter-spacing: 1px;
        }

        /* Badge Styles */
        .badge {
            padding: 0.5em 1em;
            border-radius: 20px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-presente {
            background: rgba(0, 255, 255, 0.1);
            color: var(--neon-cyan);
            border: 1px solid var(--neon-cyan);
            text-shadow: 0 0 5px var(--neon-cyan);
        }

        .badge-ausente {
            background: rgba(255, 0, 255, 0.1);
            color: var(--neon-fuchsia);
            border: 1px solid var(--neon-fuchsia);
            text-shadow: 0 0 5px var(--neon-fuchsia);
        }

        /* Animations */
        @keyframes glow {
            0% {
                box-shadow: 0 0 5px var(--neon-cyan),
                           0 0 10px var(--neon-cyan),
                           0 0 15px var(--neon-cyan);
            }
            50% {
                box-shadow: 0 0 10px var(--neon-cyan),
                           0 0 20px var(--neon-cyan),
                           0 0 30px var(--neon-cyan);
            }
            100% {
                box-shadow: 0 0 5px var(--neon-cyan),
                           0 0 10px var(--neon-cyan),
                           0 0 15px var(--neon-cyan);
            }
        }

        .card {
            animation: glow 2s infinite;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem;
            }
            
            .card {
                margin: 0;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Grid Overlay -->
    <div class="grid-overlay"></div>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Registro de Presencialidad
                        </h2>
                        <a href="/caminerasl/views/admin/dashboard.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Formulario de búsqueda -->
                        <form method="GET" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" 
                                           value="<?php echo isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="personal" class="form-label">Personal de Guardia</label>
                                    <input type="text" class="form-control" id="personal" name="personal" 
                                           placeholder="Buscar por nombre..." 
                                           value="<?php echo isset($_GET['personal']) ? htmlspecialchars($_GET['personal']) : ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tabla de resultados -->
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Departamento</th>
                                        <th>Lugar de Servicio</th>
                                        <th>Personal de Guardia</th>
                                        <th>Modalidad</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($registros)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No se encontraron registros</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($registros as $registro): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($registro['fecha_hora']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['departamento']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['lugar_servicio']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['personal_guardia']); ?></td>
                                                <td><?php echo htmlspecialchars($registro['modalidad']); ?></td>
                                                <td>
                                                    <?php if ($registro['personal_ausente']): ?>
                                                        <span class="badge badge-ausente">Ausente</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-presente">Presente</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 