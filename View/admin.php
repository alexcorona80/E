<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

include("../Template/cabecera.php");
include("../Controller/conexion.php");

// Procesar acciones principales
$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtRazon_social = (isset($_POST['txtRazon_social'])) ? $_POST['txtRazon_social'] : "";
$txtDireccion = (isset($_POST['txtDireccion'])) ? $_POST['txtDireccion'] : "";
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : "";
$correo = (isset($_POST['correo'])) ? $_POST['correo'] : "";
$RFC = (isset($_POST['RFC'])) ? $_POST['RFC'] : "";
$id_usuario = (isset($_POST['id_usuario'])) ? $_POST['id_usuario'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

// Variables para sucursales
$nombreSucursal = (isset($_POST['nombreSucursal'])) ? $_POST['nombreSucursal'] : "";
$direccionSucursal = (isset($_POST['direccionSucursal'])) ? $_POST['direccionSucursal'] : "";
$sucursalesCliente = [];

switch ($accion) {
    case "Agregar":
        $sentenciaSQL = $conexion->prepare("INSERT INTO clientes (razon_social, direccion, telefono, correo, rfc, id_usuario) VALUES (:razon_social, :direccion, :telefono, :correo, :rfc, :id_usuario)");
        $sentenciaSQL->bindParam(':razon_social', $txtRazon_social);
        $sentenciaSQL->bindParam(':direccion', $txtDireccion);
        $sentenciaSQL->bindParam(':telefono', $telefono);
        $sentenciaSQL->bindParam(':correo', $correo);
        $sentenciaSQL->bindParam(':rfc', $RFC);
        $sentenciaSQL->bindParam(':id_usuario', $id_usuario);
        $sentenciaSQL->execute();
        break;

    case "Actualizar":
        $sentenciaSQL = $conexion->prepare("UPDATE clientes SET razon_social=:razon_social, direccion=:direccion, telefono=:telefono, correo=:correo, rfc=:rfc WHERE id_cliente=:id_cliente");
        $sentenciaSQL->bindParam(':razon_social', $txtRazon_social);
        $sentenciaSQL->bindParam(':direccion', $txtDireccion);
        $sentenciaSQL->bindParam(':telefono', $telefono);
        $sentenciaSQL->bindParam(':correo', $correo);
        $sentenciaSQL->bindParam(':rfc', $RFC);
        $sentenciaSQL->bindParam(':id_cliente', $txtID);
        $sentenciaSQL->execute();
        break;

    case "Cancelar":
        // Limpiar campos
        $txtID = "";
        $txtRazon_social = "";
        $txtDireccion = "";
        $telefono = "";
        $correo = "";
        $RFC = "";
        break;

    case "Seleccionar":
        $sentenciaSQL = $conexion->prepare("SELECT * FROM clientes WHERE id_cliente = :id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $cliente = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtRazon_social = $cliente['razon_social'];
        $txtDireccion = $cliente['direccion'];
        $telefono = $cliente['telefono'];
        $correo = $cliente['correo'];
        $RFC = $cliente['rfc'];

        // Obtener sucursales del cliente
        $sentenciaSQL = $conexion->prepare("SELECT * FROM sucursales WHERE id_cliente = :id_cliente");
        $sentenciaSQL->bindParam(':id_cliente', $txtID);
        $sentenciaSQL->execute();
        $sucursalesCliente = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
        break;

    case "Borrar":
        $sentenciaSQL = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = :id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        break;

    case "AgregarSucursal":
        if ($txtID != "") {
            $sentenciaSQL = $conexion->prepare("INSERT INTO sucursales (nombre, direccion, id_cliente) VALUES (:nombre, :direccion, :id_cliente)");
            $sentenciaSQL->bindParam(':nombre', $nombreSucursal);
            $sentenciaSQL->bindParam(':direccion', $direccionSucursal);
            $sentenciaSQL->bindParam(':id_cliente', $txtID);
            $sentenciaSQL->execute();
        }
        break;
}

// Obtener lista de clientes
$sentenciaSQL = $conexion->prepare("SELECT * FROM clientes");
$sentenciaSQL->execute();
$listaClientes = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

// Actualizar lista de sucursales si hay cliente seleccionado
if ($txtID != "") {
    $sentenciaSQL = $conexion->prepare("SELECT * FROM sucursales WHERE id_cliente = :id_cliente");
    $sentenciaSQL->bindParam(':id_cliente', $txtID);
    $sentenciaSQL->execute();
    $sucursalesCliente = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="row">
    <!-- Columna Izquierda: Datos Clientes -->
    <div class="col-md-6">
        <!-- Formulario Clientes -->
        <div class="card mb-4">
            <div class="card-header">Datos de Clientes</div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>ID:</label>
                        <input type="number" class="form-control" name="txtID" value="<?php echo $txtID ?>" placeholder="ID" readonly>
                    </div>
                    <div class="form-group">
                        <label>Razón Social:</label>
                        <input type="text" class="form-control" name="txtRazon_social" value="<?php echo $txtRazon_social ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Dirección:</label>
                        <input type="text" class="form-control" name="txtDireccion" value="<?php echo $txtDireccion ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="text" class="form-control" name="telefono" value="<?php echo $telefono ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" class="form-control" name="correo" value="<?php echo $correo ?>" required>
                    </div>
                    <div class="form-group">
                        <label>RFC:</label>
                        <input type="text" class="form-control" name="RFC" value="<?php echo $RFC ?>" required>
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="accion" value="Agregar" class="btn btn-success">Agregar</button>
                        <button type="submit" name="accion" value="Actualizar" class="btn btn-warning">Actualizar</button>
                        <button type="submit" name="accion" value="Cancelar" class="btn btn-info">Limpiar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla Clientes -->
        <div class="card">
            <div class="card-header">Listado de Clientes</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Razón Social</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listaClientes as $cliente) { ?>
                        <tr>
                            <td><?php echo $cliente['id_cliente']; ?></td>
                            <td><?php echo $cliente['razon_social']; ?></td>
                            <td><?php echo $cliente['direccion']; ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="txtID" value="<?php echo $cliente['id_cliente']; ?>">
                                    <button type="submit" name="accion" value="Seleccionar" class="btn btn-primary btn-sm">Seleccionar</button>
                                    <button type="submit" name="accion" value="Borrar" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Sucursales (solo visible cuando hay cliente seleccionado) -->
    <?php if ($txtID != "") { ?>
    <div class="col-md-6">
        <!-- Formulario Sucursales -->
        <div class="card mb-4">
            <div class="card-header">Agregar Sucursal</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="txtID" value="<?php echo $txtID; ?>">
                    <div class="form-group">
                        <label>Nombre Sucursal:</label>
                        <input type="text" class="form-control" name="nombreSucursal" required>
                    </div>
                    <div class="form-group">
                        <label>Dirección:</label>
                        <input type="text" class="form-control" name="direccionSucursal" required>
                    </div>
                    <button type="submit" name="accion" value="AgregarSucursal" class="btn btn-success">Agregar Sucursal</button>
                </form>
            </div>
        </div>

        <!-- Tabla Sucursales -->
        <div class="card">
            <div class="card-header">Sucursales del Cliente</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sucursalesCliente as $sucursal) { ?>
                        <tr>
                            <td><?php echo $sucursal['nombre']; ?></td>
                            <td><?php echo $sucursal['direccion']; ?></td>
                            <td>
                                <a href="bitacoras.php?id_sucursal=<?php echo $sucursal['id_sucursal']; ?>" class="btn btn-info btn-sm">
                                    Ver Bitácoras
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<?php include("../Template/pie.php") ?>