<?php include("../Template/cabecera.php") ?>
<?php

/*print_r($_POST);*/
$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtUbicacion=(isset($_POST['txtUbicacion']))?$_POST['txtUbicacion']:"";
$txtTipo=(isset($_POST['txtTipo']))?$_POST['txtTipo']:"";
$floatCapacidad=(isset($_POST['floatCapacidad']))?$_POST['floatCapacidad']:"";
$fecha_mantenimiento=(isset($_POST['fecha_mantenimiento']))?$_POST['fecha_mantenimiento']:"";
$fecha_PH=(isset($_POST['fecha_PH']))?$_POST['fecha_PH']:"";
$txtObservaciones=(isset($_POST['txtObservaciones']))?$_POST['txtObservaciones']:"";
$accion=(isset($_POST['accion']))?$_POST['accion']:"";

include("../Controller/conexion.php");

switch($accion){
    case"Agregar":

        $sentenciaSQL = $conexion->prepare("INSERT INTO extintores (Ubicacion, Tipo, Capacidad, f_mantenimiento, f_ph, Observaciones) VALUES (:Ubicacion, :Tipo, :Capacidad, :f_mantenimiento, :f_ph, :Observaciones);");
        $sentenciaSQL->bindParam(':ubicacion',$txtUbicacion);
        $sentenciaSQL->bindParam(':tipo',$txtTipo);
        $sentenciaSQL->bindParam(':capacidad',$floatCapacidad);
        $sentenciaSQL->bindParam(':f_mantenimiento',$fecha_mantenimiento);
        $sentenciaSQL->bindParam(':f_ph',$fecha_PH);
        $sentenciaSQL->bindParam(':observaciones',$txtObservaciones);
        $sentenciaSQL->execute();

       
        break;
        case"Actualizar":
            $sentenciaSQL = $conexion->prepare("UPDATE extintores set observaciones=:observaciones, ubicacion=:ubicacion, f_mantenimiento=:f_mantenimiento, f_ph=:f_ph WHERE id_extintor =:id_extintor");
            $sentenciaSQL->bindParam(':observaciones',$txtObservaciones);
            $sentenciaSQL->bindParam(':ubicacion',$txtUbicacion);
            $sentenciaSQL->bindParam(':f_mantenimiento',$fecha_mantenimiento);
            $sentenciaSQL->bindParam(':f_ph',$fecha_PH);
            $sentenciaSQL->bindParam(':id_extintor',$txtID);
            $sentenciaSQL->execute();
            break;
            case"Cancelar":
                
                break;
                case"Seleccionar":
                    $sentenciaSQL = $conexion->prepare("SELECT * FROM extintores WHERE id_extintor =:id");
                    $sentenciaSQL->bindParam(':id',$txtID);
                    $sentenciaSQL->execute();
                    $extintor = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

                    $txtUbicacion = $extintor['ubicacion'];
                    $txtTipo = $extintor['tipo'];
                    $floatCapacidad = $extintor['capacidad'];
                    $fecha_mantenimiento = $extintor['f_mantenimiento'];
                    $fecha_PH = $extintor['f_ph'];
                    $txtObservaciones = $extintor["observaciones"];

                    /*$sentenciaSQL = $conexion->prepare("SELECT * FROM clientes WHERE id_cliente =:id");
                    $sentenciaSQL->bindParam(':id',$txtID);
                    $sentenciaSQL->execute();
                    $extintor = $sentenciaSQL->fetch(PDO::FETCH_LAZY);
                    
                    $txtNombre = $cliente['razon_social'];



                    */

                    break;
                    case"Borrar":
                        $sentenciaSQL = $conexion->prepare("DELETE FROM extintores WHERE id_extintor =:id");
                        $sentenciaSQL->bindParam(':id',$txtID);
                        $sentenciaSQL->execute();
                        break;
                        
}
/*$sentenciaSQL = $conexion->prepare("SELECT * FROM clientes WHERE id_cliente =:id");*/
$sentenciaSQL = $conexion->prepare("SELECT * FROM extintores");
$sentenciaSQL->execute();
$listaExtintores = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);



?>

<div class="col-md-5">
<div class="card">
        <div class="card-header">Datos de Extintores</div>
        <div class="card-body">
            
            <form method="POST">
                <div class="form-group">
                    <label for="txtID">ID:</label>
                    <input type="number" class="form-control" value="<?php echo $txtID ?>" name="txtID" id="txtID"  placeholder="ID">
                </div>

                <div class="form-group">
                    <label for="txtUbicacion">Ubicación:</label>
                    <input type="text" class="form-control" value="<?php echo $txtUbicacion ?>" name="txtUbicacion" id="txtUbicacion"  placeholder="Ubicación">
                </div>

                <div class="form-group">
                    <label for="txtTipo">Tipo:</label>
                    <input type="text" class="form-control" value="<?php echo $txtTipo ?>" name="txtTipo" id="txtTipo"  placeholder="Tipo">
                </div>

                <div class="form-group">
                    <label for="floatCapacidad">Capacidad:</label>
                    <input type="text" class="form-control" value="<?php echo $floatCapacidad ?>" name="floatCapacidad" id="floatCapacidad"  placeholder="Capacidad">
                </div>

                <div class="form-group">
                    <label for="fecha_mantenimiento">Fecha Mantenimiento:</label>
                    <input type="date" class="form-control" value="<?php echo $fecha_mantenimiento ?>" name="fecha_mantenimiento" id="fecha_mantenimiento"  placeholder="Fecha de mantenimiento">
                </div>

                <div class="form-group">
                    <label for="fecha_PH">Fecha de Prueba Hidroestatica:</label>
                    <input type="date" class="form-control" value="<?php echo $fecha_PH ?>" name="fecha_PH" id="fecha_PH"  placeholder="Fecha de prueba Hidroestatica">
                </div>

                <div class="form-group">
                    <label for="txtObservaciones">Observaciones:</label>
                    <input type="text" class="form-control" value="<?php echo $txtObservaciones ?>" name="txtObservaciones" id="txtObservaciones"  placeholder="Ingrese texto">
                </div> <br/>
        
    
                <div class="btn-group" role="group" aria-label="">
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
    <div class="card" >
            <div class="card-header">
                Bitacora de extintores /*agregar cliente-sucursal*/<?php echo("Select * from clientes"); ?>
                </div>
                    <div class="card-body" style="width:100%;">
                        <table class="table table.responsive table-bordered table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Ubicacion</th>
                                <th>Tipo</th>
                                <th>Capacidad</th>
                                <th>Fecha Mantenimiento</th>
                                <th>Fecha Prueba Hidroestatica</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($listaExtintores as $extintor){ ?>
                            <tr>
                                <td><?php echo $extintor['id_extintor'];?></td>
                                <td><?php echo $extintor['ubicacion'];?></td>
                                <td><?php echo $extintor['tipo'];?></td>
                                <td><?php echo $extintor['capacidad'];?></td>
                                <td><?php echo $extintor['f_mantenimiento'];?></td>
                                <td><?php echo $extintor['f_ph'];?></td>
                                <td><?php echo $extintor['observaciones'];?></td>
                                
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="txtID" id="txtID" value="<?php echo $extintor['id_extintor'];?>"/>
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
