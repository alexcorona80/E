<?php 
session_start();
include("../Template/cabecera.php"); 
include("../Controller/conexion.php"); 

// Manejo de envío del formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $sentenciaSQL = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario = :usuario");
    $sentenciaSQL->bindParam(':usuario', $usuario);
    $sentenciaSQL->execute();
    $usuarioBD = $sentenciaSQL->fetch(PDO::FETCH_ASSOC);

    if ($usuarioBD && password_verify($password, $usuarioBD['password'])) {
        $_SESSION['usuario'] = $usuarioBD['nombre_usuario'];
        $_SESSION['rol'] = $usuarioBD['rol'];
        header('Location: admin.php');
        exit();
    } else {
        $mensaje = "Nombre de usuario o contraseña incorrectos.";
    }
}
?>



<div class="login">
    <form method="POST" action="">
        <input type="text" name="usuario" placeholder="Ingresa tu nombre de usuario" required>
        <input type="password" name="password" id="password" placeholder="Ingresa tu contraseña" required>
        <button type="submit" name="login" class="enviar">Enviar</button>
        <button type="button" id="registrarBtn" class="registrar">Registrar</button> <!-- Botón con ID para JavaScript -->
        <?php if(isset($mensaje)) { echo "<p>$mensaje</p>"; } ?>
    </form>
</div>

<script>
    // Selecciona el botón "Registrar" y agrega un evento de clic
    document.getElementById('registrarBtn').addEventListener('click', function() {
        window.location.href = 'registro.php'; // Redirige a la página de registro
    });
</script>


<?php include("../Template/pie.php"); ?>

