<?php 
include("../Template/cabecera.php"); 
include("../Controller/conexion.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $nombre_usuario = $_POST['nombre_usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nombre_completo = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    $sentenciaSQL = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, password, nombre_completo, email, rol) VALUES (:nombre_usuario, :password, :nombre_completo, :email, :rol)");
    $sentenciaSQL->bindParam(':nombre_usuario', $nombre_usuario);
    $sentenciaSQL->bindParam(':password', $password);
    $sentenciaSQL->bindParam(':nombre_completo', $nombre_completo);
    $sentenciaSQL->bindParam(':email', $email);
    $sentenciaSQL->bindParam(':rol', $rol);
    $sentenciaSQL->execute();

    header('Location: login.php');
    exit();
}
?>

<div class="registro">
    <form method="POST" action="">
        <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="text" name="nombre_completo" placeholder="Nombre completo">
        <input type="email" name="email" placeholder="Correo electrónico">
        <select name="rol">
            <option value="usuario">Usuario</option>
            <option value="admin">Administrador</option>
        </select>
        <button type="submit" name="registrar" class="enviar">Registrar</button>
    </form>
</div>

<?php include("../Template/pie.php"); ?>
