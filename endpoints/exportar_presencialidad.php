<?php
   require_once '../config/database.php';
require_once '../vendor/autoload.php';

// Parámetros recibidos
$fecha = $_GET['fecha'] ?? date('Y-m-d');
$formato = $_GET['formato'] ?? 'pdf';

// Consulta de datos (ajusta los nombres de tablas/campos según tu base)
$sql = "SELECT d.nombre AS departamento, p.nombre AS puesto, r.jerarquia, r.apellido_nombre, r.funcion, r.horario, r.observaciones
        FROM registros_presencialidad r
        JOIN departamentos d ON r.departamento_id = d.id
        JOIN puestos p ON r.puesto_id = p.id
        WHERE r.fecha = ?
        ORDER BY d.nombre, p.nombre, r.jerarquia";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $fecha);
$stmt->execute();
$result = $stmt->get_result();

$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[$row['departamento']][$row['puesto']][] = $row;
}

if ($formato === 'pdf') {
    // --- Generar PDF con TCPDF ---
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetMargins(10, 35, 10);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();

    // Encabezado con logos y títulos
    $pdf->Image('../assets/logo_izq.png', 12, 8, 18); // Cambia la ruta si es necesario
    $pdf->Image('../assets/logo_der.png', 180, 8, 18); // Cambia la ruta si es necesario
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetXY(30, 10);
    $pdf->Cell(150, 5, 'POLICÍA DE LA PROVINCIA DE SAN LUIS', 0, 2, 'C');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(150, 5, 'DIRECCIÓN GENERAL DE POLICÍA CAMINERA Y SEGURIDAD VIAL DG-6', 0, 2, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'FUERZA EFECTIVA DEL DÍA DE LA FECHA - ' . date('d/m/Y', strtotime($fecha)), 0, 1, 'C');
    $pdf->Ln(2);

    // Por cada departamento y puesto, genera la tabla
    foreach ($datos as $departamento => $puestos) {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 7, 'DEPARTAMENTO DE SEGURIDAD VIAL ' . $departamento, 0, 1, 'L');
        foreach ($puestos as $puesto => $personal) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 6, 'PUESTO ' . strtoupper($puesto), 0, 1, 'L');
            // Encabezado de tabla
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(230,230,230);
            $pdf->Cell(10, 7, 'N°', 1, 0, 'C', 1);
            $pdf->Cell(30, 7, 'JERARQUÍA', 1, 0, 'C', 1);
            $pdf->Cell(50, 7, 'APELLIDO Y NOMBRE', 1, 0, 'C', 1);
            $pdf->Cell(35, 7, 'FUNCIÓN', 1, 0, 'C', 1);
            $pdf->Cell(20, 7, 'HORARIO', 1, 0, 'C', 1);
            $pdf->Cell(35, 7, 'OBSERVACIONES', 1, 1, 'C', 1);

            // Filas de personal
            $pdf->SetFont('helvetica', '', 9);
            $n = 1;
            foreach ($personal as $fila) {
                $pdf->Cell(10, 7, $n++, 1, 0, 'C');
                $pdf->Cell(30, 7, $fila['jerarquia'], 1, 0, 'L');
                $pdf->Cell(50, 7, $fila['apellido_nombre'], 1, 0, 'L');
                $pdf->Cell(35, 7, $fila['funcion'], 1, 0, 'L');
                $pdf->Cell(20, 7, $fila['horario'], 1, 0, 'C');
                $pdf->Cell(35, 7, $fila['observaciones'], 1, 1, 'L');
            }
            $pdf->Ln(2);
        }
        $pdf->Ln(2);
    }

    // Pie institucional
    $pdf->SetY(-20);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(0, 10, 'SAN LUIS', 0, 1, 'L');
    $pdf->Cell(0, 0, 'La Provincia | Seguridad', 0, 0, 'L');

    // Salida del PDF
    $pdf->Output('presencialidad_' . $fecha . '.pdf', 'I');
    exit;
} else if ($formato === 'word') {
    // --- Generar Word (HTML) ---
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="presencialidad_' . $fecha . '.doc"');
    echo '<html><head><meta charset="UTF-8"></head><body>';
    echo '<h2 style="text-align:center;">FUERZA EFECTIVA DEL DÍA DE LA FECHA - ' . date('d/m/Y', strtotime($fecha)) . '</h2>';
    foreach ($datos as $departamento => $puestos) {
        echo '<h3>DEPARTAMENTO DE SEGURIDAD VIAL ' . $departamento . '</h3>';
        foreach ($puestos as $puesto => $personal) {
            echo '<h4>PUESTO ' . strtoupper($puesto) . '</h4>';
            echo '<table border="1" cellpadding="4" cellspacing="0" width="100%">';
            echo '<tr style="background:#eee;">
                    <th>N°</th>
                    <th>JERARQUÍA</th>
                    <th>APELLIDO Y NOMBRE</th>
                    <th>FUNCIÓN</th>
                    <th>HORARIO</th>
                    <th>OBSERVACIONES</th>
                  </tr>';
            $n = 1;
            foreach ($personal as $fila) {
                echo '<tr>
                        <td align="center">' . $n++ . '</td>
                        <td>' . $fila['jerarquia'] . '</td>
                        <td>' . $fila['apellido_nombre'] . '</td>
                        <td>' . $fila['funcion'] . '</td>
                        <td align="center">' . $fila['horario'] . '</td>
                        <td>' . $fila['observaciones'] . '</td>
                      </tr>';
            }
            echo '</table><br>';
        }
    }
    echo '<p>SAN LUIS<br>La Provincia | Seguridad</p>';
    echo '</body></html>';
    exit;
}
?>