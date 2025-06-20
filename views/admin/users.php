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

// Inicializar la conexión a la base de datos
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar búsqueda y filtros
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Construir la consulta SQL base
$sql = "SELECT id, username, dni, email, departamento, rol, created_at FROM users WHERE 1=1";
$params = [];

// Aplicar filtro de búsqueda si existe
if (!empty($search)) {
    $sql .= " AND (username LIKE ? OR email LIKE ? OR dni LIKE ? OR departamento LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

// Aplicar filtro por departamento
if ($filter !== 'all' && !empty($filter)) {
    $sql .= " AND departamento = ?";
    $params[] = $filter;
}

// Ordenar por fecha de creación (más recientes primero)
$sql .= " ORDER BY created_at DESC";

// Preparar y ejecutar la consulta
$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista única de departamentos para el filtro
$deptStmt = $db->query("SELECT DISTINCT departamento FROM users ORDER BY departamento");
$departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Panel de Administración</title>
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

        .search-container {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 20px var(--neon-blue),
                        inset 0 0 20px var(--neon-purple);
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--neon-purple);
            border-radius: 8px;
            color: #ffffff;
            padding: 0.75rem 1rem;
        }

        .filter-select {
            min-width: 200px;
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid var(--neon-blue);
            color: #ffffff;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .search-btn {
            background: transparent;
            border: 2px solid var(--neon-blue);
            color: var(--neon-blue);
            padding: 0.75rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: var(--neon-blue);
            color: var(--bg-color);
            box-shadow: 0 0 20px var(--neon-blue);
        }

        .users-table {
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 20px var(--neon-blue),
                        inset 0 0 20px var(--neon-purple);
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

        .delete-btn {
            border-color: #ff0055;
            color: #ff0055;
        }

        .delete-btn:hover {
            background: #ff0055;
            box-shadow: 0 0 10px #ff0055;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .search-form {
                flex-direction: column;
            }

            .search-input,
            .filter-select,
            .search-btn {
                width: 100%;
            }
        }

        /* Reutilizar los estilos del sidebar del create_user.php */
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

        .text-glow {
            color: var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .role-badge.admin {
            background: rgba(255, 0, 128, 0.2);
            color: #ff0080;
            border: 1px solid #ff0080;
            text-shadow: 0 0 5px #ff0080;
        }

        .role-badge.user {
            background: rgba(0, 255, 255, 0.2);
            color: #00ffff;
            border: 1px solid #00ffff;
            text-shadow: 0 0 5px #00ffff;
        }

        td {
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .users-table {
            margin-top: 2rem;
            overflow-x: auto;
            background: rgba(26, 26, 26, 0.9);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 0 20px var(--neon-blue),
                        inset 0 0 20px var(--neon-purple);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        th {
            padding: 1rem;
            color: var(--neon-blue);
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
        }

        td {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 10px var(--neon-blue);
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            margin: 0 0.25rem;
            border-radius: 50%;
            border: 1px solid var(--neon-blue);
            color: var(--neon-blue);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: var(--neon-blue);
            color: var(--bg-color);
            box-shadow: 0 0 10px var(--neon-blue);
            transform: translateY(-2px);
        }

        .delete-btn {
            border-color: #ff0055;
            color: #ff0055;
        }

        .delete-btn:hover {
            background: #ff0055;
            box-shadow: 0 0 10px #ff0055;
        }

        @media (max-width: 768px) {
            .users-table {
                padding: 1rem;
            }

            th, td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }

            .role-badge {
                padding: 0.2rem 0.5rem;
                font-size: 0.75rem;
            }

            .action-btn {
                width: 30px;
                height: 30px;
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
                <a href="users.php" class="nav-link active">
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
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="search-container">
            <form action="" method="GET" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="Buscar por nombre, correo, DNI..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="filter" class="filter-select">
                    <option value="all">Todos los departamentos</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $filter === $dept ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>
        </div>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>DNI</th>
                        <th>Correo</th>
                        <th>Departamento</th>
                        <th>Rol</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron usuarios</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="text-glow"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['dni']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['departamento']); ?></td>
                                <td>
                                    <span class="role-badge <?php echo $user['rol'] === 'admin' ? 'admin' : 'user'; ?>">
                                        <?php echo $user['rol'] === 'admin' ? 'Administrador' : 'Usuario'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                                       class="action-btn delete-btn" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')">
                                        <i class="fas fa-trash"></i>
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