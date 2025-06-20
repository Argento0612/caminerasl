<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // Verificar si es una solicitud AJAX
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        throw new Exception('Solicitud no válida');
    }

    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('No autorizado');
    }

    // Validar campos obligatorios
    $campos_obligatorios = [
        'fecha', 'hora', 'lugar', 'nombre_identificado', 'dni_identificado',
        'domicilio_identificado', 'sexo_identificado', 'tipo_vehiculo_id',
        'marca_vehiculo_id', 'modelo_vehiculo_id', 'dominio'
    ];

    foreach ($campos_obligatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("El campo " . ucfirst(str_replace('_', ' ', $campo)) . " es obligatorio");
        }
    }

    // Validar campos condicionales
    if ($_POST['hay_menores'] === 'si' && empty($_POST['nombre_menor'])) {
        throw new Exception("Debe ingresar los datos de al menos un menor");
    }

    if ($_POST['hubo_infraccion'] === 'si') {
        if (empty($_POST['motivo_infraccion'])) {
            throw new Exception("Debe ingresar el motivo de la infracción");
        }
        if (empty($_POST['numero_acta'])) {
            throw new Exception("Debe ingresar el número de acta");
        }
        if ($_POST['hubo_retencion'] === 'si' && empty($_POST['lugar_retencion'])) {
            throw new Exception("Debe ingresar el lugar de retención");
        }
    }

    if ($_POST['alcoholemia'] === '1') {
        if (empty($_POST['equipo_id'])) {
            throw new Exception("Debe ingresar el número de equipo");
        }
        if (empty($_POST['prueba_id'])) {
            throw new Exception("Debe ingresar el número de prueba");
        }
        if (empty($_POST['resultado_alcoholemia'])) {
            throw new Exception("Debe ingresar el resultado de la prueba");
        }
    }

    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->beginTransaction();

    // Insertar datos principales
    $stmt = $conn->prepare("INSERT INTO operativos_b (
        fecha, hora, lugar_id, nombre_identificado, dni_identificado, 
        domicilio_identificado, sexo_identificado, hay_menores, 
        tipo_vehiculo_id, marca_vehiculo_id, modelo_vehiculo_id, 
        dominio, desde_id, hasta_id, hubo_infraccion, motivo_infraccion_id,
        numero_acta, hubo_retencion, lugar_retencion_id, alcoholemia,
        equipo_id, prueba_id, resultado_alcoholemia, usuario_id
    ) VALUES (
        :fecha, :hora, :lugar_id, :nombre_identificado, :dni_identificado,
        :domicilio_identificado, :sexo_identificado, :hay_menores,
        :tipo_vehiculo_id, :marca_vehiculo_id, :modelo_vehiculo_id,
        :dominio, :desde_id, :hasta_id, :hubo_infraccion, :motivo_infraccion_id,
        :numero_acta, :hubo_retencion, :lugar_retencion_id, :alcoholemia,
        :equipo_id, :prueba_id, :resultado_alcoholemia, :usuario_id
    )");

    $stmt->execute([
        'fecha' => $_POST['fecha'],
        'hora' => $_POST['hora'],
        'lugar_id' => $_POST['lugar'],
        'nombre_identificado' => $_POST['nombre_identificado'],
        'dni_identificado' => $_POST['dni_identificado'],
        'domicilio_identificado' => $_POST['domicilio_identificado'],
        'sexo_identificado' => $_POST['sexo_identificado'],
        'hay_menores' => $_POST['hay_menores'] === 'si' ? 1 : 0,
        'tipo_vehiculo_id' => $_POST['tipo_vehiculo_id'],
        'marca_vehiculo_id' => $_POST['marca_vehiculo_id'],
        'modelo_vehiculo_id' => $_POST['modelo_vehiculo_id'],
        'dominio' => $_POST['dominio'],
        'desde_id' => $_POST['desde'] ?? null,
        'hasta_id' => $_POST['hasta'] ?? null,
        'hubo_infraccion' => $_POST['hubo_infraccion'] === 'si' ? 1 : 0,
        'motivo_infraccion_id' => $_POST['motivo_infraccion'] ?? null,
        'numero_acta' => $_POST['numero_acta'] ?? null,
        'hubo_retencion' => $_POST['hubo_retencion'] === 'si' ? 1 : 0,
        'lugar_retencion_id' => $_POST['lugar_retencion'] ?? null,
        'alcoholemia' => $_POST['alcoholemia'],
        'equipo_id' => $_POST['equipo_id'] ?? null,
        'prueba_id' => $_POST['prueba_id'] ?? null,
        'resultado_alcoholemia' => $_POST['resultado_alcoholemia'] ?? null,
        'usuario_id' => $_SESSION['user_id']
    ]);

    $operativo_id = $conn->lastInsertId();

    // Insertar menores si existen
    if ($_POST['hay_menores'] === 'si' && isset($_POST['nombre_menor'])) {
        $stmt = $conn->prepare("INSERT INTO menores_operativo (
            operativo_id, nombre, dni, domicilio, observaciones
        ) VALUES (
            :operativo_id, :nombre, :dni, :domicilio, :observaciones
        )");

        foreach ($_POST['nombre_menor'] as $key => $nombre) {
            $stmt->execute([
                'operativo_id' => $operativo_id,
                'nombre' => $nombre,
                'dni' => $_POST['dni_menor'][$key],
                'domicilio' => $_POST['domicilio_menor'][$key],
                'observaciones' => $_POST['observaciones_menor'][$key] ?? null
            ]);
        }
    }

    // Insertar acompañantes si existen
    if (isset($_POST['nombre_apellido_acompanante'])) {
        $stmt = $conn->prepare("INSERT INTO acompanantes_operativo (
            operativo_id, nombre_apellido, dni, domicilio, sexo
        ) VALUES (
            :operativo_id, :nombre_apellido, :dni, :domicilio, :sexo
        )");

        foreach ($_POST['nombre_apellido_acompanante'] as $key => $nombre) {
            $stmt->execute([
                'operativo_id' => $operativo_id,
                'nombre_apellido' => $nombre,
                'dni' => $_POST['dni_acompanante'][$key],
                'domicilio' => $_POST['domicilio_acompanante'][$key],
                'sexo' => $_POST["sexo_acompanante_$key"]
            ]);
        }
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Operativo registrado correctamente',
        'operativo_id' => $operativo_id
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el formulario: ' . $e->getMessage()
    ]);
} 