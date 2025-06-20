<?php
session_start();
require_once '../../../config/db.php';
require_once '../../../controllers/LoginController.php';

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

// Obtener estadísticas generales
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Total de operativos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM operativos_b");
    $total_operativos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Operativos por departamento
    $stmt = $conn->query("
        SELECT d.nombre, COUNT(*) as total 
        FROM operativos_b o 
        JOIN departamentos d ON o.departamento_id = d.id 
        GROUP BY d.nombre
    ");
    $operativos_por_departamento = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Operativos con infracciones
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM operativos_b 
        WHERE hubo_infraccion = 'si'
    ");
    $total_infracciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Operativos con alcoholemia positiva
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM operativos_b 
        WHERE alcoholemia = '1' AND resultado_alcoholemia > 0.0
    ");
    $total_alcoholemia_positiva = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Operativos con menores
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM operativos_b 
        WHERE hay_menores = 'si'
    ");
    $total_menores = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Operativos con acompañantes
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM operativos_b 
        WHERE hay_acompanantes = 'si'
    ");
    $total_acompanantes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas Generales</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --neon-color: #ccff00;
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
        }

        body {
            font-family: 'Orbitron', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            padding: 2rem;
        }

        .card {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid var(--neon-color);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.1);
        }

        .card-title {
            color: var(--neon-color);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--neon-color);
        }

        .stat-value {
            font-size: 2rem;
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
        }

        .stat-label {
            color: var(--text-color);
            opacity: 0.8;
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4" style="color: var(--neon-color); text-shadow: 0 0 10px var(--neon-color);">
            Estadísticas Generales
        </h1>

        <div class="row">
            <!-- Total de Operativos -->
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-title">Total de Operativos</h3>
                    <div class="stat-value"><?php echo $total_operativos; ?></div>
                    <div class="stat-label">Operativos realizados</div>
                </div>
            </div>

            <!-- Operativos con Infracciones -->
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-title">Infracciones</h3>
                    <div class="stat-value"><?php echo $total_infracciones; ?></div>
                    <div class="stat-label">Operativos con infracciones</div>
                </div>
            </div>

            <!-- Alcoholemia Positiva -->
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-title">Alcoholemia Positiva</h3>
                    <div class="stat-value"><?php echo $total_alcoholemia_positiva; ?></div>
                    <div class="stat-label">Pruebas positivas</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Gráfico de Operativos por Departamento -->
            <div class="col-md-6">
                <div class="card">
                    <h3 class="card-title">Operativos por Departamento</h3>
                    <div class="chart-container">
                        <canvas id="departamentosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Distribución de Operativos -->
            <div class="col-md-6">
                <div class="card">
                    <h3 class="card-title">Distribución de Operativos</h3>
                    <div class="chart-container">
                        <canvas id="distribucionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Operativos por Departamento
        const departamentosCtx = document.getElementById('departamentosChart').getContext('2d');
        new Chart(departamentosCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($operativos_por_departamento, 'nombre')); ?>,
                datasets: [{
                    label: 'Operativos',
                    data: <?php echo json_encode(array_column($operativos_por_departamento, 'total')); ?>,
                    backgroundColor: 'rgba(204, 255, 0, 0.2)',
                    borderColor: '#ccff00',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ffffff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ffffff'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff'
                        }
                    }
                }
            }
        });

        // Gráfico de Distribución de Operativos
        const distribucionCtx = document.getElementById('distribucionChart').getContext('2d');
        new Chart(distribucionCtx, {
            type: 'pie',
            data: {
                labels: ['Con Infracciones', 'Con Alcoholemia Positiva', 'Con Menores', 'Con Acompañantes'],
                datasets: [{
                    data: [
                        <?php echo $total_infracciones; ?>,
                        <?php echo $total_alcoholemia_positiva; ?>,
                        <?php echo $total_menores; ?>,
                        <?php echo $total_acompanantes; ?>
                    ],
                    backgroundColor: [
                        'rgba(204, 255, 0, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        '#ccff00',
                        '#ff6384',
                        '#36a2eb',
                        '#ffce56'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#ffffff'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 