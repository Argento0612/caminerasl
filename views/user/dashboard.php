<?php
session_start();

// Definir la ruta base del proyecto
$base_path = dirname(dirname(__DIR__));

// Incluir archivos necesarios
require_once $base_path . '/config/db.php';
require_once $base_path . '/controllers/LoginController.php';

// Verificar si el usuario está logueado
$loginController = new LoginController(new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS
));

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener las actas del usuario actual
    $stmt = $db->prepare("SELECT * FROM actas WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $actas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error de conexión: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Sistema de Actas</title>
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

        .actas-table {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 20px var(--neon-blue),
                        inset 0 0 20px var(--neon-purple);
            margin-top: 2rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        th {
            color: var(--neon-blue);
            font-family: 'Orbitron', sans-serif;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
        }

        td {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 10px var(--neon-blue);
        }

        .action-btn {
            background: transparent;
            border: 1px solid var(--neon-blue);
            color: var(--neon-blue);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 0 0.25rem;
        }

        .action-btn:hover {
            background: var(--neon-blue);
            color: var(--bg-color);
            box-shadow: 0 0 10px var(--neon-blue);
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

        .section-title {
            color: var(--neon-blue);
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px var(--neon-blue);
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
            <p><?php echo $_SESSION['departamento']; ?></p>
        </div>

        <nav>
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-file-alt"></i>
                    Actas
                </a>
            </div>
            <div class="nav-item">
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    Mi Perfil
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
        <h2 class="section-title">Nueva Acta</h2>
        
        <div class="form-container">
            <form action="create_acta.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_infraccion">Fecha de Infracción</label>
                            <input type="datetime-local" class="form-control" id="fecha_infraccion" name="fecha_infraccion" required>
                        </div>
                        <div class="form-group">
                            <label for="lugar">Lugar</label>
                            <input type="text" class="form-control" id="lugar" name="lugar" required>
                        </div>
                        <div class="form-group">
                            <label for="tipo_infraccion">Tipo de Infracción</label>
                            <select class="form-control" id="tipo_infraccion" name="tipo_infraccion" required>
                                <option value="">Seleccione el tipo de infracción</option>
                                <option value="Exceso de velocidad">Exceso de velocidad</option>
                                <option value="Semáforo en rojo">Semáforo en rojo</option>
                                <option value="Estacionamiento prohibido">Estacionamiento prohibido</option>
                                <option value="Documentación vencida">Documentación vencida</option>
                                <option value="Alcoholemia positiva">Alcoholemia positiva</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="datos_infractor">Datos del Infractor</label>
                            <textarea class="form-control" id="datos_infractor" name="datos_infractor" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción de la Infracción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn-neon">Crear Acta</button>
                </div>
            </form>
        </div>

        <h2 class="section-title">Mis Actas</h2>
        
        <div class="actas-table">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Tipo de Infracción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($actas)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay actas registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($actas as $acta): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($acta['fecha_infraccion'])); ?></td>
                                <td><?php echo htmlspecialchars($acta['lugar']); ?></td>
                                <td><?php echo htmlspecialchars($acta['tipo_infraccion']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $acta['estado'] === 'Pendiente' ? 'warning' : 'success'; ?>">
                                        <?php echo htmlspecialchars($acta['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_acta.php?id=<?php echo $acta['id']; ?>" class="action-btn" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_acta.php?id=<?php echo $acta['id']; ?>" class="action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="print_acta.php?id=<?php echo $acta['id']; ?>" class="action-btn" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 