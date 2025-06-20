<?php
// Definir la ruta base
define('BASE_PATH', __DIR__);

session_start();
$_SESSION['user_id'] = 1; // Simulamos un usuario autenticado
$_SESSION['departamento'] = 'Departamento de Tránsito'; // Simulamos el departamento

// Simulamos los datos POST que enviaría el formulario
$_POST = array(
    'fecha' => '2024-02-27',
    'hora' => '10:00',
    'lugar' => '1',
    'nombre_identificado' => 'Test User',
    'dni_identificado' => '12345678',
    'domicilio_identificado' => 'Test Address',
    'sexo_identificado' => 'M',
    'tipo_vehiculo' => '1',
    'marca_vehiculo' => '1',
    'modelo_vehiculo' => '1',
    'dominio' => 'ABC123',
    'hay_menores' => 'no',
    'tiene_acompanantes' => 'no',
    'hubo_infraccion' => 'no',
    'alcoholemia' => '0'
);

// Incluimos primero la configuración de la base de datos
require_once 'config/db.php';

// Incluimos el controlador
require_once 'controllers/guardar_formulario_b.php'; 