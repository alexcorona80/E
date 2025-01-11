<?php include("../Template/cabecera.php") ?>

<?php

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtRazon_social = (isset($_POST['txtRazon_social'])) ? $_POST['txtRazon_social'] : "";
$txtDireccion = (isset($_POST['txtDireccion'])) ? $_POST['txtDireccion'] : "";
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : "";
$correo = (isset($_POST['correo'])) ? $_POST['correo'] : "";
$RFC = (isset($_POST['RFC'])) ? $_POST['RFC'] : "";
$id_usuario = (isset($_POST['id_usuario'])) ? $_POST['id_usuario'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

include("../Controller/conexion.php");

switch($accion){
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
        // No action needed
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
        break;
        
    case "Borrar":
        $sentenciaSQL = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = :id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        break;
}

$sentenciaSQL = $conexion->prepare("SELECT * FROM clientes");
$sentenciaSQL->execute();
$listaClientes = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="col-md-5">
    <div class="card">
        <div class="card-header">Datos de Clientes</div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="txtID">ID:</label>
                    <input type="number" class="form-control" value="<?php echo $txtID ?>" name="txtID" id="txtID" placeholder="ID">
                </div>
                <div class="form-group">
                    <label for="txtRazon_social">Razon Social:</label>
                    <input type="text" class="form-control" value="<?php echo $txtRazon_social ?>" name="txtRazon_social" id="txtRazon_social" placeholder="Razon Social">
                </div>
                <div class="form-group">
                    <label for="txtDireccion">Direccion:</label>
                    <input type="text" class="form-control" value="<?php echo $txtDireccion ?>" name="txtDireccion" id="txtDireccion" placeholder="Direccion">
                </div>
                <div class="form-group">
                    <label for="telefono">Telefono:</label>
                    <input type="text" class="form-control" value="<?php echo $telefono ?>" name="telefono" id="telefono" placeholder="Telefono">
                </div>
                <div class="form-group">
                    <label for="correo">Email:</label>
                    <input type="email" class="form-control" value="<?php echo $correo ?>" name="correo" id="correo" placeholder="email">
                </div>
                <div class="form-group">
                    <label for="RFC">RFC:</label>
                    <input type="text" class="form-control" value="<?php echo $RFC ?>" name="RFC" id="RFC" placeholder="RFC">
                </div><br/>
                <div class="btn-group" role="group">
                    <button type="submit" name="accion" value="Agregar" class="btn btn-outline-success">Agregar</button>
                    <button type="submit" name="accion" value="Actualizar" class="btn btn-outline-warning">Actualizar</button>
                    <button type="submit" name="accion" value="Cancelar" class="btn btn-outline-info">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<br/>
<div class="col-sm-5" style="width: 50%;">
    <div class="card">
        <div class="card-header">
            Datos Clientes/<?php echo("Select * from clientes"); ?>
        </div>
        <div class="card-body" style="width:100%;">
            <table class="table table-responsive table-bordered table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Razon Social</th>
                        <th>Direccion</th>
                        <th>Telefono</th>
                        <th>Email</th>
                        <th>RFC</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaClientes as $cliente){ ?>
                    <tr>
                        <td><?php echo $cliente['id_cliente']; ?></td>
                        <td><?php echo $cliente['razon_social']; ?></td>
                        <td><?php echo $cliente['direccion']; ?></td>
                        <td><?php echo $cliente['telefono']; ?></td>
                        <td><?php echo $cliente['correo']; ?></td>
                        <td><?php echo $cliente['rfc']; ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="txtID" value="<?php echo $cliente['id_cliente']; ?>"/>
                                <input type="submit" name="accion" value="Seleccionar" class="btn btn-outline-primary btn-sm"/>
                                <input type="submit" name="accion" value="Borrar" class="btn btn-outline-danger btn-sm"/>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("../Template/pie.php") ?>
