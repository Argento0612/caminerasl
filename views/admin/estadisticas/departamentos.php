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

// Obtener estadísticas por departamento
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Operativos por departamento
    $stmt = $conn->query("
        SELECT 
            d.nombre,
            COUNT(*) as total_operativos,
            SUM(CASE WHEN o.hubo_infraccion = 'si' THEN 1 ELSE 0 END) as total_infracciones,
            SUM(CASE WHEN o.alcoholemia = '1' AND o.resultado_alcoholemia > 0.0 THEN 1 ELSE 0 END) as total_alcoholemia_positiva,
            SUM(CASE WHEN o.hay_menores = 'si' THEN 1 ELSE 0 END) as total_menores,
            SUM(CASE WHEN o.hay_acompanantes = 'si' THEN 1 ELSE 0 END) as total_acompanantes
        FROM operativos_b o
        JOIN departamentos d ON o.departamento_id = d.id
        GROUP BY d.nombre
    ");
    $estadisticas_departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas por Departamento</title>
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

        .table {
            color: var(--text-color);
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid var(--neon-color);
        }

        .table th {
            color: var(--neon-color);
            border-color: var(--neon-color);
        }

        .table td {
            border-color: var(--neon-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4" style="color: var(--neon-color); text-shadow: 0 0 10px var(--neon-color);">
            Estadísticas por Departamento
        </h1>

        <div class="row">
            <!-- Gráfico de Operativos por Departamento -->
            <div class="col-md-6">
                <div class="card">
                    <h3 class="card-title">Operativos por Departamento</h3>
                    <div class="chart-container">
                        <canvas id="operativosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Infracciones por Departamento -->
            <div class="col-md-6">
                <div class="card">
                    <h3 class="card-title">Infracciones por Departamento</h3>
                    <div class="chart-container">
                        <canvas id="infraccionesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Tabla de Estadísticas Detalladas -->
            <div class="col-12">
                <div class="card">
                    <h3 class="card-title">Estadísticas Detalladas por Departamento</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Departamento</th>
                                    <th>Total Operativos</th>
                                    <th>Infracciones</th>
                                    <th>Alcoholemia Positiva</th>
                                    <th>Menores</th>
                                    <th>Acompañantes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estadisticas_departamentos as $departamento): ?>
                                <tr>
                                    <td><?php echo $departamento['nombre']; ?></td>
                                    <td><?php echo $departamento['total_operativos']; ?></td>
                                    <td><?php echo $departamento['total_infracciones']; ?></td>
                                    <td><?php echo $departamento['total_alcoholemia_positiva']; ?></td>
                                    <td><?php echo $departamento['total_menores']; ?></td>
                                    <td><?php echo $departamento['total_acompanantes']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Operativos por Departamento
        const operativosCtx = document.getElementById('operativosChart').getContext('2d');
        new Chart(operativosCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($estadisticas_departamentos, 'nombre')); ?>,
                datasets: [{
                    label: 'Total Operativos',
                    data: <?php echo json_encode(array_column($estadisticas_departamentos, 'total_operativos')); ?>,
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

        // Gráfico de Infracciones por Departamento
        const infraccionesCtx = document.getElementById('infraccionesChart').getContext('2d');
        new Chart(infraccionesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($estadisticas_departamentos, 'nombre')); ?>,
                datasets: [{
                    label: 'Infracciones',
                    data: <?php echo json_encode(array_column($estadisticas_departamentos, 'total_infracciones')); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: '#ff6384',
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
    </script>
</body>
</html> 