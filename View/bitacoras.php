<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include("../Template/cabecera.php");
include("../Controller/conexion.php");

// Obtener el ID de la sucursal desde la URL
$id_sucursal = (isset($_GET['id_sucursal'])) ? $_GET['id_sucursal'] : "";

// Procesar acciones
$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtUbicacion = (isset($_POST['txtUbicacion'])) ? $_POST['txtUbicacion'] : "";
$txtTipo = (isset($_POST['txtTipo'])) ? $_POST['txtTipo'] : "";
$floatCapacidad = (isset($_POST['floatCapacidad'])) ? $_POST['floatCapacidad'] : "";
$fecha_mantenimiento = (isset($_POST['fecha_mantenimiento'])) ? $_POST['fecha_mantenimiento'] : "";
$fecha_PH = (isset($_POST['fecha_PH'])) ? $_POST['fecha_PH'] : "";
$txtObservaciones = (isset($_POST['txtObservaciones'])) ? $_POST['txtObservaciones'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

switch ($accion) {
    case "Agregar":
        $sentenciaSQL = $conexion->prepare("INSERT INTO extintores (ubicacion, tipo, capacidad, f_mantenimiento, f_ph, observaciones, id_sucursal) VALUES (:ubicacion, :tipo, :capacidad, :f_mantenimiento, :f_ph, :observaciones, :id_sucursal)");
        $sentenciaSQL->bindParam(':ubicacion', $txtUbicacion);
        $sentenciaSQL->bindParam(':tipo', $txtTipo);
        $sentenciaSQL->bindParam(':capacidad', $floatCapacidad);
        $sentenciaSQL->bindParam(':f_mantenimiento', $fecha_mantenimiento);
        $sentenciaSQL->bindParam(':f_ph', $fecha_PH);
        $sentenciaSQL->bindParam(':observaciones', $txtObservaciones);
        $sentenciaSQL->bindParam(':id_sucursal', $id_sucursal);
        $sentenciaSQL->execute();
        break;

    case "Actualizar":
        $sentenciaSQL = $conexion->prepare("UPDATE extintores SET ubicacion=:ubicacion, tipo=:tipo, capacidad=:capacidad, f_mantenimiento=:f_mantenimiento, f_ph=:f_ph, observaciones=:observaciones WHERE id_extintor=:id_extintor");
        $sentenciaSQL->bindParam(':ubicacion', $txtUbicacion);
        $sentenciaSQL->bindParam(':tipo', $txtTipo);
        $sentenciaSQL->bindParam(':capacidad', $floatCapacidad);
        $sentenciaSQL->bindParam(':f_mantenimiento', $fecha_mantenimiento);
        $sentenciaSQL->bindParam(':f_ph', $fecha_PH);
        $sentenciaSQL->bindParam(':observaciones', $txtObservaciones);
        $sentenciaSQL->bindParam(':id_extintor', $txtID);
        $sentenciaSQL->execute();
        break;

    case "Cancelar":
        // No action needed
        break;

    case "Seleccionar":
        $sentenciaSQL = $conexion->prepare("SELECT * FROM extintores WHERE id_extintor = :id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $extintor = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtUbicacion = $extintor['ubicacion'];
        $txtTipo = $extintor['tipo'];
        $floatCapacidad = $extintor['capacidad'];
        $fecha_mantenimiento = $extintor['f_mantenimiento'];
        $fecha_PH = $extintor['f_ph'];
        $txtObservaciones = $extintor['observaciones'];
        break;

    case "FiltrarServicio":
        // No action needed here, filtering is done below
        break;
}

// Obtener el nombre de la sucursal seleccionada
$nombre_sucursal = "";
if (!empty($id_sucursal)) {
    $sentenciaSQL = $conexion->prepare("SELECT nombre FROM sucursales WHERE id_sucursal = :id_sucursal");
    $sentenciaSQL->bindParam(':id_sucursal', $id_sucursal);
    $sentenciaSQL->execute();
    $sucursal = $sentenciaSQL->fetch(PDO::FETCH_LAZY);
    $nombre_sucursal = $sucursal['nombre'];
}

// Obtener los extintores de la sucursal seleccionada
if (!empty($id_sucursal)) {
    $sentenciaSQL = $conexion->prepare("SELECT * FROM extintores WHERE id_sucursal = :id_sucursal");
    $sentenciaSQL->bindParam(':id_sucursal', $id_sucursal);
    $sentenciaSQL->execute();
    $listaExtintores = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Si no hay sucursal seleccionada, mostrar todos los extintores
    $sentenciaSQL = $conexion->prepare("SELECT * FROM extintores");
    $sentenciaSQL->execute();
    $listaExtintores = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
}

// Filtrar extintores para servicio (fecha de mantenimiento >= 1 año)
if ($accion == "FiltrarServicio") {
    $fecha_limite = date('Y-m-d', strtotime('-1 year'));
    $sentenciaSQL = $conexion->prepare("SELECT * FROM extintores WHERE id_sucursal = :id_sucursal AND f_mantenimiento <= :fecha_limite");
    $sentenciaSQL->bindParam(':id_sucursal', $id_sucursal);
    $sentenciaSQL->bindParam(':fecha_limite', $fecha_limite);
    $sentenciaSQL->execute();
    $listaExtintoresServicio = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- Formulario para extintores -->
<div class="col-md-5">
    <div class="card">
        <div class="card-header">Datos de Extintores</div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="txtID">ID:</label>
                    <input type="number" class="form-control" value="<?php echo $txtID ?>" name="txtID" id="txtID" placeholder="ID">
                </div>
                <div class="form-group">
                    <label for="txtUbicacion">Ubicación:</label>
                    <input type="text" class="form-control" value="<?php echo $txtUbicacion ?>" name="txtUbicacion" id="txtUbicacion" placeholder="Ubicación">
                </div>
                <div class="form-group">
                    <label for="txtTipo">Tipo:</label>
                    <input type="text" class="form-control" value="<?php echo $txtTipo ?>" name="txtTipo" id="txtTipo" placeholder="Tipo">
                </div>
                <div class="form-group">
                    <label for="floatCapacidad">Capacidad:</label>
                    <input type="text" class="form-control" value="<?php echo $floatCapacidad ?>" name="floatCapacidad" id="floatCapacidad" placeholder="Capacidad">
                </div>
                <div class="form-group">
                    <label for="fecha_mantenimiento">Fecha Mantenimiento:</label>
                    <input type="date" class="form-control" value="<?php echo $fecha_mantenimiento ?>" name="fecha_mantenimiento" id="fecha_mantenimiento" placeholder="Fecha de mantenimiento">
                </div>
                <div class="form-group">
                    <label for="fecha_PH">Fecha de Prueba Hidroestática:</label>
                    <input type="date" class="form-control" value="<?php echo $fecha_PH ?>" name="fecha_PH" id="fecha_PH" placeholder="Fecha de prueba Hidroestática">
                </div>
                <div class="form-group">
                    <label for="txtObservaciones">Observaciones:</label>
                    <input type="text" class="form-control" value="<?php echo $txtObservaciones ?>" name="txtObservaciones" id="txtObservaciones" placeholder="Ingrese texto">
                </div>
                <br />
                <div class="btn-group" role="group">
                    <button type="submit" name="accion" value="Agregar" class="btn btn-outline-success">Agregar</button>
                    <button type="submit" name="accion" value="Actualizar" class="btn btn-outline-warning">Actualizar</button>
                    <button type="submit" name="accion" value="Cancelar" class="btn btn-outline-info">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<br />

<!-- Tabla de extintores -->
<div class="col-sm-5" style="width: 50%;">
    <div class="card">
        <div class="card-header">
            Bitácora de Extintores <?php echo (!empty($nombre_sucursal)) ? "de la Sucursal: $nombre_sucursal" : ""; ?>
        </div>
        <div class="card-body" style="width:100%;">
            <table class="table table-responsive table-bordered table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Ubicación</th>
                        <th>Tipo</th>
                        <th>Capacidad</th>
                        <th>Fecha Mantenimiento</th>
                        <th>Fecha Prueba Hidroestática</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaExtintores as $extintor) { ?>
                        <tr>
                            <td><?php echo $extintor['id_extintor']; ?></td>
                            <td><?php echo $extintor['ubicacion']; ?></td>
                            <td><?php echo $extintor['tipo']; ?></td>
                            <td><?php echo $extintor['capacidad']; ?></td>
                            <td><?php echo $extintor['f_mantenimiento']; ?></td>
                            <td><?php echo $extintor['f_ph']; ?></td>
                            <td><?php echo $extintor['observaciones']; ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="txtID" value="<?php echo $extintor['id_extintor']; ?>" />
                                    <input type="submit" name="accion" value="Seleccionar" class="btn btn-outline-primary btn-sm" />
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Botón para filtrar extintores para servicio -->
            <form method="POST">
                <button type="submit" name="accion" value="FiltrarServicio" class="btn btn-outline-secondary btn-sm">
                    Filtrar extintores para servicio
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Tabla de extintores para servicio -->
<?php if ($accion == "FiltrarServicio" && !empty($listaExtintoresServicio)) { ?>
    <div class="col-sm-5" style="width: 50%;">
        <div class="card">
            <div class="card-header">
                Extintores para servicio (Fecha de mantenimiento >= 1 año)
            </div>
            <div class="card-body" style="width:100%;">
                <table class="table table-responsive table-bordered table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ubicación</th>
                            <th>Tipo</th>
                            <th>Capacidad</th>
                            <th>Fecha Mantenimiento</th>
                            <th>Fecha Prueba Hidroestática</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listaExtintoresServicio as $extintor) { ?>
                            <tr>
                                <td><?php echo $extintor['id_extintor']; ?></td>
                                <td><?php echo $extintor['ubicacion']; ?></td>
                                <td><?php echo $extintor['tipo']; ?></td>
                                <td><?php echo $extintor['capacidad']; ?></td>
                                <td><?php echo $extintor['f_mantenimiento']; ?></td>
                                <td><?php echo $extintor['f_ph']; ?></td>
                                <td><?php echo $extintor['observaciones']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>

<?php include("../Template/pie.php") ?>