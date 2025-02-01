<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

ob_start();

include("../Template/cabecera.php");
include("../Controller/conexion.php");
require('../libs/fpdf.php');

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Reporte de Servicio de Extintores', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
}

$id_sucursal = $_GET['id_sucursal'] ?? '';
$accion = $_POST['accion'] ?? '';

// Procesar generación de PDF primero
if ($accion === "GenerarPDF") {
    try {
        $stmt = $conexion->prepare("SELECT c.razon_social, s.nombre AS sucursal 
                                  FROM sucursales s
                                  INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                                  WHERE s.id_sucursal = :id_sucursal");
        $stmt->execute([':id_sucursal' => $id_sucursal]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        $fecha_limite = date('Y-m-d', strtotime('-365 days'));
        $stmt = $conexion->prepare("SELECT * FROM extintores 
                                  WHERE id_sucursal = :id_sucursal 
                                  AND f_mantenimiento <= :fecha_limite");
        $stmt->execute([
            ':id_sucursal' => $id_sucursal,
            ':fecha_limite' => $fecha_limite
        ]);
        $extintores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new PDF();
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Cliente: ' . utf8_decode($datos['razon_social']), 0, 1);
        $pdf->Cell(0, 10, 'Sucursal: ' . utf8_decode($datos['sucursal']), 0, 1);
        $pdf->Cell(0, 10, 'Fecha de Reporte: ' . date('d/m/Y'), 0, 1);
        $pdf->Ln(15);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(211, 211, 211);
        $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Ubicación', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Tipo', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Capacidad', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Último Mantenimiento', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 9);
        foreach ($extintores as $extintor) {
            $pdf->Cell(20, 10, $extintor['id_extintor'], 1, 0, 'C');
            $pdf->Cell(50, 10, utf8_decode($extintor['ubicacion']), 1);
            $pdf->Cell(40, 10, $extintor['tipo'], 1);
            $pdf->Cell(30, 10, $extintor['capacidad'], 1);
            $pdf->Cell(50, 10, date('d/m/Y', strtotime($extintor['f_mantenimiento'])), 1, 1, 'C');
        }

        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Firma de Aceptación del Cliente', 0, 1, 'C');
        $pdf->Cell(0, 15, '_________________________________________', 0, 1, 'C');

        ob_end_clean();
        $pdf->Output('D', 'Reporte_Servicio_' . date('Ymd_His') . '.pdf');
        exit;
    } catch (Exception $e) {
        error_log("Error al generar PDF: " . $e->getMessage());
        exit;
    }
}

// Procesar otras acciones
$txtID = $_POST['txtID'] ?? '';
$txtUbicacion = $_POST['txtUbicacion'] ?? '';
$txtTipo = $_POST['txtTipo'] ?? '';
$floatCapacidad = $_POST['floatCapacidad'] ?? '';
$fecha_mantenimiento = $_POST['fecha_mantenimiento'] ?? '';
$fecha_PH = $_POST['fecha_PH'] ?? '';
$txtObservaciones = $_POST['txtObservaciones'] ?? '';

switch ($accion) {
    case "Agregar":
        $stmt = $conexion->prepare("INSERT INTO extintores (ubicacion, tipo, capacidad, f_mantenimiento, f_ph, observaciones, id_sucursal) 
                                  VALUES (:ubicacion, :tipo, :capacidad, :f_mantenimiento, :f_ph, :observaciones, :id_sucursal)");
        $stmt->execute([
            ':ubicacion' => $txtUbicacion,
            ':tipo' => $txtTipo,
            ':capacidad' => $floatCapacidad,
            ':f_mantenimiento' => $fecha_mantenimiento,
            ':f_ph' => $fecha_PH,
            ':observaciones' => $txtObservaciones,
            ':id_sucursal' => $id_sucursal
        ]);
        break;

    case "Actualizar":
        $stmt = $conexion->prepare("UPDATE extintores SET 
                                  ubicacion = :ubicacion, 
                                  tipo = :tipo, 
                                  capacidad = :capacidad, 
                                  f_mantenimiento = :f_mantenimiento, 
                                  f_ph = :f_ph, 
                                  observaciones = :observaciones 
                                  WHERE id_extintor = :id");
        $stmt->execute([
            ':ubicacion' => $txtUbicacion,
            ':tipo' => $txtTipo,
            ':capacidad' => $floatCapacidad,
            ':f_mantenimiento' => $fecha_mantenimiento,
            ':f_ph' => $fecha_PH,
            ':observaciones' => $txtObservaciones,
            ':id' => $txtID
        ]);
        break;

    case "Seleccionar":
        $stmt = $conexion->prepare("SELECT * FROM extintores WHERE id_extintor = :id");
        $stmt->execute([':id' => $txtID]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            $txtUbicacion = $resultado['ubicacion'];
            $txtTipo = $resultado['tipo'];
            $floatCapacidad = $resultado['capacidad'];
            $fecha_mantenimiento = $resultado['f_mantenimiento'];
            $fecha_PH = $resultado['f_ph'];
            $txtObservaciones = $resultado['observaciones'];
        }
        break;
}

// Obtener datos para mostrar
try {
    // Todos los extintores para bitácora
    $stmt = $conexion->prepare("SELECT * FROM extintores WHERE id_sucursal = :id_sucursal");
    $stmt->execute([':id_sucursal' => $id_sucursal]);
    $listaExtintores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Extintores para servicio (solo si se solicita)
    $extintores_servicio = [];
    if ($accion === "FiltrarServicio") {
        $fecha_limite = date('Y-m-d', strtotime('-365 days'));
        $stmt = $conexion->prepare("SELECT * FROM extintores 
                                  WHERE id_sucursal = :id_sucursal 
                                  AND f_mantenimiento <= :fecha_limite");
        $stmt->execute([
            ':id_sucursal' => $id_sucursal,
            ':fecha_limite' => $fecha_limite
        ]);
        $extintores_servicio = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener nombre de sucursal
    $stmt = $conexion->prepare("SELECT nombre FROM sucursales WHERE id_sucursal = :id_sucursal");
    $stmt->execute([':id_sucursal' => $id_sucursal]);
    $sucursal = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre_sucursal = $sucursal['nombre'] ?? '';

} catch (PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage());
    exit;
}

ob_end_flush();
?>

<div class="container-fluid">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="row">
        <!-- Formulario -->
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-fire-extinguisher me-2"></i>Gestión de Extintores
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="txtID" value="<?= htmlspecialchars($txtID) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" class="form-control" name="txtUbicacion" 
                                   value="<?= htmlspecialchars($txtUbicacion) ?>" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo</label>
                                <input type="text" class="form-control" name="txtTipo" 
                                       value="<?= htmlspecialchars($txtTipo) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Capacidad (kg)</label>
                                <input type="number" step="0.1" class="form-control" 
                                       name="floatCapacidad" value="<?= htmlspecialchars($floatCapacidad) ?>" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha Mantenimiento</label>
                                <input type="date" class="form-control" name="fecha_mantenimiento" 
                                       value="<?= htmlspecialchars($fecha_mantenimiento) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Prueba Hidroestatica</label>
                                <input type="date" class="form-control" name="fecha_PH" 
                                       value="<?= htmlspecialchars($fecha_PH) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="txtObservaciones" rows="2"><?= htmlspecialchars($txtObservaciones) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-block">
                            <button type="submit" name="accion" value="Agregar" class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i>Agregar
                            </button>
                            <button type="submit" name="accion" value="Actualizar" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Actualizar
                            </button>
                            <button type="submit" name="accion" value="Cancelar" class="btn btn-secondary">
                                <i class="fas fa-times-circle me-2"></i>Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tablas -->
        <div class="col-md-7">
            <!-- Bitácora (siempre visible) -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Bitácora de Extintores - <?= htmlspecialchars($nombre_sucursal) ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Ubicación</th>
                                    <th>Tipo</th>
                                    <th>Capacidad</th>
                                    <th>Último Mantenimiento</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listaExtintores as $extintor): ?>
                                <tr>
                                    <td><?= htmlspecialchars($extintor['id_extintor']) ?></td>
                                    <td><?= htmlspecialchars($extintor['ubicacion']) ?></td>
                                    <td><?= htmlspecialchars($extintor['tipo']) ?></td>
                                    <td><?= htmlspecialchars($extintor['capacidad']) ?> kg</td>
                                    <td><?= date('d/m/Y', strtotime($extintor['f_mantenimiento'])) ?></td>
                                    <td>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="txtID" value="<?= $extintor['id_extintor'] ?>">
                                            <button type="submit" name="accion" value="Seleccionar" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Botón para mostrar servicio -->
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="id_sucursal" value="<?= $id_sucursal ?>">
                        <button type="submit" name="accion" value="FiltrarServicio" class="btn btn-warning">
                            <i class="fas fa-filter me-2"></i>Mostrar para Servicio
                        </button>
                    </form>
                </div>
            </div>

            <!-- Extintores para Servicio (solo al filtrar) -->
            <?php if ($accion === "FiltrarServicio" && !empty($extintores_servicio)): ?>
            <div class="card shadow mt-4">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Extintores para Servicio
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Ubicación</th>
                                    <th>Último Mantenimiento</th>
                                    <th>Días sin Servicio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($extintores_servicio as $extintor): 
                                    $dias = (time() - strtotime($extintor['f_mantenimiento'])) / 86400;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($extintor['id_extintor']) ?></td>
                                    <td><?= htmlspecialchars($extintor['ubicacion']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($extintor['f_mantenimiento'])) ?></td>
                                    <td class="<?= $dias > 365 ? 'text-danger fw-bold' : '' ?>">
                                        <?= floor($dias) ?> días
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Botón para PDF -->
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="id_sucursal" value="<?= $id_sucursal ?>">
                        <button type="submit" name="accion" value="GenerarPDF" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Generar PDF
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("../Template/pie.php") ?>