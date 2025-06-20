<?php
session_start();
require_once('../config/db.php');

class FormController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Guardar formulario operativo
    public function saveOperativeForm($data) {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();

            // Insertar datos principales
            $stmt = $this->conn->prepare("
                INSERT INTO operativos (
                    fecha, hora, lugar_id, user_id, departamento_id,
                    nombre_identificado, dni_identificado, domicilio_identificado,
                    sexo_identificado, hay_menores, cantidad_menores,
                    tiene_acompanantes, vehiculo_tipo_id, vehiculo_marca_id,
                    vehiculo_modelo_id, dominio, hubo_infraccion,
                    numero_acta, lugar_retencion_id, alcoholemia,
                    equipo_id, prueba_id, resultado_alcoholemia
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");

            $stmt->execute([
                $data['fecha'], $data['hora'], $data['lugar_id'],
                $_SESSION['user_id'], $_SESSION['departamento_id'],
                $data['nombre_identificado'], $data['dni_identificado'],
                $data['domicilio_identificado'], $data['sexo_identificado'],
                $data['hay_menores'], $data['cantidad_menores'],
                $data['tiene_acompanantes'], $data['vehiculo_tipo_id'],
                $data['vehiculo_marca_id'], $data['vehiculo_modelo_id'],
                $data['dominio'], $data['hubo_infraccion'], $data['numero_acta'],
                $data['lugar_retencion_id'], $data['alcoholemia'],
                $data['equipo_id'], $data['prueba_id'], $data['resultado_alcoholemia']
            ]);

            $operativo_id = $this->conn->lastInsertId();

            // Si hay menores, guardar sus datos
            if ($data['hay_menores'] && isset($data['menores'])) {
                foreach ($data['menores'] as $menor) {
                    $stmt = $this->conn->prepare("
                        INSERT INTO menores (
                            operativo_id, nombre, dni, domicilio, observaciones
                        ) VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $operativo_id, $menor['nombre'], $menor['dni'],
                        $menor['domicilio'], $menor['observaciones']
                    ]);
                }
            }

            // Si hay acompañantes, guardar sus datos
            if ($data['tiene_acompanantes'] && isset($data['acompanantes'])) {
                foreach ($data['acompanantes'] as $acompanante) {
                    $stmt = $this->conn->prepare("
                        INSERT INTO acompanantes (
                            operativo_id, nombre_apellido, dni, domicilio, sexo
                        ) VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $operativo_id, $acompanante['nombre_apellido'],
                        $acompanante['dni'], $acompanante['domicilio'],
                        $acompanante['sexo']
                    ]);
                }
            }

            // Confirmar transacción
            $this->conn->commit();
            return ['success' => true, 'message' => 'Formulario guardado correctamente'];

        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al guardar el formulario: ' . $e->getMessage()];
        }
    }

    // Obtener lugares
    public function getLugares() {
        try {
            $stmt = $this->conn->query("SELECT id, nombre FROM lugares ORDER BY nombre");
            return ['success' => true, 'lugares' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al obtener lugares'];
        }
    }

    // Buscar lugares
    public function searchLugares($term) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, nombre 
                FROM lugares 
                WHERE nombre LIKE ? 
                ORDER BY nombre 
                LIMIT 10
            ");
            $stmt->execute(['%' . $term . '%']);
            return ['success' => true, 'results' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error en la búsqueda'];
        }
    }

    // Obtener tipos de vehículos
    public function getTiposVehiculo() {
        try {
            $stmt = $this->conn->query("SELECT id, nombre FROM tipos_vehiculo ORDER BY nombre");
            return ['success' => true, 'tipos' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al obtener tipos de vehículo'];
        }
    }

    // Obtener marcas de vehículos
    public function getMarcasVehiculo() {
        try {
            $stmt = $this->conn->query("SELECT id, nombre FROM marcas_vehiculo ORDER BY nombre");
            return ['success' => true, 'marcas' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al obtener marcas'];
        }
    }

    // Obtener modelos por marca
    public function getModelosPorMarca($marca_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, nombre 
                FROM modelos_vehiculo 
                WHERE marca_id = ? 
                ORDER BY nombre
            ");
            $stmt->execute([$marca_id]);
            return ['success' => true, 'modelos' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al obtener modelos'];
        }
    }

    // Obtener equipos de alcoholemia
    public function getEquipos() {
        try {
            $stmt = $this->conn->query("SELECT id, numero FROM equipos ORDER BY numero");
            return ['success' => true, 'equipos' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al obtener equipos'];
        }
    }

    // Obtener lugares de retención
    public function getLugaresRetencion() {
        try {
            $stmt = $this->conn->query("SELECT id, nombre FROM lugares_retencion ORDER BY nombre");
            return ['success' => true, 'lugares' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al obtener lugares de retención'];
        }
    }
} 